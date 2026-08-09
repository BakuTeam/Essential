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

use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\serializer\PacketSerializer;
use pocketmine\network\mcpe\protocol\types\DebugMarkerData;
use function array_search;

class ClientboundDebugRendererPacket extends DataPacket implements ClientboundPacket{
	public const NETWORK_ID = ProtocolInfo::CLIENTBOUND_DEBUG_RENDERER_PACKET;

	public const TYPE_CLEAR = 1;
	public const TYPE_ADD_CUBE = 2;

	private const TRANSLATION = [
		"cleardebugmarkers" => self::TYPE_CLEAR,
		"adddebugmarkercube" => self::TYPE_ADD_CUBE,
	];

	private int $type;

	//TODO: if more types are added, we'll probably want to make a separate data type and interfaces
	private string $text;
	private Vector3 $position;
	private float $red;
	private float $green;
	private float $blue;
	private float $alpha;
	private int $durationMillis;
	private ?DebugMarkerData $data = null;

	private static function base(int $type) : self{
		$result = new self();
		$result->type = $type;
		return $result;
	}

	public static function clear() : self{ return self::base(self::TYPE_CLEAR); }

	public static function addCube(string $text, Vector3 $position, float $red, float $green, float $blue, float $alpha, int $durationMillis) : self{
		$result = self::base(self::TYPE_ADD_CUBE);
		$result->text = $text;
		$result->position = $position;
		$result->red = $red;
		$result->green = $green;
		$result->blue = $blue;
		$result->alpha = $alpha;
		$result->durationMillis = $durationMillis;
		$result->data = new DebugMarkerData($text, $position, new \pocketmine\color\Color((int) ($red * 255), (int) ($green * 255), (int) ($blue * 255), (int) ($alpha * 255)), $durationMillis);
		return $result;
	}

	private function getTypeName() : string{
		$typeName = array_search($this->type, self::TRANSLATION, true);
		if($typeName === false){
			throw new \InvalidArgumentException("Unknown type " . $this->type);
		}
		return $typeName;
	}

	private function getTypeIdFromName(string $name) : int{
		return self::TRANSLATION[$name] ?? throw new PacketDecodeException("Unknown type " . $name);
	}

	public function getType() : int{ return $this->type; }

	public function getText() : string{ return $this->text; }

	public function getPosition() : Vector3{ return $this->position; }

	public function getRed() : float{ return $this->red; }

	public function getGreen() : float{ return $this->green; }

	public function getBlue() : float{ return $this->blue; }

	public function getAlpha() : float{ return $this->alpha; }

	public function getDurationMillis() : int{ return $this->durationMillis; }

	public function getData() : ?DebugMarkerData{ return $this->data; }

	protected function decodePayload(PacketSerializer $in) : void{
		if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_130){
			$this->type = $this->getTypeIdFromName($in->getString());
			$this->data = $in->readOptional(fn() => DebugMarkerData::read($in));
			if($this->data !== null){
				$this->text = $this->data->getText();
				$this->position = $this->data->getPosition();
				$this->red = $this->data->getColor()->getR() / 255;
				$this->green = $this->data->getColor()->getG() / 255;
				$this->blue = $this->data->getColor()->getB() / 255;
				$this->alpha = $this->data->getColor()->getA() / 255;
				$this->durationMillis = $this->data->getDurationMillis();
			}
			return;
		}

		$this->type = $in->getLInt();

		switch($this->type){
			case self::TYPE_CLEAR:
				//NOOP
				break;
			case self::TYPE_ADD_CUBE:
				$this->text = $in->getString();
				$this->position = $in->getVector3();
				$this->red = $in->getLFloat();
				$this->green = $in->getLFloat();
				$this->blue = $in->getLFloat();
				$this->alpha = $in->getLFloat();
				$this->durationMillis = $in->getLLong();
				$this->data = new DebugMarkerData($this->text, $this->position, new \pocketmine\color\Color((int) ($this->red * 255), (int) ($this->green * 255), (int) ($this->blue * 255), (int) ($this->alpha * 255)), $this->durationMillis);
				break;
			default:
				throw new PacketDecodeException("Unknown type " . $this->type);
		}
	}

	protected function encodePayload(PacketSerializer $out) : void{
		if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_130){
			$out->putString($this->getTypeName());
			$out->writeOptional($this->data, fn(DebugMarkerData $data) => $data->write($out));
			return;
		}

		$out->putLInt($this->type);

		switch($this->type){
			case self::TYPE_CLEAR:
				//NOOP
				break;
			case self::TYPE_ADD_CUBE:
				$out->putString($this->text);
				$out->putVector3($this->position);
				$out->putLFloat($this->red);
				$out->putLFloat($this->green);
				$out->putLFloat($this->blue);
				$out->putLFloat($this->alpha);
				$out->putLLong($this->durationMillis);
				break;
			default:
				throw new \InvalidArgumentException("Unknown type " . $this->type);
		}
	}

	public function handle(PacketHandlerInterface $handler) : bool{
		return $handler->handleClientboundDebugRenderer($this);
	}
}
