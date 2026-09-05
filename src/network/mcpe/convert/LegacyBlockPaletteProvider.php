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
 * @link https:
 *
 *
 */

declare(strict_types=1);

namespace pocketmine\network\mcpe\convert;

use pocketmine\data\bedrock\BedrockDataFiles;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\LongTag;
use pocketmine\nbt\tag\ShortTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\nbt\tag\Tag;
use pocketmine\network\mcpe\protocol\ProtocolInfo;
use pocketmine\network\mcpe\protocol\types\BlockPaletteEntry;
use pocketmine\network\mcpe\protocol\types\CacheableNbt;
use pocketmine\utils\AssumptionFailedError;
use pocketmine\utils\Filesystem;
use function is_array;
use function json_decode;
use function str_replace;
use const JSON_THROW_ON_ERROR;

/**
 * @internal
 */
final class LegacyBlockPaletteProvider{

	/**
	 * @var BlockPaletteEntry[][]
	 * @phpstan-var array<int, list<BlockPaletteEntry>>
	 */
	private static array $cache = [];

	private function __construct(){
	}

	/**
	 * Returns whether the given protocol expects the pre-1.16.100 wrapped block palette in StartGamePacket.
	 */
	public static function isRequired(int $protocolId) : bool{
		return $protocolId < ProtocolInfo::PROTOCOL_1_16_100;
	}

	/**
	 * @return BlockPaletteEntry[]
	 * @phpstan-return list<BlockPaletteEntry>
	 */
	public static function getPalette(int $protocolId) : array{
		return self::$cache[$protocolId] ??= self::build($protocolId);
	}

	/**
	 * @return BlockPaletteEntry[]
	 * @phpstan-return list<BlockPaletteEntry>
	 */
	private static function build(int $protocolId) : array{
		$raw = Filesystem::fileGetContents(str_replace(
			".json",
			BlockTranslator::getCanonicalBlockStatesPath($protocolId) . ".json",
			BedrockDataFiles::CANONICAL_BLOCK_STATES_JSON
		));
		$decoded = json_decode($raw, true, flags: JSON_THROW_ON_ERROR);
		if(!is_array($decoded)){
			throw new AssumptionFailedError("Invalid canonical block state palette for protocol $protocolId");
		}

		$shortLegacyIds = $protocolId < ProtocolInfo::PROTOCOL_1_16_0;

		$entries = [];
		foreach($decoded as $entry){
			if(!is_array($entry) || !isset($entry["name"]) || !is_array($entry["states"] ?? null)){
				throw new AssumptionFailedError("Invalid canonical block state entry for protocol $protocolId");
			}
			$name = (string) $entry["name"];

			$states = CompoundTag::create();
			foreach($entry["states"] as $state){
				$states->setTag((string) $state["name"], self::makeTag((int) $state["type"], $state["value"]));
			}

			$block = CompoundTag::create()
				->setString("name", $name)
				->setInt("version", (int) ($entry["version"] ?? 0))
				->setTag("states", $states);

			$legacyId = (int) ($entry["id"] ?? 0);
			$wrapper = CompoundTag::create()->setTag("block", $block);
			$wrapper->setTag("id", $shortLegacyIds ? new ShortTag($legacyId) : new IntTag($legacyId));

			$entries[] = new BlockPaletteEntry($name, new CacheableNbt($wrapper));
		}

		return $entries;
	}

	private static function makeTag(int $type, mixed $value) : Tag{
		return match($type){
			1 => new ByteTag((int) $value),
			2 => new ShortTag((int) $value),
			3 => new IntTag((int) $value),
			4 => new LongTag((int) $value),
			8 => new StringTag((string) $value),
			default => throw new AssumptionFailedError("Unknown blockstate property NBT type $type")
		};
	}
}
