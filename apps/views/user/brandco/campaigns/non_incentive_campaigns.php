<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoHeader')->render($data['pageStatus'])) ?>
<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoTopHeader')->render($data['pageStatus'])) ?>

<?php
if ($data['cp_action']->type == CpAction::TYPE_QUESTIONNAIRE) {
    $comm_join_class = 'jsPreCheckCommJoinForm';
    $cp_join_class = 'jsPreCheckCpJoinForm';
    write_html($this->parseTemplate('ImagePreviewModal.php'));
    write_html('<span id="jsHasOpeningCpQuestionnaire" style="display: none;"></span>');
} else {
    $comm_join_class = 'jsDefaultCommJoinForm';
    $cp_join_class = 'jsDefaultCpJoinForm';
}
?>

    <article>

        <section class="messageWrap">

            <section class="campaign jsCampaignFormWrap" id="cp_<?php assign($data["action_info"]["cp"]["id"]); ?>" >

                <?php if($data["action_info"]["concrete_action"]["image_url"]): ?>
                    <p class="campaignImg"><img src="<?php assign($data["action_info"]["concrete_action"]["image_url"]); ?>" width="690" height="280" alt="campaign img"></p>
                <?php endif;?>

                <?php $message_text = $data['action_info']['concrete_action']["html_content"] ? $data['action_info']['concrete_action']["html_content"] : $this->toHalfContentDeeply($data['action_info']['concrete_action']["text"]); ?>
                <section class="campaignText"><?php write_html($message_text); ?></section>

                <?php if ($data['pageStatus']['isNotMatchDemography']): ?>
                    <p class="joinLimit" id="joinLimit"><?php write_html($data['pageStatus']['demographyErrors']) ?></p>
                <?php endif ?>

                <?php if ($data['cp_action']->type == CpAction::TYPE_QUESTIONNAIRE): ?>
                    <?php write_html(aafwWidgets::getInstance()->loadWidget('OpeningCpQuestionnaireActionForm')->render(array(
                        'cp_action' => $data['cp_action'],
                        'join_status' => !($data['action_info']['cp']['can_entry'] && !$data['pageStatus']['isNotMatchDemography'])
                    ))); ?>
                <?php elseif ($data['cp_action']->type == CpAction::TYPE_PAYMENT): ?>
                    <?php write_html(aafwWidgets::getInstance()->loadWidget('OpeningCpPaymentActionForm')->render(array(
                        'cp_action' => $data['cp_action'],
                        'join_status' => !($data['action_info']['cp']['can_entry'] && !$data['pageStatus']['isNotMatchDemography'])
                    ))); ?>
                <?php endif ?>

                <?php if($data['cp_action']->type != CpAction::TYPE_PAYMENT):?><?php //決済の場合はログインの動線まるっと消す?>
                <?php if ($data['action_info']['cp']['can_entry'] && !$data['pageStatus']['isNotMatchDemography']): ?>

                    <?php $isLogin = $data['pageStatus']["isLogin"] && $data["userInfo"] != null && $data["userInfo"]->id;?>

                    <?php if ($data['action_info']['cp']['is_au_campaign']): ?>
                        <div class="au-thirdParty">
                            <h1>参加はこちらから</h1>
                            <ul class="au-btnSet">
                                <li class="au-btn5"><a href="javascript:void(0);" data-href="<?php assign($data['action_info']['cp']['au_login_url']) ?>" class="large1 <?php assign($comm_join_class) ?>" onclick="ga('<?php assign(config('Analytics.TrackerName')) ?>.send', 'event', 'au-join', '<?php assign('campaigns_' . $data['action_info']['cp']['id']);?>', location.href, {'page': '<?php assign($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']) ?>'});"><?php assign($data["action_info"]["concrete_action"]["button_label_text"]); ?></a></li>
                                <!-- /.btnSet --></ul>
                            <p class="supplement1">※ご参加にはauスマートパスの会員登録と、モニプラ・<a href="<?php assign('//'.config('Domain.aaid'))?>" target="_blank"><img src="<?php assign($this->setVersion('/img/icon/iconAlliedID2.png')); ?>" alt="アライドID">アライドID</a>の登録が必要です。<br>
                                <a href="<?php assign('//'.config('Domain.aaid'))?>/agreement" target="_blank">アライドID利用規約</a>
                            <?php if($data['brand']->id == 479): ?>
                                、<a href="<?php assign(Util::rewriteUrl('page', 'privacy')); ?>" class="openNewWindow1" target="_blank">一般社団法人 日本健康生活推進協会 個人情報保護方針</a>
                            <?php else: ?>
                                <?php if ($data['brand']->agreement) : ?>
                                    、<?php write_html('<a href="' . '//' . config('Domain.aaid') . '/agreement" target="_blank">' . $data['brand']->name . '公式ファンサイト利用規約</a>'); ?>
                                <?php endif; ?>
                            <?php endif; ?>
                                に同意の上、ご応募ください。 </p>
                        <!-- /.au-thirdParty--></div>
                    <?php elseif($data['cp']->join_limit_sns_flg == Cp::JOIN_LIMIT_SNS_ON): ?>
                        <form>
                            <div class="joinCommSite">
                                <?php if( $data['action_info']['cp']['join_limit_sns_without_platform'] ):?>
                                    <h1>お持ちのアカウントの参加ボタンで、アンケートに回答しよう！</h1>
                                    <ul class="btnSet">
                                        <?php if(in_array(SocialAccountService::SOCIAL_MEDIA_FACEBOOK, $data['action_info']['cp']['join_limit_sns'])):?>
                                            <li class="btnSnsFb1"><a class="arrow1 <?php assign($comm_join_class) ?>" href="javascript:void(0)" data-href="<?php assign(Util::rewriteUrl( 'auth', 'campaign_login', '', array('platform' => 'fb', 'redirect_url' => urlencode(Util::getCurrentUrl()), 'cp_id' => $data['action_info']['cp']['id']))) ?>" onclick="ga('<?php assign(config('Analytics.TrackerName')) ?>.send', 'event', 'fb-login', '<?php assign('campaigns_' . $data['action_info']['cp']['id']);?>', location.href, {'page': '<?php assign($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']) ?>'});" title="Facebook"><span class="inner">Facebook<br>で参加</span></a></li>
                                        <?php endif;?>
                                        <?php if(in_array(SocialAccountService::SOCIAL_MEDIA_TWITTER, $data['action_info']['cp']['join_limit_sns'])):?>
                                            <li class="btnSnsTw1"><a class="arrow1 <?php assign($comm_join_class) ?>" href="javascript:void(0)" data-href="<?php assign(Util::rewriteUrl( 'auth', 'campaign_login', '', array('platform' => 'tw', 'redirect_url' => urlencode(Util::getCurrentUrl()), 'cp_id' => $data['action_info']['cp']['id']))) ?>" onclick="ga('<?php assign(config('Analytics.TrackerName')) ?>.send', 'event', 'tw-login', '<?php assign('campaigns_' . $data['action_info']['cp']['id']);?>', location.href, {'page': '<?php assign($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']) ?>'});" title="Twitter"><span class="inner">Twitter<br>で参加</span></a></li>
                                        <?php endif;?>
                                        <?php if (in_array(SocialAccountService::SOCIAL_MEDIA_LINE, $data['action_info']['cp']['join_limit_sns'])): ?>
                                            <li class="btnSnsLn1"><a class="arrow1 <?php assign($comm_join_class) ?>" href="javascript:void(0)" data-href="<?php assign(Util::rewriteUrl('auth', 'campaign_login', '', array('platform' => 'line', 'redirect_url' => urlencode(Util::getCurrentUrl()), 'cp_id' => $data['action_info']['cp']['id']))) ?>" onclick="ga('<?php assign(config('Analytics.TrackerName')) ?>.send', 'event', 'ln-login', '<?php assign('campaigns_' . $data['action_info']['cp']['id']);?>', location.href, {'page': '<?php assign($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']) ?>'});" title="LINE"><span class="inner">LINE<br>で参加</span></a></li>
                                        <?php endif ?>
                                        <?php if(!$data['pageStatus']['is_sugao_brand']):?>
                                            <?php if(in_array(SocialAccountService::SOCIAL_MEDIA_INSTAGRAM, $data['action_info']['cp']['join_limit_sns'])): ?>
                                                <li class="btnSnsIg1"><a class="arrow1 <?php assign($comm_join_class) ?>" href="javascript:void(0)" data-href="<?php assign(Util::rewriteUrl( 'auth', 'campaign_login', '', array('platform' => 'insta', 'redirect_url' => urlencode(Util::getCurrentUrl()), 'cp_id' => $data['action_info']['cp']['id']))) ?>" onclick="ga('<?php assign(config('Analytics.TrackerName')) ?>.send', 'event', 'ig-login', '<?php assign('campaigns_' . $data['action_info']['cp']['id']);?>', location.href, {'page': '<?php assign($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']) ?>'});" title="Instagram"><span class="inner">Instagram<br>で参加</span></a></li>
                                            <?php endif;?>
                                            <?php if(in_array(SocialAccountService::SOCIAL_MEDIA_GOOGLE, $data['action_info']['cp']['join_limit_sns'])):?>
                                                <li class="btnSnsGp1"><a class="arrow1 <?php assign($comm_join_class) ?>" href="javascript:void(0)" data-href="<?php assign(Util::rewriteUrl( 'auth', 'campaign_login', '', array('platform' => 'ggl', 'redirect_url' => urlencode(Util::getCurrentUrl()), 'cp_id' => $data['action_info']['cp']['id']))) ?>" onclick="ga('<?php assign(config('Analytics.TrackerName')) ?>.send', 'event', 'ggl-login', '<?php assign('campaigns_' . $data['action_info']['cp']['id']);?>', location.href, {'page': '<?php assign($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']) ?>'});" title="Google"><span class="inner">Google<br>で参加</span></a></li>
                                            <?php endif;?>
                                            <?php if(in_array(SocialAccountService::SOCIAL_MEDIA_YAHOO, $data['action_info']['cp']['join_limit_sns'])):?>
                                                <li class="btnSnsYh1"><a class="arrow1 <?php assign($comm_join_class) ?>" href="javascript:void(0)" data-href="<?php assign(Util::rewriteUrl( 'auth', 'campaign_login', '', array('platform' => 'yh', 'redirect_url' => urlencode(Util::getCurrentUrl()), 'cp_id' => $data['action_info']['cp']['id']))) ?>" onclick="ga('<?php assign(config('Analytics.TrackerName')) ?>.send', 'event', 'yh-login', '<?php assign('campaigns_' . $data['action_info']['cp']['id']);?>', location.href, {'page': '<?php assign($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']) ?>'});" title="Yahoo!"><span class="inner">Yahoo!<br><span class="space"> </span>JAPAN ID<br>で参加</span></a></li>
                                            <?php endif;?>
                                        <?php endif;?>
                                        <?php if(in_array(SocialAccountService::SOCIAL_MEDIA_LINKEDIN, $data['action_info']['cp']['join_limit_sns'])):?>
                                            <li class="btnSnsIn1"><a class="arrow1 <?php assign($comm_join_class) ?>" href="javascript:void(0)" data-href="<?php assign(Util::rewriteUrl( 'auth', 'campaign_login', '', array('platform' => 'linkedin', 'redirect_url' => urlencode(Util::getCurrentUrl()), 'cp_id' => $data['action_info']['cp']['id']))) ?>" onclick="ga('<?php assign(config('Analytics.TrackerName')) ?>.send', 'event', 'linkedin-login', '<?php assign('campaigns_' . $data['action_info']['cp']['id']);?>', location.href, {'page': '<?php assign($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']) ?>'});" title="LinkedIn"><span class="inner">LinkedIn<br>で参加</span></a></li>
                                        <?php endif;?>
                                    </ul>
                                <?php endif;?>
                                <?php if(in_array(SocialAccountService::SOCIAL_MEDIA_PLATFORM, $data['action_info']['cp']['join_limit_sns'])):?>
                                    <h2 class="jsLoginAddressHeader">メールアドレスで参加</h2>
                                    <div class="jsLoginAddressWrap" style="position: relative; overflow: hidden;">
                                        <?php write_html($this->parseTemplate($data['template_file'], $data)); ?>
                                    </div>
                                <?php endif;?>
                                <?php write_html( $this->parseTemplate('Cooperation.php', array('brand' => $data['brand'], 'action' => '応募'))) ?>
                                <!-- /.campaignJoin --></div>
                        </form>
                    <?php elseif($isLogin): ?>

                        <form name="campaignJoinForm" action="<?php assign(Util::rewriteUrl('messages', 'join', '', '', '', true)); ?>" class="jsCampaignJoinForm" method="POST" enctype="multipart/form-data" >
                            <?php write_html($this->csrf_tag()); ?>
                            <?php write_html($this->formHidden('cp_id', $data["action_info"]['cp']["id"])); ?>
                            <ul class="btnSet">
                                <li class="btn3"><a href="javascript:void(0);" class="large1 <?php assign($cp_join_class) ?>"><?php assign($data["action_info"]["concrete_action"]["button_label_text"]); ?></a></li>
                                <!-- /.btnSet --></ul>
                        </form>

                    <?php else: ?>
                        <div class="joinCommSite">
                            <h1 class="jsLoginSnsHeader">お持ちのアカウントの参加ボタンで、アンケートに回答しよう！</h1>
                            <ul class="btnSet">
                                <li class="btnSnsFb1"><a class="arrow1 <?php assign($comm_join_class) ?>" href="javascript:void(0)" data-href="<?php assign(Util::rewriteUrl( 'auth', 'campaign_login', '', array('platform' => 'fb', 'redirect_url' => urlencode(Util::getCurrentUrl()), 'cp_id' => $data['action_info']['cp']['id']))) ?>" onclick="ga('<?php assign(config('Analytics.TrackerName')) ?>.send', 'event', 'fb-login', '<?php assign('campaigns_' . $data['action_info']['cp']['id']);?>', location.href, {'page': '<?php assign($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']) ?>'});" title="Facebook"><span class="inner">Facebook<br>で参加</span></a></li>
                                <li class="btnSnsTw1"><a class="arrow1 <?php assign($comm_join_class) ?>" href="javascript:void(0)" data-href="<?php assign(Util::rewriteUrl( 'auth', 'campaign_login', '', array('platform' => 'tw', 'redirect_url' => urlencode(Util::getCurrentUrl()), 'cp_id' => $data['action_info']['cp']['id']))) ?>" onclick="ga('<?php assign(config('Analytics.TrackerName')) ?>.send', 'event', 'tw-login', '<?php assign('campaigns_' . $data['action_info']['cp']['id']);?>', location.href, {'page': '<?php assign($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']) ?>'});" title="Twitter"><span class="inner">Twitter<br>で参加</span></a></li>
                                <li class="btnSnsLn1"><a class="arrow1 <?php assign($comm_join_class) ?>" href="javascript:void(0)" data-href="<?php assign(Util::rewriteUrl( 'auth', 'campaign_login', '', array('platform' => 'line', 'redirect_url' => urlencode(Util::getCurrentUrl()), 'cp_id' => $data['action_info']['cp']['id']))) ?>" onclick="ga('<?php assign(config('Analytics.TrackerName')) ?>.send', 'event', 'ln-login', '<?php assign('campaigns_' . $data['action_info']['cp']['id']);?>', location.href, {'page': '<?php assign($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']) ?>'});" title="LINE"><span class="inner">LINE<br>で参加</span></a></li>
                                <?php if(!$data['pageStatus']['is_sugao_brand']):?>
                                    <li class="btnSnsIg1"><a class="arrow1 <?php assign($comm_join_class) ?>" href="javascript:void(0)" data-href="<?php assign(Util::rewriteUrl( 'auth', 'campaign_login', '', array('platform' => 'insta', 'redirect_url' => urlencode(Util::getCurrentUrl()), 'cp_id' => $data['action_info']['cp']['id']))) ?>" onclick="ga('<?php assign(config('Analytics.TrackerName')) ?>.send', 'event', 'ig-login', '<?php assign('campaigns_' . $data['action_info']['cp']['id']);?>', location.href, {'page': '<?php assign($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']) ?>'});" title="Instagram"><span class="inner">Instagram<br>で参加</span></a></li>
                                    <li class="btnSnsGp1"><a class="arrow1 <?php assign($comm_join_class) ?>" href="javascript:void(0)" data-href="<?php assign(Util::rewriteUrl( 'auth', 'campaign_login', '', array('platform' => 'ggl', 'redirect_url' => urlencode(Util::getCurrentUrl()), 'cp_id' => $data['action_info']['cp']['id']))) ?>" onclick="ga('<?php assign(config('Analytics.TrackerName')) ?>.send', 'event', 'ggl-login', '<?php assign('campaigns_' . $data['action_info']['cp']['id']);?>', location.href, {'page': '<?php assign($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']) ?>'});" title="Google"><span class="inner">Google<br>で参加</span></a></li>
                                    <li class="btnSnsYh1"><a class="arrow1 <?php assign($comm_join_class) ?>" href="javascript:void(0)" data-href="<?php assign(Util::rewriteUrl( 'auth', 'campaign_login', '', array('platform' => 'yh', 'redirect_url' => urlencode(Util::getCurrentUrl()), 'cp_id' => $data['action_info']['cp']['id']))) ?>" onclick="ga('<?php assign(config('Analytics.TrackerName')) ?>.send', 'event', 'yh-login', '<?php assign('campaigns_' . $data['action_info']['cp']['id']);?>', location.href, {'page': '<?php assign($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']) ?>'});" title="Yahoo!"><span class="inner">Yahoo!<br><span class="space"> </span>JAPAN ID<br>で参加</span></a></li>
                                <?php endif; ?>
                                <?php if($data['canLoginByLinkedIn']): ?>
                                    <li class="btnSnsIn1"><a class="arrow1 <?php assign($comm_join_class) ?>" href="javascript:void(0)" data-href="<?php assign(Util::rewriteUrl( 'auth', 'campaign_login', '', array('platform' => 'linkedin', 'redirect_url' => urlencode(Util::getCurrentUrl()), 'cp_id' => $data['action_info']['cp']['id']))) ?>" onclick="ga('<?php assign(config('Analytics.TrackerName')) ?>.send', 'event', 'linkedin-login', '<?php assign('campaigns_' . $data['action_info']['cp']['id']);?>', location.href, {'page': '<?php assign($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']) ?>'});" title="LinkedIn"><span class="inner">LinkedIn<br>で参加</span></a></li>
                                <?php endif; ?>
                            </ul>

                            <h2 class="jsLoginAddressHeader">メールアドレスで参加</h2>
                            <div class="jsLoginAddressWrap" style="position: relative; overflow: hidden;">
                                <?php write_html($this->parseTemplate($data['template_file'], $data)); ?>
                            </div>
                            <?php write_html( $this->parseTemplate('Cooperation.php', array('brand' => $data['brand'], 'action' => '応募'))) ?>
                            <!-- /.campaignJoin --></div>
                    <?php endif ?>

                <?php else: ?>

                    <form>
                        <ul class="btnSet">
                            <li class="btn1"><span class="large1"><?php assign($data["action_info"]["concrete_action"]["button_label_text"]); ?></span></li>
                            <?php if ($data["cp"]->isOverLimitWinner()): ?>
                                <?php if ($data["cp"]->selection_method == CpCreator::ANNOUNCE_FIRST): ?>
                                    <li><p class="attention1"><?php assign(config("@message.userMessage.cp_join_limit.msg")) ?></p></li>
                                <?php elseif ($data["cp"]->selection_method == CpCreator::ANNOUNCE_LOTTERY): ?>
                                    <li><p class="attention1"><?php assign(config("@message.userMessage.cp_winner_limit.msg")) ?></p></li>
                                <?php endif; ?>
                            <?php endif; ?>
                            <!-- /.btnSet --></ul>
                    </form>

                <?php endif; ?>
                <?php endif;?>
                <?php write_html($this->parseTemplate('CampaignsAnnounce.php', $data)); ?>

                <!-- /.campaign --></section>

            <?php
            // TODO SynExtension 使うとき外す
            // write_html($this->parseTemplate('SynExtension.php', array('brand_id' => $data['cp']->brand_id, 'visible' => true)));
            ?>

            <!-- /.messageWrap --></section>
    </article>

<?php write_html($this->parseTemplate('auth/CompletePasswordIssueModal.php')); ?>
<?php write_html($this->scriptTag('BrandcoLoggingFormService'))?>

<?php $data['pageStatus']['extend_tag'] = $data["action_info"]["cp"]["extend_tag"] ?>
<?php write_html($this->scriptTag('user/OpeningCpActionService')); ?>
<?php write_html($this->parseTemplate('BrandcoFooter.php', $data['pageStatus'])); ?>