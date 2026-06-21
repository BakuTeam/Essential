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

namespace pocketmine\network\mcpe\protocol\types\biome;

use pocketmine\network\mcpe\protocol\serializer\PacketSerializer;
use function count;

final class BiomeConsolidatedFeaturesData{

	/**
	 * @param BiomeConsolidatedFeatureData[] $features
	 */
	public function __construct(
		private array $features,
	){}

	/**
	 * @return BiomeConsolidatedFeatureData[]
	 */
	public function getFeatures() : array{ return $this->features; }

	public static function read(PacketSerializer $in) : self{
		$features = [];

		for($i = 0, $count = $in->getUnsignedVarInt(); $i < $count; ++$i){
			$features[] = BiomeConsolidatedFeatureData::read($in);
		}

		return new self($features);
	}

	public function write(PacketSerializer $out) : void{
		$out->putUnsignedVarInt(count($this->features));
		foreach($this->features as $feature){
			$feature->write($out);
		}
	}
}
