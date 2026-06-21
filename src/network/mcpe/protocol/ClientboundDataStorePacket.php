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

namespace pocketmine\network\mcpe\protocol;

use pocketmine\network\mcpe\protocol\serializer\PacketSerializer;
use pocketmine\network\mcpe\protocol\types\DataStore;
use pocketmine\network\mcpe\protocol\types\DataStoreChange;
use pocketmine\network\mcpe\protocol\types\DataStoreRemoval;
use pocketmine\network\mcpe\protocol\types\DataStoreType;
use pocketmine\network\mcpe\protocol\types\DataStoreUpdate;
use function count;

class ClientboundDataStorePacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::CLIENTBOUND_DATA_STORE_PACKET;

	/**
	 * @var DataStore[]
	 * @phpstan-var list<DataStore>
	 */
	public array $values = [];

	/**
	 * @generate-create-func
	 * @param DataStore[] $values
	 * @phpstan-param list<DataStore> $values
	 */
	public static function create(array $values) : self{
		$result = new self();
		$result->values = $values;
		return $result;
	}

	protected function decodePayload(PacketSerializer $in) : void{
		$this->values = [];
		for($i = 0, $len = $in->getUnsignedVarInt(); $i < $len; ++$i){
			$this->values[] = match($in->getUnsignedVarInt()){
				DataStoreType::UPDATE => DataStoreUpdate::read($in),
				DataStoreType::CHANGE => DataStoreChange::read($in),
				DataStoreType::REMOVAL => DataStoreRemoval::read($in),
				default => throw new PacketDecodeException("Unknown DataStore type")
			};
		}
	}

	protected function encodePayload(PacketSerializer $out) : void{
		$out->putUnsignedVarInt(count($this->values));
		foreach($this->values as $value){
			$out->putUnsignedVarInt($value->getTypeId());
			$value->write($out);
		}
	}

	public function handle(PacketHandlerInterface $handler) : bool{
		return $handler->handleClientboundDataStore($this);
	}
}
