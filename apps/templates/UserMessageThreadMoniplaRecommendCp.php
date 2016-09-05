<?php //キャンペーン導線  ?>
<section class="message_backToMonipla jsMessageBackToMonipla">
    <h1 class="messageHd1"><small class="poweredByTitle">さらに…</small>その場で当たりがわかるスピードくじに挑戦してプレゼントをGETしよう！</h1>
    <div class="cpRecommend">
        <?php if ($data['recommend_cp']->image_rectangle_url): ?>
            <p><a href="<?php assign(config('Protocol.Secure').'://'.config('Domain.brandco').$data['recommend_cp']->reference_url.'?fid=mpcpthx'); ?>" target="_blank" class="large1" onclick="ga('<?php assign(config('Analytics.TrackerName')) ?>.send', 'event', 'back-monipla', '<?php assign('campaigns_' . $data['cp_info']['cp']['id']);?>', location.href, {'page': '<?php assign($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']) ?>'});"><img src=<?php assign($data['recommend_cp']->image_rectangle_url); ?> alt=""></a></p>
        <?php else: ?>
            <p><a href="<?php assign(config('Protocol.Secure').'://'.config('Domain.brandco').$data['recommend_cp']->reference_url.'?fid=mpcpthx'); ?>" target="_blank" class="large1" onclick="ga('<?php assign(config('Analytics.TrackerName')) ?>.send', 'event', 'back-monipla', '<?php assign('campaigns_' . $data['cp_info']['cp']['id']);?>', location.href, {'page': '<?php assign($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']) ?>'});"><img src=<?php assign($data['recommend_cp']->image_url); ?> alt=""></a></p>
        <?php endif; ?>
    <!-- /.cpRecommend --></div>
    <ul class="btnSet">
        <li class="btn4"><a href="<?php assign(config('Protocol.Secure').'://'.config('Domain.brandco').$data['recommend_cp']->reference_url.'?fid=mpcpthx'); ?>" target="_blank" class="large1" onclick="ga('<?php assign(config('Analytics.TrackerName')) ?>.send', 'event', 'back-monipla', '<?php assign('campaigns_' . $data['cp_info']['cp']['id']);?>', location.href, {'page': '<?php assign($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']) ?>'});">今すぐ応募する</a></li>
    <!-- /.btnSet --></ul>
    <div class="cpRecommendFooter">
        <p class="poweredByText"><img src="<?php assign($this->setVersion('/img/base/imgLogo_lg.png')); ?>" width="77" height="18" alt="モニプラ"><small>モニプラ運営事務局</small></p>
    <!-- /.cpRecommend --></div>
<!-- /.message_backToMonipla --></section>

<script type="text/javascript">
    $(document).ready(function(){if (typeof(ga) !== 'undefined') {
        ga('<?php assign(config('Analytics.TrackerName')) ?>.send', 'event', '<?php assign($data['recommend_cp_ga_tag']) ?>', '<?php assign('campaigns_' . $data['cp_user']->cp_id);?>', location.href, {'page': '<?php assign($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']) ?>'});
    }});
</script>