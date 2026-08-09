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
use pocketmine\network\mcpe\protocol\types\MapImage;
use pocketmine\network\mcpe\protocol\types\MapInfoRequestPacketClientPixel;
use function count;

class MapInfoRequestPacket extends DataPacket implements ServerboundPacket{
	public const NETWORK_ID = ProtocolInfo::MAP_INFO_REQUEST_PACKET;

	public int $mapId;
	/** @var MapInfoRequestPacketClientPixel[] */
	public array $clientPixels = [];

	/**
	 * @generate-create-func
	 * @param MapInfoRequestPacketClientPixel[] $clientPixels
	 */
	public static function create(int $mapId, array $clientPixels) : self{
		$result = new self();
		$result->mapId = $mapId;
		$result->clientPixels = $clientPixels;
		return $result;
	}

	protected function decodePayload(PacketSerializer $in) : void{
		$this->mapId = $in->getActorUniqueId();

		if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_19_20){
			$this->clientPixels = [];
			$count = $in->getLInt();
			if($count > MapImage::MAX_HEIGHT * MapImage::MAX_WIDTH){
				throw new PacketDecodeException("Too many pixels");
			}
			for($i = 0; $i < $count; $i++){
				$this->clientPixels[] = MapInfoRequestPacketClientPixel::read($in);
			}
		}
	}

	protected function encodePayload(PacketSerializer $out) : void{
		$out->putActorUniqueId($this->mapId);

		if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_19_20){
			$out->putLInt(count($this->clientPixels));
			foreach($this->clientPixels as $pixel){
				$pixel->write($out);
			}
		}
	}

	public function handle(PacketHandlerInterface $handler) : bool{
		return $handler->handleMapInfoRequest($this);
	}
}
