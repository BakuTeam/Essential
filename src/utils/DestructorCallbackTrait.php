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

/**
 * This trait provides destructor callback functionality to objects which use it. This enables a weakmap-like system
 * to function without actually having weak maps.
 * TODO: remove this in PHP 8
 */
trait DestructorCallbackTrait{
	/**
	 * @var ObjectSet
	 * @phpstan-var ObjectSet<\Closure() : void>|null
	 */
	private $destructorCallbacks = null;

	/** @phpstan-return ObjectSet<\Closure() : void> */
	public function getDestructorCallbacks() : ObjectSet{
		return $this->destructorCallbacks === null ? ($this->destructorCallbacks = new ObjectSet()) : $this->destructorCallbacks;
	}

	public function __destruct(){
		if($this->destructorCallbacks !== null){
			foreach($this->destructorCallbacks as $callback){
				$callback();
			}
		}
	}
}
