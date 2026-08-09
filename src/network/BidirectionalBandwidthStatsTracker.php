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

namespace pocketmine\network;

final class BidirectionalBandwidthStatsTracker{
	private BandwidthStatsTracker $send;
	private BandwidthStatsTracker $receive;

	/** @phpstan-param positive-int $historySize */
	public function __construct(int $historySize){
		$this->send = new BandwidthStatsTracker($historySize);
		$this->receive = new BandwidthStatsTracker($historySize);
	}

	public function getSend() : BandwidthStatsTracker{ return $this->send; }

	public function getReceive() : BandwidthStatsTracker{ return $this->receive; }

	public function add(int $sendBytes, int $recvBytes) : void{
		$this->send->add($sendBytes);
		$this->receive->add($recvBytes);
	}

	/** @see BandwidthStatsTracker::rotateHistory() */
	public function rotateAverageHistory() : void{
		$this->send->rotateHistory();
		$this->receive->rotateHistory();
	}

	/** @see BandwidthStatsTracker::resetHistory() */
	public function resetHistory() : void{
		$this->send->resetHistory();
		$this->receive->resetHistory();
	}
}
