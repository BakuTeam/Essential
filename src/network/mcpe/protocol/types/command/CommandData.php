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

namespace pocketmine\network\mcpe\protocol\types\command;

class CommandData{
	/**
	 * @param CommandOverload[]       $overloads
	 * @param ChainedSubCommandData[] $chainedSubCommandData
	 */
	public function __construct(
		public string $name,
		public string $description,
		public int $flags,
		public int $permission,
		public ?CommandEnum $aliases,
		public array $overloads,
		public array $chainedSubCommandData
	){
		(function(CommandOverload ...$overloads) : void{})(...$overloads);
		(function(ChainedSubCommandData ...$chainedSubCommandData) : void{})(...$chainedSubCommandData);
	}

	public function getName() : string{
		return $this->name;
	}

	public function getDescription() : string{
		return $this->description;
	}

	public function getFlags() : int{
		return $this->flags;
	}

	public function getPermission() : int{
		return $this->permission;
	}

	public function getAliases() : ?CommandEnum{
		return $this->aliases;
	}

	/**
	 * @return CommandOverload[]
	 */
	public function getOverloads() : array{
		return $this->overloads;
	}

	/**
	 * @return ChainedSubCommandData[]
	 */
	public function getChainedSubCommandData() : array{
		return $this->chainedSubCommandData;
	}
}
