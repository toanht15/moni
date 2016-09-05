<section class="message_engagement jsMessage" id="message_<?php assign($data['message_info']["message"]->id); ?>">
    <a id="<?php assign('ca_' . $data["message_info"]["cp_action"]->id) ?>"></a>

    <form class="jsExecuteLineAddFriendActionForm" action="<?php assign(Util::rewriteUrl('messages', "api_execute_line_add_friend_action.json")); ?>" method="POST">

        <?php write_html($this->csrf_tag()); ?>
        <?php write_html($this->formHidden('cp_action_id', $data['message_info']["cp_action"]->id)); ?>
        <?php write_html($this->formHidden('cp_user_id', $data['cp_user']->id)); ?>
        <?php write_html($this->formHidden('cp_line_add_friend_action_id', $data['message_info']["concrete_action"]->id)); ?>
        <?php write_html($this->formHidden('clicked_link', $data['clicked_line_add_friend_url'])); ?>

        <h1 class="messageHd1"><?php assign($data['message_info']["concrete_action"]->title)?></h1>
        <div class="engagementLineInner">
            <div class="engagementLn">
                <p class="lineBtn">
                    <a href="<?php assign($data['message_info']['action_status']->status == CpUserActionStatus::NOT_JOIN ? 'javascript:void(0)' : 'https://line.me/ti/p/@'.$data['message_info']["concrete_action"]->line_account_id) ?>"
                       data-target_url="https://line.me/ti/p/@<?php assign($data['message_info']["concrete_action"]->line_account_id) ?>" class="<?php assign($data['message_info']['action_status']->status == CpUserActionStatus::NOT_JOIN ? 'jsLineAddFriend' : '')?>"
                        <?php assign($data['message_info']['action_status']->status == CpUserActionStatus::NOT_JOIN ? '' : 'target="_blank"')?>
                        >
                        <img height="36" border="0" alt="友だち追加" src="http://biz.line.naver.jp/line_business/img/btn/addfriends_ja.png">
                    </a>
                </p>
            </div>
            <p class="descriptionText"><?php assign($data['message_info']["concrete_action"]->comment)?></p>
            <!-- /.engagementInner --></div>

        <div class="messageFooter">
            <?php if ($data['message_info']['action_status']->status == CpUserActionStatus::NOT_JOIN): ?>
                <p class="skip"><a href="javascript:void(0)" class="jsSkipExecuteLineAddFriendAction"><small>友だちに追加せず次へ</small></a></p>
            <?php endif; ?>
        </div>
    </form>
<!-- /.message --></section>

<?php write_html($this->scriptTag("user/UserActionLineAddFriendService")); ?>