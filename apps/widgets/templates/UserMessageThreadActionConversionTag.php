<section class="message jsMessage" id="message_<?php assign($data['message_info']['message']->id); ?>" style="display: none;">
    <a id="<?php assign('ca_' . $data["message_info"]["cp_action"]->id) ?>"></a>

    <form class="executeConversionTagActionForm" action="<?php assign(Util::rewriteUrl('messages', "api_execute_conversion_tag_action.json")); ?>" method="POST" enctype="multipart/form-data">
        <?php write_html($this->csrf_tag()); ?>
        <?php write_html($this->formHidden('cp_gift_action_id', $data['message_info']["concrete_action"]->id)); ?>
        <?php write_html($this->formHidden('cp_action_id', $data['message_info']["cp_action"]->id)); ?>
        <?php write_html($this->formHidden('cp_user_id', $data['cp_user']->id)); ?>
        <?php write_html($this->formHidden('brand_id', $data['pageStatus']['brand']->id));?>
        <?php if ($data["message_info"]["action_status"]->status == CpUserActionStatus::NOT_JOIN): ?>
            <?php write_html($data['message_info']['concrete_action']->script_code);?>
            <span class="cmd_execute_conversion_tag_action middle1" data-messageid="<?php assign($data['message_info']["message"]->id); ?>" style="display: none;"></span>
        <?php endif; ?>
    </form>
    <!-- /.message --></section>
<?php write_html($this->scriptTag("user/UserActionConversionTagService")); ?>