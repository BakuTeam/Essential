<?php

/*
 * This file is part of BedrockProtocol.
 * Copyright (C) 2014-2022 PocketMine Team <https://github.com/pmmp/BedrockProtocol>
 *
 * BedrockProtocol is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 */

declare(strict_types=1);

namespace pocketmine\network\mcpe\protocol;

use pocketmine\data\bedrock\BedrockDataFiles;
use pocketmine\network\mcpe\convert\TypeConverter;
use pocketmine\network\mcpe\protocol\serializer\PacketSerializer;
use pocketmine\network\mcpe\protocol\types\BlockPosition;
use pocketmine\utils\Filesystem;
use function is_array;
use function json_decode;

class UpdateBlockPacket extends DataPacket implements ClientboundPacket{
	public const NETWORK_ID = ProtocolInfo::UPDATE_BLOCK_PACKET;

	public const FLAG_NONE = 0b0000;
	public const FLAG_NEIGHBORS = 0b0001;
	public const FLAG_NETWORK = 0b0010;
	public const FLAG_NOGRAPHIC = 0b0100;
	public const FLAG_PRIORITY = 0b1000;

	public const DATA_LAYER_NORMAL = 0;
	public const DATA_LAYER_LIQUID = 1;

	public BlockPosition $blockPosition;
	public int $blockRuntimeId;
	private static ?array $legacyBlockIdMap1_1_5 = null;
	private static array $legacyRuntimeIdCache = [];
	/**
	 * @var int
	 * Flags are used by MCPE internally for block setting, but only flag 2 (network flag) is relevant for network.
	 * This field is pointless really.
	 */
	public int $flags = self::FLAG_NETWORK;
	public int $dataLayerId = self::DATA_LAYER_NORMAL;

	/**
	 * @generate-create-func
	 */
	public static function create(BlockPosition $blockPosition, int $blockRuntimeId, int $flags, int $dataLayerId) : self{
		$result = new self;
		$result->blockPosition = $blockPosition;
		$result->blockRuntimeId = $blockRuntimeId;
		$result->flags = $flags;
		$result->dataLayerId = $dataLayerId;
		return $result;
	}

	protected function decodePayload(PacketSerializer $in) : void{
		$this->blockPosition = $in->getBlockPosition($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_10);
		if($in->getProtocolId() <= ProtocolInfo::PROTOCOL_1_1_5){
			$legacyBlockId = $in->getUnsignedVarInt();
			$aux = $in->getUnsignedVarInt();
			$this->blockRuntimeId = $legacyBlockId;
			$this->flags = $aux >> 4;
			$this->dataLayerId = self::DATA_LAYER_NORMAL;
			return;
		}
		$this->blockRuntimeId = $in->getUnsignedVarInt();
		$this->flags = $in->getUnsignedVarInt();
		$this->dataLayerId = $in->getUnsignedVarInt();
	}

	protected function encodePayload(PacketSerializer $out) : void{
		$out->putBlockPosition($this->blockPosition, $out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_10);
		if($out->getProtocolId() <= ProtocolInfo::PROTOCOL_1_1_5){
			[$legacyBlockId, $legacyMeta] = self::legacyRuntimeIdToIdMeta($this->blockRuntimeId, $out->getProtocolId());
			$out->putUnsignedVarInt($legacyBlockId);
			$out->putUnsignedVarInt(($this->flags << 4) | ($legacyMeta & 0x0f));
			return;
		}
		$out->putUnsignedVarInt($this->blockRuntimeId);
		$out->putUnsignedVarInt($this->flags);
		$out->putUnsignedVarInt($this->dataLayerId);
	}

	/**
	 * @phpstan-return array{int, int}
	 */
	private static function legacyRuntimeIdToIdMeta(int $runtimeId, int $protocolId) : array{
		if(isset(self::$legacyRuntimeIdCache[$protocolId][$runtimeId])){
			return self::$legacyRuntimeIdCache[$protocolId][$runtimeId];
		}

		$blockTranslator = TypeConverter::getInstance($protocolId)->getBlockTranslator();
		$state = $blockTranslator->getBlockStateDictionary()->generateDataFromStateId($runtimeId);
		if($state === null){
			return self::$legacyRuntimeIdCache[$protocolId][$runtimeId] = [0, 0];
		}

		$name = self::legacyBlockName($state->getName());
		$legacyId = self::getLegacyBlockIdMap1_1_5()[$name] ?? 0;
		if($legacyId < 0 || $legacyId > 255){
			$legacyId = 0;
		}
		$legacyMeta = $blockTranslator->getBlockStateDictionary()->getMetaFromStateId($runtimeId) ?? 0;

		return self::$legacyRuntimeIdCache[$protocolId][$runtimeId] = [$legacyId, $legacyMeta & 0x0f];
	}

	private static function legacyBlockName(string $name) : string{
		return match($name){
			"minecraft:grass_block" => "minecraft:grass",
			"minecraft:oak_planks", "minecraft:spruce_planks", "minecraft:birch_planks", "minecraft:jungle_planks", "minecraft:acacia_planks", "minecraft:dark_oak_planks" => "minecraft:planks",
			"minecraft:oak_log", "minecraft:spruce_log", "minecraft:birch_log", "minecraft:jungle_log" => "minecraft:log",
			"minecraft:acacia_log", "minecraft:dark_oak_log" => "minecraft:log2",
			"minecraft:oak_leaves", "minecraft:spruce_leaves", "minecraft:birch_leaves", "minecraft:jungle_leaves" => "minecraft:leaves",
			"minecraft:acacia_leaves", "minecraft:dark_oak_leaves" => "minecraft:leaves2",
			"minecraft:white_wool", "minecraft:orange_wool", "minecraft:magenta_wool", "minecraft:light_blue_wool", "minecraft:yellow_wool", "minecraft:lime_wool", "minecraft:pink_wool", "minecraft:gray_wool", "minecraft:light_gray_wool", "minecraft:cyan_wool", "minecraft:purple_wool", "minecraft:blue_wool", "minecraft:brown_wool", "minecraft:green_wool", "minecraft:red_wool", "minecraft:black_wool" => "minecraft:wool",
			default => $name,
		};
	}

	private static function getLegacyBlockIdMap1_1_5() : array{
		if(self::$legacyBlockIdMap1_1_5 === null){
			$decoded = json_decode(Filesystem::fileGetContents(BedrockDataFiles::LEGACY_BLOCK_ID_MAP_1_1_5_JSON), true);
			if(!is_array($decoded)){
				throw new \RuntimeException("Invalid legacy 1.1.5 block ID map");
			}
			self::$legacyBlockIdMap1_1_5 = $decoded;
		}

		return self::$legacyBlockIdMap1_1_5;
	}

	public function handle(PacketHandlerInterface $handler) : bool{
		return $handler->handleUpdateBlock($this);
	}
}
