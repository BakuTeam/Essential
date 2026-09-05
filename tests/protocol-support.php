<?php

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

use pocketmine\block\VanillaBlocks;
use pocketmine\network\mcpe\convert\ItemTranslator;
use pocketmine\network\mcpe\convert\LegacyBlockPaletteProvider;
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

$legacyProtocols = [
	'1.14.60' => ProtocolInfo::PROTOCOL_1_14_60,
	'1.14.0' => ProtocolInfo::PROTOCOL_1_14_0,
];

echo "Bedrock 1.26.45 protocol smoke test passed (protocol $protocol, item schema $schemaId).\n";

foreach($legacyProtocols as $legacyVersion => $legacyProtocol){
	if(!in_array($legacyProtocol, ProtocolInfo::ACCEPTED_PROTOCOL, true)){
		throw new RuntimeException("Protocol $legacyProtocol is not accepted");
	}

	if(!LegacyBlockPaletteProvider::isRequired($legacyProtocol)){
		throw new RuntimeException("Protocol $legacyProtocol should use the legacy block palette");
	}

	$legacyPalette = LegacyBlockPaletteProvider::getPalette($legacyProtocol);
	$blockTranslator = TypeConverter::getInstance($legacyProtocol)->getBlockTranslator();
	if(count($legacyPalette) !== count($blockTranslator->getBlockStateDictionary()->getStates())){
		throw new RuntimeException("Protocol $legacyProtocol block palette doesn't match the block state dictionary");
	}
	if($blockTranslator->internalIdToNetworkId(VanillaBlocks::STONE()->getStateId()) !== 1){
		throw new RuntimeException("Protocol $legacyProtocol should map minecraft:stone to runtime ID 1");
	}

	$legacySchemaId = ItemTranslator::getItemSchemaId($legacyProtocol);
	if($legacySchemaId !== 11){
		throw new RuntimeException("Protocol $legacyProtocol uses item schema $legacySchemaId, expected 11");
	}

	echo "Bedrock $legacyVersion protocol smoke test passed (protocol $legacyProtocol, " . count($legacyPalette) . " block states, item schema $legacySchemaId).\n";
}
