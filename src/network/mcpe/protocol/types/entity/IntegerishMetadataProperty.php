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

namespace pocketmine\network\mcpe\protocol\types\entity;

trait IntegerishMetadataProperty{
	public function __construct(
		private int $value
	){
		// if($value < $this->min() or $value > $this->max()){
		// 	throw new \InvalidArgumentException("Value is out of range " . $this->min() . " - " . $this->max());
		// }
	}

	abstract protected function min() : int;

	abstract protected function max() : int;

	public function getValue() : int{
		return $this->value;
	}

	public function equals(MetadataProperty $other) : bool{
		return $other instanceof self && $other->value === $this->value;
	}

	/**
	 * @param bool[] $flags
	 * @phpstan-param array<int, bool> $flags
	 */
	public static function buildFromFlags(array $flags) : self{
		$value = 0;
		foreach($flags as $flag => $v){
			if($v){
				$value |= 1 << $flag;
			}
		}
		return new self($value);
	}
}
