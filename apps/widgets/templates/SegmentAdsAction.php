<dt class="require1"><label>広告アカウント</label></dt>
<dd>
    <ul class="addAcountList jsLoadAdsAccount">
        <?php foreach($data['ads_accounts'] as $account): ?>
                <li><label><input type="checkbox" name="ads_account_ids[]" value="<?php assign($account->id) ?>"><span class="<?php assign(AdsAccount::$sns_icon_class_1[$account->social_app_id])?>"><?php assign($account->account_name)?></span></label></li>
        <?php endforeach; ?>
        <!-- /.addAcountList --></ul>
    <span class="iconError1 jsAdsAccountError" style="display: none;"></span>
    <p class="addAcount">
        <a href="javascript:void(0)" class="jsOpenAdsAccountModal">新規にアカウントを連携する</a>
    <!-- /.addAcount --></p>
</dd>
<dt class="require1"><label>タイトル</label></dt>
<dd>
    <input type="text" name="ads_audience_name" placeholder="カスタムオーディエンス名を入れます。" maxlength="255">
    <span class="iconError1 jsAdsAudienceNameInputError" style="display: none;"></span>
    <span class="jsCheckToggleWrap">
        <span class="sub">
            <label><input type="checkbox" class="jsCheckToggle" name="ads_description_flg">メモ</label>
        </span>
        <input type="text" name="ads_audience_description" placeholder="覚書用のメモを残せます。" class="jsCheckToggleTarget" style="display:none;" maxlength="255">
        <span class="iconError1 jsAdsAudienceDescriptionInputError" style="display: none;"></span>
    </span>
</dd>
<dt>送信対象人数</dt>
<dd>
    <?php write_html(aafwWidgets::getInstance()->loadWidget('SegmentAdsTargetList')->render(array('provision_ids' => $data['provision_ids']))) ?>
</dd>
