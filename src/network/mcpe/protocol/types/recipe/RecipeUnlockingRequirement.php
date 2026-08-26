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

namespace pocketmine\network\mcpe\protocol\types\recipe;

use pocketmine\network\mcpe\protocol\serializer\PacketSerializer;
use pocketmine\network\mcpe\protocol\ProtocolInfo;
use function count;

final class RecipeUnlockingRequirement{

	/**
	 * @param RecipeIngredient[]|null $unlockingIngredients
	 * @phpstan-param list<RecipeIngredient>|null $unlockingIngredients
	 */
	public function __construct(
		private ?array $unlockingIngredients
	){}

	/**
	 * @return RecipeIngredient[]|null
	 * @phpstan-return list<RecipeIngredient>|null
	 */
	public function getUnlockingIngredients() : ?array{ return $this->unlockingIngredients; }

	public static function read(PacketSerializer $in) : self{
		if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_40){
			$unlockingContext = $in->getVarInt();
			$unlockingIngredients = $in->readOptional(function() use ($in) : array{
				$result = [];
				for($i = 0, $count = $in->getUnsignedVarInt(); $i < $count; ++$i){
					$result[] = $in->getRecipeIngredient();
				}
				return $result;
			});
			if($unlockingContext !== 0 && $unlockingIngredients !== null){
				throw new \InvalidArgumentException("Unlocking ingredients are only valid for context NONE");
			}
			return new self($unlockingIngredients);
		}
		//I don't know what the point of this structure is. It could easily have been a list<RecipeIngredient> instead.
		//It's basically just an optional list, which could have been done by an empty list wherever it's not needed.
		$unlockingContext = $in->getBool();
		$unlockingIngredients = null;
		if(!$unlockingContext){
			$unlockingIngredients = [];
			for($i = 0, $count = $in->getUnsignedVarInt(); $i < $count; $i++){
				$unlockingIngredients[] = $in->getRecipeIngredient();
			}
		}

		return new self($unlockingIngredients);
	}

	public function write(PacketSerializer $out) : void{
		if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_40){
			$out->putVarInt($this->unlockingIngredients === null ? 1 : 0);
			$out->writeOptional($this->unlockingIngredients, function(array $ingredients) use ($out) : void{
				$out->putUnsignedVarInt(count($ingredients));
				foreach($ingredients as $ingredient){
					$out->putRecipeIngredient($ingredient);
				}
			});
			return;
		}
		$out->putBool($this->unlockingIngredients === null);
		if($this->unlockingIngredients !== null){
			$out->putUnsignedVarInt(count($this->unlockingIngredients));
			foreach($this->unlockingIngredients as $ingredient){
				$out->putRecipeIngredient($ingredient);
			}
		}
	}
}
