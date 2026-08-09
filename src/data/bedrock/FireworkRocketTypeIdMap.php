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

namespace pocketmine\data\bedrock;

use pocketmine\item\FireworkRocketType;
use pocketmine\utils\SingletonTrait;

final class FireworkRocketTypeIdMap{
	use SingletonTrait;

	/**
	 * @var FireworkRocketType[]
	 * @phpstan-var array<int, FireworkRocketType>
	 */
	private array $idToEnum = [];

	/**
	 * @var int[]
	 * @phpstan-var array<int, int>
	 */
	private array $enumToId = [];

	private function __construct(){
		$this->register(FireworkRocketTypeIds::SMALL_BALL, FireworkRocketType::SMALL_BALL);
		$this->register(FireworkRocketTypeIds::LARGE_BALL, FireworkRocketType::LARGE_BALL);
		$this->register(FireworkRocketTypeIds::STAR, FireworkRocketType::STAR);
		$this->register(FireworkRocketTypeIds::CREEPER, FireworkRocketType::CREEPER);
		$this->register(FireworkRocketTypeIds::BURST, FireworkRocketType::BURST);
	}

	private function register(int $id, FireworkRocketType $type) : void{
		$this->idToEnum[$id] = $type;
		$this->enumToId[$type->id()] = $id;
	}

	public function fromId(int $id) : ?FireworkRocketType{
		return $this->idToEnum[$id] ?? null;
	}

	public function toId(FireworkRocketType $type) : int{
		if(!isset($this->enumToId[$type->id()])){
			throw new \InvalidArgumentException("Type does not have a mapped ID");
		}
		return $this->enumToId[$type->id()];
	}
}
