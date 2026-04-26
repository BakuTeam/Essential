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

use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\network\mcpe\protocol\serializer\PacketSerializer;
use pocketmine\network\mcpe\protocol\types\CacheableNbt;
use pocketmine\network\mcpe\protocol\types\camera\CameraPreset;
use function array_map;
use function count;

class CameraPresetsPacket extends DataPacket implements ClientboundPacket{
	public const NETWORK_ID = ProtocolInfo::CAMERA_PRESETS_PACKET;

	/** @var CameraPreset[] */
	private array $presets;

	/**
	 * @generate-create-func
	 * @param CameraPreset[] $presets
	 */
	public static function create(array $presets) : self{
		$result = new self;
		$result->presets = $presets;
		return $result;
	}

	/**
	 * @return CameraPreset[]
	 */
	public function getPresets() : array{ return $this->presets; }

	protected function decodePayload(PacketSerializer $in) : void{
		if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_20_30){
			$this->presets = [];
			for($i = 0, $count = $in->getUnsignedVarInt(); $i < $count; $i++){
				$this->presets[] = CameraPreset::read($in);
			}
		}else{
			$this->fromNBT($in->getNbtCompoundRoot());
		}
	}

	protected function fromNBT(CompoundTag $nbt) : void{
		$this->presets = [];

		$presents = $nbt->getListTag("presets") ?? throw new \InvalidArgumentException("Missing presets tag");
		foreach($presents as $presetTag){
			if(!$presetTag instanceof CompoundTag){
				throw new \InvalidArgumentException("Expected CompoundTag, got " . $presetTag->getType());
			}

			$this->presets[] = CameraPreset::fromNBT($presetTag);
		}
	}

	protected function encodePayload(PacketSerializer $out) : void{
		if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_20_30){
			$out->putUnsignedVarInt(count($this->presets));
			foreach($this->presets as $preset){
				$preset->write($out);
			}
		}else{
			$data = new CacheableNbt($this->toNBT($out->getProtocolId()));
			$out->put($data->getEncodedNbt());
		}
	}

	protected function toNBT(int $protocolId) : CompoundTag{
		return CompoundTag::create()->setTag("presets", new ListTag(array_map(fn(CameraPreset $preset) => $preset->toNBT($protocolId), $this->presets)));
	}

	public function handle(PacketHandlerInterface $handler) : bool{
		return $handler->handleCameraPresets($this);
	}
}
