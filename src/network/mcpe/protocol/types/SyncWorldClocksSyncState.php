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
