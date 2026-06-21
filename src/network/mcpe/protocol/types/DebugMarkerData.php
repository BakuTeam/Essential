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

use pocketmine\color\Color;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\ProtocolInfo;
use pocketmine\network\mcpe\protocol\serializer\PacketSerializer;

final class DebugMarkerData{
	public function __construct(
		private string $text,
		private Vector3 $position,
		private Color $color,
		private int $durationMillis
	){}

	public function getText() : string{ return $this->text; }

	public function getPosition() : Vector3{ return $this->position; }

	public function getColor() : Color{ return $this->color; }

	public function getDurationMillis() : int{ return $this->durationMillis; }

	public static function read(PacketSerializer $in) : self{
		$text = $in->getString();
		$position = $in->getVector3();
		if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_130){
			$color = new Color(
				(int) ($in->getLFloat() * 255),
				(int) ($in->getLFloat() * 255),
				(int) ($in->getLFloat() * 255),
				(int) ($in->getLFloat() * 255)
			);
		}else{
			$color = Color::fromARGB($in->getLInt());
		}
		return new self($text, $position, $color, $in->getLLong());
	}

	public function write(PacketSerializer $out) : void{
		$out->putString($this->text);
		$out->putVector3($this->position);
		if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_130){
			$out->putLFloat($this->color->getR() / 255);
			$out->putLFloat($this->color->getG() / 255);
			$out->putLFloat($this->color->getB() / 255);
			$out->putLFloat($this->color->getA() / 255);
		}else{
			$out->putLInt($this->color->toARGB());
		}
		$out->putLLong($this->durationMillis);
	}
}
