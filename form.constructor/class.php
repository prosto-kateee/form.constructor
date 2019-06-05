<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
/** @var CBitrixComponent $this */
/** @var array $arParams */
/** @var array $arResult */
/** @var string $componentPath */
/** @var string $componentName */
/** @var string $componentTemplate */
/** @global CDatabase $DB */
/** @global CUser $USER */

/** @global CMain $APPLICATION */

use Bitrix\Iblock;
use Bitrix\Main\Context;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Type\DateTime;
use Bitrix\Main\Application;

if (!Loader::includeModule('iblock')) {
	ShowError(Loc::getMessage('IBLOCK_MODULE_NOT_INSTALLED'));

	return;
}

class ConstructorForms extends CBitrixComponent {

	public $request = array();
	public $postRes = [];
	private $idAblockAddMessages = 12;
	private $arSort = [];

	public function executeComponent() {
		$this->request = Application::getInstance()->getContext()->getRequest();

		if(
			empty($this->arParams["IBLOCK_TYPE"]) ||
			!is_numeric($this->arParams["IBLOCK_ID"]) ||
			count($this->arParams["FORM_FIELDS"]) <= 0
		)
			return;

		try {
			if ($this->StartResultCache(false)) {
				global $APPLICATION;

				$this->getFormFields();
				if (empty($this->arFoundFields)) {
					$APPLICATION->RestartBuffer();
					echo 'N';
					die();
				}
				$this->setFormFields();
				if (empty($this->arResult['ITEMS'])) {
					$APPLICATION->RestartBuffer();
					echo 'N';
					die();
				}
				$this->controller();
			}
		} catch (Exception $exc) {
			$this->abortResultCache();
		}
	}

	public function controller() {
		$this->postRes = $this->request->getPostList();

		if (
			$this->request->isAjaxRequest() &&
			$this->request->getPost('ajax_form') === 'Y' &&
			$this->request->getPost('ajax_form_key') === md5($this->arParams['FORM_KEY']) &&
			empty($this->request->getPost('chtxt'))
		) {

			$this->addIblockElement();
			global $APPLICATION;
			if (!$this->addId) {
				$APPLICATION->RestartBuffer();
				echo 'N';
				die();
			}
			$this->sendEmail();

			$APPLICATION->RestartBuffer();
			echo 'Y';
			die();

		} else {
			global $APPLICATION;

			if(empty($this->arParams['FORM_NAME'])) {
				$this->arResult['FORM_NAME'] = Loc::getMessage("TITLE_MODAL_DEFAULT");
			} else {
				$this->arResult['FORM_NAME'] = $this->arParams['FORM_NAME'];
			}

			if(empty($this->arParams['FORM_BUTTON_TEXT'])) {
				$this->arResult['FORM_BUTTON_TEXT'] = Loc::getMessage("TEXT_MODAL_DEFAULT");
			} else {
				$this->arResult['FORM_BUTTON_TEXT'] = $this->arParams['FORM_BUTTON_TEXT'];
			}

			if($this->arParams["ID_FORM"]) {
				$this->arResult["ID_FORM"] = $this->arParams["ID_FORM"];
			}

			if($this->arParams["SUCCESS_TEMPLATE"]) {
				$this->arResult["SUCCESS_TEMPLATE"] = $this->arParams["SUCCESS_TEMPLATE"];
			}

			if($this->arParams["~TEXT_INTRO"]) {
				$this->arResult["~TEXT_INTRO"] = $this->arParams["~TEXT_INTRO"];
			}

			$this->includeComponentTemplate();
		}
	}

	public function getFormFields() {
		$dbFoundFields = CIBlockElement::GetList(
			array('SORT' => 'ASC'),
			array(
				'IBLOCK_TYPE' => $this->arParams['IBLOCK_TYPE'],
				'IBLOCK_ID' => $this->arParams['IBLOCK_ID'],
				'ACTIVE' => 'Y'
			),
			false,
			array(),
			array(
				'ID',
				'NAME',
				'IBLOCK_ID',
				'IBLOCK_TYPE',
				'SORT',
				'CODE',
				'PROPERTY_TYPE',
				'PROPERTY_PLACEHOLDER',
				'PROPERTY_DATA_VALIDATE',
				'PROPERTY_DATA_VALIDATE_LENG',
				'PROPERTY_DATA_VALIDATE_ERR_MESS',
				'PROPERTY_DATA_VALIDATE_CUST_ERR_MESS',
				'PROPERTY_ROWS',
				'PROPERTY_REG_EXPR'
			)
		);

		while ($dbFoundFieldsList = $dbFoundFields->Fetch()) {
			$dataType = '';
			$dataValidate = '';

			if($dbFoundFieldsList['PROPERTY_DATA_VALIDATE_ENUM_ID']) {
				$dataValidate = CIBlockPropertyEnum::GetByID($dbFoundFieldsList['PROPERTY_DATA_VALIDATE_ENUM_ID']);
			}
			if($dbFoundFieldsList['PROPERTY_TYPE_ENUM_ID']) {
				$dataType = CIBlockPropertyEnum::GetByID($dbFoundFieldsList['PROPERTY_TYPE_ENUM_ID']);
			}

			$this->arFoundFields[$dbFoundFieldsList['ID']] = array(
				'NAME' => $dbFoundFieldsList['NAME'],
				'ID' => $dbFoundFieldsList['ID'],
				'SORT' => $dbFoundFieldsList['SORT'],
				'CODE' => $dbFoundFieldsList['CODE'],
				'PLACEHOLDER' => $dbFoundFieldsList['PROPERTY_PLACEHOLDER_VALUE'],
				'DATA_VALIDATE' => $dataValidate["XML_ID"],
				'DATA_TYPE' => $dataType["XML_ID"],
				'DATA_VALIDATE_LENG' => ($dbFoundFieldsList['PROPERTY_DATA_VALIDATE_LENG_VALUE']) ? "min" . $dbFoundFieldsList['PROPERTY_DATA_VALIDATE_LENG_VALUE'] : '',
				'DATA_VALIDATE_ERR_MESS' => $dbFoundFieldsList['PROPERTY_DATA_VALIDATE_ERR_MESS_VALUE'],
				'DATA_VALIDATE_CUST_ERR_MESS' => $dbFoundFieldsList['PROPERTY_DATA_VALIDATE_CUST_ERR_MESS_VALUE'],
				'ROWS' => $dbFoundFieldsList['PROPERTY_ROWS_VALUE'],
				'REG_EXPR' => $dbFoundFieldsList['PROPERTY_REG_EXPR_VALUE'],
			);
		}
	}

	public function setFormFields() {
		$this->arResult['ITEMS'] = array();

		foreach($this->arFoundFields as $key=>$val) {
			if(in_array($key, $this->arParams['FORM_FIELDS']) === true) {
				$this->arResult['ITEMS'][] = $val;
			}
		}
	}

	public function addIblockElement() {
		$objElem = new CIBlockElement();
		$arField = array(
			"IBLOCK_ID" => $this->idAblockAddMessages,
			"NAME" => $this->arParams['FORM_NAME'] .' ('. FormatDate("j F Y H:i:s", date()) . ')',
			"ACTIVE" => "Y",
			"PROPERTY_VALUES" => array(
				'RESULT' => $this->setTextFields(),
				'TIME_CREATE' => FormatDate("j F Y H:i:s", date())
			)
		);
		$this->addId = $objElem->Add($arField);
	}

	public function setTextFields() {
		$sText = Loc::getMessage("FLXMD_FORM_DATE") . "\n";
		foreach ($this->arResult['ITEMS'] as $arItem) {
			if (!empty($this->request->getPost($arItem['CODE']))) {
				$val = '';
				$val = htmlspecialchars($this->request->getPost($arItem['CODE']));
				$val = str_replace("&nbsp;", " ", $val);
				$val = str_replace("&quot;", "\"", $val);
				$val = str_replace("&amp;", "&", $val);

				$str = str_replace("&nbsp;", " ", $arItem["NAME"]);
				$sText .= $str . ' - ' . $val ."\n";
			}
		}

		if ($this->arParams['FORM_NAME']) {
			$sText .= "\n";
			$sText .=  Loc::getMessage('FLXMD_FORM_CONSTRUCTOR_NAME') .' - ' . str_replace("&nbsp;", " ", $this->arParams['FORM_NAME']);
		}

		$url = (CMain::IsHTTPS()) ? "https://" : "http://" . $_SERVER["HTTP_HOST"] . str_replace("?{$_SERVER["QUERY_STRING"]}", "", $_SERVER["REQUEST_URI"]);
		$sText .= "\n";
		$sText .=  Loc::getMessage('FLXMD_FORM_CONSTRUCTOR_LINK_PAGE') .' - ' . $url;

		return $sText;
	}

	public function sendEmail() {
		if ($this->addId) {
			$arEventFields = array(
				'TITLE' => $this->arParams['FORM_NAME'],
				'RESULT' => $this->setTextFields()
			);
			$mail_template = 'FEEDBACK_FORM';
			if (!empty($this->arParams['MAIL_TEMPLATE'])) {
				$mail_template = $this->arParams['MAIL_TEMPLATE'];
			}
			CEvent::Send($mail_template, SITE_ID, $arEventFields,  "N");
		}
	}

}
?>