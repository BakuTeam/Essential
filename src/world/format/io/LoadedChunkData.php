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

namespace pocketmine\world\format\io;

/**
 * Encapsulates information returned when loading a chunk. This includes more information than saving a chunk, since the
 * data might have been upgraded or need post-processing.
 */
final class LoadedChunkData{
	public const FIXER_FLAG_NONE = 0;
	public const FIXER_FLAG_ALL = ~0;

	public function __construct(
		private ChunkData $data,
		private bool $upgraded,
		private int $fixerFlags
	){}

	public function getData() : ChunkData{ return $this->data; }

	public function isUpgraded() : bool{ return $this->upgraded; }

	public function getFixerFlags() : int{ return $this->fixerFlags; }
}
