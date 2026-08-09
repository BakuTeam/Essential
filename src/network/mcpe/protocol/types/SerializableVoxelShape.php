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
use function count;

final class SerializableVoxelShape{
	/**
	 * @param float[] $xCoordinates
	 * @param float[] $yCoordinates
	 * @param float[] $zCoordinates
	 * @phpstan-param list<float> $xCoordinates
	 * @phpstan-param list<float> $yCoordinates
	 * @phpstan-param list<float> $zCoordinates
	 */
	public function __construct(
		private SerializableVoxelCells $cells,
		private array $xCoordinates,
		private array $yCoordinates,
		private array $zCoordinates
	){}

	public function getCells() : SerializableVoxelCells{ return $this->cells; }

	/**
	 * @return float[]
	 * @phpstan-return list<float>
	 */
	public function getXCoordinates() : array{ return $this->xCoordinates; }

	/**
	 * @return float[]
	 * @phpstan-return list<float>
	 */
	public function getYCoordinates() : array{ return $this->yCoordinates; }

	/**
	 * @return float[]
	 * @phpstan-return list<float>
	 */
	public function getZCoordinates() : array{ return $this->zCoordinates; }

	public static function read(PacketSerializer $in) : self{
		$cells = SerializableVoxelCells::read($in);
		$xCoordinates = [];
		for($i = 0, $count = $in->getUnsignedVarInt(); $i < $count; ++$i){
			$xCoordinates[] = $in->getLFloat();
		}
		$yCoordinates = [];
		for($i = 0, $count = $in->getUnsignedVarInt(); $i < $count; ++$i){
			$yCoordinates[] = $in->getLFloat();
		}
		$zCoordinates = [];
		for($i = 0, $count = $in->getUnsignedVarInt(); $i < $count; ++$i){
			$zCoordinates[] = $in->getLFloat();
		}

		return new self($cells, $xCoordinates, $yCoordinates, $zCoordinates);
	}

	public function write(PacketSerializer $out) : void{
		$this->cells->write($out);
		$out->putUnsignedVarInt(count($this->xCoordinates));
		foreach($this->xCoordinates as $value){
			$out->putLFloat($value);
		}
		$out->putUnsignedVarInt(count($this->yCoordinates));
		foreach($this->yCoordinates as $value){
			$out->putLFloat($value);
		}
		$out->putUnsignedVarInt(count($this->zCoordinates));
		foreach($this->zCoordinates as $value){
			$out->putLFloat($value);
		}
	}
}
