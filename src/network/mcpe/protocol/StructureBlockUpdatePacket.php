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
use pocketmine\network\mcpe\protocol\types\StructureEditorData;

class StructureBlockUpdatePacket extends DataPacket implements ServerboundPacket{
	public const NETWORK_ID = ProtocolInfo::STRUCTURE_BLOCK_UPDATE_PACKET;

	public BlockPosition $blockPosition;
	public StructureEditorData $structureEditorData;
	public bool $isPowered;
	public bool $waterlogged;

	/**
	 * @generate-create-func
	 */
	public static function create(BlockPosition $blockPosition, StructureEditorData $structureEditorData, bool $isPowered, bool $waterlogged) : self{
		$result = new self();
		$result->blockPosition = $blockPosition;
		$result->structureEditorData = $structureEditorData;
		$result->isPowered = $isPowered;
		$result->waterlogged = $waterlogged;
		return $result;
	}

	protected function decodePayload(PacketSerializer $in) : void{
		$this->blockPosition = $in->getBlockPosition($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_10);
		$this->structureEditorData = $in->getStructureEditorData($in->getProtocolId());
		$this->isPowered = $in->getBool();
		if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_19_30){
			$this->waterlogged = $in->getBool();
		}
	}

	protected function encodePayload(PacketSerializer $out) : void{
		$out->putBlockPosition($this->blockPosition, $out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_10);
		$out->putStructureEditorData($this->structureEditorData, $out->getProtocolId());
		$out->putBool($this->isPowered);
		if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_19_30){
			$out->putBool($this->waterlogged);
		}
	}

	public function handle(PacketHandlerInterface $handler) : bool{
		return $handler->handleStructureBlockUpdate($this);
	}
}
