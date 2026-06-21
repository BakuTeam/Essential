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

namespace pocketmine\network\mcpe\protocol\types\inventory\stackrequest;

use pocketmine\network\mcpe\protocol\ProtocolInfo;
use pocketmine\network\mcpe\protocol\serializer\PacketSerializer;
use pocketmine\network\mcpe\protocol\types\GetTypeIdFromConstTrait;

/**
 * Tells that the current transaction crafted the specified recipe.
 */
final class CraftRecipeStackRequestAction extends ItemStackRequestAction{
	use GetTypeIdFromConstTrait;

	public const ID = ItemStackRequestActionType::CRAFTING_RECIPE;

	final public function __construct(
		private int $recipeId,
		private int $repetitions
	){}

	public function getRecipeId() : int{ return $this->recipeId; }

	public function getRepetitions() : int{ return $this->repetitions; }

	public static function read(PacketSerializer $in) : self{
		$recipeId = $in->readRecipeNetId();
		if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_20){
			$repetitions = $in->getByte();
		}
		return new self($recipeId, $repetitions ?? 0);
	}

	public function write(PacketSerializer $out) : void{
		$out->writeRecipeNetId($this->recipeId);
		if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_20){
			$out->putByte($this->repetitions);
		}
	}
}
