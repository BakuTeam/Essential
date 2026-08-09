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
use pocketmine\network\mcpe\protocol\types\SerializableVoxelShape;
use function count;

class VoxelShapesPacket extends DataPacket implements ClientboundPacket{
	public const NETWORK_ID = ProtocolInfo::VOXEL_SHAPES_PACKET;

	/**
	 * @var SerializableVoxelShape[]
	 * @phpstan-var list<SerializableVoxelShape>
	 */
	private array $shapes;
	/**
	 * @var int[]
	 * @phpstan-var array<string, int>
	 */
	private array $nameMap;
	private int $customShapeCount;

	/**
	 * @generate-create-func
	 * @param SerializableVoxelShape[] $shapes
	 * @param int[]                    $nameMap
	 * @phpstan-param list<SerializableVoxelShape> $shapes
	 * @phpstan-param array<string, int>           $nameMap
	 */
	public static function create(array $shapes, array $nameMap, int $customShapeCount = 0) : self{
		$result = new self();
		$result->shapes = $shapes;
		$result->nameMap = $nameMap;
		$result->customShapeCount = $customShapeCount;
		return $result;
	}

	/**
	 * @return SerializableVoxelShape[]
	 * @phpstan-return list<SerializableVoxelShape>
	 */
	public function getShapes() : array{ return $this->shapes; }

	/**
	 * @return int[]
	 * @phpstan-return array<string, int>
	 */
	public function getNameMap() : array{ return $this->nameMap; }

	public function getCustomShapeCount() : int{ return $this->customShapeCount; }

	protected function decodePayload(PacketSerializer $in) : void{
		$this->shapes = [];
		for($i = 0, $shapesCount = $in->getUnsignedVarInt(); $i < $shapesCount; ++$i){
			$this->shapes[] = SerializableVoxelShape::read($in);
		}
		$this->nameMap = [];
		for($i = 0, $namesCount = $in->getUnsignedVarInt(); $i < $namesCount; ++$i){
			$this->nameMap[$in->getString()] = $in->getLShort();
		}
		$this->customShapeCount = $in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_10 ? $in->getLShort() : 0;
	}

	protected function encodePayload(PacketSerializer $out) : void{
		$out->putUnsignedVarInt(count($this->shapes));
		foreach($this->shapes as $shape){
			$shape->write($out);
		}
		$out->putUnsignedVarInt(count($this->nameMap));
		foreach($this->nameMap as $name => $id){
			$out->putString($name);
			$out->putLShort($id);
		}
		if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_10){
			$out->putLShort($this->customShapeCount);
		}
	}

	public function handle(PacketHandlerInterface $handler) : bool{
		return $handler->handleVoxelShapes($this);
	}
}
