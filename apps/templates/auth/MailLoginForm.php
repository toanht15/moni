<div class="mailEntryWrap jsSliderContent">
    <?php if ($data['sent_password']): ?>
        <p class="supplement1">下記アドレス宛に、仮パスワードをお送りしました。</p>
        <p class="supplement1">メールが届かない場合は、<a href="<?php assign(config('Protocol.Secure') . '://' . config('Domain.brandco') . '/monipla/inquiry'); ?>" target="_blank">お問合せ</a>をお願いします。</p>
    <?php endif; ?>

    <div class="accoutWrap">
        <form method="POST">
            <?php write_html($this->csrf_tag()); ?>
            <p class="address"><?php assign($data['ActionForm']['mail_address']); ?><span class="supplement1"><a href="javascript:void(0);" class="jsCallMailAuthForm">[変更]</a></span></p>
        </form>
        <!-- /.accoutWrap --></div>

    <form method="POST" action="<?php assign(Util::rewriteUrl('my', 'save_login')); ?>">
        <?php write_html($this->csrf_tag()); ?>
        <?php write_html($this->formHidden('mail_address', $data['ActionForm']['mail_address'])); ?>
        <?php /* api送信なので、EnterキーでのPOST送信を防ぐ */ write_html($this->formText('dummy', null, array('style' => 'display: none;'))); ?>
        <ul>
            <li class="pass jsAuthErrorWrap">
                <p class="passBtn"><a href="javascript:void(0);" class="jsTogglePasswordVisibility" data-visible="0">表示</a></p>
                <?php write_html($this->formPassword('password', null, array('class' => 'passView jsInputPassword', 'placeholder' => 'パスワード'))); ?>
            </li>
            <li class="pass">
                <small class="supplement1"><a href="javascript:void(0);" class="jsIssuePassword">パスワードをお忘れの方はこちら</a></small>
            </li>
            <li class="btn3"><a href="javascript:void(0);" class="large1 jsLoginByMail">ログイン</a></li>
        </ul>
        <!-- /.inputAddress --></form>

    <p class="signupAttention"><a href="<?php write_html(config('Protocol.Secure') . '://' . config('Domain.aaid') . '/maintenance'); ?>" target=”_blank”>パスワード管理に関する注意</a></p>
    <!-- /.mailEntryWrap --></div>
