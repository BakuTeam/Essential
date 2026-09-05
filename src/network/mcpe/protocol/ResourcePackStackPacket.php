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
use pocketmine\network\mcpe\protocol\types\Experiments;
use pocketmine\network\mcpe\protocol\types\resourcepacks\ResourcePackStackEntry;
use function count;

class ResourcePackStackPacket extends DataPacket implements ClientboundPacket{
	public const NETWORK_ID = ProtocolInfo::RESOURCE_PACK_STACK_PACKET;

	/** @var ResourcePackStackEntry[] */
	public array $resourcePackStack = [];
	/** @var ResourcePackStackEntry[] */
	public array $behaviorPackStack = [];
	public bool $mustAccept = false;
	public string $baseGameVersion = ProtocolInfo::MINECRAFT_VERSION_NETWORK;
	public Experiments $experiments;
	public bool $useVanillaEditorPacks;

	/**
	 * @generate-create-func
	 * @param ResourcePackStackEntry[] $resourcePackStack
	 * @param ResourcePackStackEntry[] $behaviorPackStack
	 */
	public static function create(array $resourcePackStack, array $behaviorPackStack, bool $mustAccept, string $baseGameVersion, Experiments $experiments, bool $useVanillaEditorPacks) : self{
		$result = new self();
		$result->resourcePackStack = $resourcePackStack;
		$result->behaviorPackStack = $behaviorPackStack;
		$result->mustAccept = $mustAccept;
		$result->baseGameVersion = $baseGameVersion;
		$result->experiments = $experiments;
		$result->useVanillaEditorPacks = $useVanillaEditorPacks;
		return $result;
	}

	protected function decodePayload(PacketSerializer $in) : void{
		$this->mustAccept = $in->getBool();
		if($in->getProtocolId() <= ProtocolInfo::PROTOCOL_1_21_124){
			$behaviorPackCount = $in->getUnsignedVarInt();
			while($behaviorPackCount-- > 0){
				$this->behaviorPackStack[] = ResourcePackStackEntry::read($in);
			}
		}

		$resourcePackCount = $in->getUnsignedVarInt();
		while($resourcePackCount-- > 0){
			$this->resourcePackStack[] = ResourcePackStackEntry::read($in);
		}

		if($in->getProtocolId() < ProtocolInfo::PROTOCOL_1_16_100){
			$this->experiments = new Experiments([], $in->getBool());
			$this->baseGameVersion = $in->getString();
			return;
		}

		$this->baseGameVersion = $in->getString();
		$this->experiments = Experiments::read($in);
		if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_20_80){
			$this->useVanillaEditorPacks = $in->getBool();
		}
	}

	protected function encodePayload(PacketSerializer $out) : void{
		$out->putBool($this->mustAccept);

		if($out->getProtocolId() <= ProtocolInfo::PROTOCOL_1_21_124){
			$out->putUnsignedVarInt(count($this->behaviorPackStack));
			foreach($this->behaviorPackStack as $entry){
				$entry->write($out);
			}
		}

		$out->putUnsignedVarInt(count($this->resourcePackStack));
		foreach($this->resourcePackStack as $entry){
			$entry->write($out);
		}

		if($out->getProtocolId() < ProtocolInfo::PROTOCOL_1_16_100){
			$out->putBool(count($this->experiments->getExperiments()) > 0);
			$out->putString($this->baseGameVersion);
			return;
		}

		$out->putString($this->baseGameVersion);
		$this->experiments->write($out);
		if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_20_80){
			$out->putBool($this->useVanillaEditorPacks);
		}
	}

	public function handle(PacketHandlerInterface $handler) : bool{
		return $handler->handleResourcePackStack($this);
	}
}
