<section class="message jsMessage" id="message_<?php assign($data['message_info']["message"]->id); ?>">
    <a id="<?php assign('ca_' . $data["message_info"]["cp_action"]->id) ?>"></a>

    <form class="" action="<?php assign(Util::rewriteUrl('messages', "api_execute_free_answer_action.json")); ?>" method="POST" enctype="multipart/form-data" >
        <?php write_html($this->csrf_tag()); ?>
        <?php write_html($this->formHidden('cp_action_id', $data['message_info']["cp_action"]->id)); ?>
        <?php write_html($this->formHidden('cp_user_id', $data['cp_user']->id)); ?>

        <?php if($data["message_info"]["concrete_action"]->image_url): ?>
            <p class="messageImg"><img src="<?php assign($data["message_info"]["concrete_action"]->image_url); ?>" alt="campaign img"></p>
        <?php endif; ?>

        <dl class="module">
            <?php $message_text = $data['message_info']['concrete_action']->html_content ? $data['message_info']['concrete_action']->html_content : $this->toHalfContentDeeply($data["message_info"]["concrete_action"]->question); ?>
            <dt><section class="messageText"><span class="num"></span><span class="require1"><?php write_html($message_text); ?></span></section></dt>
            <?php
                if ($data["message_info"]["action_status"]->status == CpUserActionStatus::JOIN) {
                    $action_manager = new CpFreeAnswerActionManager();
                    $value = $action_manager->getAnswerByUserAndQuestion($data['cp_user']->id, $data['message_info']["cp_action"]->id)->free_answer;
                    $disabled = 'disabled';
                } else {
                    $value = PHPParser::ACTION_FORM;
                    $disabled = "";
                }
            ?>
            <dd>
                <?php write_html($this->formTextArea('free_answer', $value, array('placeholder'=>'自由記述', 'maxlength'=>2048, $disabled=>$disabled))); ?>
                <span id="free_answer_error" class="iconError1" style="display:none"></span>
            </dd>
        </dl>

        <div class="messageFooter">
            <ul class="btnSet">
                <?php if ($data["message_info"]["action_status"]->status == CpUserActionStatus::NOT_JOIN) : ?>
                    <li class="btn3"><a class="freeAnswerSubmit large1" href="javascript:void(0)"><?php assign($data["message_info"]["concrete_action"]->button_label); ?></a></li>
                <?php else: ?>
                    <li class="btn3"><span class="large1"><?php assign($data["message_info"]["concrete_action"]->button_label); ?></span></li>
                <?php endif; ?>
                <!-- /.btnSet --></ul>
        </div>
    </form>
<!-- /.message --></section>
<?php write_html($this->scriptTag('user/UserActionFreeAnswerService')); ?>