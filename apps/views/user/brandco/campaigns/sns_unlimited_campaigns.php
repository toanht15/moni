<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoHeader')->render($data['pageStatus'])) ?>
<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoTopHeader')->render($data['pageStatus'])) ?>

    <article>
        <section class="messageWrap">

            <section class="campaign" id="cp_<?php assign($data["action_info"]["cp"]["id"]); ?>" >

                <?php if($data["action_info"]["concrete_action"]["image_url"]): ?>
                    <p class="campaignImg"><img src="<?php assign($data["action_info"]["concrete_action"]["image_url"]); ?>" width="690" height="280" alt="campaign img"></p>
                <?php endif;?>

                <?php write_html(aafwWidgets::getInstance()->loadWidget('SynCampaignText')->render(array('cp'=>$data["cp"]))) ?>
                <?php $message_text = $data['action_info']['concrete_action']["html_content"] ? $data['action_info']['concrete_action']["html_content"] : $this->toHalfContentDeeply($data['action_info']['concrete_action']["text"]); ?>
                <section class="campaignText"><?php write_html($message_text); ?></section>

                <?php if ($data['pageStatus']['demographyErrors']): ?>
                    <p class="joinLimit" id="joinLimit"><?php write_html($data['pageStatus']['demographyErrors']) ?></p>
                <?php endif ?>

                <?php if ($data["cp"]->canEntry(RequestuserInfoContainer::getInstance()->getStatusByCp($data["cp"])) && !$data['pageStatus']['isNotMatchDemography']): ?>

                    <?php $isLogin = $data['pageStatus']["isLogin"] && $data["userInfo"] != null && $data["userInfo"]->id;?>

                    <?php if ($data['action_info']['cp']['is_au_campaign']): ?>
                    <div class="au-thirdParty">
                        <h1>応募はこちらから</h1>
                        <ul class="au-btnSet">
                            <li class="au-btn5"><a href="<?php assign($data['action_info']['cp']['au_login_url']) ?>" class="large1" onclick="ga('<?php assign(config('Analytics.TrackerName')) ?>.send', 'event', 'au-join', '<?php assign('campaigns_' . $data['action_info']['cp']['id']);?>', location.href, {'page': '<?php assign($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']) ?>'});"><?php assign($data["action_info"]["concrete_action"]["button_label_text"]); ?></a></li>
                            <!-- /.btnSet --></ul>
                        <p class="supplement1">※ご応募にはauスマートパスの会員登録と、モニプラ・<a href="<?php assign('//'.config('Domain.aaid'))?>" target="_blank"><img src="<?php assign($this->setVersion('/img/icon/iconAlliedID2.png')); ?>" alt="アライドID">アライドID</a>の登録が必要です。<br>
                            <a href="<?php assign('//'.config('Domain.aaid'))?>/agreement" target="_blank">アライドID利用規約</a>

                            <?php if($data['brand']->id == 479): ?>
                                、<a href="<?php assign(Util::rewriteUrl('page', 'privacy')); ?>" class="openNewWindow1" target="_blank">一般社団法人 日本健康生活推進協会 個人情報保護方針</a>
                            <?php else: ?>
                                <?php if ($data['brand']->agreement): ?>
                                    、<?php write_html('<a href="' . '//' . config('Domain.aaid') . '/agreement" target="_blank">' . $data['brand']->name . '公式ファンサイト利用規約</a>'); ?>
                                <?php endif; ?>
                            <?php endif; ?>

                            に同意の上、ご応募ください。 </p>
                        <!-- /.au-thirdParty--></div>
                <?php elseif($isLogin): ?>
                    <form name="campaignJoinForm" action="<?php assign(Util::rewriteUrl('messages', 'join', '', '', '', true)); ?>" method="POST" enctype="multipart/form-data" >
                        <?php write_html($this->csrf_tag()); ?>
                        <?php write_html($this->formHidden('cp_id', $data["action_info"]['cp']["id"])); ?>
                        <ul class="btnSet">
                            <li class="btn3"><a onclick="$(this).replaceWith('<span class=\'large1\'>' + $(this).text() + '</span>'); $('form[name=campaignJoinForm]').submit();" href="javascript:void(0);" class="large1"><?php assign($data["action_info"]["concrete_action"]["button_label_text"]); ?></a></li>
                            <!-- /.btnSet --></ul>
                    </form>
                <?php else: ?>
                <?php if ($data['brand']->id === config('SynBrandId')): // Todo: Syndot特別対応 Optimizelyテストが完了次第削除する ?>
                    <script type="text/javascript">
                        window.syndot_logged_in = 'no';
                    </script>
                <?php endif; ?>

                    <div class="joinCommSite jsNewLoginOauth" style="display:none">
                        <h1>お持ちのアカウントでかんたん応募！</h1>
                        <div class="branch jsAuthModalSliderScreen jsSliderScreen">
                            <div class="jsAuthForm jsSliderContent">
                                <div class="snsJoinLargeWrap">
                                    <ul class="snsJoinLarge">
                                        <li class="btnSnsFb1"><a href="<?php assign(Util::rewriteUrl( 'auth', 'campaign_login', '', array('platform' => 'fb', 'redirect_url' => urlencode(Util::getCurrentUrl()), 'cp_id' => $data['action_info']['cp']['id']))) ?>" onclick="ga('<?php assign(config('Analytics.TrackerName')) ?>.send', 'event', 'fb-login', '<?php assign('campaigns_' . $data['action_info']['cp']['id']);?>', location.href, {'page': '<?php assign($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']) ?>'});" class="arrow1"><span class="inner">Facebook<br>で応募</span></a></li>
                                        <li class="btnSnsTw1"><a href="<?php assign(Util::rewriteUrl( 'auth', 'campaign_login', '', array('platform' => 'tw', 'redirect_url' => urlencode(Util::getCurrentUrl()), 'cp_id' => $data['action_info']['cp']['id']))) ?>" onclick="ga('<?php assign(config('Analytics.TrackerName')) ?>.send', 'event', 'tw-login', '<?php assign('campaigns_' . $data['action_info']['cp']['id']);?>', location.href, {'page': '<?php assign($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']) ?>'});" class="arrow1"><span class="inner">Twitter<br>で応募</span></a></li>
                                        <!-- /.snsJoinLarge --></ul>
                                    <!-- /.snsJoinLargeWrap --></div>

                                <div class="snsJoinOtherWrap">
                                    <ul class="snsJoinOther">
                                        <li class="btnSnsIn1"><a href="<?php assign(Util::rewriteUrl( 'auth', 'campaign_login', '', array('platform' => 'linkedin', 'redirect_url' => urlencode(Util::getCurrentUrl()), 'cp_id' => $data['action_info']['cp']['id']))) ?>" onclick="ga('<?php assign(config('Analytics.TrackerName')) ?>.send', 'event', 'linkedin-login', '<?php assign('campaigns_' . $data['action_info']['cp']['id']);?>', location.href, {'page': '<?php assign($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']) ?>'});" class="square1" title="LinkedIn"><span class="inner">LinkedIn</span></a></li>
                                        <li class="btnSnsLn1"><a href="<?php assign(Util::rewriteUrl( 'auth', 'campaign_login', '', array('platform' => 'line', 'redirect_url' => urlencode(Util::getCurrentUrl()), 'cp_id' => $data['action_info']['cp']['id']))) ?>" onclick="ga('<?php assign(config('Analytics.TrackerName')) ?>.send', 'event', 'ln-login', '<?php assign('campaigns_' . $data['action_info']['cp']['id']);?>', location.href, {'page': '<?php assign($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']) ?>'});" class="square1" title="LINE"><span class="inner">LINE</span></a></li>
                                        <li class="btnSnsIg1"><a href="<?php assign(Util::rewriteUrl( 'auth', 'campaign_login', '', array('platform' => 'insta', 'redirect_url' => urlencode(Util::getCurrentUrl()), 'cp_id' => $data['action_info']['cp']['id']))) ?>" onclick="ga('<?php assign(config('Analytics.TrackerName')) ?>.send', 'event', 'ig-login', '<?php assign('campaigns_' . $data['action_info']['cp']['id']);?>', location.href, {'page': '<?php assign($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']) ?>'});" class="square1" title="Instagram"><span class="inner">Instagram</span></a></li>
                                        <li class="btnSnsGp1"><a href="<?php assign(Util::rewriteUrl( 'auth', 'campaign_login', '', array('platform' => 'ggl', 'redirect_url' => urlencode(Util::getCurrentUrl()), 'cp_id' => $data['action_info']['cp']['id']))) ?>" onclick="ga('<?php assign(config('Analytics.TrackerName')) ?>.send', 'event', 'ggl-login', '<?php assign('campaigns_' . $data['action_info']['cp']['id']);?>', location.href, {'page': '<?php assign($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']) ?>'});" class="square1" title="Google"><span class="inner">Google</span></a></li>
                                        <li class="btnSnsYh1"><a href="<?php assign(Util::rewriteUrl( 'auth', 'campaign_login', '', array('platform' => 'yh', 'redirect_url' => urlencode(Util::getCurrentUrl()), 'cp_id' => $data['action_info']['cp']['id']))) ?>" onclick="ga('<?php assign(config('Analytics.TrackerName')) ?>.send', 'event', 'yh-login', '<?php assign('campaigns_' . $data['action_info']['cp']['id']);?>', location.href, {'page': '<?php assign($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']) ?>'});" class="square1" title="Yahoo!&#10;JAPAN ID"><span class="inner">Yahoo! JAPAN ID</span></a></li>
                                        <!-- /.snsJoinOther --></ul>
                                    <!-- /.snsJoinOtherWrap --></div>

                                <div class="addressJoinWrap">
                                    <ul class="btnList">
                                        <li class="btnMail1"><a href="javascript:void(0);" class="arrow1 jsCallMailAuthFormWrap" onclick="ga('<?php assign(config('Analytics.TrackerName')) ?>.send', 'event', 'mail-login', '<?php assign('campaigns_' . $data['cp_id']);?>', location.href, {'page': '<?php assign($data['redirect_url']) ?>'});"><span class="inner">メールアドレス<br>で応募</span></a></li>
                                        <!-- /.btnList --></ul>
                                    <!-- /.addressJoinWrap --></div>

                            </div>
                        </div>

                        <div class="jsAuthModal"><?php write_html($this->csrf_tag()); ?></div>

                        <?php write_html( $this->parseTemplate('Cooperation.php', array('brand' => $data['brand'], 'action' => '応募'))) ?>
                        <!-- /.joinCommSite --></div>



                    <div class="joinCommSite jsOldLoginOauth">
                        <form>
                            <h1 class="jsLoginSnsHeader">お持ちのアカウントでかんたん応募！</h1>
                            <ul class="btnSet">
                                <li class="btnSnsFb1"><a href="<?php assign(Util::rewriteUrl( 'auth', 'campaign_login', '', array('platform' => 'fb', 'redirect_url' => urlencode(Util::getCurrentUrl()), 'cp_id' => $data['action_info']['cp']['id']))) ?>" onclick="ga('<?php assign(config('Analytics.TrackerName')) ?>.send', 'event', 'fb-login', '<?php assign('campaigns_' . $data['action_info']['cp']['id']);?>', location.href, {'page': '<?php assign($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']) ?>'});" title="Facebook" class="arrow1"><span class="inner">Facebook<br>で応募</span></a></li>
                                <li class="btnSnsTw1"><a href="<?php assign(Util::rewriteUrl( 'auth', 'campaign_login', '', array('platform' => 'tw', 'redirect_url' => urlencode(Util::getCurrentUrl()), 'cp_id' => $data['action_info']['cp']['id']))) ?>" onclick="ga('<?php assign(config('Analytics.TrackerName')) ?>.send', 'event', 'tw-login', '<?php assign('campaigns_' . $data['action_info']['cp']['id']);?>', location.href, {'page': '<?php assign($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']) ?>'});" title="Twitter" class="arrow1"><span class="inner">Twitter<br>で応募</span></a></li>
                                <li class="btnSnsLn1"><a href="<?php assign(Util::rewriteUrl( 'auth', 'campaign_login', '', array('platform' => 'line', 'redirect_url' => urlencode(Util::getCurrentUrl()), 'cp_id' => $data['action_info']['cp']['id']))) ?>" onclick="ga('<?php assign(config('Analytics.TrackerName')) ?>.send', 'event', 'ln-login', '<?php assign('campaigns_' . $data['action_info']['cp']['id']);?>', location.href, {'page': '<?php assign($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']) ?>'});" title="LINE" class="arrow1"><span class="inner">LINE<br>で応募</span></a></li>
                                <?php if(!$data['pageStatus']['is_sugao_brand']):?>
                                    <li class="btnSnsIg1"><a href="<?php assign(Util::rewriteUrl( 'auth', 'campaign_login', '', array('platform' => 'insta', 'redirect_url' => urlencode(Util::getCurrentUrl()), 'cp_id' => $data['action_info']['cp']['id']))) ?>" onclick="ga('<?php assign(config('Analytics.TrackerName')) ?>.send', 'event', 'ig-login', '<?php assign('campaigns_' . $data['action_info']['cp']['id']);?>', location.href, {'page': '<?php assign($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']) ?>'});" title="Instagram" class="arrow1"><span class="inner">Instagram<br>で応募</span></a></li>
                                    <li class="btnSnsGp1"><a href="<?php assign(Util::rewriteUrl( 'auth', 'campaign_login', '', array('platform' => 'ggl', 'redirect_url' => urlencode(Util::getCurrentUrl()), 'cp_id' => $data['action_info']['cp']['id']))) ?>" onclick="ga('<?php assign(config('Analytics.TrackerName')) ?>.send', 'event', 'ggl-login', '<?php assign('campaigns_' . $data['action_info']['cp']['id']);?>', location.href, {'page': '<?php assign($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']) ?>'});" title="Google" class="arrow1"><span class="inner">Google<br>で応募</span></a></li>
                                    <li class="btnSnsYh1"><a href="<?php assign(Util::rewriteUrl( 'auth', 'campaign_login', '', array('platform' => 'yh', 'redirect_url' => urlencode(Util::getCurrentUrl()), 'cp_id' => $data['action_info']['cp']['id']))) ?>" onclick="ga('<?php assign(config('Analytics.TrackerName')) ?>.send', 'event', 'yh-login', '<?php assign('campaigns_' . $data['action_info']['cp']['id']);?>', location.href, {'page': '<?php assign($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']) ?>'});" title="Yahoo!" class="arrow1"><span class="inner">Yahoo!<br><span class="space"> </span>JAPAN ID<br>で応募</span></a></li>
                                <?php endif;?>
                                <?php if($data['canLoginByLinkedIn']): ?>
                                    <li class="btnSnsIn1"><a href="<?php assign(Util::rewriteUrl( 'auth', 'campaign_login', '', array('platform' => 'linkedin', 'redirect_url' => urlencode(Util::getCurrentUrl()), 'cp_id' => $data['action_info']['cp']['id']))) ?>" onclick="ga('<?php assign(config('Analytics.TrackerName')) ?>.send', 'event', 'linkedin-login', '<?php assign('campaigns_' . $data['action_info']['cp']['id']);?>', location.href, {'page': '<?php assign($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']) ?>'});" title="LinkedIn" class="arrow1"><span class="inner">LinkedIn<br>で応募</span></a></li>
                                <?php endif; ?>
                            </ul>
                        </form>

                        <h2 class="jsLoginAddressHeader">メールアドレスで応募</h2>
                        <div class="jsLoginAddressWrap" style="position: relative; overflow: hidden;">
                            <?php write_html($this->parseTemplate($data['template_file'], $data)); ?>
                        </div>
                        <?php write_html( $this->parseTemplate('Cooperation.php', array('brand' => $data['brand'], 'action' => '応募'))) ?>
                        <!-- /.campaignJoin --></div>

                <?php endif; ?>

                <?php else: ?>

                    <form>
                        <?php if (!$data["cp"]->isOverTime() && $data["cp"]->isOverLimitWinner()): ?>
                            <?php if ($data["cp"]->selection_method == CpCreator::ANNOUNCE_FIRST): ?>
                                <p class="joinLimit"><?php assign(config("@message.userMessage.cp_join_limit.msg")) ?></p>
                            <?php elseif ($data["cp"]->selection_method == CpCreator::ANNOUNCE_LOTTERY): ?>
                                <p class="joinLimit"><?php assign(config("@message.userMessage.cp_winner_limit.msg")) ?></p>
                            <?php endif; ?>
                        <?php endif; ?>
                        <ul class="btnSet">
                            <li class="btn1"><span class="large1"><?php assign($data["action_info"]["concrete_action"]["button_label_text"]); ?></span></li>
                            <!-- /.btnSet --></ul>
                    </form>

                <?php endif; ?>

                <?php write_html($this->parseTemplate('CampaignsAnnounce.php', $data)); ?>

                <!-- /.campaign --></section>

            <?php
            // TODO SynExtension 使うとき外す
            // write_html($this->parseTemplate('SynExtension.php', array('brand_id' => $data['cp']->brand_id, 'visible' => true)));
            ?>

            <!-- /.messageWrap --></section>
    </article>

    <span id="jsPathInfoForGA" style="display:none;"><?php assign($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']) ?></span>


<?php if (config('Stage') === 'product'): ?>
    <span class="jsGoogleAnalyticsTrackingAction"
          data-product='{"id": "P<?php assign($data['cp_id']); ?>", "name": "campaign_<?php assign($data['cp_id']); ?>"}'
          data-action="detail"></span>
<?php endif ?>

<?php write_html($this->parseTemplate('auth/CompletePasswordIssueModal.php')); ?>
<?php write_html($this->scriptTag('BrandcoLoggingFormService'))?>
<?php write_html($this->scriptTag('auth/AuthModalService'))?>
<?php write_html($this->scriptTag('auth/MailAuthFormService'))?>

<?php $data['pageStatus']['extend_tag'] = $data["action_info"]["cp"]["extend_tag"] ?>
<?php write_html($this->parseTemplate('BrandcoFooter.php', $data['pageStatus'])); ?>