<section class="message_engagement jsMessage" id="message_<?php assign($data['message_info']["message"]->id); ?>">
    <a id="<?php assign('ca_' . $data["message_info"]["cp_action"]->id) ?>"></a>

    <form class="executeInstagramFollowActionForm"
          action="<?php assign(Util::rewriteUrl('messages', "api_execute_instagram_follow_action.json")); ?>"
          method="POST">
        <?php write_html($this->csrf_tag()); ?>
        <?php write_html($this->formHidden('cp_action_id', $data['message_info']["cp_action"]->id)); ?>
        <?php write_html($this->formHidden('cp_user_id', $data['cp_user']->id)); ?>

        <h1 class="messageHd1">「<?php assign($data['brand_social_account']->screen_name); ?>」のInstagramアカウントをフォローしよう！</h1>
        <div class="engagementInner followIg">
            <div class="engagementIg">
                <?php write_html($data['response_html']); ?>
            <!-- /.engagementIg --></div>
        <!-- /.engagementInner --></div>

        <div class="messageFooter">
            <?php if (!$data['is_last_action']): ?>
                <ul class="btnSet">
                    <?php if ($data['message_info']['action_status']->status == CpUserActionStatus::NOT_JOIN): ?>
                        <li class="btn3"><a class="btn_execute_instagram_follow_action middle1" data-messageid="<?php assign($data['message_info']['message']->id); ?>" href="javascript:void(0)">次へ</a></li>
                    <?php else: ?>
                        <li class="btn3"><span class="middle1">次へ</span></li>
                    <?php endif; ?>
                <!-- /.btnSet --></ul>
            <?php else: ?>
                <?php if ($data['message_info']['action_status']->status == CpUserActionStatus::NOT_JOIN): ?>
                    <div class="cmd_execute_instagram_follow_action"></div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </form>
<!-- /.message --></section>
<?php write_html($this->scriptTag("user/UserActionInstagramFollowService")); ?>