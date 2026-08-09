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

namespace pocketmine\scheduler;

use function time;

/**
 * @internal
 */
final class AsyncPoolWorkerEntry{

	public int $lastUsed;
	/**
	 * @var \SplQueue|AsyncTask[]
	 * @phpstan-var \SplQueue<AsyncTask>
	 */
	public \SplQueue $tasks;

	public function __construct(
		public readonly AsyncWorker $worker,
		public readonly int $sleeperNotifierId
	){
		$this->lastUsed = time();
		$this->tasks = new \SplQueue();
	}

	public function submit(AsyncTask $task) : void{
		$this->tasks->enqueue($task);
		$this->lastUsed = time();
		$this->worker->stack($task);
	}
}
