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

namespace pocketmine\network\mcpe\protocol\types\login;

/**
 * Model class for LoginPacket JSON data for JsonMapper
 */
final class ClientData{

	/**
	 * @var ClientDataAnimationFrame[]
	 * @required
	 */
	public array $AnimatedImageData;

	/** @required */
	public string $ArmSize;

	/** @required */
	public string $CapeData;

	/** @required */
	public string $CapeId;

	/** @required */
	public int $CapeImageHeight;

	/** @required */
	public int $CapeImageWidth;

	/** @required */
	public bool $CapeOnClassicSkin;

	/** @required */
	public int $ClientRandomId;

	/** >= PROTOCOL_1_19_80 */
	public bool $CompatibleWithClientSideChunkGen;

	/** @required */
	public int $CurrentInputMode;

	/** @required */
	public int $DefaultInputMode;

	/** @required */
	public string $DeviceId;

	/** @required */
	public string $DeviceModel;

	/** @required */
	public int $DeviceOS;

	/** @required */
	public string $GameVersion;

	/** >= ProtocolInfo::PROTOCOL_1_21_70 */
	public int $GraphicsMode;

	/** @required */
	public int $GuiScale;

	/** >= PROTOCOL_1_19_10 */
	public bool $IsEditorMode;

	/** @required */
	public string $LanguageCode;

	/** >= ProtocolInfo::PROTOCOL_1_21_40 */
	public int $MaxViewDistance;

	/** >= ProtocolInfo::PROTOCOL_1_21_40 */
	public int $MemoryTier;

	/** >= PROTOCOL_1_19_63 */
	public bool $OverrideSkin;

	/** >= ProtocolInfo::PROTOCOL_1_26_0 */
	public string $PartyId = "";

	/**
	 * @var ClientDataPersonaSkinPiece[]
	 * @required
	 */
	public array $PersonaPieces;

	/** @required */
	public bool $PersonaSkin;

	/**
	 * @var ClientDataPersonaPieceTintColor[]
	 * @required
	 */
	public array $PieceTintColors;

	/** @required */
	public string $PlatformOfflineId;

	/** @required */
	public string $PlatformOnlineId;

	/** >= ProtocolInfo::PROTOCOL_1_21_40 */
	public int $PlatformType;

	public string $PlatformUserId = ""; //xbox-only, apparently

	/** >= PROTOCOL_1_16_210 */
	public string $PlayFabId;

	/** @required */
	public bool $PremiumSkin = false;

	/** @required */
	public string $SelfSignedId;

	/** @required */
	public string $ServerAddress;

	/** @required */
	public string $SkinAnimationData;

	/** @required */
	public string $SkinColor;

	/** @required */
	public string $SkinData;

	/** @required */
	public string $SkinGeometryData;

	/** >= PROTOCOL_1_17_30 */
	public string $SkinGeometryDataEngineVersion;

	/** @required */
	public string $SkinId;

	/** @required */
	public int $SkinImageHeight;

	/** @required */
	public int $SkinImageWidth;

	/** @required */
	public string $SkinResourcePatch;

	/** @required */
	public string $ThirdPartyName;

	/** <= ProtocolInfo::PROTOCOL_1_21_80 */
	public bool $ThirdPartyNameOnly;

	/** >= PROTOCOL_1_19_20 */
	public bool $TrustedSkin;

	/** @required */
	public int $UIProfile;
}
