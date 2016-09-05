<?php write_html(aafwWidgets::getInstance()->loadWidget("BrandcoHeader")->render($data["pageStatus"])) ?>
<?php write_html(aafwWidgets::getInstance()->loadWidget("BrandcoAccountHeader")->render($data["pageStatus"])) ?>

<article>
    <section class="noticeBar1 jsNoticeBarArea1" >
        <p class="<?php assign(config('@message.adminMessage.send-target-user-success.class')) ?> jsNoticeBarClose" id="send_success_target"><?php assign(config('@message.adminMessage.send-target-user-success.msg')) ?></p>
    </section>
    <h1 class="hd1">SNS広告との連携データ管理</h1>

    <div class="customaudienceWrap">
        <?php write_html(aafwWidgets::getInstance()->loadWidget('AdsAccountList')->render(array(
            'ads_users' => $data["ads_users"]
        ))) ?>

        <h2 class="hd2">カスタムオーディエンス一覧</h2>
        <div class="customaudienceTableHeader">
            <?php if($data['ads_audiences']): ?>
                <p class="manualSend">チェックした項目を<span class="btn1"><a href="javascript:void(0)" class="small1 jsSendTarget">手動送信する</a></span></p>
            <?php endif; ?>
            <p class="create"><span class="btn1"><a href="<?php assign(Util::rewriteUrl( 'admin-fan', 'create_ads_audience' )); ?>" class="large2">カスタムオーディエンスの作成</a></span></p>
        <!-- /.customaudienceTableHeader --></div>

        <?php if($data['ads_audiences']): ?>
            <div class="jsListAudience"></div>
        <?php endif; ?>
        
        <?php write_html($this->csrf_tag()); ?>
        <?php write_html($this->formHidden('send_target_url', Util::rewriteUrl('admin-fan', 'api_send_ads_target_from_ads_list.json'))) ?>
        <?php write_html($this->formHidden('load_audience_url', Util::rewriteUrl('admin-fan', 'api_get_ads_list_audience.json'))) ?>
        <?php write_html($this->formHidden('update_send_target_url', Util::rewriteUrl('admin-fan', 'api_update_auto_send_target_flg.json'))) ?>
        <!-- /.customaudienceWrap --></div>
</article>

<?php write_html($this->parseTemplate('ads/AddAdsAccountModal.php', array())) ?>

<div class="modal2 jsModal" id="ConfirmCopyModal">
    <section class="modalCont-small jsModalCont" id="jsModalCont">
        <h1>確認</h1>
        <p><span class="attention1" id="copy_message">コピーしますか？</span></p>
        <p class="btnSet">
        <span class="btn2">
            <a href="#closeModal" class="middle1">キャンセル</a>
        </span>
        <span class="btn4"><a id="copy_confirm" href="javascript:void(0)" class="middle1">はい</a>
        </span></p>
    </section>
</div>

<?php $script = array('admin-fan/AdsListService') ?>
<?php $param = array_merge($data['pageStatus'], array('script' => $script)) ?>
<?php write_html($this->parseTemplate("BrandcoFooter.php", $param)); ?>