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

/**
 * Permission related classes
 */

namespace pocketmine\permission;

use pocketmine\lang\Translatable;

/**
 * Represents a permission
 */
class Permission{
	private Translatable|string $description;

	/**
	 * Creates a new Permission object to be attached to Permissible objects
	 *
	 * @param bool[] $children
	 * @phpstan-param array<string, bool> $children
	 */
	public function __construct(
		private string $name,
		Translatable|string|null $description = null,
		private array $children = []
	){
		$this->description = $description ?? ""; //TODO: wtf ????

		$this->recalculatePermissibles();
	}

	public function getName() : string{
		return $this->name;
	}

	/**
	 * @return bool[]
	 * @phpstan-return array<string, bool>
	 */
	public function getChildren() : array{
		return $this->children;
	}

	public function getDescription() : Translatable|string{
		return $this->description;
	}

	public function setDescription(Translatable|string $value) : void{
		$this->description = $value;
	}

	/**
	 * @return PermissibleInternal[]
	 */
	public function getPermissibles() : array{
		return PermissionManager::getInstance()->getPermissionSubscriptions($this->name);
	}

	public function recalculatePermissibles() : void{
		$perms = $this->getPermissibles();

		foreach($perms as $p){
			$p->recalculatePermissions();
		}
	}

	public function addChild(string $name, bool $value) : void{
		$this->children[$name] = $value;
		$this->recalculatePermissibles();
	}

	public function removeChild(string $name) : void{
		unset($this->children[$name]);
		$this->recalculatePermissibles();

	}
}
