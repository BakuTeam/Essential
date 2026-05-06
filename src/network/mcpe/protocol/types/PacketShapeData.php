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

namespace pocketmine\network\mcpe\protocol\types;

use pocketmine\color\Color;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\PacketDecodeException;
use pocketmine\network\mcpe\protocol\PrimitiveShapesPacket;
use pocketmine\network\mcpe\protocol\ProtocolInfo;
use pocketmine\network\mcpe\protocol\serializer\PacketSerializer;
use pocketmine\network\mcpe\protocol\ServerScriptDebugDrawerPacket;

/**
 * @see ServerScriptDebugDrawerPacket
 */
final class PacketShapeData{

	public function __construct(
		private int $networkId,
		private ?ScriptDebugShapeType $type,
		private ?Vector3 $location,
		private ?float $scale,
		private ?Vector3 $rotation,
		private ?float $totalTimeLeft,
		private ?Color $color,
		private ?string $text,
		private ?Vector3 $boxBound,
		private ?Vector3 $lineEndLocation,
		private ?float $arrowHeadLength,
		private ?float $arrowHeadRadius,
		private ?int $segments,
		private ?int $dimensionId = null,
		private ?int $attachedToEntityId = null,
		private ?float $maximumRenderDistance = null,
		private ?bool $useRotation = null,
		private ?Color $backgroundColor = null,
		private ?bool $depthTest = null,
		private ?bool $showBackface = null,
		private ?bool $showTextBackface = null,
	){}

	public static function remove(int $networkId) : self{
		return new self($networkId, null, null, null, null, null, null, null, null, null, null, null, null);
	}

	public static function line(int $networkId, Vector3 $location, Vector3 $lineEndLocation, ?Color $color = null) : self{
		return new self(
			networkId: $networkId,
			type: ScriptDebugShapeType::LINE,
			location: $location,
			scale: null,
			rotation: null,
			totalTimeLeft: null,
			color: $color,
			text: null,
			boxBound: null,
			lineEndLocation: $lineEndLocation,
			arrowHeadLength: null,
			arrowHeadRadius: null,
			segments: null
		);
	}

	public static function box(int $networkId, Vector3 $location, Vector3 $boxBound, ?float $scale = null, ?Color $color = null) : self{
		return new self(
			networkId: $networkId,
			type: ScriptDebugShapeType::BOX,
			location: $location,
			scale: $scale,
			rotation: null,
			totalTimeLeft: null,
			color: $color,
			text: null,
			boxBound: $boxBound,
			lineEndLocation: null,
			arrowHeadLength: null,
			arrowHeadRadius: null,
			segments: null
		);
	}

	public static function sphere(int $networkId, Vector3 $location, ?float $scale = null, ?Color $color = null, ?int $segments = null) : self{
		return new self(
			networkId: $networkId,
			type: ScriptDebugShapeType::SPHERE,
			location: $location,
			scale: $scale,
			rotation: null,
			totalTimeLeft: null,
			color: $color,
			text: null,
			boxBound: null,
			lineEndLocation: null,
			arrowHeadLength: null,
			arrowHeadRadius: null,
			segments: $segments
		);
	}

	public static function circle(int $networkId, Vector3 $location, ?float $scale = null, ?Color $color = null, ?int $segments = null) : self{
		return new self(
			networkId: $networkId,
			type: ScriptDebugShapeType::CIRCLE,
			location: $location,
			scale: $scale,
			rotation: null,
			totalTimeLeft: null,
			color: $color,
			text: null,
			boxBound: null,
			lineEndLocation: null,
			arrowHeadLength: null,
			arrowHeadRadius: null,
			segments: $segments
		);
	}

	public static function text(int $networkId, Vector3 $location, string $text, ?Color $color = null) : self{
		return new self(
			networkId: $networkId,
			type: ScriptDebugShapeType::TEXT,
			location: $location,
			scale: null,
			rotation: null,
			totalTimeLeft: null,
			color: $color,
			text: $text,
			boxBound: null,
			lineEndLocation: null,
			arrowHeadLength: null,
			arrowHeadRadius: null,
			segments: null
		);
	}

	public static function arrow(int $networkId, Vector3 $location, Vector3 $lineEndLocation, ?float $scale = null, ?Color $color = null, ?float $arrowHeadLength = null, ?float $arrowHeadRadius = null, ?int $segments = null) : self{
		return new self(
			networkId: $networkId,
			type: ScriptDebugShapeType::ARROW,
			location: $location,
			scale: $scale,
			rotation: null,
			totalTimeLeft: null,
			color: $color,
			text: null,
			boxBound: null,
			lineEndLocation: $lineEndLocation,
			arrowHeadLength: $arrowHeadLength,
			arrowHeadRadius: $arrowHeadRadius,
			segments: $segments
		);
	}

	public function getNetworkId() : int{ return $this->networkId; }

	public function getType() : ?ScriptDebugShapeType{ return $this->type; }

	public function getLocation() : ?Vector3{ return $this->location; }

	public function getScale() : ?float{ return $this->scale; }

	public function getRotation() : ?Vector3{ return $this->rotation; }

	public function getTotalTimeLeft() : ?float{ return $this->totalTimeLeft; }

	public function getMaximumRenderDistance() : ?float{ return $this->maximumRenderDistance; }

	public function getColor() : ?Color{ return $this->color; }

	public function getDimensionId() : ?int{ return $this->dimensionId; }

	public function getText() : ?string{ return $this->text; }

	public function getUseRotation() : ?bool{ return $this->useRotation; }

	public function getBackgroundColor() : ?Color{ return $this->backgroundColor; }

	public function getDepthTest() : ?bool{ return $this->depthTest; }

	public function getShowBackface() : ?bool{ return $this->showBackface; }

	public function getShowTextBackface() : ?bool{ return $this->showTextBackface; }

	public function getBoxBound() : ?Vector3{ return $this->boxBound; }

	public function getLineEndLocation() : ?Vector3{ return $this->lineEndLocation; }

	public function getArrowHeadLength() : ?float{ return $this->arrowHeadLength; }

	public function getArrowHeadRadius() : ?float{ return $this->arrowHeadRadius; }

	public function getSegments() : ?int{ return $this->segments; }

	public function getAttachedToEntityId() : ?int{ return $this->attachedToEntityId; }

	public static function read(PacketSerializer $in) : self{
		$networkId = $in->getUnsignedVarLong();
		$type = $in->readOptional(fn() => ScriptDebugShapeType::fromPacket($in->getByte()));
		$location = $in->readOptional($in->getVector3(...));
		$scale = $in->readOptional($in->getLFloat(...));
		$rotation = $in->readOptional($in->getVector3(...));
		$totalTimeLeft = $in->readOptional($in->getLFloat(...));
		$maximumRenderDistance = $in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_20 ? $in->readOptional($in->getLFloat(...)) : null;
		$color = $in->readOptional(fn() => Color::fromARGB($in->getLInt()));
		$text = null;
		$useRotation = null;
		$backgroundColor = null;
		$depthTest = null;
		$showBackface = null;
		$showTextBackface = null;
		$boxBound = null;
		$lineEndLocation = null;
		$arrowHeadLength = null;
		$arrowHeadRadius = null;
		$segments = null;
		$dimensionId = null;
		$attachedToEntityId = null;

		if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_120){
			if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_0){
				$dimensionId = $in->readOptional(fn() => $in->getVarInt());
				$attachedToEntityId = $in->readOptional(fn() => $in->getActorRuntimeId());
			}else{
				$dimensionId = $in->getVarInt();
			}
			$payloadType = $in->getUnsignedVarInt();
			if(
				($type !== null && $payloadType !== $type->getPayloadType() && $payloadType !== ScriptDebugShapeType::PAYLOAD_TYPE_NONE) ||
				($type === null && $payloadType !== ScriptDebugShapeType::PAYLOAD_TYPE_NONE)
			){
				throw new PacketDecodeException("Unexpected payload type $payloadType for provided shape type " . ($type->name ?? "(not set)"));
			}
			switch($payloadType){
				case ScriptDebugShapeType::PAYLOAD_TYPE_ARROW:
					$lineEndLocation = $in->readOptional($in->getVector3(...));
					$arrowHeadLength = $in->readOptional($in->getLFloat(...));
					$arrowHeadRadius = $in->readOptional($in->getLFloat(...));
					$segments = $in->readOptional($in->getByte(...));
					break;
				case ScriptDebugShapeType::PAYLOAD_TYPE_TEXT:
					$text = $in->getString();
					if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_20){
						$useRotation = $in->getBool();
						$backgroundColor = $in->readOptional(fn() => Color::fromARGB($in->getLInt()));
						$depthTest = $in->getBool();
						$showBackface = $in->getBool();
						$showTextBackface = $in->getBool();
					}
					break;
				case ScriptDebugShapeType::PAYLOAD_TYPE_BOX:
					$boxBound = $in->getVector3();
					break;
				case ScriptDebugShapeType::PAYLOAD_TYPE_LINE:
					$lineEndLocation = $in->getVector3();
					break;
				case ScriptDebugShapeType::PAYLOAD_TYPE_CIRCLE_OR_SPHERE:
					$segments = $in->getByte();
					break;
				case ScriptDebugShapeType::PAYLOAD_TYPE_NONE:
					break;
				default:
					throw new PacketDecodeException("Unexpected payload type $payloadType");
			}
		}else{
			$text = $in->readOptional($in->getString(...));
			$boxBound = $in->readOptional($in->getVector3(...));
			$lineEndLocation = $in->readOptional($in->getVector3(...));
			$arrowHeadLength = $in->readOptional($in->getLFloat(...));
			$arrowHeadRadius = $in->readOptional($in->getLFloat(...));
			$segments = $in->readOptional($in->getByte(...));
		}

		return new self(
			$networkId,
			$type,
			$location,
			$scale,
			$rotation,
			$totalTimeLeft,
			$color,
			$text,
			$boxBound,
			$lineEndLocation,
			$arrowHeadLength,
			$arrowHeadRadius,
			$segments,
			$dimensionId,
			$attachedToEntityId,
			$maximumRenderDistance,
			$useRotation,
			$backgroundColor,
			$depthTest,
			$showBackface,
			$showTextBackface
		);
	}

	public function write(PacketSerializer $out) : void{
		$out->putUnsignedVarLong($this->networkId);
		$out->writeOptional($this->type, fn(ScriptDebugShapeType $type) => $out->putByte($type->value));
		$out->writeOptional($this->location, $out->putVector3(...));
		$out->writeOptional($this->scale, $out->putLFloat(...));
		$out->writeOptional($this->rotation, $out->putVector3(...));
		$out->writeOptional($this->totalTimeLeft, $out->putLFloat(...));
		if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_20){
			$out->writeOptional($this->maximumRenderDistance, $out->putLFloat(...));
		}
		$out->writeOptional($this->color, fn(Color $color) => $out->putLInt($color->toARGB()));
		if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_120){
			if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_0){
				$out->writeOptional($this->dimensionId, fn(int $v) => $out->putVarInt($v));
				$out->writeOptional($this->attachedToEntityId, fn(int $v) => $out->putActorRuntimeId($v));
			}else{
				$out->putVarInt($this->dimensionId ?? DimensionIds::OVERWORLD);
			}
			if($this->type === null){
				$out->putUnsignedVarInt(ScriptDebugShapeType::PAYLOAD_TYPE_NONE);
			}else{
				switch($this->type){
					case ScriptDebugShapeType::ARROW:
						$out->putUnsignedVarInt($this->type->getPayloadType());
						$out->writeOptional($this->lineEndLocation, $out->putVector3(...));
						$out->writeOptional($this->arrowHeadLength, $out->putLFloat(...));
						$out->writeOptional($this->arrowHeadRadius, $out->putLFloat(...));
						$out->writeOptional($this->segments, $out->putByte(...));
						break;
					case ScriptDebugShapeType::TEXT:
						if($this->text !== null){
							$out->putUnsignedVarInt($this->type->getPayloadType());
							$out->putString($this->text);
							if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_20){
								$out->putBool($this->useRotation ?? false);
								$out->writeOptional($this->backgroundColor, fn(Color $color) => $out->putLInt($color->toARGB()));
								$out->putBool($this->depthTest ?? true);
								$out->putBool($this->showBackface ?? true);
								$out->putBool($this->showTextBackface ?? true);
							}
						}else{
							$out->putUnsignedVarInt(ScriptDebugShapeType::PAYLOAD_TYPE_NONE);
						}
						break;
					case ScriptDebugShapeType::BOX:
						if($this->boxBound !== null){
							$out->putUnsignedVarInt($this->type->getPayloadType());
							$out->putVector3($this->boxBound);
						}else{
							$out->putUnsignedVarInt(ScriptDebugShapeType::PAYLOAD_TYPE_NONE);
						}
						break;
					case ScriptDebugShapeType::LINE:
						if($this->lineEndLocation !== null){
							$out->putUnsignedVarInt($this->type->getPayloadType());
							$out->putVector3($this->lineEndLocation);
						}else{
							$out->putUnsignedVarInt(ScriptDebugShapeType::PAYLOAD_TYPE_NONE);
						}
						break;
					case ScriptDebugShapeType::CIRCLE:
					case ScriptDebugShapeType::SPHERE:
						if($this->segments !== null){
							$out->putUnsignedVarInt($this->type->getPayloadType());
							$out->putByte($this->segments);
						}else{
							$out->putUnsignedVarInt(ScriptDebugShapeType::PAYLOAD_TYPE_NONE);
						}
						break;
				}
			}
		}else{
			$out->writeOptional($this->text, $out->putString(...));
			$out->writeOptional($this->boxBound, $out->putVector3(...));
			$out->writeOptional($this->lineEndLocation, $out->putVector3(...));
			$out->writeOptional($this->arrowHeadLength, $out->putLFloat(...));
			$out->writeOptional($this->arrowHeadRadius, $out->putLFloat(...));
			$out->writeOptional($this->segments, $out->putByte(...));
		}
	}
}
