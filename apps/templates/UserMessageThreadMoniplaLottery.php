<?php // モニプラ宝くじ ?>
<section class="message_backToMonipla jsMessageBackToMonipla">
    <h1 class="messageHd1"><small class="poweredByTitle">「<?php assign($data['cp']->getTitle()); ?>」 powered by モニプラ</small>期間限定「モニプラ宝くじ」開催中！</h1>
    <div class="messageWrap">
        <div class="backToMoniplaInner">
            <p>モニプラは食品、人気の豪華家電、ギフト券などが当たるキャンペーン情報満載！あなたが欲しいプレゼントもあるかも？</p>
        <!-- /.backToMoniplaInner --></div>
        <ul class="btnSet">
            <li class="btn4"><a href="<?php assign('https://'.config('Domain.monipla_media').'/login?ref=cmcpth'); ?>" target="_blank" class="large1" onclick="ga('<?php assign(config('Analytics.TrackerName')) ?>.send', 'event', 'back-monipla', '<?php assign('campaigns_' . $data['cp_user']->cp_id);?>', location.href, {'page': '<?php assign($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']) ?>'});">キャンペーンを探す</a></li>
        <!-- /.btnSet --></ul>
        <div class="lotteryPrWrap">
            <p class="getCode">＼宝くじ券を獲得しました！／</p>
            <p class="lotteryPrText">キャンペーンにたくさん応募すればするだけおトクなチャンス！<br>
                宝くじ券を集めて景品をGETしよう。</p>
            <ul class="btnSet">
                <li class="btn4"><a href="<?php assign(Util::createApplicationUrl(config('Domain.monipla_media'), array('event', 'lottery', 'login'))); ?>" target="_blank" class="large1" onclick="ga('<?php assign(config('Analytics.TrackerName')) ?>.send', 'event', 'back-monipla_lottery', '<?php assign('campaigns_' . $data['cp_user']->cp_id);?>', location.href, {'page': '<?php assign($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']) ?>'});">モニプラ宝くじを見に行く</a></li>
            <!-- /.btnSet --></ul>
        </div>
        <div class="messageFooter">
            <p class="poweredByText"><img src="<?php assign($this->setVersion('/img/base/imgLogo_lg.png')); ?>" width="77" height="18" alt="モニプラ"></p>
        <!-- /.messageFooter --></div>
    </div>
<!-- /.message_backToMonipla --></section>

<script type="text/javascript">
    <!--
    setTimeout(function() {
        $('.jsMessageBackToMonipla').show();
        if (typeof(ga) !== 'undefined') {
            ga('<?php assign(config('Analytics.TrackerName')) ?>.send', 'event', 'show-back-monipla', '<?php assign('campaigns_' . $data['cp_user']->cp_id);?>', location.href, {'page': '<?php assign($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']) ?>'});
        }
    }, 200);
    -->
</script>