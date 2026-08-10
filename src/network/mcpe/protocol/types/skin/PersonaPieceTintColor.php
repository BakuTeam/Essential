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

namespace pocketmine\network\mcpe\protocol\types\skin;

final class PersonaPieceTintColor{

	public const PIECE_TYPE_PERSONA_EYES = "persona_eyes";
	public const PIECE_TYPE_PERSONA_HAIR = "persona_hair";
	public const PIECE_TYPE_PERSONA_MOUTH = "persona_mouth";

	/** Number of colours each tint always carries on the wire since 1.26.40. */
	public const EXPECTED_COLOR_COUNT = 4;

	/**
	 * @param string[] $colors
	 */
	public function __construct(
		private string $pieceType,
		private array $colors
	){}

	public function getPieceType() : string{
		return $this->pieceType;
	}

	/**
	 * @return string[]
	 */
	public function getColors() : array{
		return $this->colors;
	}
}
