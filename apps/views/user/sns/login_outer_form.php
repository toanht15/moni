<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoLoginOuterHeader')->render($data['pageStatus'])) ?>

<article>
<h1 class="hd1"><?php assign(ucfirst($data['title'])); ?></h1>
<?php if ($data['social_app']->isFacebook()): ?>
    <h2 class="hd2">メールでお送りしたパスワードを入力し、送信ください。</h2>
<?php else: ?>
    <h2 class="hd2">メールでお送りしたパスワードを入力し、アカウントと連携してください</h2>
<?php endif ?>
<form name="sns_outer_form" method="POST" action="<?php assign(Util::rewriteUrl('sns', 'login_outer')); ?>">
        <?php write_html($this->formHidden('token', $data['token'], array('id'=>'token'))) ?>
        <?php write_html($this->csrf_tag()); ?>
        <p class="attention1">
            <?php if ($this->ActionError && !$this->ActionError->isValid('token')): ?>
                <li class="iconError1"><?php assign($this->ActionError->getMessage('token')); ?></li>
            <?php endif; ?>
            <?php if ($this->ActionError && !$this->ActionError->isValid('password')): ?>
                <span class="iconError1"><?php assign($this->ActionError->getMessage('password')); ?></span><br>
            <?php endif; ?>
        </p>
        <p>
            <?php write_html($this->formPassword('password', PHPParser::ACTION_FORM, array('placeholder' => 'パスワード'))); ?>
            <span class="btn3">
                <a href="javascript:void(0);" class="small1" onclick="document.sns_outer_form.submit();">
                    <?php if ($data['social_app']->isFacebook()): ?>送信<?php else: ?>連携<?php endif ?>
                </a>
            </span>
        </p>
</form>
</article>
