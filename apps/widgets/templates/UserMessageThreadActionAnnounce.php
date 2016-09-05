<?php $isDefault = $data['message_info']['concrete_action']->design_type == CpAnnounceAction::DEFAULT_DESIGN_TYPE; ?>
    <section class="<?php if ($isDefault): ?>message_win<?php else: ?>message<?php endif; ?> jsMessage" id="message_<?php assign($data['message_info']["message"]->id); ?>" >
        <a id="<?php assign('ca_' . $data["message_info"]["cp_action"]->id) ?>"></a>

        <form class="executeAnnounceActionForm" action="<?php assign(Util::rewriteUrl('messages', "api_execute_announce_action.json")); ?>" method="POST">


            <?php if ($isDefault): ?>
            <h1 class="messageHd1">Congratulations!<span><?php write_html($this->toHalfContentDeeply($data["message_info"]["concrete_action"]->title)); ?></span></h1>
            <div class="messageInner">
                <?php endif; ?>

                <?php write_html($this->csrf_tag()); ?>
                <?php write_html($this->formHidden('cp_action_id', $data['message_info']["cp_action"]->id)); ?>
                <?php write_html($this->formHidden('cp_user_id', $data['cp_user']->id)); ?>

                <?php if($data["message_info"]["concrete_action"]->image_url): ?>
                    <p class="messageImg"><img src="<?php assign($data["message_info"]["concrete_action"]->image_url); ?>" alt="campaign img"></p>
                <?php endif; ?>

                <?php $message_text = $data['message_info']['concrete_action']->html_content ? $data['message_info']['concrete_action']->html_content : $this->toHalfContentDeeply($data['message_info']['concrete_action']->text); ?>
                <section class="messageText"><?php write_html($message_text); ?></section>

                <ul class="btnSet">
                    <?php if ($data["message_info"]["action_status"]->status == CpUserActionStatus::NOT_JOIN): ?>
                        <li class="btn3" style="display: none"><a class="cmd_execute_announce_action middle1" data-messageid="<?php assign($data['message_info']["message"]->id); ?>" href="#">次へ</a></li>
                    <?php else: ?>
                        <li class="btn3" style="display: none"><span class="middle1">次へ</span></li>
                    <?php endif; ?>
                    <!-- /.btnSet --></ul>

                <?php if ($isDefault): ?>
            </div>
        <?php endif; ?>


        </form>

        <!-- /.message --></section>
<?php write_html($this->scriptTag('user/UserActionAnnounceService')); ?>