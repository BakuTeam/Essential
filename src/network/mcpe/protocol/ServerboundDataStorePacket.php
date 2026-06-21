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
use pocketmine\network\mcpe\protocol\types\DataStoreUpdate;

class ServerboundDataStorePacket extends DataPacket implements ServerboundPacket{
	public const NETWORK_ID = ProtocolInfo::SERVERBOUND_DATA_STORE_PACKET;

	private DataStoreUpdate $update;

	/**
	 * @generate-create-func
	 */
	public static function create(DataStoreUpdate $update) : self{
		$result = new self();
		$result->update = $update;
		return $result;
	}

	public function getUpdate() : DataStoreUpdate{ return $this->update; }

	protected function decodePayload(PacketSerializer $in) : void{
		$this->update = DataStoreUpdate::read($in);
	}

	protected function encodePayload(PacketSerializer $out) : void{
		$this->update->write($out);
	}

	public function handle(PacketHandlerInterface $handler) : bool{
		return $handler->handleServerboundDataStore($this);
	}
}
