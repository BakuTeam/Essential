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

namespace pocketmine\network\mcpe\protocol\types\biome;

use pocketmine\network\mcpe\protocol\serializer\PacketSerializer;
use function count;

final class BiomeConditionalTransformationData{

	/**
	 * @param BiomeWeightedData[] $weightedBiomes
	 */
	public function __construct(
		private array $weightedBiomes,
		private int $conditionJSON,
		private int $minPassingNeighbors,
	){}

	/**
	 * @return BiomeWeightedData[]
	 */
	public function getWeightedBiomes() : array{ return $this->weightedBiomes; }

	public function getConditionJSON() : int{ return $this->conditionJSON; }

	public function getMinPassingNeighbors() : int{ return $this->minPassingNeighbors; }

	public static function read(PacketSerializer $in) : self{
		$weightedBiomes = [];
		for($i = 0, $count = $in->getUnsignedVarInt(); $i < $count; ++$i){
			$weightedBiomes[] = BiomeWeightedData::read($in);
		}

		$conditionJSON = $in->getLShort();
		$minPassingNeighbors = $in->getLInt();

		return new self(
			$weightedBiomes,
			$conditionJSON,
			$minPassingNeighbors,
		);
	}

	public function write(PacketSerializer $out) : void{
		$out->putUnsignedVarInt(count($this->weightedBiomes));
		foreach($this->weightedBiomes as $biome){
			$biome->write($out);
		}

		$out->putLShort($this->conditionJSON);
		$out->putLInt($this->minPassingNeighbors);
	}
}
