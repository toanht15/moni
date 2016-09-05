<section class="<?php assign($data["message_info"]["concrete_action"]->design_type != CpJoinFinishAction::NORMAL_DESIGN_TYPE ? 'message_thanks' : 'message') ?> jsMessage" id="message_<?php assign($data['message_info']["message"]->id); ?>" >
    <a id="<?php assign('ca_' . $data["message_info"]["cp_action"]->id) ?>"></a>

    <form class="executeJoinFinishActionForm" action="<?php assign(Util::rewriteUrl('messages', "api_execute_join_finish_action.json")); ?>" method="POST">
        <?php write_html($this->csrf_tag()); ?>
        <?php write_html($this->formHidden('cp_action_id', $data['message_info']["cp_action"]->id)); ?>
        <?php write_html($this->formHidden('cp_user_id', $data['cp_user']->id)); ?>

        <?php if($data["message_info"]["concrete_action"]->image_url): ?>
            <p class="messageImg"><img src="<?php assign($data['message_info']['concrete_action']->image_url); ?>"></p>
        <?php endif; ?>

        <?php if ($data['message_info']['concrete_action']->design_type == CpJoinFinishAction::NORMAL_DESIGN_TYPE): ?>
            <?php write_html($this->parseTemplate('CpUserJoinFinishInner.php', array('message_info' => $data['message_info'],  'cp_info' => $data['cp_info'], 'pageStatus' => $data['pageStatus']))) ?>
        <?php else: ?>
            <h1 class="messageHd1">Thank you!<span><?php write_html($this->toHalfContentDeeply($data["message_info"]["concrete_action"]->title)); ?></span></h1>
            <div class="messageInner">
                <?php write_html($this->parseTemplate('CpUserJoinFinishInner.php', array('message_info' => $data['message_info'],  'cp_info' => $data['cp_info'], 'pageStatus' => $data['pageStatus']))) ?>
            </div>
        <?php endif; ?>

        <div class="messageFooter">
            <?php if ($data["message_info"]["action_status"]->status == CpUserActionStatus::NOT_JOIN): ?>
                <span class="cmd_execute_join_finish_action middle1" data-last_action_flg="<?php assign(($data['message_info']['cp_action']->id == $data['last_action']->id) ? 1 : 0); ?>" data-messageid="<?php assign($data['message_info']["message"]->id); ?>" style="display: none;"></span>
            <?php endif; ?>
        </div>

    </form>
<!-- /.message --></section>

<?php if (config('Stage') === 'product'): ?>
    <span class="jsGoogleAnalyticsTrackingAction"
          data-product='{"id": "P<?php assign($data['cp_info']['cp']['id']); ?>", "name": "campaign_<?php assign($data['cp_info']['cp']['id']); ?>"}'
          data-action="checkout"></span>
    <script>
        if (typeof(GoogleAnalyticsTrackingService) !== 'undefined') {
            GoogleAnalyticsTrackingService.generate("<?php assign(config('Analytics.ID')) ?>", "<?php assign(config('Analytics.TrackerName')) ?>", {'page': "<?php assign(Util::getBaseUrl() . '/messages/thread/' . $data['cp_info']['cp']['id']. '-purchase'); ?>" });
        }
    </script>
<?php endif ?>

<?php if (config('AdEbis.Status') && $data["message_info"]["action_status"]->status == CpUserActionStatus::NOT_JOIN): ?>
    <?php write_html(aafwWidgets::getInstance()->loadWidget('AdEbis')->render(array('cp_user_id' => $data['cp_user']->id, 'page_type' => Cps::ADEBIS_CP_JOIN_FINISH))); ?>
<?php endif; ?>
<?php write_html($this->scriptTag('user/UserActionJoinFinishService')); ?>

<?php if ($data["message_info"]["action_status"]->status == CpUserActionStatus::NOT_JOIN): ?>
    <?php write_html($data["message_info"]["concrete_action"]->cv_tag);/*運用対応用のカラム*/?>
<?php endif ?>