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

namespace pocketmine\network\mcpe\encryption;

use pocketmine\network\mcpe\JwtUtils;
use pocketmine\scheduler\AsyncTask;
use pocketmine\utils\AssumptionFailedError;
use function igbinary_serialize;
use function igbinary_unserialize;
use function openssl_error_string;
use function openssl_pkey_get_details;
use function openssl_pkey_new;
use function random_bytes;

class PrepareEncryptionTask extends AsyncTask{

	private const TLS_KEY_ON_COMPLETION = "completion";

	private static ?\OpenSSLAsymmetricKey $SERVER_PRIVATE_KEY = null;

	private string $serverPrivateKey;

	private ?string $aesKey = null;
	private ?string $handshakeJwt = null;

	/**
	 * @phpstan-param \Closure(string $encryptionKey, string $handshakeJwt) : void $onCompletion
	 */
	public function __construct(
		private string $clientPub,
		\Closure $onCompletion
	){
		if(self::$SERVER_PRIVATE_KEY === null){
			$serverPrivateKey = openssl_pkey_new(["ec" => ["curve_name" => "secp384r1"]]);
			if($serverPrivateKey === false){
				throw new \RuntimeException("openssl_pkey_new() failed: " . openssl_error_string());
			}
			self::$SERVER_PRIVATE_KEY = $serverPrivateKey;
		}

		$this->serverPrivateKey = igbinary_serialize(openssl_pkey_get_details(self::$SERVER_PRIVATE_KEY));
		$this->storeLocal(self::TLS_KEY_ON_COMPLETION, $onCompletion);
	}

	public function onRun() : void{
		/** @var mixed[] $serverPrivDetails */
		$serverPrivDetails = igbinary_unserialize($this->serverPrivateKey);
		$serverPriv = openssl_pkey_new($serverPrivDetails);
		if($serverPriv === false) throw new AssumptionFailedError("Failed to restore server signing key from details");
		$clientPub = JwtUtils::parseDerPublicKey($this->clientPub);
		$sharedSecret = EncryptionUtils::generateSharedSecret($serverPriv, $clientPub);

		$salt = random_bytes(16);
		$this->aesKey = EncryptionUtils::generateKey($sharedSecret, $salt);
		$this->handshakeJwt = EncryptionUtils::generateServerHandshakeJwt($serverPriv, $salt);
	}

	public function onCompletion() : void{
		/**
		 * @var \Closure $callback
		 * @phpstan-var \Closure(string $encryptionKey, string $handshakeJwt) : void $callback
		 */
		$callback = $this->fetchLocal(self::TLS_KEY_ON_COMPLETION);
		if($this->aesKey === null || $this->handshakeJwt === null){
			throw new AssumptionFailedError("Something strange happened here ...");
		}
		$callback($this->aesKey, $this->handshakeJwt);
	}
}
