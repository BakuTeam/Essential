<?php

/*
 *
 *  ____            _        _   __  __ _                  __  __ ____
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine Team
 * @link http://www.pocketmine.net/
 *
 *
 */

declare(strict_types=1);

namespace pocketmine\network\mcpe\convert;

use pocketmine\network\mcpe\protocol\PacketDecodeException;
use pocketmine\network\mcpe\protocol\ProtocolInfo;
use pocketmine\utils\Binary;
use pocketmine\utils\BinaryDataException;

final class PacketIdTranslator{
	/**
	 * Pocket Edition 1.1.x still uses one-byte packet IDs, and several packet
	 * IDs collide with modern packets. This map keeps the core IDs stable while
	 * allowing old clients to use their historical IDs on the wire.
	 *
	 * @var int[]
	 * @phpstan-var array<int, int>
	 */
	private const CORE_TO_1_1_5 = [
		ProtocolInfo::LOGIN_PACKET => 0x01,
		ProtocolInfo::PLAY_STATUS_PACKET => 0x02,
		ProtocolInfo::SERVER_TO_CLIENT_HANDSHAKE_PACKET => 0x03,
		ProtocolInfo::CLIENT_TO_SERVER_HANDSHAKE_PACKET => 0x04,
		ProtocolInfo::DISCONNECT_PACKET => 0x05,
		ProtocolInfo::RESOURCE_PACKS_INFO_PACKET => 0x06,
		ProtocolInfo::RESOURCE_PACK_STACK_PACKET => 0x07,
		ProtocolInfo::RESOURCE_PACK_CLIENT_RESPONSE_PACKET => 0x08,
		ProtocolInfo::TEXT_PACKET => 0x09,
		ProtocolInfo::SET_TIME_PACKET => 0x0a,
		ProtocolInfo::START_GAME_PACKET => 0x0b,
		ProtocolInfo::ADD_PLAYER_PACKET => 0x0c,
		ProtocolInfo::ADD_ACTOR_PACKET => 0x0d,
		ProtocolInfo::REMOVE_ACTOR_PACKET => 0x0e,
		ProtocolInfo::ADD_ITEM_ACTOR_PACKET => 0x0f,
		ProtocolInfo::ADD_HANGING_ACTOR_PACKET => 0x10,
		ProtocolInfo::TAKE_ITEM_ACTOR_PACKET => 0x11,
		ProtocolInfo::MOVE_ACTOR_ABSOLUTE_PACKET => 0x12,
		ProtocolInfo::MOVE_PLAYER_PACKET => 0x13,
		ProtocolInfo::PASSENGER_JUMP_PACKET => 0x14,
		ProtocolInfo::REMOVE_BLOCK_PACKET => 0x15,
		ProtocolInfo::UPDATE_BLOCK_PACKET => 0x16,
		ProtocolInfo::ADD_PAINTING_PACKET => 0x17,
		ProtocolInfo::EXPLODE_PACKET => 0x18,
		ProtocolInfo::LEVEL_SOUND_EVENT_PACKET => 0x19,
		ProtocolInfo::LEVEL_EVENT_PACKET => 0x1a,
		ProtocolInfo::BLOCK_EVENT_PACKET => 0x1b,
		ProtocolInfo::ACTOR_EVENT_PACKET => 0x1c,
		ProtocolInfo::MOB_EFFECT_PACKET => 0x1d,
		ProtocolInfo::UPDATE_ATTRIBUTES_PACKET => 0x1e,
		ProtocolInfo::MOB_EQUIPMENT_PACKET => 0x1f,
		ProtocolInfo::MOB_ARMOR_EQUIPMENT_PACKET => 0x20,
		ProtocolInfo::INTERACT_PACKET => 0x21,
		ProtocolInfo::BLOCK_PICK_REQUEST_PACKET => 0x22,
		ProtocolInfo::USE_ITEM_PACKET => 0x23,
		ProtocolInfo::PLAYER_ACTION_PACKET => 0x24,
		ProtocolInfo::ACTOR_FALL_PACKET => 0x25,
		ProtocolInfo::HURT_ARMOR_PACKET => 0x26,
		ProtocolInfo::SET_ACTOR_DATA_PACKET => 0x27,
		ProtocolInfo::SET_ACTOR_MOTION_PACKET => 0x28,
		ProtocolInfo::SET_ACTOR_LINK_PACKET => 0x29,
		ProtocolInfo::SET_HEALTH_PACKET => 0x2a,
		ProtocolInfo::SET_SPAWN_POSITION_PACKET => 0x2b,
		ProtocolInfo::ANIMATE_PACKET => 0x2c,
		ProtocolInfo::RESPAWN_PACKET => 0x2d,
		ProtocolInfo::DROP_ITEM_PACKET => 0x2e,
		ProtocolInfo::INVENTORY_ACTION_PACKET => 0x2f,
		ProtocolInfo::CONTAINER_OPEN_PACKET => 0x30,
		ProtocolInfo::CONTAINER_CLOSE_PACKET => 0x31,
		ProtocolInfo::INVENTORY_SLOT_PACKET => 0x32,
		ProtocolInfo::CONTAINER_SET_DATA_PACKET => 0x33,
		ProtocolInfo::INVENTORY_CONTENT_PACKET => 0x34,
		ProtocolInfo::CRAFTING_DATA_PACKET => 0x35,
		ProtocolInfo::CRAFTING_EVENT_PACKET => 0x36,
		ProtocolInfo::ADVENTURE_SETTINGS_PACKET => 0x37,
		ProtocolInfo::BLOCK_ACTOR_DATA_PACKET => 0x38,
		ProtocolInfo::PLAYER_INPUT_PACKET => 0x39,
		ProtocolInfo::LEVEL_CHUNK_PACKET => 0x3a,
		ProtocolInfo::SET_COMMANDS_ENABLED_PACKET => 0x3b,
		ProtocolInfo::SET_DIFFICULTY_PACKET => 0x3c,
		ProtocolInfo::CHANGE_DIMENSION_PACKET => 0x3d,
		ProtocolInfo::SET_PLAYER_GAME_TYPE_PACKET => 0x3e,
		ProtocolInfo::PLAYER_LIST_PACKET => 0x3f,
		ProtocolInfo::SIMPLE_EVENT_PACKET => 0x40,
		ProtocolInfo::LEGACY_TELEMETRY_EVENT_PACKET => 0x41,
		ProtocolInfo::SPAWN_EXPERIENCE_ORB_PACKET => 0x42,
		ProtocolInfo::CLIENTBOUND_MAP_ITEM_DATA_PACKET => 0x43,
		ProtocolInfo::MAP_INFO_REQUEST_PACKET => 0x44,
		ProtocolInfo::REQUEST_CHUNK_RADIUS_PACKET => 0x45,
		ProtocolInfo::CHUNK_RADIUS_UPDATED_PACKET => 0x46,
		ProtocolInfo::ITEM_FRAME_DROP_ITEM_PACKET => 0x47,
		ProtocolInfo::GAME_RULES_CHANGED_PACKET => 0x49,
		ProtocolInfo::CAMERA_PACKET => 0x4a,
		ProtocolInfo::ADD_ITEM_PACKET => 0x4b,
		ProtocolInfo::BOSS_EVENT_PACKET => 0x4c,
		ProtocolInfo::SHOW_CREDITS_PACKET => 0x4d,
		ProtocolInfo::AVAILABLE_COMMANDS_PACKET => 0x4e,
		ProtocolInfo::COMMAND_STEP_PACKET => 0x4f,
		ProtocolInfo::COMMAND_BLOCK_UPDATE_PACKET => 0x50,
		ProtocolInfo::UPDATE_TRADE_PACKET => 0x51,
		ProtocolInfo::UPDATE_EQUIP_PACKET => 0x52,
		ProtocolInfo::RESOURCE_PACK_DATA_INFO_PACKET => 0x53,
		ProtocolInfo::RESOURCE_PACK_CHUNK_DATA_PACKET => 0x54,
		ProtocolInfo::RESOURCE_PACK_CHUNK_REQUEST_PACKET => 0x55,
		ProtocolInfo::TRANSFER_PACKET => 0x56,
		ProtocolInfo::PLAY_SOUND_PACKET => 0x57,
		ProtocolInfo::STOP_SOUND_PACKET => 0x58,
		ProtocolInfo::SET_TITLE_PACKET => 0x59,
		ProtocolInfo::ADD_BEHAVIOR_TREE_PACKET => 0x5a,
		ProtocolInfo::STRUCTURE_BLOCK_UPDATE_PACKET => 0x5b,
		ProtocolInfo::SHOW_STORE_OFFER_PACKET => 0x5c,
		ProtocolInfo::PURCHASE_RECEIPT_PACKET => 0x5d,
	];

	/** @var int[]|null */
	private static ?array $netToCore1_1_5 = null;

	private function __construct(){
		//NOOP
	}

	/**
	 * @throws BinaryDataException
	 * @throws PacketDecodeException
	 */
	public static function readPacketId(string $buffer, int $protocolId) : int{
		if($protocolId <= ProtocolInfo::PROTOCOL_1_1_5){
			if($buffer === ""){
				throw new PacketDecodeException("Missing legacy packet header");
			}
			return self::fromNetworkId($protocolId, ord($buffer[0]));
		}

		$offset = 0;
		return Binary::readUnsignedVarInt($buffer, $offset) & \pocketmine\network\mcpe\protocol\DataPacket::PID_MASK;
	}

	public static function fromNetworkId(int $protocolId, int $packetId) : int{
		if($protocolId <= ProtocolInfo::PROTOCOL_1_1_5){
			self::$netToCore1_1_5 ??= array_flip(self::CORE_TO_1_1_5);
			return self::$netToCore1_1_5[$packetId] ?? throw new PacketDecodeException("Unknown 1.1.5 packet ID $packetId");
		}

		return $packetId;
	}

	public static function toNetworkId(int $protocolId, int $packetId) : int{
		if($protocolId <= ProtocolInfo::PROTOCOL_1_1_5){
			return self::CORE_TO_1_1_5[$packetId] ?? throw new PacketDecodeException("Packet $packetId cannot be sent to protocol $protocolId");
		}

		return $packetId;
	}
}
