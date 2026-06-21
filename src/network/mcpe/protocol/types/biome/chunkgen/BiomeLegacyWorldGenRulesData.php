<?php

/*
 *
 *  ____            _        _   __  __ _                  __  __ ____
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine Team
 * @link http://www.pocketmine.net/
 *
 *
 */

declare(strict_types=1);

namespace pocketmine\network\mcpe\protocol\types\biome\chunkgen;

use pocketmine\network\mcpe\protocol\serializer\PacketSerializer;
use function count;

final class BiomeLegacyWorldGenRulesData{

	/**
	 * @param BiomeConditionalTransformationData[] $legacyPreHills
	 */
	public function __construct(
		private array $legacyPreHills,
	){}

	/**
	 * @return BiomeConditionalTransformationData[]
	 */
	public function getLegacyPreHills() : array{ return $this->legacyPreHills; }

	public static function read(PacketSerializer $in) : self{
		$legacyPreHills = [];
		for($i = 0, $count = $in->getUnsignedVarInt(); $i < $count; ++$i){
			$legacyPreHills[] = BiomeConditionalTransformationData::read($in);
		}

		return new self(
			$legacyPreHills,
		);
	}

	public function write(PacketSerializer $out) : void{
		$out->putUnsignedVarInt(count($this->legacyPreHills));
		foreach($this->legacyPreHills as $biome){
			$biome->write($out);
		}
	}
}
