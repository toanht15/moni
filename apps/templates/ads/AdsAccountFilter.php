<div class="sortBox jsAreaToggleTarget">
    <p class="boxCloseBtn"><a href="javascript:void(0)" class="jsAreaToggle">閉じる</a></p>
    <ul class="">
        <?php foreach($data['ads_accounts'] as $account): ?>
            <li><label><input type="checkbox" name="ads_account[]" value="<?php assign($account->id)?>" <?php assign(in_array($account->id, $data['target_account_ids']) ? 'checked' : '')?>><span class="<?php assign(AdsAccount::$sns_icon_class_1[$account->social_app_id])?>"><?php assign($account->account_name)?></span></label></li>
        <?php endforeach; ?>
        <!-- /.addAcountList --></ul>
    <p class="btnSet">
        <span class="btn2"><a href="javascript:void(0)" class="small1 jsAreaToggle jsClearAdsAccountFilter">リセット</a></span>
        <span class="btn3"><a href="javascript:void(0)" class="small1 jsAreaToggle jsSearchAdsAccountFilter">絞り込む</a></span>
    </p>
<!-- /.sortBox --></div>