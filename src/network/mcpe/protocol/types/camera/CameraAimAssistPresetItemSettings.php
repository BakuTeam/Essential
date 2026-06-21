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

final class CameraAimAssistPresetItemSettings{

	public function __construct(
		private string $itemIdentifier,
		private string $categoryName,
	){}

	public function getItemIdentifier() : string{ return $this->itemIdentifier; }

	public function getCategoryName() : string{ return $this->categoryName; }

	public static function read(PacketSerializer $in) : self{
		$itemIdentifier = $in->getString();
		$categoryName = $in->getString();
		return new self(
			$itemIdentifier,
			$categoryName
		);
	}

	public function write(PacketSerializer $out) : void{
		$out->putString($this->itemIdentifier);
		$out->putString($this->categoryName);
	}
}
