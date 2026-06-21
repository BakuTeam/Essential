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

namespace pocketmine\network\mcpe\protocol;

use pocketmine\network\mcpe\protocol\serializer\PacketSerializer;
use pocketmine\network\mcpe\protocol\types\BlockPosition;
use pocketmine\network\mcpe\protocol\types\PlayerAction;

class PlayerActionPacket extends DataPacket implements ClientboundPacket, ServerboundPacket{
	public const NETWORK_ID = ProtocolInfo::PLAYER_ACTION_PACKET;

	public int $actorRuntimeId;
	/** @see PlayerAction */
	public int $action;
	public BlockPosition $blockPosition;
	public BlockPosition $resultPosition;
	public int $face;

	/**
	 * @generate-create-func
	 */
	public static function create(int $actorRuntimeId, int $action, BlockPosition $blockPosition, BlockPosition $resultPosition, int $face) : self{
		$result = new self();
		$result->actorRuntimeId = $actorRuntimeId;
		$result->action = $action;
		$result->blockPosition = $blockPosition;
		$result->resultPosition = $resultPosition;
		$result->face = $face;
		return $result;
	}

	protected function decodePayload(PacketSerializer $in) : void{
		$this->actorRuntimeId = $in->getActorRuntimeId();
		$this->action = $in->getVarInt();
		$this->blockPosition = $in->getBlockPosition($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_10);
		if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_19_0){
		$this->resultPosition = $in->getBlockPosition($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_10);
		}
		$this->face = $in->getVarInt();
	}

	protected function encodePayload(PacketSerializer $out) : void{
		$out->putActorRuntimeId($this->actorRuntimeId);
		$out->putVarInt($this->action);
		$out->putBlockPosition($this->blockPosition, $out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_10);
		if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_19_0){
		$out->putBlockPosition($this->resultPosition, $out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_10);
		}
		$out->putVarInt($this->face);
	}

	public function handle(PacketHandlerInterface $handler) : bool{
		return $handler->handlePlayerAction($this);
	}
}
