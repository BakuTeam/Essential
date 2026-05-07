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

use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\serializer\PacketSerializer;
use pocketmine\network\mcpe\protocol\types\GraphicsOverrideParameterType;
use pocketmine\network\mcpe\protocol\types\ParameterKeyframeValue;
use function count;

class GraphicsOverrideParameterPacket extends DataPacket implements ClientboundPacket{
	public const NETWORK_ID = ProtocolInfo::GRAPHICS_OVERRIDE_PARAMETER_PACKET;

	/** @var ParameterKeyframeValue[] */
	private array $values = [];
	private ?float $unknownFloat;
	private ?Vector3 $unknownVector3;
	private string $biomeIdentifier;
	private GraphicsOverrideParameterType $parameterType;
	private bool $reset;

	/**
	 * @generate-create-func
	 * @param ParameterKeyframeValue[] $values
	 */
	public static function create(array $values, string $biomeIdentifier, GraphicsOverrideParameterType $parameterType, bool $reset, ?float $unknownFloat = null, ?Vector3 $unknownVector3 = null) : self{
		$result = new self;
		$result->values = $values;
		$result->unknownFloat = $unknownFloat;
		$result->unknownVector3 = $unknownVector3;
		$result->biomeIdentifier = $biomeIdentifier;
		$result->parameterType = $parameterType;
		$result->reset = $reset;
		return $result;
	}

	/**
	 * @return ParameterKeyframeValue[]
	 */
	public function getValues() : array{ return $this->values; }

	public function getUnknownFloat() : ?float{ return $this->unknownFloat; }

	public function getUnknownVector3() : ?Vector3{ return $this->unknownVector3; }

	public function getBiomeIdentifier() : string{ return $this->biomeIdentifier; }

	public function getParameterType() : GraphicsOverrideParameterType{ return $this->parameterType; }

	public function isReset() : bool{ return $this->reset; }

	protected function decodePayload(PacketSerializer $in) : void{
		$count = $in->getUnsignedVarInt();
		for($i = 0; $i < $count; ++$i){
			$this->values[] = ParameterKeyframeValue::read($in);
		}
		if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_0){
			$this->unknownFloat = $in->readOptional(fn() => $in->getLFloat());
			$this->unknownVector3 = $in->readOptional(fn() => $in->getVector3());
		}else{
			$this->unknownFloat = null;
			$this->unknownVector3 = null;
		}
		$this->biomeIdentifier = $in->getString();
		$this->parameterType = GraphicsOverrideParameterType::fromPacket($in->getByte());
		$this->reset = $in->getBool();
	}

	protected function encodePayload(PacketSerializer $out) : void{
		$out->putUnsignedVarInt(count($this->values));
		foreach($this->values as $value){
			$value->write($out);
		}
		if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_0){
			$out->writeOptional($this->unknownFloat, fn(float $v) => $out->putLFloat($v));
			$out->writeOptional($this->unknownVector3, fn(Vector3 $v) => $out->putVector3($v));
		}
		$out->putString($this->biomeIdentifier);
		$out->putByte($this->parameterType->value);
		$out->putBool($this->reset);
	}

	public function handle(PacketHandlerInterface $handler) : bool{
		return $handler->handleGraphicsOverrideParameter($this);
	}
}
