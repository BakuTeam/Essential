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

namespace pocketmine\network\mcpe\protocol\types;

use pocketmine\network\mcpe\protocol\ProtocolInfo;
use pocketmine\network\mcpe\protocol\serializer\PacketSerializer;

final class Enchant{
	public function __construct(
		private int $id,
		private int $level
	){}

	public function getId() : int{ return $this->id; }

	public function getLevel() : int{ return $this->level; }

	public static function read(PacketSerializer $in, ?int $protocolId = null) : self{
		$protocolId ??= $in->getProtocolId();
		$id = $protocolId >= ProtocolInfo::PROTOCOL_1_26_20 ? $in->getUnsignedVarInt() : $in->getByte();
		$level = $in->getByte();
		return new self($id, $level);
	}

	public function write(PacketSerializer $out, ?int $protocolId = null) : void{
		$protocolId ??= $out->getProtocolId();
		if($protocolId >= ProtocolInfo::PROTOCOL_1_26_20){
			$out->putUnsignedVarInt($this->id);
		}else{
			$out->putByte($this->id);
		}
		$out->putByte($this->level);
	}
}
