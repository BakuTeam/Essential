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

namespace pocketmine\network\mcpe\protocol;

use pocketmine\network\mcpe\protocol\serializer\PacketSerializer;
use Ramsey\Uuid\UuidInterface;

class ShowStoreOfferPacket extends DataPacket implements ClientboundPacket{
	public const NETWORK_ID = ProtocolInfo::SHOW_STORE_OFFER_PACKET;

	public UuidInterface $offerId;
	public bool $showAll;

	/**
	 * @generate-create-func
	 */
	public static function create(UuidInterface $offerId, bool $showAll) : self{
		$result = new self;
		$result->offerId = $offerId;
		$result->showAll = $showAll;
		return $result;
	}

	protected function decodePayload(PacketSerializer $in) : void{
		$this->offerId = $in->getUUID();
		$this->showAll = $in->getBool();
	}

	protected function encodePayload(PacketSerializer $out) : void{
		$out->putUUID($this->offerId);
		$out->putBool($this->showAll);
	}

	public function handle(PacketHandlerInterface $handler) : bool{
		return $handler->handleShowStoreOffer($this);
	}
}
