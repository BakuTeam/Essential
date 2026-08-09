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

enum ScriptDebugShapeType : int{
	use PacketIntEnumTrait;

	case LINE = 0;
	case BOX = 1;
	case SPHERE = 2;
	case CIRCLE = 3;
	case TEXT = 4;
	case ARROW = 5;

	/** @deprecated */
	const TEST = self::TEXT;

	public const PAYLOAD_TYPE_NONE = 0;
	public const PAYLOAD_TYPE_ARROW = 1;
	public const PAYLOAD_TYPE_TEXT = 2;
	public const PAYLOAD_TYPE_BOX = 3;
	public const PAYLOAD_TYPE_LINE = 4;
	public const PAYLOAD_TYPE_CIRCLE_OR_SPHERE = 5;

	public function getPayloadType() : int{
		return match($this){
			self::ARROW => self::PAYLOAD_TYPE_ARROW,
			self::TEXT => self::PAYLOAD_TYPE_TEXT,
			self::BOX => self::PAYLOAD_TYPE_BOX,
			self::LINE => self::PAYLOAD_TYPE_LINE,
			self::CIRCLE, self::SPHERE => self::PAYLOAD_TYPE_CIRCLE_OR_SPHERE
		};
	}
}
