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

namespace pocketmine\network\mcpe\protocol\types;

use pocketmine\color\Color;
use pocketmine\math\Vector2;
use pocketmine\network\mcpe\protocol\ProtocolInfo;
use pocketmine\network\mcpe\protocol\serializer\PacketSerializer;

final class LocatorBarWaypoint{
	public function __construct(
		private int $updateFlag,
		private ?bool $visible,
		private ?WorldPosition $worldPosition,
		private ?int $textureId,
		private ?Color $color,
		private ?bool $clientPositionAuthority,
		private ?int $actorUniqueId,
		private ?string $texturePath = null,
		private ?Vector2 $iconSize = null
	){}

	public function getUpdateFlag() : int{ return $this->updateFlag; }

	public function getVisible() : ?bool{ return $this->visible; }

	public function getWorldPosition() : ?WorldPosition{ return $this->worldPosition; }

	public function getTextureId() : ?int{ return $this->textureId; }

	public function getTexturePath() : ?string{ return $this->texturePath; }

	public function getIconSize() : ?Vector2{ return $this->iconSize; }

	public function getColor() : ?Color{ return $this->color; }

	public function getClientPositionAuthority() : ?bool{ return $this->clientPositionAuthority; }

	public function getActorUniqueId() : ?int{ return $this->actorUniqueId; }

	public static function read(PacketSerializer $in, ?int $protocolId = null) : self{
		$protocolId ??= $in->getProtocolId();
		$updateFlag = $in->getLInt();
		$visible = $in->readOptional($in->getBool(...));
		$worldPosition = $in->readOptional(fn() => WorldPosition::read($in));
		if($protocolId >= ProtocolInfo::PROTOCOL_1_26_20){
			$texturePath = $in->readOptional($in->getString(...));
			$iconSize = $in->readOptional($in->getVector2(...));
			$textureId = null;
		}else{
			$textureId = $in->readOptional($in->getLInt(...));
			$texturePath = null;
			$iconSize = null;
		}
		$color = $in->readOptional(fn() => Color::fromARGB($in->getLInt()));
		$clientPositionAuthority = $in->readOptional($in->getBool(...));
		$actorUniqueId = $in->readOptional($in->getActorUniqueId(...));

		return new self(
			$updateFlag,
			$visible,
			$worldPosition,
			$textureId,
			$color,
			$clientPositionAuthority,
			$actorUniqueId,
			$texturePath,
			$iconSize
		);
	}

	public function write(PacketSerializer $out, ?int $protocolId = null) : void{
		$protocolId ??= $out->getProtocolId();
		$out->putLInt($this->updateFlag);
		$out->writeOptional($this->visible, fn(bool $v) => $out->putBool($v));
		$out->writeOptional($this->worldPosition, fn(WorldPosition $v) => $v->write($out));
		if($protocolId >= ProtocolInfo::PROTOCOL_1_26_20){
			$out->writeOptional($this->texturePath, $out->putString(...));
			$out->writeOptional($this->iconSize, $out->putVector2(...));
		}else{
			$out->writeOptional($this->textureId, fn(int $v) => $out->putLInt($v));
		}
		$out->writeOptional($this->color, fn(Color $v) => $out->putLInt($v->toARGB()));
		$out->writeOptional($this->clientPositionAuthority, fn(bool $v) => $out->putBool($v));
		$out->writeOptional($this->actorUniqueId, fn(int $v) => $out->putActorUniqueId($v));
	}
}
