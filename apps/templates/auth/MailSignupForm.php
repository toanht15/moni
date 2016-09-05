<div class="mailEntryWrap jsSliderContent">
    <div class="accoutWrap">
        <form method="POST">
            <?php write_html($this->csrf_tag()); ?>
            <p class="address"><?php assign($data['ActionForm']['mail_address']); ?><span class="supplement1"><a href="javascript:void(0);" class="jsCallMailAuthForm">[変更]</a></span></p>
        </form>
        <!-- /.accoutWrap --></div>
    <p class="attension">登録したメールアドレス・パスワードを用いて応募状況や当選の確認などを行うことができます。</p>
    <form class="inputPass" method="POST" action="<?php assign(Util::rewriteUrl('my', 'save_signup')); ?>">
        <?php write_html($this->csrf_tag()); ?>
        <?php write_html($this->formHidden('mail_address', $data['ActionForm']['mail_address'])); ?>
        <?php /* api送信なので、EnterキーでのPOST送信を防ぐ */ write_html($this->formText('dummy', null, array('style' => 'display: none;'))); ?>
        <ul>
            <li class="pass jsAuthErrorWrap">
                <small class="supplement1">8文字以上の英数字を組み合わせて入力ください。</small>
                <p class="passBtn"><a href="javascript:void(0);" class="passView jsTogglePasswordVisibility" data-visible="0">表示</a></p>
                <?php write_html($this->formPassword('password', null, array('class' => 'passView jsInputPassword', 'placeholder' => 'パスワードの登録'))); ?>
            </li>
            <li class="btn3"><a href="javascript:void(0);" class="large1 jsSignupByMail">登録する</a></li>
        </ul>
    </form>

    <p class="signupAttention"><a href="https://allied-id.com/maintenance" target=”_blank”>パスワード管理に関する注意</a></p>
    <!-- /.mailEntryWrap --></div>
