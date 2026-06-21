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
use function count;

class TextPacket extends DataPacket implements ClientboundPacket, ServerboundPacket{
	public const NETWORK_ID = ProtocolInfo::TEXT_PACKET;

	public const TYPE_MESSAGE_ONLY = 0;
	public const TYPE_AUTHOR_AND_MESSAGE = 1;
	public const TYPE_MESSAGE_AND_PARAMS = 2;

	public const TYPE_RAW = 0;
	public const TYPE_CHAT = 1;
	public const TYPE_TRANSLATION = 2;
	public const TYPE_POPUP = 3;
	public const TYPE_JUKEBOX_POPUP = 4;
	public const TYPE_TIP = 5;
	public const TYPE_SYSTEM = 6;
	public const TYPE_WHISPER = 7;
	public const TYPE_ANNOUNCEMENT = 8;
	public const TYPE_JSON_WHISPER = 9;
	public const TYPE_JSON = 10;
	public const TYPE_JSON_ANNOUNCEMENT = 11;

	public int $type;
	public bool $needsTranslation = false;
	public string $sourceName;
	public string $message;
	/** @var string[] */
	public array $parameters = [];
	public string $xboxUserId = "";
	public string $platformChatId = "";
	public string $filteredMessage = "";

	private static function messageOnly(int $type, string $message) : self{
		$result = new self();
		$result->type = $type;
		$result->message = $message;
		return $result;
	}

	/**
	 * @param string[] $parameters
	 */
	private static function baseTranslation(int $type, string $key, array $parameters) : self{
		$result = new self();
		$result->type = $type;
		$result->needsTranslation = true;
		$result->message = $key;
		$result->parameters = $parameters;
		return $result;
	}

	public static function raw(string $message) : self{
		return self::messageOnly(self::TYPE_RAW, $message);
	}

	/**
	 * @param string[] $parameters
	 */
	public static function translation(string $key, array $parameters = []) : self{
		return self::baseTranslation(self::TYPE_TRANSLATION, $key, $parameters);
	}

	public static function popup(string $message) : self{
		return self::messageOnly(self::TYPE_POPUP, $message);
	}

	/**
	 * @param string[] $parameters
	 */
	public static function translatedPopup(string $key, array $parameters = []) : self{
		return self::baseTranslation(self::TYPE_POPUP, $key, $parameters);
	}

	/**
	 * @param string[] $parameters
	 */
	public static function jukeboxPopup(string $key, array $parameters = []) : self{
		return self::baseTranslation(self::TYPE_JUKEBOX_POPUP, $key, $parameters);
	}

	public static function tip(string $message) : self{
		return self::messageOnly(self::TYPE_TIP, $message);
	}

	protected function decodePayload(PacketSerializer $in) : void{
		if ($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_130) {
			$this->needsTranslation = $in->getBool();
			$type = $in->getByte();
			switch ($type) {
				case self::TYPE_MESSAGE_ONLY:
					if($in->getProtocolId() === ProtocolInfo::PROTOCOL_1_21_130){
						for ($i = 0;$i < 6;$i++) {
							$in->getString();
						}
					}
					$this->type = $in->getByte();
					$this->message = $in->getString();
					break;
				case self::TYPE_AUTHOR_AND_MESSAGE:
					if($in->getProtocolId() === ProtocolInfo::PROTOCOL_1_21_130){
						for ($i = 0;$i < 3;$i++) {
							$in->getString();
						}
					}
					$this->type = $in->getByte();
					$this->sourceName = $in->getString();
					$this->message = $in->getString();
					break;
				case self::TYPE_MESSAGE_AND_PARAMS:
					if($in->getProtocolId() === ProtocolInfo::PROTOCOL_1_21_130){
						for ($i = 0;$i < 3;$i++) {
							$in->getString();
						}
					}
					$this->type = $in->getByte();
					$this->message = $in->getString();
					$count = $in->getUnsignedVarInt();
					for($i = 0; $i < $count; ++$i){
						$this->parameters[] = $in->getString();
					}
					break;
			}
		} else {
			$this->type = $in->getByte();
			$this->needsTranslation = $in->getBool();
			switch($this->type){
				case self::TYPE_CHAT:
				case self::TYPE_WHISPER:
				/** @noinspection PhpMissingBreakStatementInspection */
				case self::TYPE_ANNOUNCEMENT:
					$this->sourceName = $in->getString();
				case self::TYPE_RAW:
				case self::TYPE_TIP:
				case self::TYPE_SYSTEM:
				case self::TYPE_JSON_WHISPER:
				case self::TYPE_JSON:
				case self::TYPE_JSON_ANNOUNCEMENT:
					$this->message = $in->getString();
					break;

				case self::TYPE_TRANSLATION:
				case self::TYPE_POPUP:
				case self::TYPE_JUKEBOX_POPUP:
					$this->message = $in->getString();
					$count = $in->getUnsignedVarInt();
					for($i = 0; $i < $count; ++$i){
						$this->parameters[] = $in->getString();
					}
					break;
			}
		}

		$this->xboxUserId = $in->getString();
		$this->platformChatId = $in->getString();
		if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_130){
			$this->filteredMessage = $in->readOptional(fn() => $in->getString()) ?? "";
		}elseif($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_0){
			$this->filteredMessage = $in->getString();
		}
	}

	protected function encodePayload(PacketSerializer $out) : void{
		if ($out->getProtocolId() < ProtocolInfo::PROTOCOL_1_21_130) {
			$out->putByte($this->type);
		}
		$out->putBool($this->needsTranslation);
		switch($this->type){
			case self::TYPE_CHAT:
			case self::TYPE_WHISPER:
			/** @noinspection PhpMissingBreakStatementInspection */
			case self::TYPE_ANNOUNCEMENT:
				if ($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_130) {
					$out->putByte(self::TYPE_AUTHOR_AND_MESSAGE);
					if($out->getProtocolId() === ProtocolInfo::PROTOCOL_1_21_130){
						$out->putString("chat");
						$out->putString("whisper");
						$out->putString("announcement");
					}
					$out->putByte($this->type);
				}
				$out->putString($this->sourceName);
				$out->putString($this->message);
				break;
			case self::TYPE_RAW:
			case self::TYPE_TIP:
			case self::TYPE_SYSTEM:
			case self::TYPE_JSON_WHISPER:
			case self::TYPE_JSON:
			case self::TYPE_JSON_ANNOUNCEMENT:
				if ($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_130) {
					$out->putByte(self::TYPE_MESSAGE_ONLY);
					if($out->getProtocolId() === ProtocolInfo::PROTOCOL_1_21_130){
						$out->putString("raw");
						$out->putString("tip");
						$out->putString("systemMessage");
						$out->putString("textObjectWhisper");
						$out->putString("textObjectAnnouncement");
						$out->putString("textObject");
					}
					$out->putByte($this->type);
				}
				$out->putString($this->message);
				break;

			case self::TYPE_TRANSLATION:
			case self::TYPE_POPUP:
			case self::TYPE_JUKEBOX_POPUP:
				if ($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_130) {
					$out->putByte(self::TYPE_MESSAGE_AND_PARAMS);
					if($out->getProtocolId() === ProtocolInfo::PROTOCOL_1_21_130){
						$out->putString("translate");
						$out->putString("popup");
						$out->putString("jukeboxPopup");
					}
					$out->putByte($this->type);
				}
				$out->putString($this->message);
				$out->putUnsignedVarInt(count($this->parameters));
				foreach($this->parameters as $p){
					$out->putString($p);
				}
				break;
		}

		$out->putString($this->xboxUserId);
		$out->putString($this->platformChatId);
		if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_130){
			$out->writeOptional($this->filteredMessage !== "" ? $this->filteredMessage : null, fn(string $v) => $out->putString($v));
		}elseif($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_0){
			$out->putString($this->filteredMessage);
		}
	}

	public function handle(PacketHandlerInterface $handler) : bool{
		return $handler->handleText($this);
	}
}
