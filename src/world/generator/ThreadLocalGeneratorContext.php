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

namespace pocketmine\world\generator;

/**
 * Manages thread-local caches for generators and the things needed to support them
 */
final class ThreadLocalGeneratorContext{
	/**
	 * @var self[]
	 * @phpstan-var array<int, self>
	 */
	private static array $contexts = [];

	public static function register(self $context, int $worldId) : void{
		self::$contexts[$worldId] = $context;
	}

	public static function unregister(int $worldId) : void{
		unset(self::$contexts[$worldId]);
	}

	public static function fetch(int $worldId) : ?self{
		return self::$contexts[$worldId] ?? null;
	}

	public function __construct(
		private Generator $generator,
		private int $worldMinY,
		private int $worldMaxY
	){}

	public function getGenerator() : Generator{ return $this->generator; }

	public function getWorldMinY() : int{ return $this->worldMinY; }

	public function getWorldMaxY() : int{ return $this->worldMaxY; }
}
