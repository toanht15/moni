<ul class="inquiryChatBody">
    <?php foreach ($data['inquiry_messages'] as $inquiry_message): ?>
        <li class="jsMessage <?php assign((InquiryMessage::isUser($inquiry_message['sender'])) ? 'fromUser' : 'fromAdmin') ?> <?php assign(($data['role'] == InquiryMessage::ADMIN && $inquiry_message['forwarded_flg']) ? 'jsForwarded' : '') ?>">
            <p class="cf">
                <small class="messageSender jsMessageUserName"><?php assign($data['sender_list'][$inquiry_message['sender']]['name']); ?></small>
                <small class="messageTimestamp"><?php assign(Util::getFormatDateTimeString($inquiry_message['created_at'])); ?></small>
                <!-- /.cf --></p>
            <p class="inquiryUser">
                <img src="<?php assign(($data['sender_list'][$inquiry_message['sender']]['image']) ?: $this->setVersion('/img/base/imgUser1.jpg')); ?>" width="40" height="40" alt="profile_image">
            </p>
            <p class="inquiryMessage jsMessageText"><?php write_html($this->toHalfContentDeeply($inquiry_message['content'])); ?></p>
            <?php if (InquiryRoom::isManager($data['operator_type'])) : ?>
                <p class="messageTransmit">
                    <?php if ($inquiry_message['forwarded_flg']): ?>
                        <span class="iconCheck3">転送済</span>
                    <?php endif; ?>
                    <?php if (InquiryMessage::isUser($inquiry_message['sender']) && InquiryRoom::isManager($data['operator_type'])): ?>
                        <a href="javascript:void(0)" class="jsForwardedMessageSet" data-inquiry_message_id="<?php assign($inquiry_message['id']); ?>">このメッセージを転送する</a>
                    <?php endif; ?>
                </p>
            <?php endif; ?>
        </li>
    <?php endforeach; ?>
    <!-- /.inquiryChatBody --></ul>