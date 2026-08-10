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

namespace pocketmine\network\mcpe\protocol;

use pocketmine\network\mcpe\protocol\serializer\PacketSerializer;
use pocketmine\network\mcpe\protocol\types\ChunkPosition;
use pocketmine\network\mcpe\protocol\types\DimensionIds;
use pocketmine\utils\Limits;
use function count;
use const PHP_INT_MAX;

class LevelChunkPacket extends DataPacket implements ClientboundPacket{
	public const NETWORK_ID = ProtocolInfo::LEVEL_CHUNK_PACKET;

	/**
	 * Client will request all subchunks as needed up to the top of the world
	 */
	private const CLIENT_REQUEST_FULL_COLUMN_FAKE_COUNT = Limits::UINT32_MAX;
	/**
	 * Client will request subchunks as needed up to the height written in the packet, and assume that anything above
	 * that height is air (wtf mojang ...)
	 */
	private const CLIENT_REQUEST_TRUNCATED_COLUMN_FAKE_COUNT = Limits::UINT32_MAX - 1;

	//this appears large enough for a world height of 1024 blocks - it may need to be increased in the future
	private const MAX_BLOB_HASHES = 64;

	private ChunkPosition $chunkPosition;
	/** @phpstan-var DimensionIds::* */
	private int $dimensionId;
	private int $subChunkCount;
	private bool $clientSubChunkRequestsEnabled;
	/** @var int[]|null */
	private ?array $usedBlobHashes = null;
	private string $extraPayload;

	/**
	 * @generate-create-func
	 * @param int[] $usedBlobHashes
	 */
	public static function create(ChunkPosition $chunkPosition, int $dimensionId, int $subChunkCount, bool $clientSubChunkRequestsEnabled, ?array $usedBlobHashes, string $extraPayload) : self{
		$result = new self();
		$result->chunkPosition = $chunkPosition;
		$result->dimensionId = $dimensionId;
		$result->subChunkCount = $subChunkCount;
		$result->clientSubChunkRequestsEnabled = $clientSubChunkRequestsEnabled;
		$result->usedBlobHashes = $usedBlobHashes;
		$result->extraPayload = $extraPayload;
		return $result;
	}

	public function getChunkPosition() : ChunkPosition{ return $this->chunkPosition; }

	public function getDimensionId() : int{ return $this->dimensionId; }

	public function getSubChunkCount() : int{
		return $this->subChunkCount;
	}

	public function isClientSubChunkRequestEnabled() : bool{
		return $this->clientSubChunkRequestsEnabled;
	}

	public function isCacheEnabled() : bool{
		return $this->usedBlobHashes !== null;
	}

	/**
	 * @return int[]|null
	 */
	public function getUsedBlobHashes() : ?array{
		return $this->usedBlobHashes;
	}

	public function getExtraPayload() : string{
		return $this->extraPayload;
	}

	protected function decodePayload(PacketSerializer $in) : void{
		$this->chunkPosition = ChunkPosition::read($in);
		if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_20_60){
			$this->dimensionId = $in->getVarInt();
		}

		if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_40){
			$this->subChunkCount = $in->getUnsignedVarInt();
			if($in->getBool()){
				$in->getVarInt(); //sub-chunk request limit - not tracked by this fork
				$this->clientSubChunkRequestsEnabled = true;
			}else{
				$this->clientSubChunkRequestsEnabled = false;
			}
			$cacheEnabled = $in->getBool();
			$count = $in->getUnsignedVarInt();
			if($count > self::MAX_BLOB_HASHES){
				throw new PacketDecodeException("Expected at most " . self::MAX_BLOB_HASHES . " blob hashes, got " . $count);
			}
			$hashes = [];
			for($i = 0; $i < $count; ++$i){
				$hashes[] = $in->getLLong();
			}
			$this->usedBlobHashes = $cacheEnabled ? $hashes : null;
		}else{
			$subChunkCountButNotReally = $in->getUnsignedVarInt();
			if($subChunkCountButNotReally === self::CLIENT_REQUEST_FULL_COLUMN_FAKE_COUNT){
				$this->clientSubChunkRequestsEnabled = true;
				$this->subChunkCount = PHP_INT_MAX;
			}elseif($subChunkCountButNotReally === self::CLIENT_REQUEST_TRUNCATED_COLUMN_FAKE_COUNT){
				$this->clientSubChunkRequestsEnabled = true;
				$this->subChunkCount = $in->getLShort();
			}else{
				$this->clientSubChunkRequestsEnabled = false;
				$this->subChunkCount = $subChunkCountButNotReally;
			}

			$cacheEnabled = $in->getBool();
			if($cacheEnabled){
				$this->usedBlobHashes = [];
				$count = $in->getUnsignedVarInt();
				if($count > self::MAX_BLOB_HASHES){
					throw new PacketDecodeException("Expected at most " . self::MAX_BLOB_HASHES . " blob hashes, got " . $count);
				}
				for($i = 0; $i < $count; ++$i){
					$this->usedBlobHashes[] = $in->getLLong();
				}
			}
		}
		$this->extraPayload = $in->getString();
	}

	protected function encodePayload(PacketSerializer $out) : void{
		$this->chunkPosition->write($out);
		if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_20_60){
			$out->putVarInt($this->dimensionId);
		}

		if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_40){
			//1.26.40 dropped the fake-count sentinels in favour of an explicit optional sub-chunk request limit,
			//and always writes the blob hash list regardless of the cache flag.
			$out->putUnsignedVarInt($this->subChunkCount === PHP_INT_MAX ? 0 : $this->subChunkCount);
			$out->putBool(false); //sub-chunk request limit - this fork always sends full chunks inline
			$out->putBool($this->usedBlobHashes !== null);
			$out->putUnsignedVarInt($this->usedBlobHashes !== null ? count($this->usedBlobHashes) : 0);
			if($this->usedBlobHashes !== null){
				foreach($this->usedBlobHashes as $hash){
					$out->putLLong($hash);
				}
			}
		}else{
			if($this->clientSubChunkRequestsEnabled && $out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_18_10){
				if($this->subChunkCount === PHP_INT_MAX){
					$out->putUnsignedVarInt(self::CLIENT_REQUEST_FULL_COLUMN_FAKE_COUNT);
				}else{
					$out->putUnsignedVarInt(self::CLIENT_REQUEST_TRUNCATED_COLUMN_FAKE_COUNT);
					$out->putLShort($this->subChunkCount);
				}
			}else{
				$out->putUnsignedVarInt($this->subChunkCount);
			}

			$out->putBool($this->usedBlobHashes !== null);
			if($this->usedBlobHashes !== null){
				$out->putUnsignedVarInt(count($this->usedBlobHashes));
				foreach($this->usedBlobHashes as $hash){
					$out->putLLong($hash);
				}
			}
		}
		$out->putString($this->extraPayload);
	}

	public function handle(PacketHandlerInterface $handler) : bool{
		return $handler->handleLevelChunk($this);
	}
}
