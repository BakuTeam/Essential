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

use function array_key_exists;
use function spl_object_id;

/**
 * @phpstan-template T of object
 * @phpstan-implements \IteratorAggregate<int, T>
 */
final class ObjectSet implements \IteratorAggregate{
	/**
	 * @var object[]
	 * @phpstan-var array<int, T>
	 */
	private array $objects = [];

	/** @phpstan-param T ...$objects */
	public function add(object ...$objects) : void{
		foreach($objects as $object){
			$this->objects[spl_object_id($object)] = $object;
		}
	}

	/** @phpstan-param T ...$objects */
	public function remove(object ...$objects) : void{
		foreach($objects as $object){
			unset($this->objects[spl_object_id($object)]);
		}
	}

	public function clear() : void{
		$this->objects = [];
	}

	public function contains(object $object) : bool{
		return array_key_exists(spl_object_id($object), $this->objects);
	}

	/** @phpstan-return \ArrayIterator<int, T> */
	public function getIterator() : \ArrayIterator{
		return new \ArrayIterator($this->objects);
	}

	/**
	 * @return object[]
	 * @phpstan-return array<int, T>
	 */
	public function toArray() : array{
		return $this->objects;
	}
}
