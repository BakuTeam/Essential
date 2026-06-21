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
use pocketmine\network\mcpe\protocol\types\BoolPackSetting;
use pocketmine\network\mcpe\protocol\types\FloatPackSetting;
use pocketmine\network\mcpe\protocol\types\PackSetting;
use pocketmine\network\mcpe\protocol\types\PackSettingType;
use pocketmine\network\mcpe\protocol\types\StringPackSetting;
use Ramsey\Uuid\UuidInterface;

class ServerboundPackSettingChangePacket extends DataPacket implements ServerboundPacket{
	public const NETWORK_ID = ProtocolInfo::SERVERBOUND_PACK_SETTING_CHANGE_PACKET;

	private UuidInterface $packId;
	private PackSetting $packSetting;

	/**
	 * @generate-create-func
	 */
	public static function create(UuidInterface $packId, PackSetting $packSetting) : self{
		$result = new self();
		$result->packId = $packId;
		$result->packSetting = $packSetting;
		return $result;
	}

	public function getPackId() : UuidInterface{ return $this->packId; }

	public function getPackSetting() : PackSetting{ return $this->packSetting; }

	protected function decodePayload(PacketSerializer $in) : void{
		$this->packId = $in->getUUID();

		$name = $in->getString();
		$typeId = PackSettingType::fromPacket($in->getUnsignedVarInt());
		$this->packSetting = match($typeId){
			PackSettingType::FLOAT => FloatPackSetting::read($in, $name),
			PackSettingType::BOOL => BoolPackSetting::read($in, $name),
			PackSettingType::STRING => StringPackSetting::read($in, $name),
		};
	}

	protected function encodePayload(PacketSerializer $out) : void{
		$out->putUUID($this->packId);
		$out->putString($this->packSetting->getName());
		$out->putUnsignedVarInt($this->packSetting->getTypeId()->value);
		$this->packSetting->write($out);
	}

	public function handle(PacketHandlerInterface $handler) : bool{
		return $handler->handleServerboundPackSettingChange($this);
	}
}
