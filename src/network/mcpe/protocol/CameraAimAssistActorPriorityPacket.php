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
use pocketmine\network\mcpe\protocol\types\camera\CameraAimAssistActorPriorityData;
use function count;

class CameraAimAssistActorPriorityPacket extends DataPacket implements ClientboundPacket{
	public const NETWORK_ID = ProtocolInfo::CAMERA_AIM_ASSIST_ACTOR_PRIORITY_PACKET;

	/**
	 * @var CameraAimAssistActorPriorityData[]
	 * @phpstan-var list<CameraAimAssistActorPriorityData>
	 */
	private array $priorityData;

	/**
	 * @generate-create-func
	 * @param CameraAimAssistActorPriorityData[] $priorityData
	 * @phpstan-param list<CameraAimAssistActorPriorityData> $priorityData
	 */
	public static function create(array $priorityData) : self{
		$result = new self();
		$result->priorityData = $priorityData;
		return $result;
	}

	/**
	 * @return CameraAimAssistActorPriorityData[]
	 * @phpstan-return list<CameraAimAssistActorPriorityData>
	 */
	public function getPriorityData() : array{ return $this->priorityData; }

	protected function decodePayload(PacketSerializer $in) : void{
		$this->priorityData = [];
		for($i = 0, $count = $in->getUnsignedVarInt(); $i < $count; ++$i){
			$this->priorityData[] = CameraAimAssistActorPriorityData::read($in);
		}
	}

	protected function encodePayload(PacketSerializer $out) : void{
		$out->putUnsignedVarInt(count($this->priorityData));
		foreach($this->priorityData as $data){
			$data->write($out);
		}
	}

	public function handle(PacketHandlerInterface $handler) : bool{
		return $handler->handleCameraAimAssistActorPriority($this);
	}
}
