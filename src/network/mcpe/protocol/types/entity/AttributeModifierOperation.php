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

/**
 * Affects how the modifier value is applied to the target attribute. These operations are described on the Minecraft
 * Wiki at https://minecraft.fandom.com/wiki/Attribute
 *
 * These operations are listed in the order that they are applied in.
 */
final class AttributeModifierOperation{

	private function __construct(){
		//NOOP
	}

	/** Adds the modifier value to the attribute's base value. */
	public const ADD = 0;
	/**
	 * Multiplies the value by (1 + x), where x is the sum of all MULTIPLY_BASE modifiers' amounts. Multiple modifiers
	 * of this type have additive effects on each other.
	 */
	public const MULTIPLY_BASE = 1;
	/**
	 * Each modifier of this type multiplies the value by (1 + x), where x is the current modifier's value. Multiple
	 * modifiers of this type have multiplicative effects on each other.
	 */
	public const MULTIPLY_TOTAL = 2;
	/**
	 * Limits the result value. If the result value is greater than the limit, it is set to the limit.
	 */
	public const CAP = 3;
}
