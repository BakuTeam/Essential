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

namespace pocketmine\network\mcpe\protocol;

use pocketmine\network\mcpe\protocol\serializer\PacketSerializer;
use function count;

class ResourcePackClientResponsePacket extends DataPacket implements ServerboundPacket{
	public const NETWORK_ID = ProtocolInfo::RESOURCE_PACK_CLIENT_RESPONSE_PACKET;

	public const STATUS_REFUSED = 1;
	public const STATUS_SEND_PACKS = 2;
	public const STATUS_HAVE_ALL_PACKS = 3;
	public const STATUS_COMPLETED = 4;

	/**
	 * Since 1.26.40 the status is followed by a string discriminator matching the numeric status.
	 * Keyed by the internal status constant above.
	 */
	private const INNER_TYPES = [
		self::STATUS_REFUSED => "cancel",
		self::STATUS_SEND_PACKS => "downloading",
		self::STATUS_HAVE_ALL_PACKS => "downloadingfinished",
		self::STATUS_COMPLETED => "resourcepackstackfinished",
	];

	public int $status;
	/** @var string[] */
	public array $packIds = [];

	/**
	 * @generate-create-func
	 * @param string[] $packIds
	 */
	public static function create(int $status, array $packIds) : self{
		$result = new self();
		$result->status = $status;
		$result->packIds = $packIds;
		return $result;
	}

	protected function decodePayload(PacketSerializer $in) : void{
		if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_40){
			//1.26.40: status is a var-int (0-based), followed by a matching string discriminator; pack IDs are
			//only present when the client is requesting packs to be sent.
			$this->status = $in->getUnsignedVarInt() + 1;
			$innerType = $in->getString();
			$expectedInnerType = self::INNER_TYPES[$this->status] ?? "unknown";
			if($innerType !== $expectedInnerType){
				throw new PacketDecodeException("Unexpected inner type $innerType for resource pack client response status $this->status, expected $expectedInnerType");
			}
			$this->packIds = [];
			if($this->status === self::STATUS_SEND_PACKS){
				for($i = 0, $count = $in->getUnsignedVarInt(); $i < $count; ++$i){
					$this->packIds[] = $in->getString();
				}
			}
		}else{
			$this->status = $in->getByte();
			$entryCount = $in->getLShort();
			$this->packIds = [];
			while($entryCount-- > 0){
				$this->packIds[] = $in->getString();
			}
		}
	}

	protected function encodePayload(PacketSerializer $out) : void{
		if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_40){
			if(!isset(self::INNER_TYPES[$this->status])){
				throw new \LogicException("Unknown resource pack client response status $this->status");
			}
			$out->putUnsignedVarInt($this->status - 1);
			$out->putString(self::INNER_TYPES[$this->status]);
			if($this->status === self::STATUS_SEND_PACKS){
				$out->putUnsignedVarInt(count($this->packIds));
				foreach($this->packIds as $id){
					$out->putString($id);
				}
			}
		}else{
			$out->putByte($this->status);
			$out->putLShort(count($this->packIds));
			foreach($this->packIds as $id){
				$out->putString($id);
			}
		}
	}

	public function handle(PacketHandlerInterface $handler) : bool{
		return $handler->handleResourcePackClientResponse($this);
	}
}
