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

namespace pocketmine\network\mcpe\protocol\types\camera;

use pocketmine\network\mcpe\protocol\ProtocolInfo;
use pocketmine\network\mcpe\protocol\serializer\PacketSerializer;
use function count;

final class CameraAimAssistPresetExclusionDefinition{
	/**
	 * @param string[] $blocks
	 * @param string[] $entities
	 * @param string[] $blockTags
	 * @param string[] $entityTypeFamilies
	 * @phpstan-param list<string> $blocks
	 * @phpstan-param list<string> $entities
	 * @phpstan-param list<string> $blockTags
	 * @phpstan-param list<string> $entityTypeFamilies
	 */
	public function __construct(
		private array $blocks,
		private array $entities = [],
		private array $blockTags = [],
		private array $entityTypeFamilies = []
	){}

	/**
	 * @return string[]
	 * @phpstan-return list<string>
	 */
	public function getBlocks() : array{ return $this->blocks; }

	/**
	 * @return string[]
	 * @phpstan-return list<string>
	 */
	public function getEntities() : array{ return $this->entities; }

	/**
	 * @return string[]
	 * @phpstan-return list<string>
	 */
	public function getBlockTags() : array{ return $this->blockTags; }

	/**
	 * @return string[]
	 * @phpstan-return list<string>
	 */
	public function getEntityTypeFamilies() : array{ return $this->entityTypeFamilies; }

	public static function read(PacketSerializer $in) : self{
		$blocks = [];
		for($i = 0, $len = $in->getUnsignedVarInt(); $i < $len; ++$i){
			$blocks[] = $in->getString();
		}

		$entities = [];
		$blockTags = [];
		$entityTypeFamilies = [];
		if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_130){
			for($i = 0, $len = $in->getUnsignedVarInt(); $i < $len; ++$i){
				$entities[] = $in->getString();
			}
			for($i = 0, $len = $in->getUnsignedVarInt(); $i < $len; ++$i){
				$blockTags[] = $in->getString();
			}
			if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_0){
				for($i = 0, $len = $in->getUnsignedVarInt(); $i < $len; ++$i){
					$entityTypeFamilies[] = $in->getString();
				}
			}
		}

		return new self($blocks, $entities, $blockTags, $entityTypeFamilies);
	}

	public function write(PacketSerializer $out) : void{
		$out->putUnsignedVarInt(count($this->blocks));
		foreach($this->blocks as $block){
			$out->putString($block);
		}
		if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_130){
			$out->putUnsignedVarInt(count($this->entities));
			foreach($this->entities as $entity){
				$out->putString($entity);
			}
			$out->putUnsignedVarInt(count($this->blockTags));
			foreach($this->blockTags as $blockTag){
				$out->putString($blockTag);
			}
			if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_0){
				$out->putUnsignedVarInt(count($this->entityTypeFamilies));
				foreach($this->entityTypeFamilies as $entityTypeFamily){
					$out->putString($entityTypeFamily);
				}
			}
		}
	}
}
