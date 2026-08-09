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

namespace pocketmine\data\bedrock\block;

/**
 * Implementors of this interface decide how a block should be deserialized and represented at runtime. This is used by
 * world providers when decoding blockstates into block IDs.
 *
 * @phpstan-type BlockStateId int
 */
interface BlockStateDeserializer{
	/**
	 * Deserializes blockstate NBT into an implementation-defined blockstate ID, for runtime paletted storage.
	 *
	 * @phpstan-return BlockStateId
	 * @throws BlockStateDeserializeException
	 */
	public function deserialize(BlockStateData $stateData) : int;
}
