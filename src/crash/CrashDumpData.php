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

namespace pocketmine\crash;

final class CrashDumpData implements \JsonSerializable{

	public int $format_version;

	public float $time;

	public float $uptime;

	/** @var mixed[] */
	public array $lastError = [];

	/** @var mixed[] */
	public array $error;

	public string $thread;

	public string $plugin_involvement;

	public string $plugin = "";

	/**
	 * @var string[]
	 * @phpstan-var array<int, string>
	 */
	public array $code = [];

	/** @var string[] */
	public array $trace;

	/**
	 * @var CrashDumpDataPluginEntry[]
	 * @phpstan-var array<string, CrashDumpDataPluginEntry>
	 */
	public array $plugins = [];

	/**
	 * @var string[]
	 * @phpstan-var list<string>
	 */
	public array $parameters = [];

	public string $serverDotProperties = "";

	public string $pocketmineDotYml = "";

	/**
	 * @var string[]
	 * @phpstan-var array<string, string>
	 */
	public array $extensions = [];

	public ?int $jit_mode = null;

	public string $phpinfo = "";

	public CrashDumpDataGeneral $general;

	/**
	 * @return mixed[]
	 */
	public function jsonSerialize() : array{
		$result = (array) $this;
		unset($result["serverDotProperties"]);
		unset($result["pocketmineDotYml"]);
		$result["pocketmine.yml"] = $this->pocketmineDotYml;
		$result["server.properties"] = $this->serverDotProperties;
		return $result;
	}
}
