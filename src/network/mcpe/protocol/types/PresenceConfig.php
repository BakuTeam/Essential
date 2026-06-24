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

namespace pocketmine\network\mcpe\protocol\types;

use pocketmine\network\mcpe\protocol\ProtocolInfo;
use pocketmine\network\mcpe\protocol\serializer\PacketSerializer;

final class PresenceConfig{
	public function __construct(
		private ?string $experienceName,
		private ?string $worldName,
		private string $richPresenceId = ""
	){}

	public function getExperienceName() : ?string{ return $this->experienceName; }

	public function getWorldName() : ?string{ return $this->worldName; }

	public function getRichPresenceId() : string{ return $this->richPresenceId; }

	public static function read(PacketSerializer $in) : self{
		if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_30){
			$experienceName = $in->readOptional(fn() => $in->getString());
			$worldName = $in->readOptional(fn() => $in->getString());
			$richPresenceId = $in->getString();
			return new self($experienceName, $worldName, $richPresenceId);
		}
		return new self($in->getString(), $in->getString());
	}

	public function write(PacketSerializer $out) : void{
		if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_30){
			$out->writeOptional($this->experienceName, fn(string $v) => $out->putString($v));
			$out->writeOptional($this->worldName, fn(string $v) => $out->putString($v));
			$out->putString($this->richPresenceId);
			return;
		}
		$out->putString($this->experienceName ?? "");
		$out->putString($this->worldName ?? "");
	}
}
