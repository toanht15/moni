<div class="modal1 jsModal" id="segmentAdsActionModal" style="height: 1449px;">
    <section class="dataLinkSettingWrap modalCont-large2 jsModalCont" style="display: block; opacity: 1; top: 30px;">
        <h1>広告を出稿する広告アカウントを選択し、送信してください</h1>
        <dl class="adDataSetting jsLoadSegmentAdsAction">
        <!--/.adDataSetting --></dl>
        <ul class="btnSet">
            <li class="btn2"><a href="javascript:void(0)" class="large1 jsCancelAdsModal">キャンセル</a></li>
            <li class="btn3"><a href="javascript:void(0)" class="large1 jsSendTargetUser">データの送信</a></li>
        </ul>
    </section>
    <?php write_html($this->formHidden('get_segment_ads_action_url', Util::rewriteUrl('admin-segment', 'api_load_segment_ads_action.json'))) ?>
    <?php write_html($this->formHidden('send_ads_target_url', Util::rewriteUrl('admin-fan', 'api_send_ads_target_from_segment.json'))) ?>
<!-- /.modal1 --></div>