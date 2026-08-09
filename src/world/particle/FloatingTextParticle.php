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

namespace pocketmine\world\particle;

use pocketmine\block\VanillaBlocks;
use pocketmine\entity\Entity;
use pocketmine\entity\Skin;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\convert\TypeConverter;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\network\mcpe\protocol\AddPlayerPacket;
use pocketmine\network\mcpe\protocol\PlayerListPacket;
use pocketmine\network\mcpe\protocol\RemoveActorPacket;
use pocketmine\network\mcpe\protocol\types\AbilitiesData;
use pocketmine\network\mcpe\protocol\types\AbilitiesLayer;
use pocketmine\network\mcpe\protocol\types\command\CommandPermissions;
use pocketmine\network\mcpe\protocol\types\entity\ByteMetadataProperty;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataFlags;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;
use pocketmine\network\mcpe\protocol\types\entity\FloatMetadataProperty;
use pocketmine\network\mcpe\protocol\types\entity\IntMetadataProperty;
use pocketmine\network\mcpe\protocol\types\entity\LongMetadataProperty;
use pocketmine\network\mcpe\protocol\types\entity\PropertySyncData;
use pocketmine\network\mcpe\protocol\types\entity\StringMetadataProperty;
use pocketmine\network\mcpe\protocol\types\inventory\ItemStack;
use pocketmine\network\mcpe\protocol\types\inventory\ItemStackWrapper;
use pocketmine\network\mcpe\protocol\types\PlayerListEntry;
use pocketmine\network\mcpe\protocol\types\PlayerPermissions;
use pocketmine\network\mcpe\protocol\UpdateAbilitiesPacket;
use Ramsey\Uuid\Uuid;
use function array_fill;
use function str_repeat;

class FloatingTextParticle extends ProtocolParticle{
	//TODO: HACK!

	protected ?int $entityId = null;
	protected bool $invisible = false;

	public function __construct(
		protected string $text,
		protected string $title = ""
	){
		//parent::__construct(VanillaBlocks::AIR()); pm5 codes
	}

	public function getText() : string{
		return $this->text;
	}

	public function setText(string $text) : void{
		$this->text = $text;
	}

	public function getTitle() : string{
		return $this->title;
	}

	public function setTitle(string $title) : void{
		$this->title = $title;
	}

	public function isInvisible() : bool{
		return $this->invisible;
	}

	public function setInvisible(bool $value = true) : void{
		$this->invisible = $value;
	}

	public function encode(Vector3 $pos) : array{
		$p = [];

		if($this->entityId === null){
			$this->entityId = Entity::nextRuntimeId();
		}else{
			$p[] = RemoveActorPacket::create($this->entityId);
		}

		if(!$this->invisible){
			$uuid = Uuid::uuid4();
			$name = $this->title . ($this->text !== "" ? "\n" . $this->text : "");

			$add = new PlayerListPacket();
			$add->type = PlayerListPacket::TYPE_ADD;
			$add->entries = [PlayerListEntry::createAdditionEntry($uuid, $this->entityId, $name, TypeConverter::getInstance($this->protocolId)->getSkinAdapter()->toSkinData(new Skin("Standard_Custom", str_repeat("\x00", 8192))))];
			$p[] = $add;

			$flags = (
				1 << EntityMetadataFlags::NO_AI
			);
			$metadata = [
				EntityMetadataProperties::FLAGS => new LongMetadataProperty($flags),
				EntityMetadataProperties::SCALE => new FloatMetadataProperty(0.01), //zero causes problems on debug builds
				EntityMetadataProperties::BOUNDING_BOX_WIDTH => new FloatMetadataProperty(0.0),
				EntityMetadataProperties::BOUNDING_BOX_HEIGHT => new FloatMetadataProperty(0.0),
			];
			$pk = AddPlayerPacket::create($uuid, $name, $this->entityId, "", $pos, null, 0, 0, 0, ItemStackWrapper::legacy(ItemStack::null()), 0, $metadata, new PropertySyncData([], []), UpdateAbilitiesPacket::create(new AbilitiesData(CommandPermissions::NORMAL, PlayerPermissions::VISITOR, $this->entityId, [
				new AbilitiesLayer(
					AbilitiesLayer::LAYER_BASE,
					array_fill(0, AbilitiesLayer::NUMBER_OF_ABILITIES, false),
					0.0,
					0.0,
					0.0
				)
			])), [], "", 0);

			$p[] = $pk;

			$remove = new PlayerListPacket();
			$remove->type = PlayerListPacket::TYPE_REMOVE;
			$remove->entries = [PlayerListEntry::createRemovalEntry($uuid)];
			$p[] = $remove;

			//pm5 codes
			// $name = $this->title . ($this->text !== "" ? "\n" . $this->text : "");

			// $actorFlags = (
			// 	1 << EntityMetadataFlags::NO_AI
			// );
			// $actorMetadata = [
			// 	EntityMetadataProperties::FLAGS => new LongMetadataProperty($actorFlags),
			// 	EntityMetadataProperties::SCALE => new FloatMetadataProperty(0.01), //zero causes problems on debug builds
			// 	EntityMetadataProperties::BOUNDING_BOX_WIDTH => new FloatMetadataProperty(0.0),
			// 	EntityMetadataProperties::BOUNDING_BOX_HEIGHT => new FloatMetadataProperty(0.0),
			// 	EntityMetadataProperties::NAMETAG => new StringMetadataProperty($name),
			// 	EntityMetadataProperties::VARIANT => new IntMetadataProperty($this->toRuntimeId()),
			// 	EntityMetadataProperties::ALWAYS_SHOW_NAMETAG => new ByteMetadataProperty(1),
			// ];
			// $p[] = AddActorPacket::create(
			// 	$this->entityId, //TODO: actor unique ID
			// 	$this->entityId,
			// 	EntityIds::FALLING_BLOCK,
			// 	$pos, //TODO: check offset (0.49?)
			// 	null,
			// 	0,
			// 	0,
			// 	0,
			// 	0,
			// 	[],
			// 	$actorMetadata,
			// 	new PropertySyncData([], []),
			// 	[]
			// );
		}

		return $p;
	}
}
