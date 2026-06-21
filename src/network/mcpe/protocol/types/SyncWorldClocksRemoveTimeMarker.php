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

final class SyncWorldClocksRemoveTimeMarker extends SyncWorldClocksPayload{
	public const ID = SyncWorldClocksType::REMOVE_TIME_MARKER;

	/**
	 * @param int[] $markerIds
	 * @phpstan-param list<int> $markerIds
	 */
	public function __construct(private int $clockId, private array $markerIds){}

	public function getTypeId() : int{ return self::ID; }

	public function getClockId() : int{ return $this->clockId; }

	/**
	 * @return int[]
	 * @phpstan-return list<int>
	 */
	public function getMarkerIds() : array{ return $this->markerIds; }

	public static function read(PacketSerializer $in) : self{
		$clockId = $in->getUnsignedVarLong();
		$markerIds = [];
		for($i = 0, $len = $in->getUnsignedVarInt(); $i < $len; ++$i){
			$markerIds[] = $in->getUnsignedVarLong();
		}

		return new self($clockId, $markerIds);
	}

	public function write(PacketSerializer $out) : void{
		$out->putUnsignedVarLong($this->clockId);
		$out->putUnsignedVarInt(count($this->markerIds));
		foreach($this->markerIds as $markerId){
			$out->putUnsignedVarLong($markerId);
		}
	}
}
