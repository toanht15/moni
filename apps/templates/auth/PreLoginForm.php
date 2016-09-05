<div class="mailEntryWrap jsLoginAddressForm">
    <form action="<?php assign(Util::rewriteUrl('auth', "api_verify_mail_address.json")); ?>" method="POST">
        <?php write_html($this->csrf_tag()); ?>
        <ul>
            <li class="address">
                <?php write_html($this->formHidden('page_type', $data['page_type'])); ?>
                <?php write_html($this->formText('dummy', 'dummy', array('style' => 'display: none;'))); ?>
                <?php write_html($this->formEmail('mail_address', $data['preset_mail_address'], array('placeholder' => 'メールアドレス'))); ?>
            </li>
            <li class="btn3"><a href="javascript:void(0);" class="large1 jsVerifyMailAddress">次へ</a></li>
        </ul>
        <!-- /.inputAddress --></form>
    <!-- /.mailEntryWrap --></div>
