<?php $concrete_action = $data['message_info']['concrete_action'] ?>
<a id="code_auth"></a>
<section class="message jsMessage inview" id="message_<?php assign($data['message_info']["message"]->id); ?>">
    <a id="<?php assign('ca_' . $data["message_info"]["cp_action"]->id) ?>"></a>

    <form class="executeCodeAuthActionForm" method="POST" action="<?php assign(Util::rewriteUrl('messages', 'api_execute_code_auth_action.json')) ?>">
        <?php write_html($this->csrf_tag()) ?>
        <?php write_html($this->formHidden('user_id', $data['cp_user']->user_id)) ?>
        <?php write_html($this->formHidden('cp_user_id', $data['cp_user']->id)) ?>
        <?php write_html($this->formHidden('cp_action_id', $data['message_info']["cp_action"]->id)) ?>
        <?php write_html($this->formHidden('code_auth_id', $concrete_action->code_auth_id)) ?>

        <?php if($concrete_action->image_url): ?>
            <p class="messageImg"><img src="<?php assign($concrete_action->image_url); ?>"></p>
        <?php endif; ?>
        <?php $message_text = $concrete_action->html_content ? $concrete_action->html_content : $this->toHalfContentDeeply($concrete_action->text); ?>
        <section class="messageText"><?php write_html($message_text); ?></section>

        <div class="jsMsgCodeAuthCodeList">
            <?php write_html(aafwWidgets::getInstance()->loadWidget('MsgCodeAuthCodeList')->render(array('user_id' => $data['cp_user']->user_id, 'cp_user_id' => $data['cp_user']->id, 'cp_action_id' => $data['message_info']["cp_action"]->id))) ?>
        </div>
    </form>

    <!-- /.message --></section>
<?php write_html($this->scriptTag('user/UserActionCodeAuthService')); ?>