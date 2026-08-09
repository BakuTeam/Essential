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

namespace pocketmine\utils;

use function count;

class WeightedRandom{
	/** @var array<array<int, mixed>> */
	private array $items = [];
	private int $totalWeight = 0;

	public function __construct(private Random $random){
	}

	/*	@phpstan-ignore-next-line */
	public function insert($item, int $weight) : void{
		$this->items[] = [$weight, $item];
		$this->totalWeight += $weight;
	}

	public function next(bool $remove = true) : mixed{
		if(count($this->items) < 1){
			return null;
		}
		$w = $this->random->nextRange(end: $this->totalWeight);
		foreach($this->items as $i => [$weight, $item]){
			$w -= $weight;
			if($w >= 0){
				continue;
			}
			if($remove){
				$this->totalWeight -= $weight;
				unset($this->items[$i]);
			}
			return $item;
		}
		return null;
	}
}
