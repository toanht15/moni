<section class="inquiryChat jsChat">
    <p class="chatTimestamp">お問い合わせ日時:　<?php assign(Util::getFormatDateTimeString($data['inquiry']->updated_at)); ?></p>

    <div class="jsChatBody">
        <?php write_html($this->parseTemplate('InquiryChatBody.php', $data)); ?>
    </div>

    <?php if (InquiryMessage::isUser($data['role'])) : ?>
        <form action="<?php assign(Util::rewriteUrl('inquiry', 'api_save_inquiry_message.json')); ?>" method="POST">
    <?php else : ?>
        <form action="<?php assign(Util::rewriteUrl(InquiryRoom::getDir($data['operator_type']), 'api_save_inquiry_message.json')); ?>" method="POST">
    <?php endif; ?>
        <?php write_html($this->csrf_tag()); ?>
        <?php write_html($this->formHidden('inquiry_room_id', $data['inquiry_room']->id)); ?>
        <?php write_html($this->formHidden('inquiry_message_id', ($data['inquiry_message_draft']) ? $data['inquiry_message_draft']['id'] : 0, array('data-action_type' => 'save'))); ?>

        <p class="inpuiryMessageInput jsTemplate">
            <?php if (!InquiryMessage::isUser($data['role'])) : ?>


                <a href="javascript:void(0)" class="jsOpenTemplateModal" data-open_modal_type="Template">テンプレートを使う</a>
            <?php endif; ?>

            <span class="jsContentError"></span>
            <textarea name="content" class="jsContent" cols="30" rows="10"><?php assign($data['inquiry_message_draft']['content']); ?></textarea>
            <!-- /.inpuiryMessageInput --></p>

        <p class="btnSet">
            <span class="btn2"><a href="javascript:void(0)" class="large1 jsMessageSave" data-draft_flg="1">下書き保存する</a></span>
            <span class="btn3"><a href="javascript:void(0)" class="large1 jsOpenInquiryMessageSaveModal" data-open_modal_type="InquiryMessageSave">送信する</a></span></p>
            <span style="display:none;" class="jsMessageSave" data-draft_flg="0"></span>
        </p>
    </form>

    <?php if (!InquiryMessage::isUser($data['role'])) : ?>
        <div class="inquiryMessageTransmit jsCheckToggleWrap">
            <p class="transmitTorigger">
                <label><input type="checkbox" class="jsCheckToggle" <?php if ($this->ActionError && !$this->ActionError->isValid('inquiry_message_id')) { write_html('checked'); } ?>><?php assign(InquiryRoom::isManager($data['operator_type']) ? '企業に転送する' : InquiryBrand::MANAGER_BRAND_NAME . 'に転送する'); ?></label>
            </p>

            <div class="jsCheckToggleTarget" <?php if ($this->ActionError && (!$this->ActionError->isValid('inquiry_brand_id') || !$this->ActionError->isValid('inquiry_message_id'))) { write_html('style="display: block;"'); } ?>>
                <form action="<?php assign(Util::rewriteUrl(InquiryRoom::getDir($data['operator_type']), 'forward_inquiry')); ?>" method="POST">
                    <?php write_html($this->csrf_tag())?>
                    <?php write_html($this->formHidden('inquiry_id', $data['inquiry']->id))?>
                    <?php write_html($this->formHidden('inquiry_room_id', $data['inquiry_room']->id))?>
                    <?php write_html($this->formHidden('inquiry_message_id', '0', array('data-action_type' => 'forward')))?>
                    <div class="messageTransmitMemo">
                        <p class=" jsForwardedMessageArea">
                            <?php if ($this->ActionError && !$this->ActionError->isValid('inquiry_brand_id')): ?>
                                <span class="iconError1"><?php assign($this->ActionError->getMessage('inquiry_brand_id')) ?></span>
                            <?php endif;?>
                            <select name="inquiry_brand_id" disabled="disabled">
                                <?php if (InquiryRoom::isManager($data['operator_type'])): ?>
                                    <option value="<?php assign(InquiryMessage::ADMIN); ?>"><?php assign($data['sender_list'][InquiryMessage::ADMIN]['name']); ?></option>
                                <?php else: ?>
                                    <option value="<?php assign(InquiryMessage::MANAGER); ?>"><?php assign($data['sender_list'][InquiryMessage::MANAGER]['name']); ?></option>
                                <?php endif; ?>
                            </select>
                            <textarea name="memo" cols="30" rows="10" placeholder="転送先へのメッセージ入力が可能です。(ユーザには回答されません)"></textarea>
                        </p>

                        <?php if (InquiryRoom::isManager($data['operator_type'])): ?>
                            <p class="transmitText jsForwardedMessage">
                                <?php if ($this->ActionError && !$this->ActionError->isValid('inquiry_message_id')): ?>
                                    <span class="iconError1 jsNoForwardedMessage"><?php assign($this->ActionError->getMessage('inquiry_message_id')) ?></span>
                                <?php else: ?>
                                    <span class="jsNoForwardedMessage">転送メッセージを選択してください</span>
                                <?php endif; ?>
                            </p>
                        <?php endif; ?>
                        <!-- /.messageTransmitMemo --></div>

                    <p class="btnSet"><span class="btn4"><a href="javascript:void(0)" class="large1 jsOpenInquiryForwardModal" data-open_modal_type="InquiryForward">転送する</a></span></p>
                    <span style="display:none;" class="jsMessageForward"></span>
                </form>
            </div>
            <!-- /.inquiryMessageTransmit --></div>
    <?php endif; ?>
    <!-- /.inquiryChat --></section>