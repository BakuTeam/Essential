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
