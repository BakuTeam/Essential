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
use pocketmine\network\mcpe\protocol\types\resourcepacks\BehaviorPackInfoEntry;
use pocketmine\network\mcpe\protocol\types\resourcepacks\ResourcePackInfoEntry;
use Ramsey\Uuid\UuidInterface;
use function count;

class ResourcePacksInfoPacket extends DataPacket implements ClientboundPacket{
	public const NETWORK_ID = ProtocolInfo::RESOURCE_PACKS_INFO_PACKET;

	/** @var ResourcePackInfoEntry[] */
	public array $resourcePackEntries = [];
	/** @var BehaviorPackInfoEntry[] */
	public array $behaviorPackEntries = [];
	public bool $mustAccept = false; //if true, forces client to choose between accepting packs or being disconnected
	public bool $hasAddons = false;
	public bool $hasScripts = false; //if true, causes disconnect for any platform that doesn't support scripts yet
	public bool $forceServerPacks = false;
	/**
	 * @var string[]
	 * @phpstan-var array<string, string>
	 */
	public array $cdnUrls = [];
	private UuidInterface $worldTemplateId;
	private string $worldTemplateVersion;
	private bool $forceDisableVibrantVisuals;

	/**
	 * @generate-create-func
	 * @param ResourcePackInfoEntry[] $resourcePackEntries
	 * @param BehaviorPackInfoEntry[] $behaviorPackEntries
	 * @param string[]                $cdnUrls
	 * @phpstan-param array<string, string> $cdnUrls
	 */
	public static function create(
		array $resourcePackEntries,
		array $behaviorPackEntries,
		bool $mustAccept,
		bool $hasAddons,
		bool $hasScripts,
		bool $forceServerPacks,
		array $cdnUrls,
		UuidInterface $worldTemplateId,
		string $worldTemplateVersion,
		bool $forceDisableVibrantVisuals,
	) : self{
		$result = new self;
		$result->resourcePackEntries = $resourcePackEntries;
		$result->behaviorPackEntries = $behaviorPackEntries;
		$result->mustAccept = $mustAccept;
		$result->hasAddons = $hasAddons;
		$result->hasScripts = $hasScripts;
		$result->forceServerPacks = $forceServerPacks;
		$result->cdnUrls = $cdnUrls;
		$result->worldTemplateId = $worldTemplateId;
		$result->worldTemplateVersion = $worldTemplateVersion;
		$result->forceDisableVibrantVisuals = $forceDisableVibrantVisuals;
		return $result;
	}

	public function getWorldTemplateId() : UuidInterface{ return $this->worldTemplateId; }

	public function getWorldTemplateVersion() : string{ return $this->worldTemplateVersion; }

	public function isForceDisablingVibrantVisuals() : bool{ return $this->forceDisableVibrantVisuals; }

	protected function decodePayload(PacketSerializer $in) : void{
		$this->mustAccept = $in->getBool();
		if($in->getProtocolId() <= ProtocolInfo::PROTOCOL_1_1_5){
			$behaviorPackCount = $in->getLShort();
			while($behaviorPackCount-- > 0){
				$this->behaviorPackEntries[] = BehaviorPackInfoEntry::read($in);
			}

			$resourcePackCount = $in->getLShort();
			while($resourcePackCount-- > 0){
				$this->resourcePackEntries[] = ResourcePackInfoEntry::read($in);
			}
			return;
		}

		if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_20_70){
			$this->hasAddons = $in->getBool();
		}
		$this->hasScripts = $in->getBool();
		if($in->getProtocolId() <= ProtocolInfo::PROTOCOL_1_21_20){
			if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_17_10){
				$this->forceServerPacks = $in->getBool();
			}
			$behaviorPackCount = $in->getLShort();
			while($behaviorPackCount-- > 0){
				$this->behaviorPackEntries[] = BehaviorPackInfoEntry::read($in);
			}
		}
		if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_50){
			if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_90){
				$this->forceDisableVibrantVisuals = $in->getBool();
			}
			$this->worldTemplateId = $in->getUUID();
			$this->worldTemplateVersion = $in->getString();
		}

		$resourcePackCount = $in->getLShort();
		while($resourcePackCount-- > 0){
			$this->resourcePackEntries[] = ResourcePackInfoEntry::read($in);
		}

		if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_20_30 && $in->getProtocolId() < ProtocolInfo::PROTOCOL_1_21_40){
			$this->cdnUrls = [];
			for($i = 0, $count = $in->getUnsignedVarInt(); $i < $count; $i++){
				$packId = $in->getString();
				$cdnUrl = $in->getString();
				$this->cdnUrls[$packId] = $cdnUrl;
			}
		}
	}

	protected function encodePayload(PacketSerializer $out) : void{
		$out->putBool($this->mustAccept);
		if($out->getProtocolId() <= ProtocolInfo::PROTOCOL_1_1_5){
			$out->putLShort(count($this->behaviorPackEntries));
			foreach($this->behaviorPackEntries as $entry){
				$entry->write($out);
			}

			$out->putLShort(count($this->resourcePackEntries));
			foreach($this->resourcePackEntries as $entry){
				$entry->write($out);
			}
			return;
		}

		if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_20_70){
			$out->putBool($this->hasAddons);
		}
		$out->putBool($this->hasScripts);
		if($out->getProtocolId() <= ProtocolInfo::PROTOCOL_1_21_20){
			if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_17_10){
				$out->putBool($this->forceServerPacks);
			}
			$out->putLShort(count($this->behaviorPackEntries));
			foreach($this->behaviorPackEntries as $entry){
				$entry->write($out);
			}
		}
		if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_50){
			if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_90){
				$out->putBool($this->forceDisableVibrantVisuals);
			}
			$out->putUUID($this->worldTemplateId);
			$out->putString($this->worldTemplateVersion);
		}
		$out->putLShort(count($this->resourcePackEntries));
		foreach($this->resourcePackEntries as $entry){
			$entry->write($out);
		}
		if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_20_30 && $out->getProtocolId() < ProtocolInfo::PROTOCOL_1_21_40){
			$out->putUnsignedVarInt(count($this->cdnUrls));
			foreach($this->cdnUrls as $packId => $cdnUrl){
				$out->putString((string)$packId);
				$out->putString($cdnUrl);
			}
		}
	}

	public function handle(PacketHandlerInterface $handler) : bool{
		return $handler->handleResourcePacksInfo($this);
	}
}
