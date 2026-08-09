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

namespace pocketmine\network\mcpe\protocol\types\camera;

use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\serializer\PacketSerializer;
use function count;

final class CameraSplineInstruction{
	/**
	 * @see CameraSetInstructionEaseType
	 *
	 * @param Vector3[]              $curve
	 * @param CameraProgressOption[] $progressKeyFrames
	 * @param CameraRotationOption[] $rotationOptions
	 * @phpstan-param list<Vector3> $curve
	 * @phpstan-param list<CameraProgressOption> $progressKeyFrames
	 * @phpstan-param list<CameraRotationOption> $rotationOptions
	 */
	public function __construct(
		private float $totalTime,
		private int $easeType,
		private array $curve,
		private array $progressKeyFrames,
		private array $rotationOptions,
		private string $splineIdentifier,
		private bool $loadFromJson
	){}

	public function getTotalTime() : float{ return $this->totalTime; }

	public function getEaseType() : int{ return $this->easeType; }

	/**
	 * @return Vector3[]
	 * @phpstan-return list<Vector3>
	 */
	public function getCurve() : array{ return $this->curve; }

	/**
	 * @return CameraProgressOption[]
	 * @phpstan-return list<CameraProgressOption>
	 */
	public function getProgressKeyFrames() : array{ return $this->progressKeyFrames; }

	/**
	 * @return CameraRotationOption[]
	 * @phpstan-return list<CameraRotationOption>
	 */
	public function getRotationOptions() : array{ return $this->rotationOptions; }

	public function getSplineIdentifier() : string{ return $this->splineIdentifier; }

	public function isLoadFromJson() : bool{ return $this->loadFromJson; }

	public static function read(PacketSerializer $in) : self{
		$totalTime = $in->getLFloat();
		$easeType = $in->getByte();
		$curve = [];
		for($i = 0, $count = $in->getUnsignedVarInt(); $i < $count; ++$i){
			$curve[] = $in->getVector3();
		}
		$progressKeyFrames = [];
		for($i = 0, $count = $in->getUnsignedVarInt(); $i < $count; ++$i){
			$progressKeyFrames[] = CameraProgressOption::read($in);
		}
		$rotationOptions = [];
		for($i = 0, $count = $in->getUnsignedVarInt(); $i < $count; ++$i){
			$rotationOptions[] = CameraRotationOption::read($in);
		}

		return new self(
			$totalTime,
			$easeType,
			$curve,
			$progressKeyFrames,
			$rotationOptions,
			$in->getString(),
			$in->getBool()
		);
	}

	public function write(PacketSerializer $out) : void{
		$out->putLFloat($this->totalTime);
		$out->putByte($this->easeType);
		$out->putUnsignedVarInt(count($this->curve));
		foreach($this->curve as $point){
			$out->putVector3($point);
		}
		$out->putUnsignedVarInt(count($this->progressKeyFrames));
		foreach($this->progressKeyFrames as $keyFrame){
			$keyFrame->write($out);
		}
		$out->putUnsignedVarInt(count($this->rotationOptions));
		foreach($this->rotationOptions as $option){
			$option->write($out);
		}
		$out->putString($this->splineIdentifier);
		$out->putBool($this->loadFromJson);
	}
}
