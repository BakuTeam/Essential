<?php

/*
 *
 *  _____                    _   _       _
 * | ____|___ ___  ___ _ __ | |_(_) __ _| |
 * |  _| / __/ __|/ _ \ '_ \| __| |/ _` | |
 * | |___\__ \__ \  __/ | | | |_| | (_| | |
 * |_____|___/___/\___|_| |_|\__|_|\__,_|_|
 *
 * Essential — PocketMine-MP Fork
 * Supported MCPE/Bedrock versions: 1.12, 1.16 - 1.26.x
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author Essential Team
 * @link https://github.com/BakuTeam/Essential
 *
 *
 */

declare(strict_types=1);

namespace pocketmine\network\mcpe\convert;

use InvalidArgumentException;
use pocketmine\data\bedrock\block\BlockStateData;
use pocketmine\data\bedrock\block\BlockStateDeserializeException;
use pocketmine\data\bedrock\block\BlockTypeNames;
use pocketmine\data\bedrock\item\BlockItemIdMap;
use pocketmine\data\bedrock\item\downgrade\ItemIdMetaDowngrader;
use pocketmine\data\bedrock\item\ItemDeserializer;
use pocketmine\data\bedrock\item\ItemSerializer;
use pocketmine\data\bedrock\item\ItemTypeDeserializeException;
use pocketmine\data\bedrock\item\ItemTypeSerializeException;
use pocketmine\data\bedrock\item\SavedItemData;
use pocketmine\item\Item;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\ProtocolInfo;
use pocketmine\network\mcpe\protocol\serializer\ItemTypeDictionary;
use pocketmine\utils\AssumptionFailedError;
use pocketmine\world\format\io\GlobalBlockStateHandlers;
use pocketmine\world\format\io\GlobalItemDataHandlers;

/**
 * This class handles translation between network item ID+metadata to PocketMine-MP internal ID+metadata and vice versa.
 */
final class ItemTranslator{
	public const NO_BLOCK_RUNTIME_ID = 0; //this is technically a valid block runtime ID, but is used to represent "no block" (derp mojang)

	public function __construct(
		private ItemTypeDictionary $itemTypeDictionary,
		private BlockStateDictionary $blockStateDictionary,
		private ItemSerializer $itemSerializer,
		private ItemDeserializer $itemDeserializer,
		private BlockItemIdMap $blockItemIdMap,
		private ItemIdMetaDowngrader $itemDataDowngrader,
		private int $protocolId = ProtocolInfo::CURRENT_PROTOCOL,
	){}

	/**
	 * @return int[]|null
	 * @phpstan-return array{int, int, ?int}|null
	 */
	public function toNetworkIdQuiet(Item $item) : ?array{
		try{
			return $this->toNetworkId($item);
		}catch(ItemTypeSerializeException){
			return null;
		}
	}

	public function isItemTypeNetworkCompatible(Item $item) : bool{
		try{
			$itemData = $this->itemSerializer->serializeType($item, $this->itemDataDowngrader);
		}catch(ItemTypeSerializeException){
			return false;
		}
		try{
			$this->itemTypeDictionary->fromStringId($itemData->getName());
		}catch(InvalidArgumentException){
			return false;
		}
		return true;
	}

	/**
	 * @return int[]
	 * @phpstan-return array{int, int, ?int}
	 *
	 * @throws ItemTypeSerializeException
	 */
	public function toNetworkId(Item $item) : array{
		//TODO: we should probably come up with a cache for this

		$itemData = $this->itemSerializer->serializeType($item, $this->itemDataDowngrader);

		try {
			$numericId = $this->itemTypeDictionary->fromStringId($itemData->getName());
		} catch (InvalidArgumentException) {
			try {
				$numericId = $this->itemTypeDictionary->fromStringId("minecraft:dirt");
			} catch (InvalidArgumentException) {
				throw new ItemTypeSerializeException("Unknown item type " . $itemData->getName() . " and fallback dirt also missing");
			}
		}

		$blockStateData = $itemData->getBlock();

		if($blockStateData !== null){
			$blockRuntimeId = $this->blockStateDictionary->lookupStateIdFromData($blockStateData);
			if($blockRuntimeId === null){
				$blockRuntimeId = $this->blockStateDictionary->lookupStateIdFromData(BlockStateData::current(BlockTypeNames::DIRT, []));
				if ($blockRuntimeId === null) {
					throw new AssumptionFailedError("Unmapped blockstate returned by blockstate serializer: " . $blockStateData->toNbt() . " and fallback dirt missing");
				}
			}
		}else{
			$blockRuntimeId = null;
		}

		return [$numericId, $itemData->getMeta(), $blockRuntimeId];
	}

	/**
	 * @throws ItemTypeSerializeException
	 */
	public function toNetworkNbt(Item $item) : CompoundTag{
		//TODO: this relies on the assumption that network item NBT is the same as disk item NBT, which may not always
		//be true - if we stick on an older world version while updating network version, this could be a problem (and
		//may be a problem for multi version implementations)
		return $this->itemSerializer->serializeStack($item, null, $this->itemDataDowngrader)->toNbt();
	}

	/**
	 * @throws TypeConversionException
	 */
	public function fromNetworkId(int $networkId, int $networkMeta, int $networkBlockRuntimeId) : Item{
		try{
			$stringId = $this->itemTypeDictionary->fromIntId($networkId);
		}catch(InvalidArgumentException $e){
			//TODO: a quiet version of fromIntId() would be better than catching InvalidArgumentException
			throw TypeConversionException::wrap($e, "Invalid network itemstack ID $networkId");
		}

		$blockStateData = null;
		if($this->protocolId < ProtocolInfo::PROTOCOL_1_16_220){
			$legacyId = $stringId;
			$legacyMeta = $networkMeta;
			[$stringId, $networkMeta] = GlobalItemDataHandlers::getUpgrader()->getIdMetaUpgrader()->upgrade($stringId, $networkMeta);

			$blockId = $this->blockItemIdMap->lookupBlockId($stringId);
			if($blockId !== null){
				try{
					$blockStateData = GlobalBlockStateHandlers::getUpgrader()->getBlockIdMetaUpgrader()->fromStringIdMeta($legacyId, $legacyMeta);
				}catch(BlockStateDeserializeException){
					$blockStateData = BlockStateData::current($blockId, []);
				}
			}
		}else{
			if($this->blockItemIdMap->lookupBlockId($stringId) !== null){
				$blockStateData = $this->blockStateDictionary->generateCurrentDataFromStateId($networkBlockRuntimeId);
				if($blockStateData === null){
					throw new TypeConversionException("Blockstate runtimeID $networkBlockRuntimeId does not correspond to any known blockstate");
				}
			}elseif($networkBlockRuntimeId !== self::NO_BLOCK_RUNTIME_ID){
				throw new TypeConversionException("Item $stringId is not a blockitem, but runtime ID $networkBlockRuntimeId was provided");
			}

			[$stringId, $networkMeta] = GlobalItemDataHandlers::getUpgrader()->getIdMetaUpgrader()->upgrade($stringId, $networkMeta);
		}

		try{
			return $this->itemDeserializer->deserializeType(new SavedItemData($stringId, $networkMeta, $blockStateData));
		}catch(ItemTypeDeserializeException $e){
			throw TypeConversionException::wrap($e, "Invalid network itemstack data");
		}
	}

	public static function getItemSchemaId(int $protocolId) : int{
		return match($protocolId){
			ProtocolInfo::PROTOCOL_1_26_45,
			ProtocolInfo::PROTOCOL_1_26_40 => 271,

			ProtocolInfo::PROTOCOL_1_26_30,
			ProtocolInfo::PROTOCOL_1_26_20 => 261,

			ProtocolInfo::PROTOCOL_1_26_10,
			ProtocolInfo::PROTOCOL_1_26_0,
			ProtocolInfo::PROTOCOL_1_21_130,
			ProtocolInfo::PROTOCOL_1_21_124,
			ProtocolInfo::PROTOCOL_1_21_120,
			ProtocolInfo::PROTOCOL_1_21_110 => 251,
			ProtocolInfo::PROTOCOL_1_21_100 => 241,

			ProtocolInfo::PROTOCOL_1_21_93,
			ProtocolInfo::PROTOCOL_1_21_90,
			ProtocolInfo::PROTOCOL_1_21_80,
			ProtocolInfo::PROTOCOL_1_21_70,
			ProtocolInfo::PROTOCOL_1_21_60,
			ProtocolInfo::PROTOCOL_1_21_50 => 231,

			ProtocolInfo::PROTOCOL_1_21_40 => 221,

			ProtocolInfo::PROTOCOL_1_21_30 => 211,

			ProtocolInfo::PROTOCOL_1_21_20 => 201,

			ProtocolInfo::PROTOCOL_1_21_2,
			ProtocolInfo::PROTOCOL_1_21_0 => 191,

			ProtocolInfo::PROTOCOL_1_20_80 => 181,

			ProtocolInfo::PROTOCOL_1_20_70 => 171,

			ProtocolInfo::PROTOCOL_1_20_60 => 161,

			ProtocolInfo::PROTOCOL_1_20_50 => 151,

			ProtocolInfo::PROTOCOL_1_20_40,
			ProtocolInfo::PROTOCOL_1_20_30 => 141,

			ProtocolInfo::PROTOCOL_1_20_10 => 121,

			ProtocolInfo::PROTOCOL_1_20_0 => 111,
			ProtocolInfo::PROTOCOL_1_19_80 => 101,

			ProtocolInfo::PROTOCOL_1_19_70 => 91,

			ProtocolInfo::PROTOCOL_1_19_63,
			ProtocolInfo::PROTOCOL_1_19_60,
			ProtocolInfo::PROTOCOL_1_19_50,
			ProtocolInfo::PROTOCOL_1_19_40,
			ProtocolInfo::PROTOCOL_1_19_30 => 81,

			ProtocolInfo::PROTOCOL_1_19_21,
			ProtocolInfo::PROTOCOL_1_19_20,
			ProtocolInfo::PROTOCOL_1_19_10,
			ProtocolInfo::PROTOCOL_1_19_0,
			ProtocolInfo::PROTOCOL_1_18_30 => 71,

			ProtocolInfo::PROTOCOL_1_18_10 => 61,

			ProtocolInfo::PROTOCOL_1_18_0,
			ProtocolInfo::PROTOCOL_1_17_40 => 51,

			ProtocolInfo::PROTOCOL_1_17_30,
			ProtocolInfo::PROTOCOL_1_17_10,
			ProtocolInfo::PROTOCOL_1_17_0,
			ProtocolInfo::PROTOCOL_1_16_220,
			ProtocolInfo::PROTOCOL_1_16_210 => 41,

			ProtocolInfo::PROTOCOL_1_16_200 => 31,

			ProtocolInfo::PROTOCOL_1_16_100 => 21,

			ProtocolInfo::PROTOCOL_1_16_20,
			ProtocolInfo::PROTOCOL_1_16_0,
			ProtocolInfo::PROTOCOL_1_14_60,
			ProtocolInfo::PROTOCOL_1_14_0 => 11,

			default => throw new AssumptionFailedError("Unknown protocol ID $protocolId"),
		};
	}
}
