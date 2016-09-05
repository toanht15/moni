<section class="messageWrap" id="message_<?php assign($data['message_info']["message"]->id); ?>" >
    <a id="<?php assign('ca_' . $data["message_info"]["cp_action"]->id) ?>"></a>

    <form class="" action="" id="instantWinApi"
          data-execute-url="<?php assign(Util::rewriteUrl('messages', "api_execute_instant_win_action.json")); ?>"
          data-execute-class="executeInstantWinActionForm"
          data-pre-execute-url="<?php assign(Util::rewriteUrl('messages', "api_pre_execute_draw_instant_win_action.json")); ?>"
          data-pre-execute-class="preExecuteDrawInstantWinActionForm"
          method="POST">

        <?php write_html($this->csrf_tag()); ?>
        <?php write_html($this->formHidden('cp_action_id', $data['message_info']["cp_action"]->id)); ?>
        <?php write_html($this->formHidden('cp_user_id', $data['cp_user']->id)); ?>
        <?php write_html($this->formHidden('user_id', $data['cp_user']->user_id)); ?>
        <?php write_html($this->formHidden('next_time', $data['next_time'])); ?>
        <?php write_html($this->formHidden('ani_draw', $this->setVersion($data['isDoubleUpChallenge'] ? '/img/module/instantWin/animeLucky_draw_doubleUp.gif' : '/img/module/instantWin/animeLucky_draw1.gif'))); ?>
        <?php write_html($this->formHidden('ani_win', $this->setVersion('/img/module/instantWin/animeLucky_win1.gif'))); ?>
        <?php write_html($this->formHidden('ani_lose', $this->setVersion('/img/module/instantWin/animeLucky_lose1.gif'))); ?>
        <?php write_html($this->formHidden('back_monipla_media_default', $data['message_info']['back_monipla_media_default'])); ?>
        <?php write_html($this->formHidden('messageid', $data['message_info']["message"]->id)); ?>
        <?php write_html($this->formHidden('animation_time', $data['isDoubleUpChallenge'] ? 12300 : 6400)); ?>
        <?php write_html($this->formHidden('is_visible_next_button', $data['isVisibleNextButton'] ? 'true' : 'false')); ?>
        <?php write_html($this->formHidden('second_chance_challenge_image', $this->setVersion('/img/campaign/synLotWchance.png'))); ?>
        <?php write_html($this->formHidden('has_second_challenge', $data['hasEmptySecondChallenge'] ? 'true' : 'false')); ?>
        <?php write_html($this->formHidden('tracker_name',config('Analytics.TrackerName'))); ?>
        <?php write_html($this->formHidden('view_number',$data['view_number'])); ?>

        <section class="messageWrap">
            <?php if($data['view_number'] != UserMessageThreadActionInstantWin::DRAW_VIEW): ?>
                <section class="jsMessage"></section>
            <?php endif; ?>

            <section class="message jsMessage">
                <?php if($data['view_number'] == UserMessageThreadActionInstantWin::DRAW_VIEW): ?>
                    <?php $message_text = $data["html_content"] ? $data["html_content"] : $this->toHalfContentDeeply($data["text"]); ?>
                    <p class="messageImg" id="drawImg"><img src="<?php assign($this->setVersion('/img/module/instantWin/animeLucky_start1.gif'))?>"></p>
                    <section class="messageText" id="drawText"><?php write_html($message_text); ?></section>
                    <ul class="btnSet">
                        <li class="btn3" id="drawBtn"><a href="javascript:void(0)" class="cmd_pre_execute_draw_instant_win_action large1">チャレンジする</a></li>
                    </ul>
                <?php elseif($data['view_number'] == UserMessageThreadActionInstantWin::DRAW_FINISH_VIEW || $data['view_number'] == UserMessageThreadActionInstantWin::DRAW_LIMIT_VIEW): ?>
                    <?php $message_text = $data["html_content"] ? $data["html_content"] : $this->toHalfContentDeeply($data["text"]); ?>
                    <p class="messageImg"><img src="<?php assign($this->setVersion('/img/module/instantWin/animeLucky_start1.gif'))?>"></p>
                    <section class="messageText"><?php write_html($message_text); ?></section>
                    <p class="messageLotNext jsMessageLotNext"><strong><?php assign($data['view_number'] == UserMessageThreadActionInstantWin::DRAW_LIMIT_VIEW ? '当選者数に達したため、このキャンペーンは終了致しました。': 'このキャンペーンは終了致しました。')?></strong></p>
                <?php elseif($data['view_number'] == UserMessageThreadActionInstantWin::STAY_WAITING_VIEW): ?>
                    <?php $message_text = $data["instant_win_prize_lose"]->html_content ? $data["instant_win_prize_lose"]->html_content : $this->toHalfContentDeeply($data["instant_win_prize_lose"]->text); ?>
                    <p class="messageImg"><img src="<?php assign($data['isForSyndotOnly'] ? $this->setVersion('/img/module/instantWin/animeLucky_lose2.gif') : $data["instant_win_prize_lose"]->image_url);?>"  width="600" height="300"></p>
                    <section class="messageText"><?php write_html($message_text); ?></section>
                    <p class="messageLotNext jsMessageLotNext">次回参加まであと<br><strong id="timeLeft"></strong></p>
                    <?php if($data['isVisibleNextButton']):?>
                        <?php if($data['hasEmptySecondChallenge']):?>
                            <div class="synLotWchance">
                                <h1>今すぐもう1回挑戦できるWチャンス！</h1>
                                <p><img src="<?php assign($this->setVersion('/img/campaign/synLotWchance.png'))?>" alt="メニューを開いて他のサービスを楽しむ！メニューを経由して毎日ラッキーくじにもう1回チャレンジ！"></p>
                            </div>

                        <?php endif;?>                       
                        <script>
                        setTimeout(function() {
                            openMenu($("#side-menu"));
                            openModalBase($("#side-menu"));
                        }, 2500);
                        </script>
                        <ul class="btnSet"><li class="btn3"><a class="ynLotMenu1" href="javascript:openMenu($('#side-menu'));openModalBase($('#side-menu'));ga('<?php assign(config('Analytics.TrackerName')) ?>.send','event','syndot', 'open_menu');">メニューを開く</a></li></ul>
                    <?php endif;?>
                <?php elseif($data['view_number'] == UserMessageThreadActionInstantWin::STAY_FINISH_VIEW): ?>
                    <?php $message_text = $data["instant_win_prize_lose"]->html_content ? $data["instant_win_prize_lose"]->html_content : $this->toHalfContentDeeply($data["instant_win_prize_lose"]->text); ?>
                    <p class="messageImg"><img src="<?php assign($data['isForSyndotOnly'] ? $this->setVersion('/img/module/instantWin/animeLucky_lose2.gif') : $data["instant_win_prize_lose"]->image_url);?>" width="600" height="300"></p>
                    <section class="messageText"><?php write_html($message_text); ?></section>
                    <p class="messageLotNext jsMessageLotNext"><strong>ご参加ありがとうございました。</strong></p>
                <?php elseif($data['view_number'] == UserMessageThreadActionInstantWin::PASS_VIEW): ?>
                    <?php $message_text = $data["instant_win_prize_win"]->html_content ? $data["instant_win_prize_win"]->html_content : $this->toHalfContentDeeply($data["instant_win_prize_win"]->text); ?>
                    <p class="messageImg"><img src="<?php assign($data["instant_win_prize_win"]->image_url);?>" width="600" height="300" id=""></p>
                    <section class="messageText"><?php write_html($message_text); ?></section>
                    <?php if ($data["message_info"]["action_status"]->status == CpUserActionStatus::NOT_JOIN) : ?>
                        <span class="cmd_execute_instant_win_action middle1"></span>
                    <?php endif; ?>
                <?php endif; ?>
                <!-- /.message --></section>
        </section>
    </form>
    <!-- /.message --></section>

<?php
// TODO SynExtension (使うときコメント外す)
//if (in_array($data['view_number'], array(
//    UserMessageThreadActionInstantWin::DRAW_VIEW,
//    UserMessageThreadActionInstantWin::STAY_WAITING_VIEW,
//    UserMessageThreadActionInstantWin::STAY_FINISH_VIEW), true)) {
//    write_html($this->parseTemplate('SynExtension.php', array('brand_id' => $data['pageStatus']['brand']->id, 'visible' => $data['view_number'] !== UserMessageThreadActionInstantWin::DRAW_VIEW)));
//}
?>

<?php if (config('Stage') === 'product'): ?>
    <span class="jsGoogleAnalyticsTrackingAction"
          data-product='{"id": "P<?php assign($data['cp_info']['cp']['id']); ?>", "name": "campaign_<?php assign($data['cp_info']['cp']['id']); ?>"}'
          data-action="checkout"></span>
    <script>
        if (typeof(GoogleAnalyticsTrackingService) !== 'undefined') {
            GoogleAnalyticsTrackingService.generate("<?php assign(config('Analytics.ID')) ?>", "<?php assign(config('Analytics.TrackerName')) ?>", {'page': "<?php assign(Util::getBaseUrl() . '/messages/thread/' . $data['cp_info']['cp']['id'] . '-purchase'); ?>"});
        }
    </script>
<?php endif ?>

<?php write_html($this->scriptTag('user/UserActionInstantWinService')); ?>