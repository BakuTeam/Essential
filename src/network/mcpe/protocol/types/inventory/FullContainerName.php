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

namespace pocketmine\network\mcpe\protocol\types\inventory;

use pocketmine\network\mcpe\protocol\ProtocolInfo;
use pocketmine\network\mcpe\protocol\serializer\PacketSerializer;

final class FullContainerName{
	public function __construct(
		private int $containerId,
		private ?int $dynamicId = null
	){}

	public function getContainerId() : int{ return $this->containerId; }

	public function getDynamicId() : ?int{ return $this->dynamicId; }

	public static function read(PacketSerializer $in) : self{
		$containerId = $in->getByte();
		if ($in->getProtocolId() < ProtocolInfo::PROTOCOL_1_19_50 && $containerId >= ContainerUIIds::RECIPE_BOOK) {
			$containerId++;
		}
		if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_30){
			$dynamicId = $in->readOptional($in->getLInt(...));
		}elseif($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_20){
			$dynamicId = $in->getLInt();
		}
		return new self($containerId, $dynamicId ?? null);
	}

	public function write(PacketSerializer $out) : void{
		$containerId = $this->containerId;
		if ($out->getProtocolId() < ProtocolInfo::PROTOCOL_1_19_50) {
			if ($containerId > ContainerUIIds::RECIPE_BOOK) {
				$containerId--;
			} elseif ($containerId === ContainerUIIds::RECIPE_BOOK) {
				throw new \InvalidArgumentException("Invalid container ID for protocol version " . $out->getProtocolId());
			}
		}
		$out->putByte($containerId);
		if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_30){
			$out->writeOptional($this->dynamicId, $out->putLInt(...));
		}elseif($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_20){
			$out->putLInt($this->dynamicId ?? 0);
		}
	}
}
