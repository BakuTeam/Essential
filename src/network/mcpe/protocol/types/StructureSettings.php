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

namespace pocketmine\network\mcpe\protocol\types;

use pocketmine\math\Vector3;

class StructureSettings{

	public string $paletteName;
	public bool $ignoreEntities;
	public bool $ignoreBlocks;
	public bool $allowNonTickingChunks;
	public BlockPosition $dimensions;
	public BlockPosition $offset;
	public int $lastTouchedByPlayerID;
	public int $rotation;
	public int $mirror;
	public int $animationMode;
	public float $animationSeconds;
	public float $integrityValue;
	public int $integritySeed;
	public Vector3 $pivot;
}
