<?php
$cp =  CpInfoContainer::getInstance()->getCpById($data['cp_user']->cp_id);
$entry_questionnaires = $data['entry_questionnaire_data']['entry_questionnaires'];
$has_entry_questionnaire = $data['entry_questionnaire_data']['has_entry_questionnaire'];

if ($cp->requireRestriction($data['cp_user'])) {
    $api_url = Util::rewriteUrl('messages', 'api_update_demography.json');
} elseif ($has_entry_questionnaire || $data['pageStatus']['needDisplayPersonalForm']) {
    $api_url = Util::rewriteUrl('messages', "api_update_personal_info_and_execute_entry.json");
} else {
    $api_url = Util::rewriteUrl('messages', "api_execute_entry_action.json");
}
?>
<section class="campaign jsMessage" id="message_<?php assign($data['message_info']["message"]->id); ?>" >
    <a id="<?php assign('ca_' . $data["message_info"]["cp_action"]->id) ?>"></a>

    <form class="executeEntryActionForm" action="<?php assign($api_url); ?>" method="POST" enctype="multipart/form-data" >

        <?php write_html($this->csrf_tag()); ?>
        <?php write_html($this->formHidden('cp_action_id', $data['message_info']["cp_action"]->id)); ?>
        <?php write_html($this->formHidden('cp_user_id', $data['cp_user']->id)); ?>

        <?php if ($data["message_info"]["concrete_action"]->image_url): ?>
            <p class="campaignImg"><img src="<?php assign($data["message_info"]["concrete_action"]->image_url); ?>" alt="campaign img"></p>
        <?php endif; ?>
        <?php $message_text = $data['message_info']['concrete_action']->html_content ? $data['message_info']['concrete_action']->html_content : $this->toHalfContentDeeply($data['message_info']['concrete_action']->text); ?>
        <?php write_html(aafwWidgets::getInstance()->loadWidget('SynCampaignText')->render(array('cp'=>$cp))) ?>
        <section class="campaignText"><?php write_html($message_text); ?></section>

        <?php if ($data['pageStatus']['isNotMatchDemography']): ?>
            <p class="joinLimit" id="joinLimit"><?php write_html($data['pageStatus']['demographyErrors']) ?></p>
        <?php endif ?>

        <?php if (!$cp->isOverTime() && $cp->isOverLimitWinner()): ?>
            <?php if ($cp->selection_method == CpCreator::ANNOUNCE_LOTTERY): ?>
                <p class="joinLimit"><?php assign(config("@message.userMessage.cp_winner_limit.msg")) ?></p>
            <?php endif; ?>
        <?php endif; ?>

        <ul class="btnSet">
            <?php if ($cp->canEntry(RequestuserInfoContainer::getInstance()->getStatusByCp($cp)) && $data["message_info"]["action_status"]->status == CpUserActionStatus::NOT_JOIN): ?>
                <li class="btn3"><a class="cmd_execute_entry_action large1" href="javascript:void(0)"><?php assign($data["message_info"]["concrete_action"]->button_label_text); ?></a></li>
            <?php else: ?>
                <?php if ($cp->isAuCampaign()): ?>
                    <li class="btn3"><span class="large1">応募済み</span></li>
                <?php elseif (!$cp->canEntry(RequestuserInfoContainer::getInstance()->getStatusByCp($cp))): ?>
                    <li class="btn1"><span class="large1">終了しました</span></li>
                <?php elseif ($cp->join_limit_sns_flg == Cp::JOIN_LIMIT_SNS_ON): ?>
                    <?php if ($data['cp_user']->join_sns == SocialAccountService::SOCIAL_MEDIA_FACEBOOK): ?>
                        <li class="btnSnsFb1"><span class="arrow1"><span class="inner">Facebook<br>で応募</span></span></li>
                    <?php elseif ($data['cp_user']->join_sns == SocialAccountService::SOCIAL_MEDIA_TWITTER): ?>
                        <li class="btnSnsTw1"><span class="arrow1"><span class="inner">Twitter<br>で応募</span></span></li>
                    <?php elseif ($data['cp_user']->join_sns == SocialAccountService::SOCIAL_MEDIA_LINE): ?>
                        <li class="btnSnsLn1"><span class="arrow1"><span class="inner">LINE<br>で応募</span></span></li>
                    <?php elseif ($data['cp_user']->join_sns == SocialAccountService::SOCIAL_MEDIA_INSTAGRAM): ?>
                        <li class="btnSnsIg1"><span class="arrow1"><span class="inner">Instagram<br>で応募</span></span></li>
                    <?php elseif ($data['cp_user']->join_sns == SocialAccountService::SOCIAL_MEDIA_GOOGLE): ?>
                        <li class="btnSnsGp1"><span class="arrow1"><span class="inner">Google<br>で応募</span></span></li>
                    <?php elseif ($data['cp_user']->join_sns == SocialAccountService::SOCIAL_MEDIA_YAHOO): ?>
                        <li class="btnSnsYh1"><span class="arrow1"><span class="inner">Yahoo!<br><span class="space"> </span>JAPAN ID<br>で応募</span></span></li>
                    <?php elseif ($data['cp_user']->join_sns == SocialAccountService::SOCIAL_MEDIA_LINKEDIN): ?>
                        <li class="btnSnsTw1"><span class="arrow1"><span class="inner">Twitter<br>で応募</span></span></li>
                        <li class="btnSnsIn1"><span class="arrow1"><span class="inner">LinkedIn<br>で応募</span></span></li>
                    <?php else : ?>
                        <li class="btn3"><span class="large1"><?php assign($data["message_info"]["concrete_action"]->button_label_text); ?></span></li>
                    <?php endif; ?>
                <?php else: ?>
                    <li class="btn3"><span class="large1"><?php assign($data["message_info"]["concrete_action"]->button_label_text); ?></span></li>
                <?php endif; ?>
            <?php endif; ?>
        </ul>
        <?php if($cp->join_limit_flg == Cp::JOIN_LIMIT_OFF && $cp->share_flg == Cp::FLAG_SHOW_VALUE):?>
            <div class="campaignShare">
                <p>このキャンペーンを友達に知らせよう</p>
                <ul class="snsBtns-box">
                    <li><div class="fb-like" data-href="<?php assign($data["action_info"]["cp"]["url"]) ?>" data-layout="button_count" data-action="like" data-show-faces="false" data-share="false"></div></li
                        ><li><a href="https://twitter.com/share" data-url="<?php assign($data["cp_info"]["cp"]["url"]) ?>" class="twitter-share-button" data-lang="ja" data-count="vertical" data-text="<?php assign($data['cp_info']['tweet_share_text']) ?>">ツイート</a></li
                        ><li class="line"><span><script type="text/javascript" src="//media.line.me/js/line-button.js?v=20140411" ></script><script type="text/javascript">new media_line_me.LineButton({"pc":false,"lang":"ja","type":"a", "withUrl":false, "text": "<?php assign($data["cp_info"]["cp"]["url"]) ?>"});</script></span></li
                        ><li><a href="http://b.hatena.ne.jp/entry/<?php assign($data['cp_info']['cp']['url'])?>" class="hatena-bookmark-button" data-hatena-bookmark-title="<?php assign($data['pageStatus']['og']['title'])?>" data-hatena-bookmark-layout="simple-balloon" data-hatena-bookmark-lang="ja" title="このエントリーをはてなブックマークに追加"><img src="https://b.st-hatena.com/images/entry-button/button-only@2x.png" alt="このエントリーをはてなブックマークに追加" width="20" height="20" style="border: none;" /></a><script type="text/javascript" src="https://b.st-hatena.com/js/bookmark_button.js" charset="utf-8" async="async"></script></li
                        ><li><div class="g-plusone" data-size="medium" data-href="<?php assign($data["cp_info"]["cp"]["url"]) ?>"></div></li>
                    <!-- /.snsBtns --></ul>
                <!-- /.campaignShare --></div>
        <?php endif;?>

        <ul class="campaignData">

            <?php if($data["cp_info"]["cp"]["show_winner_label"] == Cp::FLAG_SHOW_VALUE): ?>
                <li class="present"><span class="itemTitle">プレゼント</span><span class="itemData"><?php assign($data["cp_info"]["cp"]["winner_label"]); ?></span></li>
            <?php else : ?>
                <li class="present"><span class="itemTitle">プレゼント</span><span class="itemData"><?php assign($data["cp_info"]["cp"]["winner_count"]); ?>名様</span></li>
            <?php endif; ?>

                <li class="term"><span class="itemTitle">開催期間</span><span class="itemData"><?php assign($data["cp_info"]["cp"]["start_datetime"]); ?> ～ <?php assign($data["cp_info"]["cp"]["end_datetime"]); ?></span></li>

            <li class="result">
                <?php if ($data['cp_info']['cp']['shipping_method'] == Cp::SHIPPING_METHOD_PRESENT || $cp->selection_method == CpCreator::ANNOUNCE_LOTTERY): ?>
                    <span class="itemTitle">発表</span>
                <?php else: ?>
                    <span class="itemTitle">発表日</span>
                <?php endif; ?>

                <span class="itemData">
                    <?php if ($data['cp_info']['cp']['announce_display_label_use_flg'] == 1): ?>
                        <?php asign($data['cp_info']['cp']['announce_display_label']) ?>
                    <?php elseif ($data["cp_info"]["cp"]["shipping_method"] == Cp::SHIPPING_METHOD_PRESENT): ?>
                        賞品の発送をもって発表
                    <?php elseif ($cp->selection_method == CpCreator::ANNOUNCE_LOTTERY): ?>
                        スピードくじの結果により即時
                    <?php else: ?>
                        <?php assign($data["cp_info"]["cp"]["announce_date"]); ?>
                    <?php endif; ?>
                </span>
            </li>
            <li class="sponsor"><span class="itemTitle">開催</span><span class="itemData"><?php assign($data["cp_info"]["cp"]["sponsor"]); ?></span></li>
            <?php if($data["cp_info"]["cp"]["show_recruitment_note"] == Cp::FLAG_SHOW_VALUE): ?>
                <li class="attention"><span class="itemTitle">注意事項</span><span class="itemData"><?php write_html($this->toHalfContent($data["cp_info"]["cp"]["recruitment_note"], false)); ?></span></li>
            <?php endif; ?>

            <!-- /.campaignData --></ul>

        <!-- /.btnSet --></ul>
    </form>

    <!-- /.message --></section>
<?php
if (!$data['cp_user']->isNotMatchDemography() && (($data['pageStatus']['isFirstEntryRead'] && $has_entry_questionnaire) || $data['pageStatus']['needDisplayUserProfileForm'] || $data['pageStatus']['needDisplayPersonalForm'])) {
    if ($data['pageStatus']['needDisplayUserProfileForm']) {
        write_html('<section class="message jsMessage">');
        write_html($this->parseTemplate('auth/UserProfileForm.php', array(
            'is_api' => true,
            'need_display_personal_form' => $data['pageStatus']['needDisplayPersonalForm'],
            'cp_id' => $data['cp_user']->cp_id,
            'cp_action_id' => $data['message_info']["cp_action"]->id,
            'cp_user_id' => $data['cp_user']->id,
            'parent_class_name' => 'message',
            'brand' => $data['brand'],
            'pageStatus' => $data['pageStatus'],
            'ActionForm' => $this->ActionForm,
            'ActionError' => $this->ActionError
        )));
        write_html('</section>');
    }

    if ($data['pageStatus']['needDisplayPersonalForm']) {
        if ($data['pageStatus']['needDisplayUserProfileForm']) {
            write_html('<section class="message jsMessageHidden" style="display: none;">');
        } else {
            write_html('<section class="message jsMessage">');
        }

        write_html(aafwWidgets::getInstance()->loadWidget('BrandcoSignupForm')->render(array(
            'is_api' => true,
            'cp' => $cp,
            'parent_class_name' => 'message',
            'brand' => $data['pageStatus']['brand'],
            'ActionForm' => $this->ActionForm,
            'ActionError' => $this->ActionError,
            'entry_questionnaires' => $entry_questionnaires,
            'entry_questionnaire_only' => $has_entry_questionnaire && $data['pageStatus']['isFirstEntryRead'] && !$cp->requireRestriction($data['cp_user']),
            'brands_users_relation_id' => $data['brands_users_relation_id'],
            'ignore_prefill' => $data['ignore_prefill']
        )));
        write_html('</section>');
    }
}
?>
<?php write_html($this->scriptTag('user/UserActionEntryService')); ?>
