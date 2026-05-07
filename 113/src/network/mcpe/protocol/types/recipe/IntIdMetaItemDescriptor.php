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

namespace pocketmine\network\mcpe\protocol\types\recipe;

use pocketmine\network\mcpe\protocol\ProtocolInfo;
use pocketmine\network\mcpe\protocol\serializer\PacketSerializer;
use pocketmine\network\mcpe\protocol\types\GetTypeIdFromConstTrait;

final class IntIdMetaItemDescriptor implements ItemDescriptor{
	use GetTypeIdFromConstTrait;

	public const ID = ItemDescriptorType::INT_ID_META;

	public function __construct(
		private int $id,
		private int $meta
	){
		if($id === 0 && $meta !== 0){
			throw new \InvalidArgumentException("Meta cannot be non-zero for air");
		}
	}

	public function getId() : int{ return $this->id; }

	public function getMeta() : int{ return $this->meta; }

	public static function read(PacketSerializer $in) : self{
		$newFormat = $in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_19_30;

		$id = $newFormat ? $in->getSignedLShort() : $in->getVarInt();
		if($id !== 0){
			$meta = $newFormat ? $in->getSignedLShort() : $in->getVarInt();
		}else{
			$meta = 0;
		}

		return new self($id, $meta);
	}

	public function write(PacketSerializer $out) : void{
		$newFormat = $out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_19_30;

		$newFormat ? $out->putLShort($this->id) : $out->putVarInt($this->id);
		if($this->id !== 0){
			$newFormat ? $out->putLShort($this->meta) : $out->putVarInt($this->meta);
		}
	}
}
