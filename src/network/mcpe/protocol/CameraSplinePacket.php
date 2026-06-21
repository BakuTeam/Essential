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
use pocketmine\network\mcpe\protocol\types\camera\CameraSplineDefinition;
use function count;

class CameraSplinePacket extends DataPacket implements ClientboundPacket{
	public const NETWORK_ID = ProtocolInfo::CAMERA_SPLINE_PACKET;

	/**
	 * @var CameraSplineDefinition[]
	 * @phpstan-var list<CameraSplineDefinition>
	 */
	private array $splines;

	/**
	 * @generate-create-func
	 * @param CameraSplineDefinition[] $splines
	 * @phpstan-param list<CameraSplineDefinition> $splines
	 */
	public static function create(array $splines) : self{
		$result = new self();
		$result->splines = $splines;
		return $result;
	}

	/**
	 * @return CameraSplineDefinition[]
	 * @phpstan-return list<CameraSplineDefinition>
	 */
	public function getSplines() : array{ return $this->splines; }

	protected function decodePayload(PacketSerializer $in) : void{
		$this->splines = [];
		for($i = 0, $count = $in->getUnsignedVarInt(); $i < $count; ++$i){
			$this->splines[] = CameraSplineDefinition::read($in);
		}
	}

	protected function encodePayload(PacketSerializer $out) : void{
		$out->putUnsignedVarInt(count($this->splines));
		foreach($this->splines as $spline){
			$spline->write($out);
		}
	}

	public function handle(PacketHandlerInterface $handler) : bool{
		return $handler->handleCameraSpline($this);
	}
}
