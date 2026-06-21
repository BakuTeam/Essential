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

namespace pocketmine\network\mcpe\protocol\types;

use pocketmine\network\mcpe\protocol\serializer\PacketSerializer;
use function count;

final class SyncWorldClockData{
	/**
	 * @param SyncWorldClockMarkerData[] $markers
	 * @phpstan-param list<SyncWorldClockMarkerData> $markers
	 */
	public function __construct(
		private int $id,
		private string $name,
		private int $time,
		private bool $paused,
		private array $markers
	){}

	public function getId() : int{ return $this->id; }

	public function getName() : string{ return $this->name; }

	public function getTime() : int{ return $this->time; }

	public function isPaused() : bool{ return $this->paused; }

	/**
	 * @return SyncWorldClockMarkerData[]
	 * @phpstan-return list<SyncWorldClockMarkerData>
	 */
	public function getMarkers() : array{ return $this->markers; }

	public static function read(PacketSerializer $in) : self{
		$id = $in->getUnsignedVarLong();
		$name = $in->getString();
		$time = $in->getVarInt();
		$paused = $in->getBool();
		$markers = [];
		for($i = 0, $len = $in->getUnsignedVarInt(); $i < $len; ++$i){
			$markers[] = SyncWorldClockMarkerData::read($in);
		}

		return new self($id, $name, $time, $paused, $markers);
	}

	public function write(PacketSerializer $out) : void{
		$out->putUnsignedVarLong($this->id);
		$out->putString($this->name);
		$out->putVarInt($this->time);
		$out->putBool($this->paused);
		$out->putUnsignedVarInt(count($this->markers));
		foreach($this->markers as $marker){
			$marker->write($out);
		}
	}
}
