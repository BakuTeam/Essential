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

namespace pocketmine\plugin;

use pocketmine\utils\Utils;
use function count;
use function file;
use function implode;
use function is_file;
use function str_contains;
use function str_ends_with;
use const FILE_IGNORE_NEW_LINES;
use const FILE_SKIP_EMPTY_LINES;

/**
 * Simple script loader, not for plugin development
 * For an example see https://gist.github.com/shoghicp/516105d470cf7d140757
 */
class ScriptPluginLoader implements PluginLoader{

	public function canLoadPlugin(string $path) : bool{
		return is_file($path) && str_ends_with($path, ".php");
	}

	/**
	 * Loads the plugin contained in $file
	 */
	public function loadPlugin(string $file) : void{
		include_once $file;
	}

	/**
	 * Gets the PluginDescription from the file
	 */
	public function getPluginDescription(string $file) : ?PluginDescription{
		$content = @file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		if($content === false){
			return null;
		}

		$insideHeader = false;

		$docCommentLines = [];
		foreach($content as $line){
			if(!$insideHeader){
				if(str_contains($line, "/**")){
					$insideHeader = true;
				}else{
					continue;
				}
			}

			$docCommentLines[] = $line;

			if(str_contains($line, "*/")){
				break;
			}
		}

		$data = Utils::parseDocComment(implode("\n", $docCommentLines));
		if(count($data) !== 0){
			return new PluginDescription($data);
		}

		return null;
	}

	public function getAccessProtocol() : string{
		return "";
	}
}
