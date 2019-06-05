<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

/** @var array $arCurrentValues */

global $APPLICATION;
global $USER_FIELD_MANAGER;
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);
if(!\Bitrix\Main\Loader::includeModule("iblock"))
	return;

$arIBlockType = CIBlockParameters::GetIBlockTypes();

$arIBlock = array();
$rsIBlock = CIBlock::GetList(Array("sort" => "asc"), Array("TYPE" => $arCurrentValues["IBLOCK_TYPE"], "ACTIVE"=>"Y"));
while ($arr = $rsIBlock->Fetch() ) {
	$arIBlock[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];
}

if(isset($arCurrentValues["IBLOCK_ID"])) {
	$dbFoundFields = CIBlockElement::GetList(
		array('SORT' => 'ASC'),
		array(
			'IBLOCK_ID' => $arCurrentValues["IBLOCK_ID"],
			'ACTIVE' => 'Y'
		),
		false,
		array(),
		array()
	);

	while ($dbFoundFieldsList = $dbFoundFields->Fetch()) {
		$arFields[$dbFoundFieldsList['ID']] = "[" . $dbFoundFieldsList["ID"] . "] " . $dbFoundFieldsList["NAME"];
	}
}


$arComponentParameters = array(
	'GROUPS'     => array(),
	'PARAMETERS' => array(
		"IBLOCK_TYPE" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => Loc::GetMessage("FLXMD_CF_IBLOCK_TYPE"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlockType,
			"REFRESH" => "Y",
		),
		"IBLOCK_ID" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => Loc::GetMessage("FLXMD_CF_IBLOCK_ID"),
			"TYPE" => "LIST",
			"ADDITIONAL_VALUES" => "Y",
			"VALUES" => $arIBlock,
			"REFRESH" => "Y",
		),
		"FORM_FIELDS" =>array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => Loc::GetMessage("FLXMD_CF_FORM_FIELDS"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"ADDITIONAL_VALUES" => "Y",
			"VALUES" => $arFields,
			"REFRESH" => "Y"
		),
		"FORM_NAME" => array(
			"PARENT" => "BASE",
			"NAME" => Loc::GetMessage("FLXMD_CF_FORM_NAME"),
			"TYPE" => "LINE"
		),
		"FORM_BUTTON_TEXT" => array(
			"PARENT" => "BASE",
			"NAME" => Loc::GetMessage("FLXMD_CF_FORM_NAME_BUTTON"),
			"TYPE" => "LINE"
		),
		"MAIL_TEMPLATE" => array(
			"PARENT" => "BASE",
			"NAME" => Loc::GetMessage('FLXMD_CF_MAIL_TEMPLATE'),
			"TYPE" => "LINE"
		),
		"FORM_KEY" => array(
			"PARENT" => "BASE",
			"NAME" => Loc::GetMessage('FLXMD_CF_FORM_KEY'),
			"TYPE" => "LINE"
		),
		"ID_FORM" => array(
			"PARENT" => "BASE",
			"NAME" => Loc::GetMessage('FLXMD_CF_FORM_CLASS'),
			"TYPE" => "LINE"
		),
		"SUCCESS_TEMPLATE" => array(
			"PARENT" => "BASE",
			"NAME" => Loc::GetMessage('FLXMD_CF_FORM_CLASS_SUCCESS'),
			"TYPE" => "LINE"
		),
		"TEXT_INTRO" => Array(
			"PARENT"   => "DATA_SOURSE",
			"NAME"      => GetMessage('FLXMD_CF_SUCCESS_TEMPLATE'),
			"TYPE"      => "CUSTOM",
			"JS_FILE"   =>  str_replace("{$_SERVER["DOCUMENT_ROOT"]}", "",  __DIR__) . "/settings.js",
			"JS_EVENT"   => "OnTextAreaConstruct",
			"DEFAULT"   => null,
		),
	)
);
