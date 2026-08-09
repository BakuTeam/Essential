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

namespace pocketmine\network\mcpe\protocol\types\inventory;

use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\TreeRoot;
use pocketmine\network\mcpe\protocol\serializer\NetworkNbtSerializer;
use function base64_encode;
use function count;

final class ItemStack implements \JsonSerializable{
	/**
	 * @param string[] $canPlaceOn
	 * @param string[] $canDestroy
	 */
	public function __construct(
		private int $id,
		private int $meta,
		private int $count,
		private int $blockRuntimeId,
		private ?CompoundTag $nbt,
		private array $canPlaceOn,
		private array $canDestroy,
		private ?int $shieldBlockingTick = null,
		private ?string $rawExtraData = null
	){}

	public static function null() : self{
		return new self(0, 0, 0, 0, null, [], [], null);
	}

	public function getId() : int{
		return $this->id;
	}

	public function getMeta() : int{
		return $this->meta;
	}

	public function getCount() : int{
		return $this->count;
	}

	public function getBlockRuntimeId() : int{ return $this->blockRuntimeId; }

	/**
	 * @return string[]
	 */
	public function getCanPlaceOn() : array{
		return $this->canPlaceOn;
	}

	/**
	 * @return string[]
	 */
	public function getCanDestroy() : array{
		return $this->canDestroy;
	}

	public function getNbt() : ?CompoundTag{
		return $this->nbt;
	}

	public function getShieldBlockingTick() : ?int{
		return $this->shieldBlockingTick;
	}

	public function getRawExtraData() : ?string{
		return $this->rawExtraData;
	}

	public function equals(ItemStack $itemStack) : bool{
		return
			$this->id === $itemStack->id &&
			$this->meta === $itemStack->meta &&
			$this->count === $itemStack->count &&
			$this->blockRuntimeId === $itemStack->blockRuntimeId &&
			$this->canPlaceOn === $itemStack->canPlaceOn &&
			$this->canDestroy === $itemStack->canDestroy &&
			$this->shieldBlockingTick === $itemStack->shieldBlockingTick && (
				($this->rawExtraData !== null && $itemStack->rawExtraData !== null && $this->rawExtraData === $itemStack->rawExtraData) ||
				$this->nbt === $itemStack->nbt || //this covers null === null and fast object identity
				($this->nbt !== null && $itemStack->nbt !== null && $this->nbt->equals($itemStack->nbt))
			);
	}

	/** @return mixed[] */
	public function jsonSerialize() : array{
		$result = [
			"id" => $this->id,
			"meta" => $this->meta,
			"count" => $this->count,
			"blockRuntimeId" => $this->blockRuntimeId,
		];
		if(count($this->canPlaceOn) > 0){
			$result["canPlaceOn"] = $this->canPlaceOn;
		}
		if(count($this->canDestroy) > 0){
			$result["canDestroy"] = $this->canDestroy;
		}
		if($this->shieldBlockingTick !== null){
			$result["shieldBlockingTick"] = $this->shieldBlockingTick;
		}
		if($this->nbt !== null){
			$result["nbt"] = base64_encode((new NetworkNbtSerializer())->write(new TreeRoot($this->nbt)));
		}
		if($this->rawExtraData !== null){
			$result["rawExtraData"] = base64_encode($this->rawExtraData);
		}
		return $result;
	}
}
