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
