<section class="message jsMessage" id="message_<?php assign($data['message_info']["message"]->id); ?>" >
    <a id="<?php assign('ca_' . $data["message_info"]["cp_action"]->id) ?>"></a>

    <form class="executeMessageActionForm" action="<?php assign(Util::rewriteUrl('messages', "api_execute_message_action.json")); ?>" method="POST">

        <?php write_html($this->csrf_tag()); ?>
        <?php write_html($this->formHidden('cp_action_id', $data['message_info']["cp_action"]->id)); ?>
        <?php write_html($this->formHidden('cp_user_id', $data['cp_user']->id)); ?>

        <?php if($data["message_info"]["concrete_action"]->image_url): ?>
            <p class="messageImg"><img src="<?php assign($data["message_info"]["concrete_action"]->image_url); ?>" alt="campaign img"></p>
        <?php endif; ?>
        <?php $message_text = $data['message_info']['concrete_action']->html_content ? $data['message_info']['concrete_action']->html_content : $this->toHalfContentDeeply($data['message_info']['concrete_action']->text); ?>
        <section class="messageText"><?php write_html($message_text); ?></section>
        <div class="messageFooter">
            <ul class="btnSet">
                <?php if ($data["message_info"]["action_status"]->status == CpUserActionStatus::NOT_JOIN): ?>
                    <?php if ($data["message_info"]["concrete_action"]->manual_step_flg == Cp::FLAG_SHOW_VALUE): ?>
                        <li class="btn3"><a class="btn_execute_message_action large1" data-messageid="<?php assign($data['message_info']["message"]->id); ?>" href="#">次へ</a></li>
                    <?php else: ?>
                        <li class="btn3" style="display: none;"><a class="cmd_execute_message_action large1" data-messageid="<?php assign($data['message_info']["message"]->id); ?>" href="#">次へ</a></li>
                    <?php endif; ?>
                <?php else: ?>
                    <?php if ($data["message_info"]["concrete_action"]->manual_step_flg == Cp::FLAG_SHOW_VALUE): ?>
                        <li class="btn3"><span class="large1">次へ</span></li>
                    <?php endif; ?>
                <?php endif; ?>
                <!-- /.btnSet --></ul>
        </div>
    </form>

<!-- /.message --></section>
<?php if ($data["message_info"]["action_status"]->status == CpUserActionStatus::NOT_JOIN): ?>
    <?php write_html($this->scriptTag('user/UserActionMessageService')); ?>
<?php endif ?>
