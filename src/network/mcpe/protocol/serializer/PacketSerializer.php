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

namespace pocketmine\network\mcpe\protocol\serializer;

use pocketmine\math\Vector2;
use pocketmine\math\Vector3;
use pocketmine\nbt\LittleEndianNbtSerializer;
use pocketmine\nbt\NbtDataException;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\TreeRoot;
use pocketmine\network\mcpe\convert\TypeConverter;
use pocketmine\network\mcpe\protocol\PacketDecodeException;
use pocketmine\network\mcpe\protocol\ProtocolInfo;
use pocketmine\network\mcpe\protocol\types\BlockPosition;
use pocketmine\network\mcpe\protocol\types\BoolGameRule;
use pocketmine\network\mcpe\protocol\types\command\CommandOriginData;
use pocketmine\network\mcpe\protocol\types\entity\BlockPosMetadataProperty;
use pocketmine\network\mcpe\protocol\types\entity\ByteMetadataProperty;
use pocketmine\network\mcpe\protocol\types\entity\CompoundTagMetadataProperty;
use pocketmine\network\mcpe\protocol\types\entity\EntityLink;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataFlags;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;
use pocketmine\network\mcpe\protocol\types\entity\FloatMetadataProperty;
use pocketmine\network\mcpe\protocol\types\entity\IntMetadataProperty;
use pocketmine\network\mcpe\protocol\types\entity\LongMetadataProperty;
use pocketmine\network\mcpe\protocol\types\entity\MetadataProperty;
use pocketmine\network\mcpe\protocol\types\entity\ShortMetadataProperty;
use pocketmine\network\mcpe\protocol\types\entity\StringMetadataProperty;
use pocketmine\network\mcpe\protocol\types\entity\Vec3MetadataProperty;
use pocketmine\network\mcpe\protocol\types\FloatGameRule;
use pocketmine\network\mcpe\protocol\types\GameRule;
use pocketmine\network\mcpe\protocol\types\IntGameRule;
use pocketmine\network\mcpe\protocol\types\inventory\ItemStack;
use pocketmine\network\mcpe\protocol\types\inventory\ItemStackWrapper;
use pocketmine\network\mcpe\protocol\types\recipe\ComplexAliasItemDescriptor;
use pocketmine\network\mcpe\protocol\types\recipe\IntIdMetaItemDescriptor;
use pocketmine\network\mcpe\protocol\types\recipe\ItemDescriptorType;
use pocketmine\network\mcpe\protocol\types\recipe\MolangItemDescriptor;
use pocketmine\network\mcpe\protocol\types\recipe\RecipeIngredient;
use pocketmine\network\mcpe\protocol\types\recipe\StringIdMetaItemDescriptor;
use pocketmine\network\mcpe\protocol\types\recipe\TagItemDescriptor;
use pocketmine\network\mcpe\protocol\types\skin\PersonaPieceTintColor;
use pocketmine\network\mcpe\protocol\types\skin\PersonaSkinPiece;
use pocketmine\network\mcpe\protocol\types\skin\SkinAnimation;
use pocketmine\network\mcpe\protocol\types\skin\SkinData;
use pocketmine\network\mcpe\protocol\types\skin\SkinImage;
use pocketmine\network\mcpe\protocol\types\StructureEditorData;
use pocketmine\network\mcpe\protocol\types\StructureSettings;
use pocketmine\utils\Binary;
use pocketmine\utils\BinaryDataException;
use pocketmine\utils\BinaryStream;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use function count;
use function strlen;
use function strrev;
use function substr;

class PacketSerializer extends BinaryStream{

	private int $shieldItemRuntimeId;
	protected function __construct(private int $protocolId, string $buffer = "", int $offset = 0){
		//overridden to change visibility
		parent::__construct($buffer, $offset);

		$this->shieldItemRuntimeId = TypeConverter::getInstance($protocolId)->getItemTypeDictionary()->fromStringId("minecraft:shield");
	}

	public static function encoder(int $protocolId) : self{
		return new self($protocolId);
	}

	public static function decoder(int $protocolId, string $buffer, int $offset) : self{
		return new self($protocolId, $buffer, $offset);
	}

	public function getProtocolId() : int{
		return $this->protocolId;
	}

	/**
	 * @throws BinaryDataException
	 */
	public function getString() : string{
		return $this->get($this->getUnsignedVarInt());
	}

	public function putString(string $v) : void{
		$this->putUnsignedVarInt(strlen($v));
		$this->put($v);
	}

	/**
	 * @throws BinaryDataException
	 */
	public function getUUID() : UuidInterface{
		//This is two little-endian longs: bytes 7-0 followed by bytes 15-8
		$p1 = strrev($this->get(8));
		$p2 = strrev($this->get(8));
		return Uuid::fromBytes($p1 . $p2);
	}

	public function putUUID(UuidInterface $uuid) : void{
		$bytes = $uuid->getBytes();
		$this->put(strrev(substr($bytes, 0, 8)));
		$this->put(strrev(substr($bytes, 8, 8)));
	}

	public function getSkin() : SkinData{
		$skinId = $this->getString();
		if($this->getProtocolId() >= ProtocolInfo::PROTOCOL_1_16_210){
			$skinPlayFabId = $this->getString();
		}
		$skinResourcePatch = $this->getString();
		$skinData = $this->getSkinImage();
		$animationCount = $this->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_40 ? $this->getUnsignedVarInt() : $this->getLInt();
		$animations = [];
		for($i = 0; $i < $animationCount; ++$i){
			$skinImage = $this->getSkinImage();
			$animationType = $this->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_40 ? $this->getUnsignedVarInt() : $this->getLInt();
			$animationFrames = $this->getLFloat();

			if($this->getProtocolId() >= ProtocolInfo::PROTOCOL_1_16_100){
				$expressionType = $this->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_40 ? $this->getUnsignedVarInt() : $this->getLInt();
			}
			$animations[] = new SkinAnimation($skinImage, $animationType, $animationFrames, $expressionType ?? 0);
		}
		$capeData = $this->getSkinImage();
		$geometryData = $this->getString();

		if($this->getProtocolId() >= ProtocolInfo::PROTOCOL_1_17_30){
			$geometryDataVersion = $this->getString();
		}

		$animationData = $this->getString();

		if($this->getProtocolId() < ProtocolInfo::PROTOCOL_1_17_30){
			$premium = $this->getBool();
			$persona = $this->getBool();
			$capeOnClassic = $this->getBool();
		}

		$capeId = $this->getString();
		$fullSkinId = $this->getString();
		if($this->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_40){
			$armSize = $this->getByte() === 0 ? SkinData::ARM_SIZE_SLIM : SkinData::ARM_SIZE_WIDE;
			$skinColor = self::skinColorFromInt($this->getLInt());
		}else{
			$armSize = $this->getString();
			$skinColor = $this->getString();
		}
		$personaPieceCount = $this->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_40 ? $this->getUnsignedVarInt() : $this->getLInt();
		$personaPieces = [];
		for($i = 0; $i < $personaPieceCount; ++$i){
			$pieceId = $this->getString();
			if($this->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_40){
				$pieceType = self::personaPieceTypeFromOrdinal($this->getLInt());
				$packId = $this->getUUID()->toString();
			}else{
				$pieceType = $this->getString();
				$packId = $this->getString();
			}
			$isDefaultPiece = $this->getBool();
			$productId = $this->getString();
			$personaPieces[] = new PersonaSkinPiece($pieceId, $pieceType, $packId, $isDefaultPiece, $productId);
		}
		$pieceTintColorCount = $this->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_40 ? $this->getUnsignedVarInt() : $this->getLInt();
		$pieceTintColors = [];
		for($i = 0; $i < $pieceTintColorCount; ++$i){
			$pieceType = $this->getString();
			if($this->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_40){
				//since 1.26.40 the colour count is fixed at 4 and each colour is a network ARGB integer
				$colors = [];
				for($j = 0; $j < PersonaPieceTintColor::EXPECTED_COLOR_COUNT; ++$j){
					$colors[] = self::skinColorFromInt($this->getLInt());
				}
			}else{
				$colorCount = $this->getLInt();
				$colors = [];
				for($j = 0; $j < $colorCount; ++$j){
					$colors[] = $this->getString();
				}
			}
			$pieceTintColors[] = new PersonaPieceTintColor(
				$pieceType,
				$colors
			);
		}

		if($this->getProtocolId() >= ProtocolInfo::PROTOCOL_1_17_30){
			$premium = $this->getBool();
			$persona = $this->getBool();
			$capeOnClassic = $this->getBool();
			$isPrimaryUser = $this->getBool();
		}

		$override = false;
		if($this->getProtocolId() >= ProtocolInfo::PROTOCOL_1_19_63){
			$override = $this->getBool();
		}

		if($this->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_40){
			$this->getString(); //trusted skin flag - not tracked by this fork
			$this->getString(); //profile hash - not tracked by this fork
		}

		return new SkinData(
			$skinId,
			$skinPlayFabId ?? "",
			$skinResourcePatch,
			$skinData,
			$animations,
			$capeData,
			$geometryData,
			$geometryDataVersion ?? "",
			$animationData,
			$capeId,
			$fullSkinId,
			$armSize,
			$skinColor,
			$personaPieces,
			$pieceTintColors,
			true,
			$premium,
			$persona,
			$capeOnClassic,
			$isPrimaryUser ?? true, //isPrimaryUser is only present on the wire since 1.17.30
			$override,
		);
	}

	/**
	 * Persona skin piece types, ordered so the array index matches the network ordinal used since 1.26.40.
	 */
	private const PERSONA_PIECE_TYPES = [
		"unknown", "skeleton", "body", "skin", "bottom", "feet", "dress", "top", "high_pants", "hands",
		"outerwear", "facialhair", "mouth", "eyes", "hair", "hood", "back", "faceaccessory", "head", "legs",
		"leftleg", "rightleg", "arms", "leftarm", "rightarm", "capes", "classicskin", "emote",
	];

	private static function personaPieceTypeToOrdinal(string $value) : int{
		$ordinal = array_search($value, self::PERSONA_PIECE_TYPES, true);
		return $ordinal === false ? 0 : $ordinal;
	}

	private static function personaPieceTypeFromOrdinal(int $ordinal) : string{
		return self::PERSONA_PIECE_TYPES[$ordinal] ?? "unknown";
	}

	/** Parses a "#AARRGGBB" style skin colour string into a network ARGB integer (since 1.26.40). */
	private static function skinColorToInt(string $color) : int{
		$hex = ltrim($color, "#");
		if($hex === "" || strlen($hex) > 8 || !ctype_xdigit($hex)){
			return 0;
		}
		return (int) hexdec($hex);
	}

	private static function skinColorFromInt(int $argb) : string{
		return sprintf("#%08x", $argb & 0xFFFFFFFF);
	}

	public function putSkin(SkinData $skin) : void{
		$this->putString($skin->getSkinId());

		if($this->getProtocolId() >= ProtocolInfo::PROTOCOL_1_16_210){
			$this->putString($skin->getPlayFabId());
		}

		$this->putString($skin->getResourcePatch());
		$this->putSkinImage($skin->getSkinImage());
		if($this->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_40){
			$this->putUnsignedVarInt(count($skin->getAnimations()));
		}else{
			$this->putLInt(count($skin->getAnimations()));
		}
		foreach($skin->getAnimations() as $animation){
			$this->putSkinImage($animation->getImage());
			if($this->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_40){
				$this->putUnsignedVarInt($animation->getType());
			}else{
				$this->putLInt($animation->getType());
			}
			$this->putLFloat($animation->getFrames());

			if($this->getProtocolId() >= ProtocolInfo::PROTOCOL_1_16_100){
				if($this->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_40){
					$this->putUnsignedVarInt($animation->getExpressionType());
				}else{
					$this->putLInt($animation->getExpressionType());
				}
			}
		}
		$this->putSkinImage($skin->getCapeImage());
		$geometryData = $skin->getGeometryData();
		if($geometryData === "" && $this->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_40){
			//since 1.26.40 the client drops the connection on an empty geometry string; older clients
			//require the raw empty value or the referenced default geometry fails to resolve (invisible players)
			$geometryData = "{}";
		}
		$this->putString($geometryData);

		if($this->getProtocolId() >= ProtocolInfo::PROTOCOL_1_17_30){
			//1.26.40 expects the legacy engine version marker; older clients expect the network version string
			$this->putString($this->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_40 ? "0.0.0" : $skin->getGeometryDataEngineVersion());
		}

		$this->putString($skin->getAnimationData());

		if($this->getProtocolId() < ProtocolInfo::PROTOCOL_1_17_30){
			$this->putBool($skin->isPremium());
			$this->putBool($skin->isPersona());
			$this->putBool($skin->isPersonaCapeOnClassic());
		}

		$this->putString($skin->getCapeId());
		$this->putString($skin->getFullSkinId());
		if($this->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_40){
			$this->putByte($skin->getArmSize() === SkinData::ARM_SIZE_SLIM ? 0 : 1);
			$this->putLInt(self::skinColorToInt($skin->getSkinColor()));
		}else{
			$this->putString($skin->getArmSize());
			$this->putString($skin->getSkinColor());
		}
		if($this->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_40){
			$this->putUnsignedVarInt(count($skin->getPersonaPieces()));
		}else{
			$this->putLInt(count($skin->getPersonaPieces()));
		}
		foreach($skin->getPersonaPieces() as $piece){
			$this->putString($piece->getPieceId());
			if($this->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_40){
				$this->putLInt(self::personaPieceTypeToOrdinal($piece->getPieceType()));
				$this->putUUID(\Ramsey\Uuid\Uuid::fromString($piece->getPackId()));
			}else{
				$this->putString($piece->getPieceType());
				$this->putString($piece->getPackId());
			}
			$this->putBool($piece->isDefaultPiece());
			$this->putString($piece->getProductId());
		}
		if($this->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_40){
			$this->putUnsignedVarInt(count($skin->getPieceTintColors()));
		}else{
			$this->putLInt(count($skin->getPieceTintColors()));
		}
		foreach($skin->getPieceTintColors() as $tint){
			$this->putString($tint->getPieceType());
			if($this->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_40){
				foreach($tint->getColors() as $color){
					$this->putLInt(self::skinColorToInt($color));
				}
			}else{
				$this->putLInt(count($tint->getColors()));
				foreach($tint->getColors() as $color){
					$this->putString($color);
				}
			}
		}

		if($this->getProtocolId() >= ProtocolInfo::PROTOCOL_1_17_30){
			$this->putBool($skin->isPremium());
			$this->putBool($skin->isPersona());
			$this->putBool($skin->isPersonaCapeOnClassic());
			$this->putBool($skin->isPrimaryUser());
		}

		if($this->getProtocolId() >= ProtocolInfo::PROTOCOL_1_19_63){
			$this->putBool($skin->isOverride());
		}
		if($this->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_40){
			$this->putString($skin->isVerified() ? "true" : "false");
			$this->putString(""); //profile hash - not tracked by this fork
		}
	}

	private function getSkinImage() : SkinImage{
		$width = $this->getLInt();
		$height = $this->getLInt();
		$data = $this->getString();
		try{
			return new SkinImage($height, $width, $data);
		}catch(\InvalidArgumentException $e){
			throw new PacketDecodeException($e->getMessage(), 0, $e);
		}
	}

	private function putSkinImage(SkinImage $image) : void{
		$this->putLInt($image->getWidth());
		$this->putLInt($image->getHeight());
		$this->putString($image->getData());
	}

	/**
	 * @throws PacketDecodeException
	 * @throws BinaryDataException
	 */
	public function getItemStackWithoutStackId(bool $decodeExtraData = true) : ItemStack{
		if($this->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_40){
			$id = $this->getVarInt();
			$count = $this->getLShort();
			$meta = $this->getUnsignedVarInt();
			$blockRuntimeId = $this->getVarInt();
			$rawExtraData = $this->getString();

			if(!$decodeExtraData){
				return new ItemStack($id, $meta, $count, $blockRuntimeId, null, [], [], null, $rawExtraData);
			}
			$extraData = self::decoder($this->getProtocolId(), $rawExtraData, 0);
			$stack = self::readExtraItemStackData($extraData, $id, $meta, $count, $blockRuntimeId, $rawExtraData);
			if(!$extraData->feof()){
				throw new PacketDecodeException("Unexpected trailing extradata for network item $id");
			}
			return $stack;
		}
		return $this->getItemStack(function() : void{
			//NOOP
		}, $decodeExtraData);
	}

	public function putItemStackWithoutStackId(ItemStack $item) : void{
		if($this->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_40){
			$this->putVarInt($item->getId());
			$this->putLShort($item->getCount());
			$this->putUnsignedVarInt($item->getMeta());
			$this->putVarInt($item->getBlockRuntimeId());

			$rawExtraData = $item->getRawExtraData();
			if($rawExtraData === null){
				$extraData = self::encoder($this->getProtocolId());
				self::putExtraItemStackData($extraData, $item);
				$rawExtraData = $extraData->getBuffer();
			}
			$this->putString($rawExtraData);
			return;
		}
		$this->putItemStack($item, function() : void{
			//NOOP
		});
	}

	/**
	 * 1.26.20 uses this compact descriptor in inventory slot/equipment packets instead of the regular stack wrapper.
	 *
	 * @throws PacketDecodeException
	 * @throws BinaryDataException
	 */
	public function getNetworkItemStackDescriptor(bool $decodeExtraData = true) : ItemStackWrapper{
		$id = $this->getLShort();
		$count = $this->getLShort();
		$meta = $this->getUnsignedVarInt();

		$stackId = 0;
		if($this->getBool()){
			if($this->getProtocolId() < ProtocolInfo::PROTOCOL_1_26_40){
				$this->getUnsignedVarInt(); //legacy stack-ID variant
			}
			$stackId = $this->getVarInt();
		}

		$blockRuntimeId = $this->getUnsignedVarInt();
		if($this->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_40){
			$blockRuntimeId = Binary::signInt($blockRuntimeId);
		}
		$rawExtraData = $this->getString();
		if($id === 0){
			return new ItemStackWrapper($stackId, ItemStack::null());
		}

		if(!$decodeExtraData){
			return new ItemStackWrapper($stackId, new ItemStack($id, $meta, $count, $blockRuntimeId, null, [], [], null, $rawExtraData));
		}

		$extraData = self::decoder($this->getProtocolId(), $rawExtraData, 0);
		$stack = self::readExtraItemStackData($extraData, $id, $meta, $count, $blockRuntimeId, $rawExtraData);
		if(!$extraData->feof()){
			throw new PacketDecodeException("Unexpected trailing extradata for network item descriptor $id");
		}

		return new ItemStackWrapper($stackId, $stack);
	}

	public function putNetworkItemStackDescriptor(ItemStackWrapper $itemStackWrapper) : void{
		$item = $itemStackWrapper->getItemStack();
		$this->putLShort($item->getId());
		$this->putLShort($item->getCount());
		$this->putUnsignedVarInt($item->getMeta());

		$this->putBool($hasNetId = $itemStackWrapper->getStackId() !== 0);
		if($hasNetId){
			if($this->getProtocolId() < ProtocolInfo::PROTOCOL_1_26_40){
				$this->putUnsignedVarInt(0); //legacy stack-ID variant
			}
			$this->putVarInt($itemStackWrapper->getStackId());
		}

		$blockRuntimeId = $item->getBlockRuntimeId();
		$this->putUnsignedVarInt($this->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_40 ? Binary::unsignInt($blockRuntimeId) : $blockRuntimeId);
		if($item->getId() === 0){
			$this->putString("");
			return;
		}

		$rawExtraData = $item->getRawExtraData();
		if($rawExtraData === null){
			$extraData = self::encoder($this->getProtocolId());
			self::putExtraItemStackData($extraData, $item);
			$rawExtraData = $extraData->getBuffer();
		}
		$this->putString($rawExtraData);
	}

	/**
	 * @phpstan-param \Closure(PacketSerializer) : void $readExtraCrapInTheMiddle
	 *
	 * @throws PacketDecodeException
	 * @throws BinaryDataException
	 */
	public function getItemStack(\Closure $readExtraCrapInTheMiddle, bool $decodeExtraData = true) : ItemStack{
		$id = $this->getVarInt();
		if($id === 0){
			return ItemStack::null();
		}

		if($this->getProtocolId() >= ProtocolInfo::PROTOCOL_1_16_220){
			$count = $this->getLShort();
			$meta = $this->getUnsignedVarInt();

			$readExtraCrapInTheMiddle($this);

			$blockRuntimeId = $this->getVarInt();
			$rawExtraData = $this->getString();
			if(!$decodeExtraData){
				return new ItemStack($id, $meta, $count, $blockRuntimeId, null, [], [], null, $rawExtraData);
			}
			$extraData = self::decoder($this->getProtocolId(), $rawExtraData, 0);
		}else{
			$auxValue = $this->getVarInt();
			$count = $auxValue & 0xff;
			$meta = $auxValue >> 8;

			$blockRuntimeId = 0; // TODO: somehow get a runtime id?
			$extraData = $this;
		}

		$stack = self::readExtraItemStackData($extraData, $id, $meta, $count, $blockRuntimeId, $rawExtraData ?? null);

		if($extraData !== $this) {
			if(!$extraData->feof()){
				throw new PacketDecodeException("Unexpected trailing extradata for network item $id");
			}
		}

		return $stack;
	}

	private static function readExtraItemStackData(PacketSerializer $serializer, int $id, int $meta, int $count, int $blockRuntimeId, ?string $rawExtraData = null) : ItemStack{
		if($serializer->getProtocolId() >= ProtocolInfo::PROTOCOL_1_16_220) {
			$getListCount = \Closure::fromCallable([$serializer, "getLInt"]);
			$getString = \Closure::fromCallable(function() use ($serializer) : string{
				return $serializer->get($serializer->getLShort());
			});
			$getNBT = \Closure::fromCallable(function() use ($serializer) : CompoundTag{
				$offset = $serializer->getOffset();
				try{
					return (new LittleEndianNbtSerializer())->read($serializer->getBuffer(), $offset, 512)->mustGetCompoundTag();
				}catch(NbtDataException $e){
					throw PacketDecodeException::wrap($e, "Failed decoding NBT root");
				}finally{
					$serializer->setOffset($offset);
				}
			});
			$getBlockingTick = \Closure::fromCallable([$serializer, "getLLong"]);
		}else{
			$getListCount = \Closure::fromCallable([$serializer, "getVarInt"]);
			$getString = \Closure::fromCallable([$serializer, "getString"]);
			$getNBT = \Closure::fromCallable([$serializer, "getNbtCompoundRoot"]);
			$getBlockingTick = \Closure::fromCallable([$serializer, "getVarLong"]);
		}

		$nbtLen = $serializer->getLShort();

		/** @var CompoundTag|null $compound */
		$compound = null;
		if($nbtLen === 0xffff){
			$nbtDataVersion = $serializer->getByte();
			if($nbtDataVersion !== 1){
				throw new PacketDecodeException("Unexpected NBT data version $nbtDataVersion");
			}
			$compound = $getNBT();
		}elseif($nbtLen !== 0){
			throw new PacketDecodeException("Unexpected fake NBT length $nbtLen");
		}

		$canPlaceOn = [];
		for($i = 0, $canPlaceOnCount = $getListCount(); $i < $canPlaceOnCount; ++$i){
			$canPlaceOn[] = $getString();
		}
		$canDestroy = [];
		for($i = 0, $canDestroyCount = $getListCount(); $i < $canDestroyCount; ++$i){
			$canDestroy[] = $getString();
		}

		$shieldBlockingTick = null;
		if($id === $serializer->shieldItemRuntimeId){
			$shieldBlockingTick = $getBlockingTick();
		}

		return new ItemStack($id, $meta, $count, $blockRuntimeId, $compound, $canPlaceOn, $canDestroy, $shieldBlockingTick, $rawExtraData);
	}

	/**
	 * @phpstan-param \Closure(PacketSerializer) : void $writeExtraCrapInTheMiddle
	 */
	public function putItemStack(ItemStack $item, \Closure $writeExtraCrapInTheMiddle) : void{
		if($item->getId() === 0){
			$this->putVarInt(0);

			return;
		}

		$this->putVarInt($item->getId());

		if($this->getProtocolId() >= ProtocolInfo::PROTOCOL_1_16_220){
			$this->putLShort($item->getCount());
			$this->putUnsignedVarInt($item->getMeta());

			$writeExtraCrapInTheMiddle($this);

			$this->putVarInt($item->getBlockRuntimeId());

			$rawExtraData = $item->getRawExtraData();
			if($rawExtraData === null){
				$extraData = PacketSerializer::encoder($this->getProtocolId());
				self::putExtraItemStackData($extraData, $item);
				$rawExtraData = $extraData->getBuffer();
			}
			$this->putString($rawExtraData);
			return;
		}

		$auxValue = (($item->getMeta() & 0x7fff) << 8) | $item->getCount();
		$this->putVarInt($auxValue);

		self::putExtraItemStackData($this, $item);
	}

	private static function putExtraItemStackData(PacketSerializer $serializer, ItemStack $item) : void{
		if($serializer->getProtocolId() >= ProtocolInfo::PROTOCOL_1_16_220) {
			$putListCount = \Closure::fromCallable([$serializer, "putLInt"]);
			$putString = \Closure::fromCallable(function(string $str) use ($serializer) : void{
				$serializer->putLShort(strlen($str));
				$serializer->put($str);
			});
			$putBlockingTick = \Closure::fromCallable([$serializer, "putLLong"]);
			$nbtSerializerClass = LittleEndianNbtSerializer::class;
		}else{
			$putListCount = \Closure::fromCallable([$serializer, "putVarInt"]);
			$putString = \Closure::fromCallable([$serializer, "putString"]);
			$putBlockingTick = \Closure::fromCallable([$serializer, "putVarLong"]);
			$nbtSerializerClass = NetworkNbtSerializer::class;
		}

		$nbt = $item->getNbt();
		if($nbt !== null){
			$serializer->putLShort(0xffff);
			$serializer->putByte(1); //TODO: NBT data version (?)
			$serializer->put((new $nbtSerializerClass())->write(new TreeRoot($nbt)));
		}else{
			$serializer->putLShort(0);
		}

		$putListCount(count($item->getCanPlaceOn()));
		foreach($item->getCanPlaceOn() as $entry){
			$putString($entry);
		}
		$putListCount(count($item->getCanDestroy()));
		foreach($item->getCanDestroy() as $entry){
			$putString($entry);
		}

		$blockingTick = $item->getShieldBlockingTick();
		if($item->getId() === $serializer->shieldItemRuntimeId){
			$putBlockingTick($blockingTick ?? 0);
		}
	}

	public function getRecipeIngredient() : RecipeIngredient{
		if($this->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_40){
			//1.26.40 uses a string-tagged descriptor: a variant byte (0 = empty, 1 = present), then a string type.
			if($this->getByte() === 0){
				$this->getVarInt(); //empty descriptor sentinel meta (32767)
				$descriptor = null;
			}else{
				$typeStr = $this->getString();
				$descriptor = match($typeStr){
					"name" => StringIdMetaItemDescriptor::read($this),
					"item_tag" => TagItemDescriptor::read($this),
					"molang" => MolangItemDescriptor::read($this),
					default => null,
				};
				if($typeStr === "item_tag"){
					$this->getVarInt(); //tag descriptor meta wildcard - not tracked by this fork
				}
			}
			$count = $this->getVarInt();

			return new RecipeIngredient($descriptor, $count);
		}
		if($this->getProtocolId() >= ProtocolInfo::PROTOCOL_1_19_30){
			$descriptorType = $this->getByte();
			$descriptor = match($descriptorType){
				ItemDescriptorType::INT_ID_META => IntIdMetaItemDescriptor::read($this),
				ItemDescriptorType::STRING_ID_META => StringIdMetaItemDescriptor::read($this),
				ItemDescriptorType::TAG => TagItemDescriptor::read($this),
				ItemDescriptorType::MOLANG => MolangItemDescriptor::read($this),
				ItemDescriptorType::COMPLEX_ALIAS => ComplexAliasItemDescriptor::read($this),
				default => null
			};
			$count = $this->getVarInt();
		}else{
			$descriptor = IntIdMetaItemDescriptor::read($this);
			$count = $descriptor->getId() === 0 ? 0 : $this->getVarInt();
		}

		return new RecipeIngredient($descriptor, $count);
	}

	public function putRecipeIngredient(RecipeIngredient $ingredient) : void{
		$type = $ingredient->getDescriptor();

		if($this->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_40){
			if($type instanceof StringIdMetaItemDescriptor || $type instanceof TagItemDescriptor || $type instanceof MolangItemDescriptor){
				$this->putByte(1);
				$this->putString(match(true){
					$type instanceof StringIdMetaItemDescriptor => "name",
					$type instanceof TagItemDescriptor => "item_tag",
					$type instanceof MolangItemDescriptor => "molang",
				});
				$type->write($this);
				if($type instanceof TagItemDescriptor){
					$this->putVarInt(32767); //tag descriptor meta wildcard - not tracked by this fork
				}
			}else{
				$this->putByte(0);
				$this->putVarInt(32767); //empty descriptor sentinel meta
			}
			$this->putVarInt($ingredient->getCount());

			return;
		}

		if($this->getProtocolId() >= ProtocolInfo::PROTOCOL_1_19_30){
			$this->putByte($type?->getTypeId() ?? 0);
			$type?->write($this);

			$this->putVarInt($ingredient->getCount());
		}elseif($type instanceof IntIdMetaItemDescriptor){
			$type->write($this);
			if($type->getId() !== 0){
				$this->putVarInt($ingredient->getCount());
			}
		}elseif($type === null){
			$this->putVarInt(0);
		}else{
			throw new \InvalidArgumentException("Unsupported item descriptor type");
		}
	}

	/**
	 * Decodes entity metadata from the stream.
	 *
	 * @return MetadataProperty[]
	 * @phpstan-return array<int, MetadataProperty>
	 *
	 * @throws PacketDecodeException
	 * @throws BinaryDataException
	 */
	public function getEntityMetadata() : array{
		$count = $this->getUnsignedVarInt();
		$data = [];
		for($i = 0; $i < $count; ++$i){
			$key = $this->getUnsignedVarInt();
			$type = $this->getUnsignedVarInt();
			if($this->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_40){
				$duplicateType = $this->getByte();
				if($duplicateType !== $type){
					throw new PacketDecodeException("Mismatched entity metadata type IDs $type and $duplicateType");
				}
			}

			$data[$key] = $this->readMetadataProperty($type);
		}

		return $data;
	}

	private function readMetadataProperty(int $type) : MetadataProperty{
		return match($type){
			ByteMetadataProperty::ID => ByteMetadataProperty::read($this),
			ShortMetadataProperty::ID => ShortMetadataProperty::read($this),
			IntMetadataProperty::ID => IntMetadataProperty::read($this),
			FloatMetadataProperty::ID => FloatMetadataProperty::read($this),
			StringMetadataProperty::ID => StringMetadataProperty::read($this),
			CompoundTagMetadataProperty::ID => CompoundTagMetadataProperty::read($this),
			BlockPosMetadataProperty::ID => BlockPosMetadataProperty::read($this),
			LongMetadataProperty::ID => LongMetadataProperty::read($this),
			Vec3MetadataProperty::ID => Vec3MetadataProperty::read($this),
			default => throw new PacketDecodeException("Unknown entity metadata type " . $type),
		};
	}

	/**
	 * Writes entity metadata to the packet buffer.
	 *
	 * @param MetadataProperty[] $metadata
	 *
	 * @phpstan-param array<int, MetadataProperty> $metadata
	 */
	public function putEntityMetadata(array $metadata) : void{
		$data = EntityMetadataFlags::encode($metadata, $this->getProtocolId());
		$data = EntityMetadataProperties::encode($data, $this->getProtocolId());

		$this->putUnsignedVarInt(count($data));
		foreach($data as $key => $d){
			$this->putUnsignedVarInt($key);
			$this->putUnsignedVarInt($d->getTypeId());
			if($this->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_40){
				$this->putByte($d->getTypeId());
			}
			$d->write($this);
		}
	}

	/**
	 * @throws BinaryDataException
	 */
	final public function getActorUniqueId() : int{
		return $this->getVarLong();
	}

	public function putActorUniqueId(int $eid) : void{
		$this->putVarLong($eid);
	}

	/**
	 * @throws BinaryDataException
	 */
	final public function getActorRuntimeId() : int{
		return $this->getUnsignedVarLong();
	}

	public function putActorRuntimeId(int $eid) : void{
		$this->putUnsignedVarLong($eid);
	}

	/**
	 * Reads a block position with unsigned Y coordinate.
	 *
	 * @throws BinaryDataException
	 */
	public function getBlockPosition(bool $signedY = false) : BlockPosition{
		$x = $this->getVarInt();
		$y = $signedY ? $this->getVarInt() : Binary::signInt($this->getUnsignedVarInt()); //Y coordinate may be signed, but it's written unsigned before 1.26.10 :<
		$z = $this->getVarInt();
		return new BlockPosition($x, $y, $z);
	}

	/**
	 * Writes a block position with unsigned Y coordinate.
	 */
	public function putBlockPosition(BlockPosition $blockPosition, bool $signedY = false) : void{
		$this->putVarInt($blockPosition->getX());
		if($signedY){
			$this->putVarInt($blockPosition->getY());
		}else{
			$this->putUnsignedVarInt(Binary::unsignInt($blockPosition->getY())); //Y coordinate may be signed, but it's written unsigned before 1.26.10 :<
		}
		$this->putVarInt($blockPosition->getZ());
	}

	/**
	 * Reads a block position with a signed Y coordinate.
	 *
	 * @throws BinaryDataException
	 */
	public function getSignedBlockPosition() : BlockPosition{
		$x = $this->getVarInt();
		$y = $this->getVarInt();
		$z = $this->getVarInt();
		return new BlockPosition($x, $y, $z);
	}

	/**
	 * Writes a block position with a signed Y coordinate.
	 */
	public function putSignedBlockPosition(BlockPosition $blockPosition) : void{
		$this->putVarInt($blockPosition->getX());
		$this->putVarInt($blockPosition->getY());
		$this->putVarInt($blockPosition->getZ());
	}

	/**
	 * Reads a floating-point Vector3 object with coordinates rounded to 4 decimal places.
	 *
	 * @throws BinaryDataException
	 */
	public function getVector3() : Vector3{
		$x = $this->getLFloat();
		$y = $this->getLFloat();
		$z = $this->getLFloat();
		return new Vector3($x, $y, $z);
	}

	/**
	 * Reads a floating-point Vector2 object with coordinates rounded to 4 decimal places.
	 *
	 * @throws BinaryDataException
	 */
	public function getVector2() : Vector2{
		$x = $this->getLFloat();
		$y = $this->getLFloat();
		return new Vector2($x, $y);
	}

	/**
	 * Writes a floating-point Vector3 object, or 3x zero if null is given.
	 *
	 * Note: ONLY use this where it is reasonable to allow not specifying the vector.
	 * For all other purposes, use the non-nullable version.
	 *
	 * @see PacketSerializer::putVector3()
	 */
	public function putVector3Nullable(?Vector3 $vector) : void{
		if($vector !== null){
			$this->putVector3($vector);
		}else{
			$this->putLFloat(0.0);
			$this->putLFloat(0.0);
			$this->putLFloat(0.0);
		}
	}

	/**
	 * Writes a floating-point Vector3 object
	 */
	public function putVector3(Vector3 $vector) : void{
		$this->putLFloat($vector->x);
		$this->putLFloat($vector->y);
		$this->putLFloat($vector->z);
	}

	/**
	 * Writes a floating-point Vector2 object
	 */
	public function putVector2(Vector2 $vector2) : void{
		$this->putLFloat($vector2->x);
		$this->putLFloat($vector2->y);
	}

	/**
	 * @throws BinaryDataException
	 */
	public function getRotationByte() : float{
		return ($this->getByte() * (360 / 256));
	}

	public function putRotationByte(float $rotation) : void{
		$this->putByte((int) ($rotation / (360 / 256)));
	}

	private function readGameRule(int $type, bool $isPlayerModifiable, bool $isStartGame) : GameRule{
		$isLegacyStartGame = $isStartGame && $this->getProtocolId() < ProtocolInfo::PROTOCOL_1_26_40;
		return match($type){
			BoolGameRule::ID => BoolGameRule::decode($this, $isPlayerModifiable),
			IntGameRule::ID => IntGameRule::decode($this, $isPlayerModifiable, $isLegacyStartGame),
			FloatGameRule::ID => FloatGameRule::decode($this, $isPlayerModifiable),
			default => throw new PacketDecodeException("Unknown gamerule type $type"),
		};
	}

	/**
	 * Reads gamerules
	 *
	 * @return GameRule[] game rule name => value
	 * @phpstan-return array<string, GameRule>
	 *
	 * @throws PacketDecodeException
	 * @throws BinaryDataException
	 */
	public function getGameRules(bool $isStartGame = false) : array{
		$count = $this->getUnsignedVarInt();
		$rules = [];
		for($i = 0; $i < $count; ++$i){
			$name = $this->getString();
			if($this->getProtocolId() >= ProtocolInfo::PROTOCOL_1_17_0){
				$isPlayerModifiable = $this->getBool();
			}else{
				$isPlayerModifiable = false;
			}
			$type = $this->getUnsignedVarInt();
			$rules[$name] = $this->readGameRule($type, $isPlayerModifiable, $isStartGame);
		}

		return $rules;
	}

	/**
	 * Writes a gamerule array
	 *
	 * @param GameRule[] $rules
	 * @phpstan-param array<string, GameRule> $rules
	 */
	public function putGameRules(array $rules, bool $isStartGame = false) : void{
		$this->putUnsignedVarInt(count($rules));
		foreach($rules as $name => $rule){
			$this->putString($name);
			if($this->getProtocolId() >= ProtocolInfo::PROTOCOL_1_17_0){
				$this->putBool($rule->isPlayerModifiable());
			}
			$this->putUnsignedVarInt($rule->getTypeId());
			$rule->encode($this, $isStartGame && $this->getProtocolId() < ProtocolInfo::PROTOCOL_1_26_40);
		}
	}

	/**
	 * @throws BinaryDataException
	 */
	public function getEntityLink() : EntityLink{
		$fromActorUniqueId = $this->getActorUniqueId();
		$toActorUniqueId = $this->getActorUniqueId();
		$type = $this->getByte();
		$immediate = $this->getBool();
		$causedByRider = $this->getBool();
		if($this->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_20){
			$vehicleAngularVelocity = $this->getLFloat();
		}
		return new EntityLink($fromActorUniqueId, $toActorUniqueId, $type, $immediate, $causedByRider, $vehicleAngularVelocity ?? 0.0);
	}

	public function putEntityLink(EntityLink $link) : void{
		$this->putActorUniqueId($link->fromActorUniqueId);
		$this->putActorUniqueId($link->toActorUniqueId);
		$this->putByte($link->type);
		$this->putBool($link->immediate);
		$this->putBool($link->causedByRider);
		if($this->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_20){
			$this->putLFloat($link->vehicleAngularVelocity);
		}
	}

	/**
	 * @throws BinaryDataException
	 */
	public function getCommandOriginData(int $protocolId) : CommandOriginData{
		$result = new CommandOriginData();

		$result->type = $protocolId >= ProtocolInfo::PROTOCOL_1_21_130 ? $this->getString() : CommandOriginData::getTypeFromId($this->getUnsignedVarInt());
		$result->uuid = $this->getUUID();
		$result->requestId = $this->getString();

		if($protocolId >= ProtocolInfo::PROTOCOL_1_21_130){
			$result->playerActorUniqueId = $this->getLLong();
		}elseif($result->type === CommandOriginData::ORIGIN_DEV_CONSOLE || $result->type === CommandOriginData::ORIGIN_TEST){
			$result->playerActorUniqueId = $this->getVarLong();
		}

		return $result;
	}

	public function putCommandOriginData(CommandOriginData $data) : void{
		if($this->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_130){
			$this->putString($data->type);
		}else{
			$this->putUnsignedVarInt(CommandOriginData::getIdFromType($data->type));
		}
		$this->putUUID($data->uuid);
		$this->putString($data->requestId);

		if($this->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_130){
			$this->putLLong($data->playerActorUniqueId);
		}elseif($data->type === CommandOriginData::ORIGIN_DEV_CONSOLE || $data->type === CommandOriginData::ORIGIN_TEST){
			$this->putVarLong($data->playerActorUniqueId);
		}
	}

	public function getStructureSettings() : StructureSettings{
		$result = new StructureSettings();

		$result->paletteName = $this->getString();

		$result->ignoreEntities = $this->getBool();
		$result->ignoreBlocks = $this->getBool();
		if($this->getProtocolId() >= ProtocolInfo::PROTOCOL_1_18_30){
			$result->allowNonTickingChunks = $this->getBool();
		}else{
			$result->allowNonTickingChunks = false;
		}

		$result->dimensions = $this->getBlockPosition($this->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_10);
		$result->offset = $this->getBlockPosition($this->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_10);

		$result->lastTouchedByPlayerID = $this->getActorUniqueId();
		$result->rotation = $this->getByte();
		$result->mirror = $this->getByte();
		$result->animationMode = $this->getByte();
		$result->animationSeconds = $this->getLFloat();
		$result->integrityValue = $this->getLFloat();
		$result->integritySeed = $this->getLInt();
		$result->pivot = $this->getVector3();

		return $result;
	}

	public function putStructureSettings(StructureSettings $structureSettings) : void{
		$this->putString($structureSettings->paletteName);

		$this->putBool($structureSettings->ignoreEntities);
		$this->putBool($structureSettings->ignoreBlocks);
		if($this->getProtocolId() >= ProtocolInfo::PROTOCOL_1_18_30){
			$this->putBool($structureSettings->allowNonTickingChunks);
		}

		$this->putBlockPosition($structureSettings->dimensions, $this->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_10);
		$this->putBlockPosition($structureSettings->offset, $this->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_10);

		$this->putActorUniqueId($structureSettings->lastTouchedByPlayerID);
		$this->putByte($structureSettings->rotation);
		$this->putByte($structureSettings->mirror);
		$this->putByte($structureSettings->animationMode);
		$this->putLFloat($structureSettings->animationSeconds);
		$this->putLFloat($structureSettings->integrityValue);
		$this->putLInt($structureSettings->integritySeed);
		$this->putVector3($structureSettings->pivot);
	}

	public function getStructureEditorData() : StructureEditorData{
		$result = new StructureEditorData();

		$result->structureName = $this->getString();
		if($this->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_60){
			$result->filteredStructureName = $this->getString();
		}
		$result->structureDataField = $this->getString();

		$result->includePlayers = $this->getBool();
		$result->showBoundingBox = $this->getBool();

		$result->structureBlockType = $this->getVarInt();
		$result->structureSettings = $this->getStructureSettings();
		$result->structureRedstoneSaveMode = $this->getVarInt();

		return $result;
	}

	public function putStructureEditorData(StructureEditorData $structureEditorData) : void{
		$this->putString($structureEditorData->structureName);
		if($this->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_60){
			$this->putString($structureEditorData->filteredStructureName);
		}
		$this->putString($structureEditorData->structureDataField);

		$this->putBool($structureEditorData->includePlayers);
		$this->putBool($structureEditorData->showBoundingBox);

		$this->putVarInt($structureEditorData->structureBlockType);
		$this->putStructureSettings($structureEditorData->structureSettings);
		$this->putVarInt($structureEditorData->structureRedstoneSaveMode);
	}

	public function getNbtRoot() : TreeRoot{
		$offset = $this->getOffset();
		try{
			return (new NetworkNbtSerializer())->read($this->getBuffer(), $offset, 512);
		}catch(NbtDataException $e){
			throw PacketDecodeException::wrap($e, "Failed decoding NBT root");
		}finally{
			$this->setOffset($offset);
		}
	}

	public function getNbtCompoundRoot() : CompoundTag{
		try{
			return $this->getNbtRoot()->mustGetCompoundTag();
		}catch(NbtDataException $e){
			throw PacketDecodeException::wrap($e, "Expected TAG_Compound NBT root");
		}
	}

	public function readRecipeNetId() : int{
		return $this->getUnsignedVarInt();
	}

	public function writeRecipeNetId(int $id) : void{
		$this->putUnsignedVarInt($id);
	}

	public function readCreativeItemNetId() : int{
		return $this->getUnsignedVarInt();
	}

	public function writeCreativeItemNetId(int $id) : void{
		$this->putUnsignedVarInt($id);
	}

	/**
	 * This is a union of ItemStackRequestId, LegacyItemStackRequestId, and ServerItemStackId, used in serverbound
	 * packets to allow the client to refer to server known items, or items which may have been modified by a previous
	 * as-yet unacknowledged request from the client.
	 *
	 * - Server itemstack ID is positive
	 * - InventoryTransaction "legacy" request ID is negative and even
	 * - ItemStackRequest request ID is negative and odd
	 * - 0 refers to an empty itemstack (air)
	 */
	public function readItemStackNetIdVariant() : int{
		return $this->getVarInt();
	}

	/**
	 * This is a union of ItemStackRequestId, LegacyItemStackRequestId, and ServerItemStackId, used in serverbound
	 * packets to allow the client to refer to server known items, or items which may have been modified by a previous
	 * as-yet unacknowledged request from the client.
	 */
	public function writeItemStackNetIdVariant(int $id) : void{
		$this->putVarInt($id);
	}

	public function readItemStackRequestId() : int{
		return $this->getVarInt();
	}

	public function writeItemStackRequestId(int $id) : void{
		$this->putVarInt($id);
	}

	public function readLegacyItemStackRequestId() : int{
		return $this->getVarInt();
	}

	public function writeLegacyItemStackRequestId(int $id) : void{
		$this->putVarInt($id);
	}

	public function readServerItemStackId() : int{
		return $this->getVarInt();
	}

	public function writeServerItemStackId(int $id) : void{
		$this->putVarInt($id);
	}

	/**
	 * @phpstan-template T
	 * @phpstan-param \Closure() : T $reader
	 * @phpstan-return T|null
	 */
	public function readOptional(\Closure $reader) : mixed{
		if($this->getBool()){
			return $reader();
		}
		return null;
	}

	/**
	 * @phpstan-template T
	 * @phpstan-param T|null $value
	 * @phpstan-param \Closure(T) : void $writer
	 */
	public function writeOptional(mixed $value, \Closure $writer) : void{
		if($value !== null){
			$this->putBool(true);
			$writer($value);
		}else{
			$this->putBool(false);
		}
	}

	/** 1.26.40 wraps some optionals in a mandatory-present outer optional. */
	public function readDummyOptional() : void{
		$dummy = $this->getByte();
		if($dummy !== 1){
			throw new PacketDecodeException("Dummy optional first byte should be 1, got $dummy");
		}
	}

	public function writeDummyOptional() : void{
		$this->putByte(1);
	}

	public function readDoubleOptional(\Closure $reader) : mixed{
		$this->readDummyOptional();
		return $this->readOptional($reader);
	}

	public function writeDoubleOptional(mixed $value, \Closure $writer) : void{
		$this->writeDummyOptional();
		$this->writeOptional($value, $writer);
	}
}
