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

/**
 * One of the most cursed packets to ever exist in Minecraft Bedrock Edition.
 *
 * This packet is only sent by the server when client-side chunk generation is enabled in vanilla. It contains NBT data
 * for biomes, similar to the BiomeDefinitionListPacket, but with a large amount of extra data for client-side chunk
 * generation.
 *
 * The data is compressed with a cursed home-brewed compression format, and it's a miracle it even works.
 * Hopefully this packet gets removed before I have to implement it...
 */
class CompressedBiomeDefinitionListPacket extends DataPacket implements ClientboundPacket{
	public const NETWORK_ID = ProtocolInfo::COMPRESSED_BIOME_DEFINITION_LIST_PACKET;

	private string $payload;

	/**
	 * @generate-create-func
	 */
	public static function create(string $payload) : self{
		$result = new self();
		$result->payload = $payload;
		return $result;
	}

	public function getPayload() : string{ return $this->payload; }

	protected function decodePayload(PacketSerializer $in) : void{
		$this->payload = $in->getString();
	}

	protected function encodePayload(PacketSerializer $out) : void{
		$out->putString($this->payload);
	}

	public function handle(PacketHandlerInterface $handler) : bool{
		return $handler->handleCompressedBiomeDefinitionList($this);
	}
}
