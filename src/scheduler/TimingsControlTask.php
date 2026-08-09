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

use pocketmine\timings\TimingsHandler;

final class TimingsControlTask extends AsyncTask{

	private const ENABLE = 1;
	private const DISABLE = 2;
	private const RELOAD = 3;

	private function __construct(
		private int $operation
	){}

	public static function setEnabled(bool $enable) : self{
		return new self($enable ? self::ENABLE : self::DISABLE);
	}

	public static function reload() : self{
		return new self(self::RELOAD);
	}

	public function onRun() : void{
		if($this->operation === self::ENABLE){
			TimingsHandler::setEnabled(true);
			\GlobalLogger::get()->debug("Enabled timings");
		}elseif($this->operation === self::DISABLE){
			TimingsHandler::setEnabled(false);
			\GlobalLogger::get()->debug("Disabled timings");
		}elseif($this->operation === self::RELOAD){
			TimingsHandler::reload();
			\GlobalLogger::get()->debug("Reset timings");
		}else{
			throw new \InvalidArgumentException("Invalid operation $this->operation");
		}
	}
}
