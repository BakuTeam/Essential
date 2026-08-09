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

final class PlayerAuthInputVehicleInfo{

	public function __construct(
		private ?float $vehicleRotationX,
		private ?float $vehicleRotationZ,
		private int $predictedVehicleActorUniqueId
	){}

	public function getVehicleRotationX() : ?float{ return $this->vehicleRotationX; }

	public function getVehicleRotationZ() : ?float{ return $this->vehicleRotationZ; }

	public function getPredictedVehicleActorUniqueId() : int{ return $this->predictedVehicleActorUniqueId; }

	public static function read(PacketSerializer $in) : self{
		if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_20_70){
			$vehicleRotationX = $in->getLFloat();
			$vehicleRotationZ = $in->getLFloat();
		}
		$predictedVehicleActorUniqueId = $in->getActorUniqueId();

		return new self($vehicleRotationX ?? null, $vehicleRotationZ ?? null, $predictedVehicleActorUniqueId);
	}

	public function write(PacketSerializer $out) : void{
		if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_20_70){
			$out->putLFloat($this->vehicleRotationX ?? throw new \InvalidArgumentException("vehicleRotationX must be set for 1.20.70+"));
			$out->putLFloat($this->vehicleRotationZ ?? throw new \InvalidArgumentException("vehicleRotationZ must be set for 1.20.70+"));
		}
		$out->putActorUniqueId($this->predictedVehicleActorUniqueId);
	}
}
