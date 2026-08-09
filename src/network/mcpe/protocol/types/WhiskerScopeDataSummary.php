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

use pocketmine\network\mcpe\protocol\serializer\PacketSerializer;

final class WhiskerScopeDataSummary{

	public function __construct(
		private string $label,
		private string $indentation,
		private int $totalHighCostNS,
		private int $totalMidCostNS,
		private int $totalLowCostNS,
	){}

	public function getLabel() : string{ return $this->label; }

	public function getIndentation() : string{ return $this->indentation; }

	public function getTotalHighCostNS() : int{ return $this->totalHighCostNS; }

	public function getTotalMidCostNS() : int{ return $this->totalMidCostNS; }

	public function getTotalLowCostNS() : int{ return $this->totalLowCostNS; }

	public static function read(PacketSerializer $in) : self{
		$label = $in->getString();
		$indentation = $in->getString();
		$totalHighCostNS = $in->getLLong();
		$totalMidCostNS = $in->getLLong();
		$totalLowCostNS = $in->getLLong();

		return new self(
			$label,
			$indentation,
			$totalHighCostNS,
			$totalMidCostNS,
			$totalLowCostNS
		);
	}

	public function write(PacketSerializer $out) : void{
		$out->putString($this->label);
		$out->putString($this->indentation);
		$out->putLLong($this->totalHighCostNS);
		$out->putLLong($this->totalMidCostNS);
		$out->putLLong($this->totalLowCostNS);
	}
}
