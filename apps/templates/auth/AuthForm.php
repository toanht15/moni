<div class="jsAuthForm jsSliderContent">
    <div class="snsJoinLargeWrap">
        <ul class="snsJoinLarge">
            <li class="btnSnsFb1"><a href="<?php assign(Util::rewriteUrl( 'auth', 'campaign_login', '', array('platform' => 'fb', 'redirect_url' => urlencode($data['redirect_url']), 'cp_id' => $data['cp_id']))) ?>" onclick="ga('<?php assign(config('Analytics.TrackerName')) ?>.send', 'event', 'fb-login', '<?php assign('campaigns_' . $data['cp_id']);?>', location.href, {'page': '<?php assign($data['redirect_url']) ?>'});" title="Facebook" class="arrow1"><span class="inner">Facebook<br>で応募</span></a></li>
            <li class="btnSnsTw1"><a href="<?php assign(Util::rewriteUrl( 'auth', 'campaign_login', '', array('platform' => 'tw', 'redirect_url' => urlencode($data['redirect_url']), 'cp_id' => $data['cp_id']))) ?>" onclick="ga('<?php assign(config('Analytics.TrackerName')) ?>.send', 'event', 'tw-login', '<?php assign('campaigns_' . $data['cp_id']);?>', location.href, {'page': '<?php assign($data['redirect_url']) ?>'});" title="Twitter" class="arrow1"><span class="inner">Twitter<br>で応募</span></a></li>
            <!-- /.snsJoinLarge --></ul>
        <!-- /.snsJoinLargeWrap --></div>

    <div class="snsJoinOtherWrap">
        <ul class="snsJoinOther">
            <li class="btnSnsLn1"><a href="<?php assign(Util::rewriteUrl( 'auth', 'campaign_login', '', array('platform' => 'line', 'redirect_url' => urlencode($data['redirect_url']), 'cp_id' => $data['cp_id']))) ?>" onclick="ga('<?php assign(config('Analytics.TrackerName')) ?>.send', 'event', 'ln-login', '<?php assign('campaigns_' . $data['cp_id']);?>', location.href, {'page': '<?php assign($data['redirect_url']) ?>'});" class="square1" title="LINE"><span class="inner">LINE<br>で応募</span></a></li>
            <?php if(!$data['pageStatus']['is_sugao_brand']):?>
                <li class="btnSnsIg1"><a href="<?php assign(Util::rewriteUrl( 'auth', 'campaign_login', '', array('platform' => 'insta', 'redirect_url' => urlencode($data['redirect_url']), 'cp_id' => $data['cp_id']))) ?>" onclick="ga('<?php assign(config('Analytics.TrackerName')) ?>.send', 'event', 'ig-login', '<?php assign('campaigns_' . $data['cp_id']);?>', location.href, {'page': '<?php assign($data['redirect_url']) ?>'});" class="square1" title="Instagram"><span class="inner">Instagram<br>で応募</span></a></li>
                <li class="btnSnsGp1"><a href="<?php assign(Util::rewriteUrl( 'auth', 'campaign_login', '', array('platform' => 'ggl', 'redirect_url' => urlencode($data['redirect_url']), 'cp_id' => $data['cp_id']))) ?>" onclick="ga('<?php assign(config('Analytics.TrackerName')) ?>.send', 'event', 'ggl-login', '<?php assign('campaigns_' . $data['cp_id']);?>', location.href, {'page': '<?php assign($data['redirect_url']) ?>'});" class="square1" title="Google"><span class="inner">Google<br>で応募</span></a></li>
                <li class="btnSnsYh1"><a href="<?php assign(Util::rewriteUrl( 'auth', 'campaign_login', '', array('platform' => 'yh', 'redirect_url' => urlencode($data['redirect_url']), 'cp_id' => $data['cp_id']))) ?>" onclick="ga('<?php assign(config('Analytics.TrackerName')) ?>.send', 'event', 'yh-login', '<?php assign('campaigns_' . $data['cp_id']);?>', location.href, {'page': '<?php assign($data['redirect_url']) ?>'});" class="square1" title="Yahoo!"><span class="inner">Yahoo!<br><span class="space"> </span>JAPAN ID<br>で応募</span></a></li>
            <?php endif;?>
            <?php if($data['canLoginByLinkedIn']): ?>
                <li class="btnSnsIn1"><a href="<?php assign(Util::rewriteUrl( 'auth', 'campaign_login', '', array('platform' => 'linkedin', 'redirect_url' => urlencode($data['redirect_url']), 'cp_id' => $data['cp_id']))) ?>" onclick="ga('<?php assign(config('Analytics.TrackerName')) ?>.send', 'event', 'linkedin-login', '<?php assign('campaigns_' . $data['cp_id']);?>', location.href, {'page': '<?php assign($data['redirect_url']) ?>'});" class="square1" title="LinkedIn"><span class="inner">LinkedIn<br>で応募</span></a></li>
            <?php endif; ?>
            <!-- /.snsJoinOther --></ul>
        <!-- /.snsJoinOtherWrap --></div>

    <div class="addressJoinWrap">
        <ul class="btnList">
            <li class="btnMail1"><a href="javascript:void(0);" class="arrow1 jsCallMailAuthFormWrap"  onclick="ga('<?php assign(config('Analytics.TrackerName')) ?>.send', 'event', 'mail-login', '<?php assign('campaigns_' . $data['cp_id']);?>', location.href, {'page': '<?php assign($data['redirect_url']) ?>'});"><span class="inner">メールアドレス<br>で応募</span></a></li>
            <!-- /.btnList --></ul>
        <!-- /.addressJoinWrap --></div>
</div>