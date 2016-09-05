<div class="modal1 jsModal" id="actionSet">
    <section class="modalCont-medium jsModalCont">
        <h1>アクション</h1>
        <ul class="actionMenu">
            <li class="actionMessage"><a href="javascript:void(0);" data-action_url="<?php assign(Util::rewriteUrl('admin-segment', 'segment_action_message')) ?>" data-action_type="<?php assign(SegmentActionLog::TYPE_ACTION_MESSAGE); ?>" class="jsSegmentAction">メッセージを送る</a></li>
<!--            <li class="actionCampaign"><a href="#">キャンペーンに誘導する</a></li>-->
            <?php if($data['can_use_ads_action']): ?>
                <li class="actionAd"><a href="javascript:void(0);" data-action_url="<?php assign(Util::rewriteUrl('admin-segment', 'segment_action_ads')) ?>" data-action_type="<?php assign(SegmentActionLog::TYPE_ACTION_ADS); ?>" class="jsSegmentAction">広告を出稿する</a></li>
            <?php endif; ?>
            <li class="actionCsv"><a href="javascript:void(0);" data-action_url="<?php assign(Util::rewriteUrl('admin-segment', 'download_provision_data')) ?>" data-action_type="<?php assign(SegmentActionLog::TYPE_ACTION_DOWNLOAD); ?>" class="jsSegmentAction">CSVダウンロード</a></li>
        </ul>
        <ul class="btnSet">
            <li class="btn2"><a href="#closeModal" class="large1">キャンセル</a></li>
        </ul>
    </section>
<!-- /.modal1 --></div>