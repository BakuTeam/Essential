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

namespace pocketmine\network\mcpe\protocol\types\recipe;

use pocketmine\network\mcpe\protocol\ProtocolInfo;
use pocketmine\network\mcpe\protocol\serializer\PacketSerializer;
use pocketmine\network\mcpe\protocol\types\inventory\ItemStack;
use Ramsey\Uuid\UuidInterface;
use function count;

final class ShapelessRecipe extends RecipeWithTypeId{
	/**
	 * @param RecipeIngredient[] $inputs
	 * @param ItemStack[]        $outputs
	 * @phpstan-param list<RecipeIngredient> $inputs
	 * @phpstan-param list<ItemStack> $outputs
	 */
	public function __construct(
		int $typeId,
		private string $recipeId,
		private array $inputs,
		private array $outputs,
		private UuidInterface $uuid,
		private string $blockName,
		private int $priority,
		private RecipeUnlockingRequirement $unlockingRequirement,
		private int $recipeNetId
	){
		parent::__construct($typeId);
	}

	public function getRecipeId() : string{
		return $this->recipeId;
	}

	/**
	 * @return RecipeIngredient[]
	 * @phpstan-return list<RecipeIngredient>
	 */
	public function getInputs() : array{
		return $this->inputs;
	}

	/**
	 * @return ItemStack[]
	 * @phpstan-return list<ItemStack>
	 */
	public function getOutputs() : array{
		return $this->outputs;
	}

	public function getUuid() : UuidInterface{
		return $this->uuid;
	}

	public function getBlockName() : string{
		return $this->blockName;
	}

	public function getPriority() : int{
		return $this->priority;
	}

	public function getUnlockingRequirement() : RecipeUnlockingRequirement{ return $this->unlockingRequirement; }

	public function getRecipeNetId() : int{
		return $this->recipeNetId;
	}

	public static function decode(int $recipeType, PacketSerializer $in) : self{
		$recipeId = $in->getString();
		$input = [];
		for($j = 0, $ingredientCount = $in->getUnsignedVarInt(); $j < $ingredientCount; ++$j){
			$input[] = $in->getRecipeIngredient();
		}
		$output = [];
		for($k = 0, $resultCount = $in->getUnsignedVarInt(); $k < $resultCount; ++$k){
			$output[] = $in->getItemStackWithoutStackId();
		}
		$uuid = $in->getUUID();
		$block = $in->getString();
		$priority = $in->getVarInt();
		if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_0){
			$unlockingRequirement = $in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_40
				? $in->readOptional(fn() => RecipeUnlockingRequirement::read($in))
				: RecipeUnlockingRequirement::read($in);
		}

		$recipeNetId = $in->readRecipeNetId();

		return new self($recipeType, $recipeId, $input, $output, $uuid, $block, $priority, $unlockingRequirement ?? new RecipeUnlockingRequirement(null), $recipeNetId);
	}

	public function encode(PacketSerializer $out) : void{
		$out->putString($this->recipeId);
		$out->putUnsignedVarInt(count($this->inputs));
		foreach($this->inputs as $item){
			$out->putRecipeIngredient($item);
		}

		$out->putUnsignedVarInt(count($this->outputs));
		foreach($this->outputs as $item){
			$out->putItemStackWithoutStackId($item);
		}

		$out->putUUID($this->uuid);
		$out->putString($this->blockName);
		$out->putVarInt($this->priority);
		if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_0){
			if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_40){
				$out->writeOptional($this->unlockingRequirement, fn(RecipeUnlockingRequirement $v) => $v->write($out));
			}else{
				$this->unlockingRequirement->write($out);
			}
		}

		$out->writeRecipeNetId($this->recipeNetId);
	}
}
