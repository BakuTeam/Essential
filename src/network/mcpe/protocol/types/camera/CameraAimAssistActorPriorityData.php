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

namespace pocketmine\network\mcpe\protocol\types\camera;

use pocketmine\network\mcpe\protocol\serializer\PacketSerializer;

final class CameraAimAssistActorPriorityData{
	public function __construct(
		private int $presetIndex,
		private int $categoryIndex,
		private int $actorIndex,
		private int $priority
	){}

	public function getPresetIndex() : int{ return $this->presetIndex; }

	public function getCategoryIndex() : int{ return $this->categoryIndex; }

	public function getActorIndex() : int{ return $this->actorIndex; }

	public function getPriority() : int{ return $this->priority; }

	public static function read(PacketSerializer $in) : self{
		return new self($in->getLInt(), $in->getLInt(), $in->getLInt(), $in->getLInt());
	}

	public function write(PacketSerializer $out) : void{
		$out->putLInt($this->presetIndex);
		$out->putLInt($this->categoryIndex);
		$out->putLInt($this->actorIndex);
		$out->putLInt($this->priority);
	}
}
