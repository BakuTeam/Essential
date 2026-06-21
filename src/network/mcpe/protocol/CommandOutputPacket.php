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
use pocketmine\network\mcpe\protocol\types\command\CommandOriginData;
use pocketmine\network\mcpe\protocol\types\command\CommandOutputMessage;
use pocketmine\utils\BinaryDataException;
use function array_search;
use function count;

class CommandOutputPacket extends DataPacket implements ClientboundPacket{
	public const NETWORK_ID = ProtocolInfo::COMMAND_OUTPUT_PACKET;

	public const TYPE_LAST = 1;
	public const TYPE_SILENT = 2;
	public const TYPE_ALL = 3;
	public const TYPE_DATA_SET = 4;

	private const TRANSLATION = [
		"lastoutput" => self::TYPE_LAST,
		"silent" => self::TYPE_SILENT,
		"alloutput" => self::TYPE_ALL,
		"dataset" => self::TYPE_DATA_SET,
	];

	public CommandOriginData $originData;
	public int $outputType;
	public int $successCount;
	/** @var CommandOutputMessage[] */
	public array $messages = [];
	public string $unknownString;
	public ?string $data = null;

	private function getOutputTypeName() : string{
		$outputType = array_search($this->outputType, self::TRANSLATION, true);
		if($outputType === false){
			throw new \InvalidArgumentException("Invalid output type id: $this->outputType");
		}
		return $outputType;
	}

	private function getOutputTypeFromName(string $name) : int{
		return self::TRANSLATION[$name] ?? throw new PacketDecodeException("Invalid output type name: $name");
	}

	protected function decodePayload(PacketSerializer $in) : void{
		$this->originData = $in->getCommandOriginData($in->getProtocolId());
		if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_130){
			$this->outputType = $this->getOutputTypeFromName($in->getString());
			$this->successCount = $in->getLInt();
		}else{
			$this->outputType = $in->getByte();
			$this->successCount = $in->getUnsignedVarInt();
		}

		for($i = 0, $size = $in->getUnsignedVarInt(); $i < $size; ++$i){
			$this->messages[] = $this->getCommandMessage($in);
		}

		if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_130){
			$this->data = $in->readOptional(fn() => $in->getString());
			if($this->data !== null){
				$this->unknownString = $this->data;
			}
		}elseif($this->outputType === self::TYPE_DATA_SET){
			$this->unknownString = $in->getString();
			$this->data = $this->unknownString;
		}
	}

	/**
	 * @throws BinaryDataException
	 */
	protected function getCommandMessage(PacketSerializer $in) : CommandOutputMessage{
		$message = new CommandOutputMessage();

		if($in->getProtocolId() <= ProtocolInfo::PROTOCOL_1_21_124){
			$message->isInternal = $in->getBool();
		}
		$message->messageId = $in->getString();
		if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_130){
			$message->isInternal = $in->getBool();
		}

		for($i = 0, $size = $in->getUnsignedVarInt(); $i < $size; ++$i){
			$message->parameters[] = $in->getString();
		}

		return $message;
	}

	protected function encodePayload(PacketSerializer $out) : void{
		$out->putCommandOriginData($this->originData);
		if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_130){
			$out->putString($this->getOutputTypeName());
			$out->putLInt($this->successCount);
		}else{
			$out->putByte($this->outputType);
			$out->putUnsignedVarInt($this->successCount);
		}

		$out->putUnsignedVarInt(count($this->messages));
		foreach($this->messages as $message){
			$this->putCommandMessage($message, $out);
		}

		if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_130){
			$data = $this->data ?? ($this->outputType === self::TYPE_DATA_SET && isset($this->unknownString) ? $this->unknownString : null);
			$out->writeOptional($data, fn(string $v) => $out->putString($v));
		}elseif($this->outputType === self::TYPE_DATA_SET){
			$out->putString($this->unknownString);
		}
	}

	protected function putCommandMessage(CommandOutputMessage $message, PacketSerializer $out) : void{
		if($out->getProtocolId() <= ProtocolInfo::PROTOCOL_1_21_124){
			$out->putBool($message->isInternal);
		}
		$out->putString($message->messageId);
		if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_130){
			$out->putBool($message->isInternal);
		}

		$out->putUnsignedVarInt(count($message->parameters));
		foreach($message->parameters as $parameter){
			$out->putString($parameter);
		}
	}

	public function handle(PacketHandlerInterface $handler) : bool{
		return $handler->handleCommandOutput($this);
	}
}
