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
use pocketmine\network\mcpe\protocol\types\inventory\FullContainerName;
use function count;

class ContainerRegistryCleanupPacket extends DataPacket implements ClientboundPacket{
	public const NETWORK_ID = ProtocolInfo::CONTAINER_REGISTRY_CLEANUP_PACKET;

	/** @var FullContainerName[] */
	private array $removedContainers;

	/**
	 * @generate-create-func
	 * @param FullContainerName[] $removedContainers
	 */
	public static function create(array $removedContainers) : self{
		$result = new self();
		$result->removedContainers = $removedContainers;
		return $result;
	}

	/**
	 * @return FullContainerName[]
	 */
	public function getRemovedContainers() : array{ return $this->removedContainers; }

	protected function decodePayload(PacketSerializer $in) : void{
		$this->removedContainers = [];
		for($i = 0, $len = $in->getUnsignedVarInt(); $i < $len; ++$i){
			$this->removedContainers[] = FullContainerName::read($in);
		}
	}

	protected function encodePayload(PacketSerializer $out) : void{
		$out->putUnsignedVarInt(count($this->removedContainers));
		foreach($this->removedContainers as $container){
			$container->write($out);
		}
	}

	public function handle(PacketHandlerInterface $handler) : bool{
		return $handler->handleContainerRegistryCleanup($this);
	}
}
