<section class="dataLinkSettingWrap modalCont-large2 jsModalCont" style="display: block; opacity: 1; top: 30px;">
    <h1>以下の内容でカスタムオーディエンスを広告アカウントに送信しました</h1>
    <dl class="adDataSetting">
        <dt class="require1"><label>広告アカウント</label></dt>
        <dd>
            <ul class="addAcountConfirmList">
                <?php foreach($data['ads_accounts'] as $account): ?>
                    <li><span class="<?php assign(AdsAccount::$sns_icon_class_1[$account->social_app_id])?>"><?php assign($account->account_name)?></span></li>
                <?php endforeach; ?>
                <!-- /.addAcountList --></ul>
        </dd>
        <dt class="require1"><label>タイトル</label></dt>
        <dd><?php assign($data['ads_audience']->name) ?>
            <?php if($data['ads_audience']->description): ?>
                <span class="jsCheckToggleWrap">
                  <span class="sub">メモ</span>
                    <?php assign($data['ads_audience']->description) ?>
            </span>
            <?php endif; ?>
        </dd>

        <dt>送信対象人数</dt>
        <dd>
            <?php write_html(aafwWidgets::getInstance()->loadWidget('SegmentAdsTargetList')->render(array('provision_ids' => $data['provision_ids']))) ?>
        </dd>
        <!-- /.adDataSetting --></dl>
    <ul class="btnSet">
        <li class="btn2">
            <a href="javascript:void(0)" class="large1 jsCloseAdsConfirmModal"
               data-redirect_url="<?php assign(Util::rewriteUrl('admin-fan', 'ads_list', array(), array("mid"=>"updated")))?>">
                閉じる
            </a>
        </li>
    </ul>
</section>