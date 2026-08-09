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

final class GeneratorManagerEntry{

	/**
	 * @phpstan-param class-string<Generator> $generatorClass
	 * @phpstan-param \Closure(string) : ?InvalidGeneratorOptionsException $presetValidator
	 */
	public function __construct(
		private string $generatorClass,
		private \Closure $presetValidator
	){}

	/** @phpstan-return class-string<Generator> */
	public function getGeneratorClass() : string{ return $this->generatorClass; }

	/**
	 * @throws InvalidGeneratorOptionsException
	 */
	public function validateGeneratorOptions(string $generatorOptions) : void{
		if(($exception = ($this->presetValidator)($generatorOptions)) !== null){
			throw $exception;
		}
	}
}
