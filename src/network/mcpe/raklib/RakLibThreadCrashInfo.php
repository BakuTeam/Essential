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

namespace pocketmine\network\mcpe\raklib;

use pocketmine\utils\Filesystem;
use function get_class;
use function sprintf;

final class RakLibThreadCrashInfo{

	public function __construct(
		private ?string $class,
		private string $message,
		private string $file,
		private int $line
	){}

	public static function fromThrowable(\Throwable $e) : self{
		return new self(get_class($e), $e->getMessage(), $e->getFile(), $e->getLine());
	}

	/**
	 * @phpstan-param array{message: string, file: string, line: int} $info
	 */
	public static function fromLastErrorInfo(array $info) : self{
		return new self(null, $info["message"], $info["file"], $info["line"]);
	}

	public function getClass() : ?string{ return $this->class; }

	public function getMessage() : string{ return $this->message; }

	public function getFile() : string{ return $this->file; }

	public function getLine() : int{ return $this->line; }

	public function makePrettyMessage() : string{
		return sprintf("%s: \"%s\" in %s on line %d", $this->class ?? "Fatal error", $this->message, Filesystem::cleanPath($this->file), $this->line);
	}
}
