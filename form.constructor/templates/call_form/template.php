<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);
?>
<? if(count($arResult["ITEMS"]) > 0):?>
	<article class="modal is-hidden js-modal" id="<?=$arResult["ID_FORM"];?>" style="max-width:874px">
		<div class="modal__header">
			<h4 class="modal__title">
				<span class="text"><?=$arResult["FORM_NAME"];?></span>
			</h4>
		</div>
		<div class="modal__body">
			<form class="form js-validate" name="callback" data-success="<?=$arResult["SUCCESS_TEMPLATE"];?>" >

				<input class="form__h" type="hidden" name="ajax_form_key" value="<?=md5($arParams['FORM_KEY']);?>">
				<input class="form__h" type="hidden" name="ajax_form" value="Y"/>
				<input class="form__h" type="text" name="chtxt" value=""/>

				<div class="form__inner">

					<? foreach($arResult["ITEMS"] as $arField):?>
						<? if($arField["DATA_TYPE"] === "text"):?>

							<div class="form__item form-col--1-2">
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

					<div class="form__item form__item--center form-col--1-2"></div>

					<div class="form__item form__item--center form-col--1-2">
						<button class="button button--green-light button--right button--big button--bold" type="submit">
							<span class="text"><?=$arResult["FORM_BUTTON_TEXT"];?></span>
						</button>
					</div>

				</div>
			</form>

		</div>

		<div class="modal__footer"></div>

		<button class="modal-close-btn js-modal-close">
			<span class="text">✕</span>
		</button>

	</article>

<?=$arResult["~TEXT_INTRO"];?>
<? endif;?>

