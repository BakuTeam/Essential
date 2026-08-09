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
use pocketmine\network\mcpe\protocol\types\EntityDiagnosticTimingInfo;
use pocketmine\network\mcpe\protocol\types\MemoryCategoryCounter;
use pocketmine\network\mcpe\protocol\types\SystemDiagnosticTimingInfo;
use pocketmine\network\mcpe\protocol\types\WhiskerScopeDataSummary;
use function count;

class ServerboundDiagnosticsPacket extends DataPacket implements ServerboundPacket{
	public const NETWORK_ID = ProtocolInfo::SERVERBOUND_DIAGNOSTICS_PACKET;

	private float $avgFps;
	private float $avgServerSimTickTimeMS;
	private float $avgClientSimTickTimeMS;
	private float $avgBeginFrameTimeMS;
	private float $avgInputTimeMS;
	private float $avgRenderTimeMS;
	private float $avgEndFrameTimeMS;
	private float $avgRemainderTimePercent;
	private float $avgUnaccountedTimePercent;
	/**
	 * @var MemoryCategoryCounter[]
	 * @phpstan-var list<MemoryCategoryCounter>
	 */
	private array $memoryCategoryValues = [];
	/**
	 * @var EntityDiagnosticTimingInfo[]
	 * @phpstan-var list<EntityDiagnosticTimingInfo>
	 */
	private array $entityDiagnostics = [];
	/**
	 * @var SystemDiagnosticTimingInfo[]
	 * @phpstan-var list<SystemDiagnosticTimingInfo>
	 */
	private array $systemDiagnostics = [];
	/**
	 * @var WhiskerScopeDataSummary[]
	 * @phpstan-var list<WhiskerScopeDataSummary>
	 */
	private array $whiskerScopes = [];

	/**
	 * @generate-create-func
	 */
	public static function create(
		float $avgFps,
		float $avgServerSimTickTimeMS,
		float $avgClientSimTickTimeMS,
		float $avgBeginFrameTimeMS,
		float $avgInputTimeMS,
		float $avgRenderTimeMS,
		float $avgEndFrameTimeMS,
		float $avgRemainderTimePercent,
		float $avgUnaccountedTimePercent,
		array $memoryCategoryValues = [],
		array $entityDiagnostics = [],
		array $systemDiagnostics = [],
	) : self{
		$result = new self();
		$result->avgFps = $avgFps;
		$result->avgServerSimTickTimeMS = $avgServerSimTickTimeMS;
		$result->avgClientSimTickTimeMS = $avgClientSimTickTimeMS;
		$result->avgBeginFrameTimeMS = $avgBeginFrameTimeMS;
		$result->avgInputTimeMS = $avgInputTimeMS;
		$result->avgRenderTimeMS = $avgRenderTimeMS;
		$result->avgEndFrameTimeMS = $avgEndFrameTimeMS;
		$result->avgRemainderTimePercent = $avgRemainderTimePercent;
		$result->avgUnaccountedTimePercent = $avgUnaccountedTimePercent;
		$result->memoryCategoryValues = $memoryCategoryValues;
		$result->entityDiagnostics = $entityDiagnostics;
		$result->systemDiagnostics = $systemDiagnostics;
		return $result;
	}

	public function getAvgFps() : float{ return $this->avgFps; }

	public function getAvgServerSimTickTimeMS() : float{ return $this->avgServerSimTickTimeMS; }

	public function getAvgClientSimTickTimeMS() : float{ return $this->avgClientSimTickTimeMS; }

	public function getAvgBeginFrameTimeMS() : float{ return $this->avgBeginFrameTimeMS; }

	public function getAvgInputTimeMS() : float{ return $this->avgInputTimeMS; }

	public function getAvgRenderTimeMS() : float{ return $this->avgRenderTimeMS; }

	public function getAvgEndFrameTimeMS() : float{ return $this->avgEndFrameTimeMS; }

	public function getAvgRemainderTimePercent() : float{ return $this->avgRemainderTimePercent; }

	public function getAvgUnaccountedTimePercent() : float{ return $this->avgUnaccountedTimePercent; }

	/**
	 * @return MemoryCategoryCounter[]
	 * @phpstan-return list<MemoryCategoryCounter>
	 */
	public function getMemoryCategoryValues() : array{ return $this->memoryCategoryValues; }

	/**
	 * @return EntityDiagnosticTimingInfo[]
	 * @phpstan-return list<EntityDiagnosticTimingInfo>
	 */
	public function getEntityDiagnostics() : array{ return $this->entityDiagnostics; }

	/**
	 * @return SystemDiagnosticTimingInfo[]
	 * @phpstan-return list<SystemDiagnosticTimingInfo>
	 */
	public function getSystemDiagnostics() : array{ return $this->systemDiagnostics; }

	/**
	 * @return WhiskerScopeDataSummary[]
	 * @phpstan-return list<WhiskerScopeDataSummary>
	 */
	public function getWhiskerScopes() : array{ return $this->whiskerScopes; }

	protected function decodePayload(PacketSerializer $in) : void{
		$this->avgFps = $in->getLFloat();
		$this->avgServerSimTickTimeMS = $in->getLFloat();
		$this->avgClientSimTickTimeMS = $in->getLFloat();
		$this->avgBeginFrameTimeMS = $in->getLFloat();
		$this->avgInputTimeMS = $in->getLFloat();
		$this->avgRenderTimeMS = $in->getLFloat();
		$this->avgEndFrameTimeMS = $in->getLFloat();
		$this->avgRemainderTimePercent = $in->getLFloat();
		$this->avgUnaccountedTimePercent = $in->getLFloat();
		if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_0){
			$this->memoryCategoryValues = [];
			for($i = 0, $count = $in->getUnsignedVarInt(); $i < $count; ++$i){
				$this->memoryCategoryValues[] = MemoryCategoryCounter::read($in);
			}
			if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_20){
				$this->entityDiagnostics = [];
				for($i = 0, $count = $in->getUnsignedVarInt(); $i < $count; ++$i){
					$this->entityDiagnostics[] = EntityDiagnosticTimingInfo::read($in);
				}

				$this->systemDiagnostics = [];
				for($i = 0, $count = $in->getUnsignedVarInt(); $i < $count; ++$i){
					$this->systemDiagnostics[] = SystemDiagnosticTimingInfo::read($in);
				}

				if($in->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_30){
					$this->whiskerScopes = [];
					for($i = 0, $count = $in->getUnsignedVarInt(); $i < $count; ++$i){
						$this->whiskerScopes[] = WhiskerScopeDataSummary::read($in);
					}
				}
			}
		}
	}

	protected function encodePayload(PacketSerializer $out) : void{
		$out->putLFloat($this->avgFps);
		$out->putLFloat($this->avgServerSimTickTimeMS);
		$out->putLFloat($this->avgClientSimTickTimeMS);
		$out->putLFloat($this->avgBeginFrameTimeMS);
		$out->putLFloat($this->avgInputTimeMS);
		$out->putLFloat($this->avgRenderTimeMS);
		$out->putLFloat($this->avgEndFrameTimeMS);
		$out->putLFloat($this->avgRemainderTimePercent);
		$out->putLFloat($this->avgUnaccountedTimePercent);
		if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_0){
			$out->putUnsignedVarInt(count($this->memoryCategoryValues));
			foreach($this->memoryCategoryValues as $value){
				$value->write($out);
			}
			if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_20){
				$out->putUnsignedVarInt(count($this->entityDiagnostics));
				foreach($this->entityDiagnostics as $value){
					$value->write($out);
				}

				$out->putUnsignedVarInt(count($this->systemDiagnostics));
				foreach($this->systemDiagnostics as $value){
					$value->write($out);
				}

				if($out->getProtocolId() >= ProtocolInfo::PROTOCOL_1_26_30){
					$out->putUnsignedVarInt(count($this->whiskerScopes));
					foreach($this->whiskerScopes as $value){
						$value->write($out);
					}
				}
			}
		}
	}

	public function handle(PacketHandlerInterface $handler) : bool{
		return $handler->handleServerboundDiagnostics($this);
	}
}
