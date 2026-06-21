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

final class SyncWorldClocksSyncState extends SyncWorldClocksPayload{
	public const ID = SyncWorldClocksType::SYNC_STATE;

	/**
	 * @param SyncWorldClockStateData[] $clockData
	 * @phpstan-param list<SyncWorldClockStateData> $clockData
	 */
	public function __construct(private array $clockData){}

	public function getTypeId() : int{ return self::ID; }

	/**
	 * @return SyncWorldClockStateData[]
	 * @phpstan-return list<SyncWorldClockStateData>
	 */
	public function getClockData() : array{ return $this->clockData; }

	public static function read(PacketSerializer $in) : self{
		$clockData = [];
		for($i = 0, $len = $in->getUnsignedVarInt(); $i < $len; ++$i){
			$clockData[] = SyncWorldClockStateData::read($in);
		}

		return new self($clockData);
	}

	public function write(PacketSerializer $out) : void{
		$out->putUnsignedVarInt(count($this->clockData));
		foreach($this->clockData as $data){
			$data->write($out);
		}
	}
}
