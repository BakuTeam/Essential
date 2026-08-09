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

final class InternetRequestResult{
	/**
	 * @param string[][] $headers
	 * @phpstan-param list<array<string, string>> $headers
	 */
	public function __construct(
		private array $headers,
		private string $body,
		private int $code
	){}

	/**
	 * @return string[][]
	 * @phpstan-return list<array<string, string>>
	 */
	public function getHeaders() : array{ return $this->headers; }

	public function getBody() : string{ return $this->body; }

	public function getCode() : int{ return $this->code; }
}
