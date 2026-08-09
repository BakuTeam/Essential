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

final class BiomeCappedSurfaceData{

	/**
	 * @param int[] $floorBlocks
	 * @param int[] $ceilingBlocks
	 */
	public function __construct(
		private array $floorBlocks,
		private array $ceilingBlocks,
		private ?int $seaBlock,
		private ?int $foundationBlock,
		private ?int $beachBlock,
	){}

	/**
	 * @return int[]
	 */
	public function getFloorBlocks() : array{ return $this->floorBlocks; }

	/**
	 * @return int[]
	 */
	public function getCeilingBlocks() : array{ return $this->ceilingBlocks; }

	public function getSeaBlock() : ?int{ return $this->seaBlock; }

	public function getFoundationBlock() : ?int{ return $this->foundationBlock; }

	public function getBeachBlock() : ?int{ return $this->beachBlock; }

	public static function read(PacketSerializer $in) : self{
		$floorBlocks = [];
		for($i = 0, $count = $in->getUnsignedVarInt(); $i < $count; ++$i){
			$floorBlocks[] = $in->getLInt();
		}

		$ceilingBlocks = [];
		for($i = 0, $count = $in->getUnsignedVarInt(); $i < $count; ++$i){
			$ceilingBlocks[] = $in->getLInt();
		}

		$seaBlock = $in->readOptional($in->getLInt(...));
		$foundationBlock = $in->readOptional($in->getLInt(...));
		$beachBlock = $in->readOptional($in->getLInt(...));

		return new self(
			$floorBlocks,
			$ceilingBlocks,
			$seaBlock,
			$foundationBlock,
			$beachBlock
		);
	}

	public function write(PacketSerializer $out) : void{
		$out->putUnsignedVarInt(count($this->floorBlocks));
		foreach($this->floorBlocks as $block){
			$out->putLInt($block);
		}

		$out->putUnsignedVarInt(count($this->ceilingBlocks));
		foreach($this->ceilingBlocks as $block){
			$out->putLInt($block);
		}

		$out->writeOptional($this->seaBlock, $out->putLInt(...));
		$out->writeOptional($this->foundationBlock, $out->putLInt(...));
		$out->writeOptional($this->beachBlock, $out->putLInt(...));
	}
}
