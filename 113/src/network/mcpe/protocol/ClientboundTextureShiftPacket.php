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
use pocketmine\network\mcpe\protocol\types\TextureShiftAction;
use function count;

class ClientboundTextureShiftPacket extends DataPacket implements ClientboundPacket{
	public const NETWORK_ID = ProtocolInfo::CLIENTBOUND_TEXTURE_SHIFT_PACKET;

	/** @see TextureShiftAction */
	private int $actionId;
	private string $collectionName;
	private string $fromStep;
	private string $toStep;
	/**
	 * @var string[]
	 * @phpstan-var list<string>
	 */
	private array $allSteps;
	private int $currentLengthTicks;
	private int $totalLengthTicks;
	private bool $enabled;

	/**
	 * @generate-create-func
	 * @param string[] $allSteps
	 * @phpstan-param list<string> $allSteps
	 */
	public static function create(
		int $actionId,
		string $collectionName,
		string $fromStep,
		string $toStep,
		array $allSteps,
		int $currentLengthTicks,
		int $totalLengthTicks,
		bool $enabled
	) : self{
		$result = new self;
		$result->actionId = $actionId;
		$result->collectionName = $collectionName;
		$result->fromStep = $fromStep;
		$result->toStep = $toStep;
		$result->allSteps = $allSteps;
		$result->currentLengthTicks = $currentLengthTicks;
		$result->totalLengthTicks = $totalLengthTicks;
		$result->enabled = $enabled;
		return $result;
	}

	public function getActionId() : int{ return $this->actionId; }

	public function getCollectionName() : string{ return $this->collectionName; }

	public function getFromStep() : string{ return $this->fromStep; }

	public function getToStep() : string{ return $this->toStep; }

	/**
	 * @return string[]
	 * @phpstan-return list<string>
	 */
	public function getAllSteps() : array{ return $this->allSteps; }

	public function getCurrentLengthTicks() : int{ return $this->currentLengthTicks; }

	public function getTotalLengthTicks() : int{ return $this->totalLengthTicks; }

	public function isEnabled() : bool{ return $this->enabled; }

	protected function decodePayload(PacketSerializer $in) : void{
		$this->actionId = $in->getByte();
		$this->collectionName = $in->getString();
		$this->fromStep = $in->getString();
		$this->toStep = $in->getString();
		$this->allSteps = [];
		for($i = 0, $count = $in->getUnsignedVarInt(); $i < $count; ++$i){
			$this->allSteps[] = $in->getString();
		}
		$this->currentLengthTicks = $in->getUnsignedVarLong();
		$this->totalLengthTicks = $in->getUnsignedVarLong();
		$this->enabled = $in->getBool();
	}

	protected function encodePayload(PacketSerializer $out) : void{
		$out->putByte($this->actionId);
		$out->putString($this->collectionName);
		$out->putString($this->fromStep);
		$out->putString($this->toStep);
		$out->putUnsignedVarInt(count($this->allSteps));
		foreach($this->allSteps as $step){
			$out->putString($step);
		}
		$out->putUnsignedVarLong($this->currentLengthTicks);
		$out->putUnsignedVarLong($this->totalLengthTicks);
		$out->putBool($this->enabled);
	}

	public function handle(PacketHandlerInterface $handler) : bool{
		return $handler->handleClientboundTextureShift($this);
	}
}
