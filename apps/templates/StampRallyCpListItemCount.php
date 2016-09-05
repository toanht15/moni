<div class="checkedStampRallyCampaignWrap jsAreaToggleWrap">
    <p class="showItemNum">
    表示件数
    <select class="showItemNum" name="cp_limit">
        <option value="<?php assign(StaticHtmlStampRallyService::DISPLAY_10_ITEMS) ?>" <?php if ($data['limit'] == StaticHtmlStampRallyService::DISPLAY_10_ITEMS) assign('selected') ?>>
            <?php assign(StaticHtmlStampRallyService::DISPLAY_10_ITEMS) ?>件
        </option>
        <option value="<?php assign(StaticHtmlStampRallyService::DISPLAY_20_ITEMS) ?>" <?php if ($data['limit'] == StaticHtmlStampRallyService::DISPLAY_20_ITEMS) assign('selected') ?>>
            <?php assign(StaticHtmlStampRallyService::DISPLAY_20_ITEMS) ?>件
        </option>
        <option value="<?php assign(StaticHtmlStampRallyService::DISPLAY_50_ITEMS) ?>" <?php if ($data['limit'] == StaticHtmlStampRallyService::DISPLAY_50_ITEMS) assign('selected') ?>>
            <?php assign(StaticHtmlStampRallyService::DISPLAY_50_ITEMS) ?>件
        </option>
    </select>
    <span class="btn2"><a href="javascript:void(0)" class="small1" name="applyFanLimit">反映</a></span>
<!-- /.showItemNum --></p>
<p class="checkedCampaign">
    <span class="iconCheck3">選択中<strong class="jsCountArea">0</strong>/<span class="targetCpNum">0</span>件</span>
<!-- /.checkedCampaign --></p>
<p class="pagePartsErrorMessage" id="selectCpNumError"></p>
<!-- /.checkedStampRallyCampaignWrap --></div>

