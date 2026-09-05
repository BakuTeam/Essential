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

use pocketmine\network\mcpe\protocol\PacketDecodeException;
use pocketmine\network\mcpe\protocol\ProtocolInfo;
use pocketmine\network\mcpe\protocol\serializer\PacketSerializer;
use pocketmine\utils\BinaryDataException;

final class LevelSettings{

	public int $seed;
	public SpawnSettings $spawnSettings;
	public int $generator = GeneratorType::OVERWORLD;
	public int $worldGamemode;
	public bool $hardcore = false;
	public int $difficulty;
	public BlockPosition $spawnPosition;
	public bool $hasAchievementsDisabled = true;
	public bool $isEditorMode = false;
	public int $editorWorldType = EditorWorldType::NON_EDITOR;
	public bool $createdInEditorMode = false;
	public bool $exportedFromEditorMode = false;
	public int $time = -1;
	public int $eduEditionOffer = EducationEditionOffer::NONE;
	public bool $hasEduFeaturesEnabled = false;
	public string $eduProductUUID = "";
	public float $rainLevel;
	public float $lightningLevel;
	public bool $hasConfirmedPlatformLockedContent = false;
	public bool $isMultiplayerGame = true;
	public bool $hasLANBroadcast = true;
	public int $xboxLiveBroadcastMode = MultiplayerGameVisibility::PUBLIC;
	public int $platformBroadcastMode = MultiplayerGameVisibility::PUBLIC;
	public bool $commandsEnabled;
	public bool $isTexturePacksRequired = true;
	/**
	 * @var GameRule[]
	 * @phpstan-var array<string, GameRule>
	 */
	public array $gameRules = [];
	public Experiments $experiments;
	public bool $hasBonusChestEnabled = false;
	public bool $hasStartWithMapEnabled = false;
	public int $defaultPlayerPermission = PlayerPermissions::MEMBER; //TODO

	public int $serverChunkTickRadius = 4; //TODO (leave as default for now)

	public bool $hasLockedBehaviorPack = false;
	public bool $hasLockedResourcePack = false;
	public bool $isFromLockedWorldTemplate = false;
	public bool $useMsaGamertagsOnly = false;
	public bool $isFromWorldTemplate = false;
	public bool $isWorldTemplateOptionLocked = false;
	public bool $onlySpawnV1Villagers = false;
	public bool $disablePersona = false;
	public bool $disableCustomSkins = false;
	public bool $muteEmoteAnnouncements = false;
	public ?string $vanillaVersion = null;
	public int $limitedWorldWidth = 0;
	public int $limitedWorldLength = 0;
	public bool $isNewNether = true;
	public ?EducationUriResource $eduSharedUriResource = null;
	public ?bool $experimentalGameplayOverride = null;
	public int $chatRestrictionLevel = ChatRestrictionLevel::NONE;
	public bool $disablePlayerInteractions = false;
	public int $serverEditorConnectionPolicy = 0; //new in 1.26.30
	public bool $allowAnonymousBlockDropsInEditorWorlds = false; //new in 1.26.30

	public string $serverIdentifier = "";
	public string $worldIdentifier = "";
	public string $scenarioIdentifier = "";
	public string $ownerIdentifier = "";

	/**
	 * @throws BinaryDataException
	 * @throws PacketDecodeException
	 */
	public static function read(PacketSerializer $in) : self{
		//TODO: in the future we'll use promoted properties + named arguments for decoding, but for now we stick with
		//this shitty way to limit BC breaks (needs more R&D)
		$result = new self();
		$result->internalRead($in);
		return $result;
	}

	/**
	 * @throws BinaryDataException
	 * @throws PacketDecodeException
	 */
	private function internalRead(PacketSerializer $in) : void{
		if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_18_30){
			$this->seed = $in->getLLong();
		}else{
			$this->seed = $in->getVarInt();
		}
		$this->spawnSettings = SpawnSettings::read($in);
		$this->generator = $in->getVarInt();
		$this->worldGamemode = $in->getVarInt();
		if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_20_80){
			$this->hardcore = $in->getBool();
		}
		$this->difficulty = $in->getVarInt();
		$this->spawnPosition = $in->getBlockPosition($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_10);
		$this->hasAchievementsDisabled = $in->getBool();
		if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_19_10) {
			$this->editorWorldType = $in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_20_30 ? $in->getVarInt() : ($in->getBool() ? EditorWorldType::PROJECT : EditorWorldType::NON_EDITOR);
		}
		if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_19_80){
			$this->createdInEditorMode = $in->getBool();
			$this->exportedFromEditorMode = $in->getBool();
		}
		$this->time = $in->getVarInt();
		$this->eduEditionOffer = $in->getVarInt();
		$this->hasEduFeaturesEnabled = $in->getBool();
		if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_16_0){
			$this->eduProductUUID = $in->getString();
		}
		$this->rainLevel = $in->getLFloat();
		$this->lightningLevel = $in->getLFloat();
		$this->hasConfirmedPlatformLockedContent = $in->getBool();
		$this->isMultiplayerGame = $in->getBool();
		$this->hasLANBroadcast = $in->getBool();
		$this->xboxLiveBroadcastMode = $in->getVarInt();
		$this->platformBroadcastMode = $in->getVarInt();
		$this->commandsEnabled = $in->getBool();
		$this->isTexturePacksRequired = $in->getBool();
		$this->gameRules = $in->getGameRules(true);

		if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_16_100){
			$this->experiments = Experiments::read($in);
		}

		$this->hasBonusChestEnabled = $in->getBool();
		$this->hasStartWithMapEnabled = $in->getBool();
		$this->defaultPlayerPermission = $in->getVarInt();
		$this->serverChunkTickRadius = $in->getLInt();
		$this->hasLockedBehaviorPack = $in->getBool();
		$this->hasLockedResourcePack = $in->getBool();
		$this->isFromLockedWorldTemplate = $in->getBool();
		$this->useMsaGamertagsOnly = $in->getBool();
		$this->isFromWorldTemplate = $in->getBool();
		$this->isWorldTemplateOptionLocked = $in->getBool();
		$this->onlySpawnV1Villagers = $in->getBool();
		if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_19_20){
			$this->disablePersona = $in->getBool();
			$this->disableCustomSkins = $in->getBool();
			if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_19_60){
				$this->muteEmoteAnnouncements = $in->getBool();
			}
		}
		$this->vanillaVersion = $in->getString();
		if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_16_0){
			$this->limitedWorldWidth = $in->getLInt();
			$this->limitedWorldLength = $in->getLInt();
			$this->isNewNether = $in->getBool();

			if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_17_30){
				$this->eduSharedUriResource = EducationUriResource::read($in);
			}

			$this->experimentalGameplayOverride = $in->readOptional($in->getBool(...));
		}
		if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_19_20){
			$this->chatRestrictionLevel = $in->getByte();
			$this->disablePlayerInteractions = $in->getBool();
		}
		if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_0 && $in->getProtocolId() <= ProtocolInfo::PROTOCOL_1_21_130){
			$this->serverIdentifier = $in->getString();
			$this->worldIdentifier = $in->getString();
			$this->scenarioIdentifier = $in->getString();
			if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_90){
				$this->ownerIdentifier = $in->getString();
			}
		}
		if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_30){
			$this->serverEditorConnectionPolicy = $in->getVarInt();
			$this->allowAnonymousBlockDropsInEditorWorlds = $in->getBool();
		}
	}

	public function write(PacketSerializer $out) : void{
		if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_18_30){
			$out->putLLong($this->seed);
		}else{
			$out->putVarInt($this->seed);
		}
		$this->spawnSettings->write($out);
		$out->putVarInt($this->generator);
		$out->putVarInt($this->worldGamemode);
		if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_20_80){
			$out->putBool($this->hardcore);
		}
		$out->putVarInt($this->difficulty);
		$out->putBlockPosition($this->spawnPosition, $out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_10);
		$out->putBool($this->hasAchievementsDisabled);
		if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_20_30){
			$out->putVarInt($this->editorWorldType);
		}elseif($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_19_10){
			$out->putBool($this->editorWorldType !== EditorWorldType::NON_EDITOR);
		}
		if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_19_80){
			$out->putBool($this->createdInEditorMode);
			$out->putBool($this->exportedFromEditorMode);
		}
		$out->putVarInt($this->time);
		$out->putVarInt($this->eduEditionOffer);
		$out->putBool($this->hasEduFeaturesEnabled);
		if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_16_0){
			$out->putString($this->eduProductUUID);
		}
		$out->putLFloat($this->rainLevel);
		$out->putLFloat($this->lightningLevel);
		$out->putBool($this->hasConfirmedPlatformLockedContent);
		$out->putBool($this->isMultiplayerGame);
		$out->putBool($this->hasLANBroadcast);
		$out->putVarInt($this->xboxLiveBroadcastMode);
		$out->putVarInt($this->platformBroadcastMode);
		$out->putBool($this->commandsEnabled);
		$out->putBool($this->isTexturePacksRequired);
		$out->putGameRules($this->gameRules, true);

		if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_16_100){
			$this->experiments->write($out);
		}

		$out->putBool($this->hasBonusChestEnabled);
		$out->putBool($this->hasStartWithMapEnabled);
		$out->putVarInt($this->defaultPlayerPermission);
		$out->putLInt($this->serverChunkTickRadius);
		$out->putBool($this->hasLockedBehaviorPack);
		$out->putBool($this->hasLockedResourcePack);
		$out->putBool($this->isFromLockedWorldTemplate);
		$out->putBool($this->useMsaGamertagsOnly);
		$out->putBool($this->isFromWorldTemplate);
		$out->putBool($this->isWorldTemplateOptionLocked);
		$out->putBool($this->onlySpawnV1Villagers);
		if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_19_20){
			$out->putBool($this->disablePersona);
			$out->putBool($this->disableCustomSkins);
			if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_19_60){
				$out->putBool($this->muteEmoteAnnouncements);
			}
		}
		$out->putString($this->vanillaVersion ?? ProtocolInfo::getMinecraftVersionNetwork($out->getProtocolId()));
		if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_16_0){
			$out->putLInt($this->limitedWorldWidth);
			$out->putLInt($this->limitedWorldLength);
			$out->putBool($this->isNewNether);

			if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_17_30){
				($this->eduSharedUriResource ?? new EducationUriResource("", ""))->write($out);
			}
			$out->writeOptional($this->experimentalGameplayOverride, $out->putBool(...));
		}
		if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_19_20){
			$out->putByte($this->chatRestrictionLevel);
			$out->putBool($this->disablePlayerInteractions);
		}
		if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_0 && $out->getProtocolId() <= ProtocolInfo::PROTOCOL_1_21_130){
			$out->putString($this->serverIdentifier);
			$out->putString($this->worldIdentifier);
			$out->putString($this->scenarioIdentifier);
			if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_90){
				$out->putString($this->ownerIdentifier);
			}
		}
		if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_30){
			$out->putVarInt($this->serverEditorConnectionPolicy);
			$out->putBool($this->allowAnonymousBlockDropsInEditorWorlds);
		}
	}
}
