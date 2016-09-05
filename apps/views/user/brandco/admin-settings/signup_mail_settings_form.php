<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoHeader')->render($data['pageStatus'])) ?>
<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoAccountHeader')->render($data['pageStatus'])) ?>

<div class="adminWrap">
    <?php write_html($this->parseTemplate('SettingSiteMenu.php', $data['pageStatus'])) ?>

    <form id="customMailTemplate" name="customMailTemplate" action="<?php assign(Util::rewriteUrl('admin-settings', 'signup_mail_settings')); ?>" method="POST">
        <?php write_html($this->csrf_tag()); ?>
        <article class="adminMainCol">

            <h1 class="hd1">登録メール設定</h1>

            <section class="adminSignUpMailWrap">
                <p>
                    <?php write_html( $this->formCheckbox('send_signup_mail_flg', array($this->getActionFormValue('send_signup_mail_flg') ? '1' : ''),array(),array(1 => '登録完了メールを送信する'))); ?>
                    <span class="iconHelp">
                        <span class="text">ヘルプ</span>
                        <span class="textBalloon1">
                            <span>登録を完了したユーザーに、<br>下記で設定したメールを自動で送信します。</span>
                        <!-- /.textBalloon1 --></span>
                    <!-- /.iconHelp --></span>
                </p>
                <p class="supplement1">※送信元メールアドレスは、info@monipla.comです。</p>
            <!-- /.adminSignUpMailWrap --></section>

            <h2 class="hd2">送信者名</h2>
            <section class="adminSignUpMailWrap">
                <p><?php write_html( $this->formText( 'sender_name', PHPParser::ACTION_FORM, array(  'class'=>'SenderName'))); ?></p>
                <?php if ($this->ActionError && !$this->ActionError->isValid('sender_name')): ?>
                    <p class="attention1"><?php assign($this->ActionError->getMessage('sender_name')) ?></p>
                <?php endif; ?>
            <!-- /.adminSignUpMailWrap --></section>

            <h2 class="hd2">メールタイトル</h2>
            <section class="adminSignUpMailWrap">
                <p><?php write_html( $this->formText( 'subject', PHPParser::ACTION_FORM)); ?></p>
                <?php if ($this->ActionError && !$this->ActionError->isValid('subject')): ?>
                    <p class="attention1"><?php assign($this->ActionError->getMessage('subject')) ?></p>
                <?php endif; ?>
            <!-- /.adminSignUpMailWrap --></section>

            <h2 class="hd2">メール本文</h2>
            <section class="adminSignUpMailWrap">
                <p class="adminRuleSetting">
                    <?php write_html($this->formTextArea('body_plain', PHPParser::ACTION_FORM,  array('cols' => 90, 'rows' => 10))) ?>
                    <?php if ($this->ActionError && !$this->ActionError->isValid('body_plain')): ?>
                        <p class="attention1"><?php assign($this->ActionError->getMessage('body_plain')) ?></p>
                    <?php endif; ?>
                </p>

                <p class="btnSet">
                    <span class="btn3"><a href="javascript:void(0)" class="middle1" onclick="document.customMailTemplate.submit();return false;">保存</a></span>
                </p>
            <!-- /.adminRuleSettingWrap --></section>
        </article>
    </form>
</div>
<?php write_html($this->parseTemplate('BrandcoFooter.php', $data['pageStatus'])); ?>
