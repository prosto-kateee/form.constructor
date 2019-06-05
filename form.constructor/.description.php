<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("FLXMD_FORM_CONSTRUCT_COMPONENT_NAME"),
	"DESCRIPTION" => GetMessage("FLXMD_FORM_CONSTRUCT_COMPONENT_DESC"),
	"ICON" => "/images/news_detail.gif",
	"SORT" => 1,
	"CACHE_PATH" => "Y",
	"PATH" => array(
		"ID" => "flxmd",
		"NAME" => GetMessage("FLXMD_FORM_CONSTRUCT_NAME"),
		"SORT" => 10,
	),
);

?>
