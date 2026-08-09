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

final class Attribute{
	/**
	 * @param AttributeModifier[] $modifiers
	 */
	public function __construct(
		private string|int $id,
		private float $min,
		private float $max,
		private float $current,
		private float $default,
		private array $modifiers
	){}

	public function getId() : string|int{
		return $this->id;
	}

	public function getMin() : float{
		return $this->min;
	}

	public function getMax() : float{
		return $this->max;
	}

	public function getCurrent() : float{
		return $this->current;
	}

	public function getDefault() : float{
		return $this->default;
	}

	/**
	 * @return AttributeModifier[]
	 */
	public function getModifiers() : array{ return $this->modifiers; }
}
