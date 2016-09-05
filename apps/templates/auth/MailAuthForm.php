<form action="<?php assign(Util::rewriteUrl('auth', "api_verify_mail_address.json")); ?>" method="POST" class="mailEntryWrap jsSliderContent">
    <?php write_html($this->csrf_tag()); ?>
    <ul class="addressJoin">
        <li class="address jsAuthErrorWrap">
            <?php write_html($this->formHidden('page_type', $data['page_type'])); ?>
            <?php /* Formの自動送信防止用 */ write_html($this->formText('dummy', 'dummy', array('style' => 'display: none;'))); ?>
            <?php write_html($this->formEmail('mail_address', PHPParser::ACTION_FORM, array('placeholder' => 'メールアドレス'))); ?>
        </li>
        <li class="btn3"><a href="javascript:void(0);" class="large1 jsCallTemplateWithMailAddress">次へ</a></li>
        <!-- /.addressJoin --></ul>
</form>
