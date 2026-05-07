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

namespace pocketmine\network\mcpe\protocol\types\biome\chunkgen;

use pocketmine\network\mcpe\protocol\serializer\PacketSerializer;
use function count;

final class BiomeReplacementData{

	/**
	 * @param int[] $targetBiomes
	 */
	public function __construct(
		private int $biome,
		private int $dimension,
		private array $targetBiomes,
		private float $amount,
		private int $replacementIndex
	){}

	public function getBiome() : int{ return $this->biome; }

	public function getDimension() : int{ return $this->dimension; }

	/**
	 * @return int[]
	 */
	public function getTargetBiomes() : array{ return $this->targetBiomes; }

	public function getAmount() : float{ return $this->amount; }

	public function getReplacementIndex() : int{ return $this->replacementIndex; }

	public static function read(PacketSerializer $in) : self{
		$biome = $in->getSignedShort();
		$dimension = $in->getVarInt();
		$targetBiomes = [];
		$targetBiomeCount = $in->getUnsignedVarInt();
		for($i = 0; $i < $targetBiomeCount; ++$i){
			$targetBiomes[] = $in->getSignedShort();
		}
		$amount = $in->getFloat();
		$replacementIndex = $in->getUnsignedVarInt();
		return new self($biome, $dimension, $targetBiomes, $amount, $replacementIndex);
	}

	public function write(PacketSerializer $out) : void{
		$out->putShort($this->biome);
		$out->putVarInt($this->dimension);
		$out->putUnsignedVarInt(count($this->targetBiomes));
		foreach($this->targetBiomes as $biome){
			$out->putShort($biome);
		}
		$out->putFloat($this->amount);
		$out->putUnsignedVarInt($this->replacementIndex);
	}
}