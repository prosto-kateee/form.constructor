<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);
?>
<? if(count($arResult["ITEMS"]) > 0):?>
	<div class="contacts__info-col">

		<h4 class="content-section-title content-section-title--h4">
			<span class="text"><?=$arResult["FORM_NAME"];?></span>
		</h4>

		<form
			id="<?=$arResult["ID_FORM"];?>"
			class="form js-validate contacts-form"
			name="callback-contacts"
			data-success="<?=$arResult["SUCCESS_TEMPLATE"];?>">

			<input class="form__h" type="hidden" name="ajax_form_key" value="<?=md5($arParams['FORM_KEY']);?>">
			<input class="form__h" type="hidden" name="ajax_form" value="Y"/>
			<input class="form__h" type="text" name="chtxt" value=""/>


			<div class="form__inner">
				<? foreach($arResult["ITEMS"] as $arField):?>
				<? if($arField["DATA_TYPE"] === "text"):?>

					<div class="form__item form-col--1-2 contacts-form__input">
						<h5 class="form__item-title">
							<span class="text"><?=$arField["NAME"];?> <?=($arField["DATA_VALIDATE"]) ? '*' : '';?></span>
						</h5>
						<div class="form-input-text">
							<input
									class="form-input-text__element"
									type="<?=$arField["DATA_TYPE"];?>"
									name="<?=$arField["CODE"];?>"
									value=""
								<?=($arField["PLACEHOLDER"]) ? 'placeholder="'. htmlspecialchars($arField["PLACEHOLDER"]) .'"' : "";?>
								<?=($arField["DATA_VALIDATE"]) ? 'data-validation="'. htmlspecialchars($arField["DATA_VALIDATE"]) .'"' : "";?>
								<?=($arField["DATA_VALIDATE_LENG"]) ? 'data-validation-length="'. htmlspecialchars($arField["DATA_VALIDATE_LENG"]) .'"' : "";?>
								<?=($arField["DATA_VALIDATE_ERR_MESS"]) ? 'data-validation-error-msg="'. htmlspecialchars($arField["DATA_VALIDATE_ERR_MESS"]) .'"' : "";?>

								<?=($arField["DATA_VALIDATE_CUST_ERR_MESS"]) ? 'data-validation-error-msg-custom="'. htmlspecialchars($arField["DATA_VALIDATE_CUST_ERR_MESS"]) .'"' : "";?>

								<?=($arField["REG_EXPR"]) ? 'data-validation-regexp="'. htmlspecialchars($arField["REG_EXPR"]) .'"' : "";?>

							/>
						</div>
					</div>

				<? else:?>

						<div class="form__item form-col--1-1">
							<h5 class="form__item-title">
								<span class="text"><?=$arField["NAME"];?></span>
							</h5>
							<div class="form-textarea">
								<textarea class="form-textarea__element" type="text" name="<?=$arField["CODE"];?>" <?=($arField["ROWS"]) ? 'rows="'. htmlspecialchars($arField["ROWS"]) .'"' : "";?> <?=($arField["PLACEHOLDER"]) ? 'placeholder="'. htmlspecialchars($arField["PLACEHOLDER"]) .'"' : "";?> ></textarea>
							</div>
						</div>

					<? endif;?>

				<? endforeach;?>

				<div class="form__item form__item--center form-col--1-2">
					<label class="form-checkbox contacts-form__checkbox">
						<input
							class="form-checkbox__element"
							type="checkbox"
							name="checkbox"
							data-validation="required"
							data-validation-error-msg="<?=Loc::getMessage("CONTACTS_FLXMD_WARN_CHECKBOX");?>"
							checked="checked"
						/>
						<span class="form-checkbox__text">
							<span class="text"><?=Loc::getMessage("CONTACTS_FLXMD_I_AGREE_CHECKBOX");?> </span>
							<span class="link link--light">
								<span class="text"><?=Loc::getMessage("CONTACTS_FLXMD_PERS_INFO_CHECKBOX");?></span>
							</span>
						</span>
					</label>
				</div>

				<div class="form__item form__item--center form-col--1-2">
					<button class="button button--green-light button--right button--big button--bold" type="submit">
						<span class="text"><?=$arResult["FORM_BUTTON_TEXT"];?></span>
					</button>
				</div>

			</div>
		</form>
	</div>
	<?
	global $APPLICATION;
	ob_start();
	echo $arResult["~TEXT_INTRO"];
	$content = ob_get_contents();
	ob_end_clean();
	$APPLICATION->AddViewContent("modal-contacts-success", $content);
	?>
<? endif;?>
