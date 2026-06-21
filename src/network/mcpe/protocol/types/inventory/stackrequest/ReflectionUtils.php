<?php

/*
 *
 *  ____            _        _   __  __ _                  __  __ ____
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine Team
 * @link http://www.pocketmine.net/
 *
 *
 */

declare(strict_types=1);

namespace pocketmine\network\mcpe\protocol\types\inventory\stackrequest;

use ReflectionClass;
use ReflectionException;

final class ReflectionUtils{
	private static $propCache = [];
	private static $methCache = [];

	/**
	 * @throws ReflectionException
	 */
	public static function setProperty(string $className, object $instance, string $propertyName, $value) : void{
		if(!isset(self::$propCache[$k = "$className - $propertyName"])){
			$refClass = new ReflectionClass($className);
			$refProp = $refClass->getProperty($propertyName);
			$refProp->setAccessible(true);
		}else{
			$refProp = self::$propCache[$k];
		}
		$refProp->setValue($instance, $value);
	}

	/**
	 * @throws ReflectionException
	 */
	public static function getProperty(string $className, object $instance, string $propertyName) : mixed{
		if(!isset(self::$propCache[$k = "$className - $propertyName"])){
			$refClass = new ReflectionClass($className);
			$refProp = $refClass->getProperty($propertyName);
			$refProp->setAccessible(true);
		}else{
			$refProp = self::$propCache[$k];
		}
		return $refProp->getValue($instance);
	}

	/**
	 * @param mixed ...$args
	 *
	 * @throws ReflectionException
	 */
	public static function invokeStatic(string $className, string $methodName, ...$args) : mixed{
		if(!isset(self::$methCache[$k = "$className - $methodName"])){
			$refClass = new ReflectionClass($className);
			$refMeth = $refClass->getMethod($methodName);
			$refMeth->setAccessible(true);
		}else{
			$refMeth = self::$methCache[$k];
		}
		return $refMeth->invoke(null, ...$args);
	}

	/**
	 * @param mixed ...$args
	 *
	 * @throws ReflectionException
	 */
	public static function invoke(string $className, object $instance, string $methodName, ...$args) : mixed{
		if(!isset(self::$methCache[$k = "$className - $methodName"])){
			$refClass = new ReflectionClass($className);
			$refMeth = $refClass->getMethod($methodName);
			$refMeth->setAccessible(true);
		}else{
			$refMeth = self::$methCache[$k];
		}
		return $refMeth->invoke($instance, ...$args);
	}
}
