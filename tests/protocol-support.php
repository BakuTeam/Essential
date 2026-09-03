<?php

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

use pocketmine\network\mcpe\convert\ItemTranslator;
use pocketmine\network\mcpe\convert\TypeConverter;
use pocketmine\network\mcpe\protocol\ProtocolInfo;

$protocol = ProtocolInfo::PROTOCOL_1_26_45;

if(!in_array($protocol, ProtocolInfo::ACCEPTED_PROTOCOL, true)){
	throw new RuntimeException("Protocol $protocol is not accepted");
}

$schemaId = ItemTranslator::getItemSchemaId($protocol);
$expectedSchemaId = ItemTranslator::getItemSchemaId(ProtocolInfo::PROTOCOL_1_26_40);

if($schemaId !== $expectedSchemaId){
	throw new RuntimeException("Protocol $protocol uses item schema $schemaId, expected $expectedSchemaId");
}

foreach(ProtocolInfo::ACCEPTED_PROTOCOL as $acceptedProtocol){
	$typeConverter = TypeConverter::getInstance($acceptedProtocol);
	$typeConverter->getItemTypeDictionary()->fromStringId('minecraft:stone');
}

echo "Bedrock 1.26.45 protocol smoke test passed (protocol $protocol, item schema $schemaId).\n";
