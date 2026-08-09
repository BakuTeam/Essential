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

namespace pocketmine\network\mcpe\serializer;

use pocketmine\block\tile\Spawnable;
use pocketmine\data\bedrock\BiomeIds;
use pocketmine\data\bedrock\LegacyBiomeIdToStringIdMap;
use pocketmine\nbt\TreeRoot;
use pocketmine\network\mcpe\convert\BlockTranslator;
use pocketmine\network\mcpe\convert\TypeConverter;
use pocketmine\network\mcpe\protocol\ProtocolInfo;
use pocketmine\network\mcpe\protocol\serializer\NetworkNbtSerializer;
use pocketmine\network\mcpe\protocol\serializer\PacketSerializer;
use pocketmine\network\mcpe\protocol\types\DimensionIds;
use pocketmine\utils\Binary;
use pocketmine\utils\BinaryStream;
use pocketmine\world\format\Chunk;
use pocketmine\world\format\PalettedBlockArray;
use pocketmine\world\format\SubChunk;
use function chr;
use function count;
use function min;
use function str_repeat;

final class ChunkSerializer{

	public const LOWER_PADDING_SIZE = 4;

	private function __construct(){
		//NOOP
	}

	/**
	 * Returns the min/max subchunk index expected in the protocol.
	 * This has no relation to the world height supported by PM.
	 *
	 * @phpstan-param DimensionIds::* $dimensionId
	 * @return int[]
	 * @phpstan-return array{int, int}
	 */
	public static function getDimensionChunkBounds(int $dimensionId) : array{
		return match($dimensionId){
			DimensionIds::OVERWORLD => [-4, 19],
			DimensionIds::NETHER => [0, 7],
			DimensionIds::THE_END => [0, 15],
			default => throw new \InvalidArgumentException("Unknown dimension ID $dimensionId"),
		};
	}

	/**
	 * Returns the number of subchunks that will be sent from the given chunk.
	 * Chunks are sent in a stack, so every chunk below the top non-empty one must be sent.
	 *
	 * @phpstan-param DimensionIds::* $dimensionId
	 */
	public static function getSubChunkCount(Chunk $chunk, int $dimensionId) : int{
		//if the protocol world bounds ever exceed the PM supported bounds again in the future, we might need to
		//polyfill some stuff here
		[$minSubChunkIndex, $maxSubChunkIndex] = self::getDimensionChunkBounds($dimensionId);
		for($y = $maxSubChunkIndex, $count = $maxSubChunkIndex - $minSubChunkIndex + 1; $y >= $minSubChunkIndex; --$y, --$count){
			if($chunk->getSubChunk($y)->isEmptyFast()){
				continue;
			}
			return $count;
		}

		return 0;

		// for($count = count($chunk->getSubChunks()); $count > 0; --$count){
		// 	if($chunk->getSubChunk($count - 1)->isEmptyFast()){
		// 		continue;
		// 	}
		// 	return $count;
		// }

		// return 0;
	}

	/**
	 * @phpstan-param DimensionIds::* $dimensionId
	 * @return string[]
	 */
	public static function serializeSubChunks(Chunk $chunk, int $dimensionId, TypeConverter $typeConverter) : array {
		$stream = PacketSerializer::encoder($typeConverter->getProtocolId());
		$subChunks = [];

		$protocolId = $typeConverter->getProtocolId();
		$subChunkCount = self::getSubChunkCount($chunk, $dimensionId);
		$writtenCount = 0;

		[$minSubChunkIndex, $maxSubChunkIndex] = self::getDimensionChunkBounds($dimensionId);

		// For pre-1.18 protocols (no negative Y / extra chunks)
		if ($protocolId < ProtocolInfo::PROTOCOL_1_18_0) {
			// Limit to old height range (0–15 in overworld)
			$minSubChunkIndex = 0;
			$maxSubChunkIndex = 15;
			$subChunkCount = ($maxSubChunkIndex - $minSubChunkIndex + 1);
		}

		for ($y = $minSubChunkIndex; $writtenCount < $subChunkCount; ++$y, ++$writtenCount) {
			$subChunkStream = clone $stream;
			self::serializeSubChunk(
				$chunk->getSubChunk($y),
				$typeConverter->getBlockTranslator(),
				$subChunkStream,
				false
			);
			$subChunks[] = $subChunkStream->getBuffer();
		}

		return $subChunks;
	}

	// public static function serializeSubChunks(Chunk $chunk, int $dimensionId, TypeConverter $typeConverter) : array
	// {
	// 	$stream = PacketSerializer::encoder($typeConverter->getProtocolId());

	// 	// $emptyChunkStream = clone $stream;
	// 	// $emptyChunkStream->putByte(8); //subchunk version 8
	// 	// $emptyChunkStream->putByte(0); //0 layers - client will treat this as all-air

	// 	$subChunks = [];

	// 	// if($typeConverter->getProtocolId() >= ProtocolInfo::PROTOCOL_1_18_0 && $dimensionId === DimensionIds::OVERWORLD){
	// 	// 	//TODO: HACK! fill in fake subchunks to make up for the new negative space client-side
	// 	// 	for($y = 0; $y < self::LOWER_PADDING_SIZE; $y++){
	// 	// 		$subChunks[] = $emptyChunkStream->getBuffer();
	// 	// 	}
	// 	// }

	// 	$subChunkCount = self::getSubChunkCount($chunk, $dimensionId);
	// 	$writtenCount = 0;

	// 	[$minSubChunkIndex, ] = self::getDimensionChunkBounds($dimensionId);
	// 	for($y = Chunk::MIN_SUBCHUNK_INDEX; $writtenCount < $subChunkCount; ++$y, ++$writtenCount){ //$minSubChunkIndex
	// 	 	$subChunkStream = clone $stream;
	// 	 	self::serializeSubChunk($chunk->getSubChunk($y), $typeConverter->getBlockTranslator(), $subChunkStream, false);
	// 	 	$subChunks[] = $subChunkStream->getBuffer();
	// 	}

	// 	return $subChunks;
	// }

	/**
	 * @phpstan-param DimensionIds::* $dimensionId
	 */
	public static function serializeFullChunk(Chunk $chunk, int $dimensionId, TypeConverter $typeConverter, ?string $tiles = null) : string{
		$stream = PacketSerializer::encoder($typeConverter->getProtocolId());

		// foreach(self::serializeSubChunks($chunk, $dimensionId, $typeConverter) as $subChunk){
		// 	$stream->put($subChunk);
		// }

		// self::serializeBiomes($chunk, $dimensionId, $stream);
		// self::serializeChunkData($chunk, $stream, $typeConverter, $tiles);

		// return $stream->getBuffer();

		if($typeConverter->getProtocolId() >= ProtocolInfo::PROTOCOL_1_18_0){
			foreach(self::serializeSubChunks($chunk, $dimensionId, $typeConverter) as $subChunk){
				$stream->put($subChunk);
			}

			self::serializeBiomes($chunk, $dimensionId, $stream);
			self::serializeChunkData($chunk, $stream, $typeConverter, $tiles);

		} else {
			$subChunkCount = min(self::getSubChunkCount($chunk, $dimensionId), 16);

			for($y = 0; $y < $subChunkCount; ++$y){
				self::serializeSubChunk($chunk->getSubChunk($y), $typeConverter->getBlockTranslator(), $stream, false);
			}

			$biome = str_repeat(chr(BiomeIds::OCEAN), 256); //2d biome array
			for($x = 0; $x < 16; ++$x){
				for($z = 0; $z < 16; ++$z){
					$biome[($z << 4) | $x] = chr($chunk->getBiomeId($x, $chunk->getHighestBlockAt($x, $z) ?? BiomeIds::OCEAN, $z));
				}
			}
			$stream->put($biome);

			$stream->putByte(0); //border block array count
			//Border block entry format: 1 byte (4 bits X, 4 bits Z). These are however useless since they crash the regular client.

			if($tiles !== null){
				$stream->put($tiles);
			}else{
				$stream->put(self::serializeTiles($chunk, $typeConverter));
			}
		}
		return $stream->getBuffer();
	}

	/**
	 * @phpstan-param DimensionIds::* $dimensionId
	 */
	public static function serializeBiomes(Chunk $chunk, int $dimensionId, PacketSerializer $stream) : void{
		//if($stream->getProtocolId() >= ProtocolInfo::PROTOCOL_1_18_0){
			[$minSubChunkIndex, $maxSubChunkIndex] = self::getDimensionChunkBounds($dimensionId);
			$biomeIdMap = LegacyBiomeIdToStringIdMap::getInstance();
			//all biomes must always be written :(
			for($y = $minSubChunkIndex; $y <= $maxSubChunkIndex; ++$y){
				self::serializeBiomePalette($chunk->getSubChunk($y)->getBiomeArray(), $biomeIdMap, $stream);
			}
		//}else{
			//$stream->put($chunk->getBiomeIdArray());
		//}
	}

	public static function serializeBorderBlocks(PacketSerializer $stream) : void {
		$stream->putByte(0); //border block array count
		//Border block entry format: 1 byte (4 bits X, 4 bits Z). These are however useless since they crash the regular client.
	}

	public static function serializeChunkData(Chunk $chunk, PacketSerializer $stream, TypeConverter $typeConverter, ?string $tiles = null) : void{
		self::serializeBorderBlocks($stream);

		if($tiles !== null){
			$stream->put($tiles);
		}else{
			$stream->put(self::serializeTiles($chunk, $typeConverter));
		}
	}

	public static function serializeSubChunk(SubChunk $subChunk, BlockTranslator $blockTranslator, PacketSerializer $stream, bool $persistentBlockStates) : void{
		$layers = $subChunk->getBlockLayers();
		$stream->putByte(8); //version

		$stream->putByte(count($layers));

		$blockStateDictionary = $blockTranslator->getBlockStateDictionary();

		$fallbackBlockId = 2; //grass
		$fallbackBlockState = $blockStateDictionary->generateDataFromStateId($fallbackBlockId);
		$infoUpdateData = $blockTranslator->getFallbackStateData();
		$infoUpdateNetworkId = $blockStateDictionary->lookupStateIdFromData($infoUpdateData);

		foreach($layers as $blocks){
			$bitsPerBlock = $blocks->getBitsPerBlock();

			$words = $blocks->getWordArray();

			$stream->putByte(($bitsPerBlock << 1) | ($persistentBlockStates ? 0 : 1));
			$stream->put($words);
			$palette = $blocks->getPalette();

			if($bitsPerBlock !== 0){
				//these LSHIFT by 1 uvarints are optimizations: the client expects zigzag varints here
				//but since we know they are always unsigned, we can avoid the extra fcall overhead of
				//zigzag and just shift directly.
				$stream->putUnsignedVarInt(count($palette) << 1); //yes, this is intentionally zigzag
			}
			if($persistentBlockStates){
				$nbtSerializer = new NetworkNbtSerializer();
				foreach($palette as $p){
					try{
						//TODO: introduce a binary cache for this
						$networkId = $blockTranslator->internalIdToNetworkId($p);
						$state = $blockStateDictionary->generateDataFromStateId($networkId);
						if($state === null){
							$state = $infoUpdateData;
						}

						if($state->getName() === $infoUpdateData->getName() && $fallbackBlockState !== null){
							$state = $fallbackBlockState;
						}

						$stream->put($nbtSerializer->write(new TreeRoot($state->toNbt())));
					}catch(\Throwable $e){
						$state = $fallbackBlockState ?? $infoUpdateData;
						$stream->put($nbtSerializer->write(new TreeRoot($state->toNbt())));
					}
				}
			}else{
				foreach($palette as $p){
					try{
						$networkId = $blockTranslator->internalIdToNetworkId($p);

						if($networkId === $infoUpdateNetworkId && $fallbackBlockId > 0){
							$networkId = $fallbackBlockId;
						}

						$stream->put(Binary::writeVarInt($networkId));
					}catch(\Throwable $e){
						$stream->put(Binary::writeVarInt($fallbackBlockId));
					}
				}
			}
		}
	}

	private static function serializeBiomePalette(PalettedBlockArray $biomePalette, LegacyBiomeIdToStringIdMap $biomeIdMap, PacketSerializer $stream) : void{
		$biomePaletteBitsPerBlock = $biomePalette->getBitsPerBlock();
		$stream->putByte(($biomePaletteBitsPerBlock << 1) | 1); //the last bit is non-persistence (like for blocks), though it has no effect on biomes since they always use integer IDs
		$stream->put($biomePalette->getWordArray());

		//these LSHIFT by 1 uvarints are optimizations: the client expects zigzag varints here
		//but since we know they are always unsigned, we can avoid the extra fcall overhead of
		//zigzag and just shift directly.
		$biomePaletteArray = $biomePalette->getPalette();
		if($biomePaletteBitsPerBlock !== 0){
			$stream->putUnsignedVarInt(count($biomePaletteArray) << 1);
		}

		foreach($biomePaletteArray as $p){
			if($biomeIdMap->legacyToString($p) === null){
				//make sure we aren't sending bogus biomes - the 1.18.0 client crashes if we do this
				$p = BiomeIds::OCEAN;
			}
			$stream->put(Binary::writeUnsignedVarInt($p << 1));
		}
	}

	public static function serializeTiles(Chunk $chunk, TypeConverter $typeConverter) : string{
		$stream = new BinaryStream();
		foreach($chunk->getTiles() as $tile){
			if($tile instanceof Spawnable){
				$stream->put($tile->getSerializedSpawnCompound($typeConverter)->getEncodedNbt());
			}
		}

		return $stream->getBuffer();
	}
}
