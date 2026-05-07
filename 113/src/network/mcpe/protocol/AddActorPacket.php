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

use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\serializer\PacketSerializer;
use pocketmine\network\mcpe\protocol\types\entity\Attribute;
use pocketmine\network\mcpe\protocol\types\entity\EntityLink;
use pocketmine\network\mcpe\protocol\types\entity\MetadataProperty;
use pocketmine\network\mcpe\protocol\types\entity\PropertySyncData;
use function count;

class AddActorPacket extends DataPacket implements ClientboundPacket{
	public const NETWORK_ID = ProtocolInfo::ADD_ACTOR_PACKET;

	private const LEGACY_TYPE_TO_ID_1_1_5 = [
		"minecraft:chicken" => 10,
		"minecraft:cow" => 11,
		"minecraft:pig" => 12,
		"minecraft:sheep" => 13,
		"minecraft:wolf" => 14,
		"minecraft:villager" => 15,
		"minecraft:villager_v2" => 15,
		"minecraft:mooshroom" => 16,
		"minecraft:squid" => 17,
		"minecraft:rabbit" => 18,
		"minecraft:bat" => 19,
		"minecraft:iron_golem" => 20,
		"minecraft:snow_golem" => 21,
		"minecraft:ocelot" => 22,
		"minecraft:horse" => 23,
		"minecraft:donkey" => 24,
		"minecraft:mule" => 25,
		"minecraft:skeleton_horse" => 26,
		"minecraft:zombie_horse" => 27,
		"minecraft:polar_bear" => 28,
		"minecraft:llama" => 29,
		"minecraft:parrot" => 30,
		"minecraft:zombie" => 32,
		"minecraft:creeper" => 33,
		"minecraft:skeleton" => 34,
		"minecraft:spider" => 35,
		"minecraft:zombie_pigman" => 36,
		"minecraft:slime" => 37,
		"minecraft:enderman" => 38,
		"minecraft:silverfish" => 39,
		"minecraft:cave_spider" => 40,
		"minecraft:ghast" => 41,
		"minecraft:magma_cube" => 42,
		"minecraft:blaze" => 43,
		"minecraft:zombie_villager" => 44,
		"minecraft:zombie_villager_v2" => 44,
		"minecraft:witch" => 45,
		"minecraft:stray" => 46,
		"minecraft:husk" => 47,
		"minecraft:wither_skeleton" => 48,
		"minecraft:guardian" => 49,
		"minecraft:elder_guardian" => 50,
		"minecraft:npc" => 51,
		"minecraft:wither" => 52,
		"minecraft:ender_dragon" => 53,
		"minecraft:shulker" => 54,
		"minecraft:endermite" => 55,
		"minecraft:agent" => 56,
		"minecraft:vindicator" => 57,
		"minecraft:armor_stand" => 61,
		"minecraft:tripod_camera" => 62,
		"minecraft:player" => 63,
		"minecraft:item" => 64,
		"minecraft:tnt" => 65,
		"minecraft:falling_block" => 66,
		"minecraft:xp_bottle" => 68,
		"minecraft:xp_orb" => 69,
		"minecraft:eye_of_ender_signal" => 70,
		"minecraft:ender_crystal" => 71,
		"minecraft:shulker_bullet" => 76,
		"minecraft:fishing_hook" => 77,
		"minecraft:dragon_fireball" => 79,
		"minecraft:arrow" => 80,
		"minecraft:snowball" => 81,
		"minecraft:egg" => 82,
		"minecraft:painting" => 83,
		"minecraft:minecart" => 84,
		"minecraft:fireball" => 85,
		"minecraft:splash_potion" => 86,
		"minecraft:ender_pearl" => 87,
		"minecraft:leash_knot" => 88,
		"minecraft:wither_skull" => 89,
		"minecraft:boat" => 90,
		"minecraft:wither_skull_dangerous" => 91,
		"minecraft:lightning_bolt" => 93,
		"minecraft:small_fireball" => 94,
		"minecraft:area_effect_cloud" => 95,
		"minecraft:hopper_minecart" => 96,
		"minecraft:tnt_minecart" => 97,
		"minecraft:chest_minecart" => 98,
		"minecraft:command_block_minecart" => 100,
		"minecraft:lingering_potion" => 101,
		"minecraft:llama_spit" => 102,
		"minecraft:evocation_fang" => 103,
		"minecraft:evocation_illager" => 104,
		"minecraft:vex" => 105,
	];

	public int $actorUniqueId;
	public int $actorRuntimeId;
	public string $type;
	public Vector3 $position;
	public ?Vector3 $motion = null;
	public float $pitch = 0.0;
	public float $yaw = 0.0;
	public float $headYaw = 0.0;
	public float $bodyYaw = 0.0; //???

	/** @var Attribute[] */
	public array $attributes = [];
	/**
	 * @var MetadataProperty[]
	 * @phpstan-var array<int, MetadataProperty>
	 */
	public array $metadata = [];
	public PropertySyncData $syncedProperties;
	/** @var EntityLink[] */
	public array $links = [];

	/**
	 * @generate-create-func
	 * @param Attribute[]        $attributes
	 * @param MetadataProperty[] $metadata
	 * @param EntityLink[]       $links
	 * @phpstan-param array<int, MetadataProperty> $metadata
	 */
	public static function create(
		int $actorUniqueId,
		int $actorRuntimeId,
		string $type,
		Vector3 $position,
		?Vector3 $motion,
		float $pitch,
		float $yaw,
		float $headYaw,
		float $bodyYaw,
		array $attributes,
		array $metadata,
		PropertySyncData $syncedProperties,
		array $links,
	) : self{
		$result = new self;
		$result->actorUniqueId = $actorUniqueId;
		$result->actorRuntimeId = $actorRuntimeId;
		$result->type = $type;
		$result->position = $position;
		$result->motion = $motion;
		$result->pitch = $pitch;
		$result->yaw = $yaw;
		$result->headYaw = $headYaw;
		$result->bodyYaw = $bodyYaw;
		$result->attributes = $attributes;
		$result->metadata = $metadata;
		$result->syncedProperties = $syncedProperties;
		$result->links = $links;
		return $result;
	}

	protected function decodePayload(PacketSerializer $in) : void{
		$this->actorUniqueId = $in->getActorUniqueId();
		$this->actorRuntimeId = $in->getActorRuntimeId();
		if($in->getProtocolId() <= ProtocolInfo::PROTOCOL_1_1_5){
			$this->type = array_flip(self::LEGACY_TYPE_TO_ID_1_1_5)[$in->getUnsignedVarInt()] ?? "minecraft:unknown";
		}else{
			$this->type = $in->getString();
		}
		$this->position = $in->getVector3();
		$this->motion = $in->getVector3();
		$this->pitch = $in->getLFloat();
		$this->yaw = $in->getLFloat();
		if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_16_0){
			$this->headYaw = $in->getLFloat();
		}
		if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_19_10){
			$this->bodyYaw = $in->getLFloat();
		}

		$attrCount = $in->getUnsignedVarInt();
		for($i = 0; $i < $attrCount; ++$i){
			$id = $in->getString();
			$min = $in->getLFloat();
			$current = $in->getLFloat();
			$max = $in->getLFloat();
			$this->attributes[] = new Attribute($id, $min, $max, $current, $current, []);
		}

		$this->metadata = $in->getEntityMetadata(); // TODO: convert back?
		if($in->getProtocolId() <= ProtocolInfo::PROTOCOL_1_1_5){
			$linkCount = $in->getUnsignedVarInt();
			for($i = 0; $i < $linkCount; ++$i){
				$this->links[] = $in->getEntityLink();
			}
			return;
		}
		if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_19_40){
			$this->syncedProperties = PropertySyncData::read($in);
		}

		$linkCount = $in->getUnsignedVarInt();
		for($i = 0; $i < $linkCount; ++$i){
			$this->links[] = $in->getEntityLink();
		}
	}

	protected function encodePayload(PacketSerializer $out) : void{
		$out->putActorUniqueId($this->actorUniqueId);
		$out->putActorRuntimeId($this->actorRuntimeId);
		if($out->getProtocolId() <= ProtocolInfo::PROTOCOL_1_1_5){
			$out->putUnsignedVarInt(self::LEGACY_TYPE_TO_ID_1_1_5[$this->type] ?? 0);
		}else{
			$out->putString($this->type);
		}
		$out->putVector3($this->position);
		$out->putVector3Nullable($this->motion);
		$out->putLFloat($this->pitch);
		$out->putLFloat($this->yaw);
		if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_16_0){
			$out->putLFloat($this->headYaw);
		}
		if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_19_10){
			$out->putLFloat($this->bodyYaw);
		}

		$out->putUnsignedVarInt(count($this->attributes));
		foreach($this->attributes as $attribute){
			$out->putString($attribute->getId());
			$out->putLFloat($attribute->getMin());
			$out->putLFloat($attribute->getCurrent());
			$out->putLFloat($attribute->getMax());
		}

		$out->putEntityMetadata($out->getProtocolId() <= ProtocolInfo::PROTOCOL_1_1_5 ? [] : $this->metadata);
		if($out->getProtocolId() <= ProtocolInfo::PROTOCOL_1_1_5){
			$out->putUnsignedVarInt(0);
			return;
		}
		if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_19_40){
			$this->syncedProperties->write($out);
		}

		$out->putUnsignedVarInt(count($this->links));
		foreach($this->links as $link){
			$out->putEntityLink($link);
		}
	}

	public function handle(PacketHandlerInterface $handler) : bool{
		return $handler->handleAddActor($this);
	}
}
