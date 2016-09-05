<section class="message_backToMoniplaPr jsMessageBackToMonipla">
    <div class="messageWrap">
        <div class="backToMonipla">
            <h1 class="messageHd1"><img src=<?php assign($this->setVersion('/img/base/imgLogoMonipla_lg.png')) ?> width="80" height="19" alt="モニプラ"></h1>
            <h2 class="messageHd2">累計300万人が当選中！<br>モニプラでちょっと楽しく&ldquo;おトク&rdquo;体験</h2>
            <p class="moniplaPrText"><span>食品・家電・雑貨など有名企業の人気アイテムの</span>
                <span>プレゼントキャンペーン情報満載！</span>
                <span>もっと応募してあなたもおトクな体験をしよう</span></p>
        <!-- /.backToMonipla --></div>
        <div class="moniplAengagementWrap">
            <h2 class="messageHd2">最新情報お届け中！モニプラとつながろう</h2>
            <div class="moniplAengagement">
                <h2 class="moniplaSnsTitle">ソーシャルメディア</h2>
                <div class="snsInner">
                    <ul class="snsBtns-box">
                        <li><div id="fb-like" class="fb-like monipla-fb-like" data-href="https://www.facebook.com/monipla.fan" data-layout="button_count" data-action="like" data-show-faces="false" data-share="false"></div><script>FB.XFBML.parse();</script></li
                        ><li><a href="https://twitter.com/monipla_tw" class="twitter-follow-button" data-show-count="false" data-show-screen-name="false">Follow @monipla_tw</a><script>twttr.widgets.load();</script></li
                        ></ul>
                <!-- /.snsInner --></div>
            <!-- /.moniplAengagement --></div>
            <?php if ($data['user_media_optin']->data->opt_in != 1): ?>
            <div class="moniplAengagement">
                <form class="jsUpdateOptin" action="<?php assign(Util::rewriteUrl('messages', 'api_update_user_optin.json')) ?>" method="POST">
                    <?php write_html($this->csrf_tag()); ?>
                    <h2 class="moniplaMailTitle">1日1回のメルマガ</h2>
                    <p class="moniplaMailText">モニプラからのおトクな情報を受け取ろう！</p>
                    <p class="optin">
                        <a href="javascript:void(0)" class="jsSwitchStatus switch switch_large off">
                            <span class="switchInner">
                                <span class="selectON">受信する</span>
                                <span class="selectOFF">受信しない</span>
                            </span>
                        </a>
                    </p>
                    <?php write_html($this->formHidden('cp_id', $data['cp']->id)) ?>
                </form>
            <!-- /.moniplAengagement --></div>
            <?php endif; ?>
         <!-- /.moniplAengagementWrap --></div>

        <ul class="btnSet">
            <li class="btn4"><a href="<?php assign(config('Protocol.Secure').'://'.config('Domain.monipla_media').'/login?ref=cmcpth'); ?>" target="_blank" class="large3" onclick="ga('<?php assign(config('Analytics.TrackerName')) ?>.send', 'event', 'back-monipla', '<?php assign('campaigns_' . $data['cp_info']['cp']['id']);?>', location.href, {'page': '<?php assign($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']) ?>'});"><small>モニプラで</small>キャンペーンを探す</a></li>
        <!-- /.btnSet --></ul>

    <!-- /.messageWrap --></div>
<!-- /.message_backToMonipla --></section>

<script type="text/javascript">
    <!--
    setTimeout(function() {
        if (typeof(ga) !== 'undefined') {
            ga('<?php assign(config('Analytics.TrackerName')) ?>.send', 'event', 'show-back-monipla', '<?php assign('campaigns_' . $data['cp_user']->cp_id);?>', location.href, {'page': '<?php assign($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']) ?>'});
        }
    }, 200);
    -->
</script>

<?php write_html($this->scriptTag('MoniplaEngagementLogService')); ?>
<?php write_html($this->scriptTag('user/UserOptinService')); ?>