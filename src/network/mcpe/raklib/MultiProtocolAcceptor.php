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

use raklib\server\ProtocolAcceptor;
use function in_array;

class MultiProtocolAcceptor implements ProtocolAcceptor{
	/**
	 * @param int[] $protocolVersions
	 */
	public function __construct(private int $primaryVersion, private array $protocolVersions){}

	public function accepts(int $protocolVersion) : bool{
		return in_array($protocolVersion, $this->protocolVersions, true);
	}

	public function getPrimaryVersion() : int{
		return $this->primaryVersion;
	}
}
