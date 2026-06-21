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

namespace pocketmine\network\mcpe\protocol\types\resourcepacks;

use pocketmine\network\mcpe\protocol\ProtocolInfo;
use pocketmine\network\mcpe\protocol\serializer\PacketSerializer;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class ResourcePackInfoEntry{
	public function __construct(
		private UuidInterface $packId,
		private string $version,
		private int $sizeBytes,
		private string $encryptionKey = "",
		private string $subPackName = "",
		private string $contentId = "",
		private bool $hasScripts = false,
		private bool $isAddonPack = false,
		private bool $isRtxCapable = false,
		private string $cdnUrl = ""
	){}

	public function getPackId() : UuidInterface{
		return $this->packId;
	}

	public function getVersion() : string{
		return $this->version;
	}

	public function getSizeBytes() : int{
		return $this->sizeBytes;
	}

	public function getEncryptionKey() : string{
		return $this->encryptionKey;
	}

	public function getSubPackName() : string{
		return $this->subPackName;
	}

	public function getContentId() : string{
		return $this->contentId;
	}

	public function hasScripts() : bool{
		return $this->hasScripts;
	}

	public function isAddonPack() : bool{ return $this->isAddonPack; }

	public function isRtxCapable() : bool{ return $this->isRtxCapable; }

	public function getCdnUrl() : string{ return $this->cdnUrl; }

	public function write(PacketSerializer $out) : void{
		if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_50){
			$out->putUUID($this->packId);
		}else{
			$out->putString($this->packId->toString());
		}
		$out->putString($this->version);
		$out->putLLong($this->sizeBytes);
		$out->putString($this->encryptionKey);
		$out->putString($this->subPackName);
		$out->putString($this->contentId);
		$out->putBool($this->hasScripts);
		if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_20){
			$out->putBool($this->isAddonPack);
		}
		if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_16_200){
			$out->putBool($this->isRtxCapable);
		}
		if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_40){
			$out->putString($this->cdnUrl);
		}
	}

	public static function read(PacketSerializer $in) : self{
		if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_50){
			$uuid = $in->getUUID();
		}else{
			$uuid = Uuid::fromString($in->getString());
		}
		$version = $in->getString();
		$sizeBytes = $in->getLLong();
		$encryptionKey = $in->getString();
		$subPackName = $in->getString();
		$contentId = $in->getString();
		$hasScripts = $in->getBool();
		if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_20){
			$isAddonPack = $in->getBool();
		}
		if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_16_200){
			$rtxCapable = $in->getBool();
		}
		if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_40){
			$cdnUrl = $in->getString();
		}
		return new self($uuid, $version, $sizeBytes, $encryptionKey, $subPackName, $contentId, $hasScripts, $isAddonPack ?? false, $rtxCapable ?? false, $cdnUrl ?? "");
	}
}
