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

namespace pocketmine\network\mcpe;

use pocketmine\inventory\transaction\action\CreateItemAction;
use pocketmine\inventory\transaction\action\DestroyItemAction;
use pocketmine\inventory\transaction\action\DropItemAction;
use pocketmine\inventory\transaction\action\InventoryAction;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\network\mcpe\convert\TypeConverter;
use pocketmine\network\mcpe\convert\TypeConversionException;
use pocketmine\network\mcpe\protocol\types\inventory\NetworkInventoryAction;

/**
 * Translates the inventory actions of a legacy (pre-ItemStackRequest) transaction into core inventory actions.
 *
 * Clients older than 1.16.100 have no ItemStackRequest at all: every inventory interaction - moving a stack between
 * slots, dropping it, taking it out of the creative menu - arrives as a NORMAL InventoryTransactionPacket instead.
 * Modern PocketMine only kept enough of that path to handle dropping an item, so the rest has to be rebuilt here.
 *
 * @internal
 */
final class LegacyInventoryActionConverter{

	public function __construct(
		private InventoryManager $inventoryManager,
		private TypeConverter $typeConverter
	){}

	/**
	 * Returns the core action matching the given network action, or null if it carries no state change we can act on
	 * (an unknown window, or one of the "magic" slots used purely as a marker).
	 *
	 * @throws TypeConversionException
	 */
	public function convert(NetworkInventoryAction $action) : ?InventoryAction{
		$sourceItem = $this->typeConverter->netItemStackToCore($action->oldItem->getItemStack());
		$targetItem = $this->typeConverter->netItemStackToCore($action->newItem->getItemStack());

		return match($action->sourceType){
			NetworkInventoryAction::SOURCE_CONTAINER => $this->convertContainerAction($action, $sourceItem, $targetItem),
			NetworkInventoryAction::SOURCE_WORLD => $action->inventorySlot === NetworkInventoryAction::ACTION_MAGIC_SLOT_DROP_ITEM
				? new DropItemAction($targetItem)
				: null,
			NetworkInventoryAction::SOURCE_CREATIVE => match($action->inventorySlot){
				NetworkInventoryAction::ACTION_MAGIC_SLOT_CREATIVE_DELETE_ITEM => new DestroyItemAction($targetItem),
				NetworkInventoryAction::ACTION_MAGIC_SLOT_CREATIVE_CREATE_ITEM => new CreateItemAction($sourceItem),
				default => null
			},
			default => null
		};
	}

	private function convertContainerAction(NetworkInventoryAction $action, \pocketmine\item\Item $sourceItem, \pocketmine\item\Item $targetItem) : ?SlotChangeAction{
		$info = $this->inventoryManager->locateWindowAndSlot($action->windowId, $action->inventorySlot);
		if($info === null){
			return null;
		}

		[$inventory, $slot] = $info;
		return new SlotChangeAction($inventory, $slot, $sourceItem, $targetItem);
	}
}
