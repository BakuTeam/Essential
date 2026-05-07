<?php

/*
 * This file is part of BedrockProtocol.
 * Copyright (C) 2014-2022 PocketMine Team <https://github.com/pmmp/BedrockProtocol>
 *
 * BedrockProtocol is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 */

declare(strict_types=1);

namespace pocketmine\network\mcpe\protocol\types\command;

use Ramsey\Uuid\UuidInterface;
use function array_search;

class CommandOriginData{
	public const ORIGIN_PLAYER = "player";
	public const ORIGIN_BLOCK = "commandblock";
	public const ORIGIN_MINECART_BLOCK = "minecartcommandblock";
	public const ORIGIN_DEV_CONSOLE = "devconsole";
	public const ORIGIN_TEST = "test";
	public const ORIGIN_AUTOMATION_PLAYER = "automationplayer";
	public const ORIGIN_CLIENT_AUTOMATION = "clientautomation";
	public const ORIGIN_DEDICATED_SERVER = "dedicatedserver";
	public const ORIGIN_ENTITY = "entity";
	public const ORIGIN_VIRTUAL = "virtual";
	public const ORIGIN_GAME_ARGUMENT = "gameargument";
	public const ORIGIN_ENTITY_SERVER = "entityserver";
	public const ORIGIN_PRECOMPILED = "precompiled";
	public const ORIGIN_GAME_DIRECTOR_ENTITY_SERVER = "gamedirectorentityserver";
	public const ORIGIN_SCRIPTING = "scripting";
	public const ORIGIN_EXECUTE_CONTEXT = "executecontext";

	private const TRANSLATION = [
		self::ORIGIN_PLAYER => 0,
		self::ORIGIN_BLOCK => 1,
		self::ORIGIN_MINECART_BLOCK => 2,
		self::ORIGIN_DEV_CONSOLE => 3,
		self::ORIGIN_TEST => 4,
		self::ORIGIN_AUTOMATION_PLAYER => 5,
		self::ORIGIN_CLIENT_AUTOMATION => 6,
		self::ORIGIN_DEDICATED_SERVER => 7,
		self::ORIGIN_ENTITY => 8,
		self::ORIGIN_VIRTUAL => 9,
		self::ORIGIN_GAME_ARGUMENT => 10,
		self::ORIGIN_ENTITY_SERVER => 11,
		self::ORIGIN_PRECOMPILED => 12,
		self::ORIGIN_GAME_DIRECTOR_ENTITY_SERVER => 13,
		self::ORIGIN_SCRIPTING => 14,
		self::ORIGIN_EXECUTE_CONTEXT => 15,
	];

	public static function getTypeFromId(int $typeId) : string{
		$type = array_search($typeId, self::TRANSLATION, true);
		if($type === false){
			throw new \InvalidArgumentException("Invalid type id: $typeId");
		}
		return $type;
	}

	public static function getIdFromType(string $type) : int{
		if(!isset(self::TRANSLATION[$type])){
			throw new \InvalidArgumentException("Invalid type: $type");
		}
		return self::TRANSLATION[$type];
	}

	public string $type;
	public UuidInterface $uuid;
	public string $requestId;
	public int $playerActorUniqueId;
}
