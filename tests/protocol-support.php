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

$oldest = ProtocolInfo::PROTOCOL_1_14_60;
if(!in_array($oldest, ProtocolInfo::ACCEPTED_PROTOCOL, true)){
	throw new RuntimeException("Protocol $oldest is not accepted");
}

$legacyPalette = LegacyBlockPaletteProvider::getPalette($oldest);
$blockTranslator = TypeConverter::getInstance($oldest)->getBlockTranslator();
if(count($legacyPalette) !== count($blockTranslator->getBlockStateDictionary()->getStates())){
	throw new RuntimeException("Protocol $oldest block palette doesn't match the block state dictionary");
}
if($blockTranslator->internalIdToNetworkId(VanillaBlocks::STONE()->getStateId()) !== 1){
	throw new RuntimeException("Protocol $oldest should map minecraft:stone to runtime ID 1");
}

echo "Bedrock 1.26.45 protocol smoke test passed (protocol $protocol, item schema $schemaId).\n";
echo "Bedrock 1.14.60 protocol smoke test passed (protocol $oldest, " . count($legacyPalette) . " block states).\n";
