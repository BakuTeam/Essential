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

final class CameraAimAssistCategoryPriorities{

	/**
	 * @param CameraAimAssistCategoryEntityPriority[] $entities
	 * @param CameraAimAssistCategoryBlockPriority[] $blocks
	 * @param CameraAimAssistCategoryPriority[] $blockTags
	 * @param CameraAimAssistCategoryPriority[] $entityTypeFamilies
	 */
	public function __construct(
		private array $entities,
		private array $blocks,
		private ?int $defaultEntityPriority,
		private ?int $defaultBlockPriority,
		private array $blockTags = [],
		private array $entityTypeFamilies = []
	){}

	/**
	 * @return CameraAimAssistCategoryEntityPriority[]
	 */
	public function getEntities() : array{ return $this->entities; }

	/**
	 * @return CameraAimAssistCategoryBlockPriority[]
	 */
	public function getBlocks() : array{ return $this->blocks; }

	/**
	 * @return CameraAimAssistCategoryPriority[]
	 */
	public function getBlockTags() : array{ return $this->blockTags; }

	/**
	 * @return CameraAimAssistCategoryPriority[]
	 */
	public function getEntityTypeFamilies() : array{ return $this->entityTypeFamilies; }

	public function getDefaultEntityPriority() : ?int{ return $this->defaultEntityPriority; }

	public function getDefaultBlockPriority() : ?int{ return $this->defaultBlockPriority; }

	public static function read(PacketSerializer $in) : self{
		$entities = [];
		for($i = 0, $len = $in->getUnsignedVarInt(); $i < $len; ++$i){
			$entities[] = CameraAimAssistCategoryEntityPriority::read($in);
		}

		$blocks = [];
		for($i = 0, $len = $in->getUnsignedVarInt(); $i < $len; ++$i){
			$blocks[] = CameraAimAssistCategoryPriority::read($in);
		}

		$blockTags = [];
		if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_130){
			for($i = 0, $len = $in->getUnsignedVarInt(); $i < $len; ++$i){
				$blockTags[] = CameraAimAssistCategoryPriority::read($in);
			}
		}

		$entityTypeFamilies = [];
		if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_0){
			for($i = 0, $len = $in->getUnsignedVarInt(); $i < $len; ++$i){
				$entityTypeFamilies[] = CameraAimAssistCategoryPriority::read($in);
			}
		}

		$defaultEntityPriority = $in->readOptional(fn() => $in->getLInt());
		$defaultBlockPriority = $in->readOptional(fn() => $in->getLInt());
		return new self(
			$entities,
			$blocks,
			$defaultEntityPriority,
			$defaultBlockPriority,
			$blockTags,
			$entityTypeFamilies
		);
	}

	public function write(PacketSerializer $out) : void{
		$out->putUnsignedVarInt(count($this->entities));
		foreach($this->entities as $entity){
			$entity->write($out);
		}

		$out->putUnsignedVarInt(count($this->blocks));
		foreach($this->blocks as $block){
			$block->write($out);
		}

		if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_130){
			$out->putUnsignedVarInt(count($this->blockTags));
			foreach($this->blockTags as $tag){
				$tag->write($out);
			}
			if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_0){
				$out->putUnsignedVarInt(count($this->entityTypeFamilies));
				foreach($this->entityTypeFamilies as $family){
					$family->write($out);
				}
			}
		}

		$out->writeOptional($this->defaultEntityPriority, fn(int $v) => $out->putLInt($v));
		$out->writeOptional($this->defaultBlockPriority, fn(int $v) => $out->putLInt($v));
	}
}
