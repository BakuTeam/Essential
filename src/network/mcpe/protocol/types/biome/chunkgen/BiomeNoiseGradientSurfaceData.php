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

final class BiomeNoiseGradientSurfaceData{

	/**
	 * @param int[]   $nonReplaceableBlocks
	 * @param int[]   $gradientBlocks
	 * @param float[] $amplitudes
	 */
	public function __construct(
		private array $nonReplaceableBlocks,
		private array $gradientBlocks,
		private string $noiseSeed,
		private int $firstOctave,
		private array $amplitudes
	){}

	/**
	 * @return int[]
	 */
	public function getNonReplaceableBlocks() : array{ return $this->nonReplaceableBlocks; }

	/**
	 * @return int[]
	 */
	public function getGradientBlocks() : array{ return $this->gradientBlocks; }

	public function getNoiseSeed() : string{ return $this->noiseSeed; }

	public function getFirstOctave() : int{ return $this->firstOctave; }

	/**
	 * @return float[]
	 */
	public function getAmplitudes() : array{ return $this->amplitudes; }

	public static function read(PacketSerializer $in) : self{
		$nonReplaceableBlocks = [];
		for($i = 0, $count = $in->getUnsignedVarInt(); $i < $count; ++$i){
			$nonReplaceableBlocks[] = $in->getLInt();
		}

		$gradientBlocks = [];
		for($i = 0, $count = $in->getUnsignedVarInt(); $i < $count; ++$i){
			$gradientBlocks[] = $in->getLInt();
		}

		$noiseSeed = $in->getString();
		$firstOctave = $in->getLInt();

		$amplitudes = [];
		for($i = 0, $count = $in->getUnsignedVarInt(); $i < $count; ++$i){
			$amplitudes[] = $in->getLFloat();
		}

		return new self($nonReplaceableBlocks, $gradientBlocks, $noiseSeed, $firstOctave, $amplitudes);
	}

	public function write(PacketSerializer $out) : void{
		$out->putUnsignedVarInt(count($this->nonReplaceableBlocks));
		foreach($this->nonReplaceableBlocks as $value){
			$out->putLInt($value);
		}

		$out->putUnsignedVarInt(count($this->gradientBlocks));
		foreach($this->gradientBlocks as $value){
			$out->putLInt($value);
		}

		$out->putString($this->noiseSeed);
		$out->putLInt($this->firstOctave);

		$out->putUnsignedVarInt(count($this->amplitudes));
		foreach($this->amplitudes as $value){
			$out->putLFloat($value);
		}
	}
}
