<div class="mailEntryWrap jsLoginAddressForm">
    <div class="accoutWrap">
        <p class="address"><?php assign($data['ActionForm']['mail_address']); ?><span class="supplement1"><a href="javascript:void(0);" class="jsRetypeMailAddress" data-mail_address="<?php assign($data['ActionForm']['mail_address']); ?>" data-url="<?php assign(Util::rewriteUrl('my', "api_get_pre_login_form.json")); ?>">[変更]</a></span></p>
        <!-- /.accoutWrap --></div>
    <p class="attension">登録したメールアドレス・パスワードを用いて応募状況や当選の確認などを行うことができます。</p>
    <form id="email_form" name="email_form" class="jsCampaignJoinForm inputPass" method="POST" action="<?php assign(Util::rewriteUrl('my', 'save_signup')); ?>">
        <?php write_html($this->csrf_tag()); ?>
        <?php write_html($this->formHidden('mail_type', 'signup')); ?>
        <?php write_html($this->formHidden('page_type', $data['page_type'])); ?>
        <?php write_html($this->formHidden('mail_address', $data['ActionForm']['mail_address'])); ?>
        <ul>
            <li class="pass">
                <small class="supplement1">8文字以上の英数字を組み合わせて入力ください。</small>
                <?php if ($this->ActionError && (!$this->ActionError->isValid('password'))): ?>
                    <span class="iconError1"><?php assign($this->ActionError->getMessage('password')); ?></span>
                <?php endif; ?>
                <p class="passBtn"><a href="javascript:void(0);" class="jsPassViewBtn" data-visible="0">表示</a></p>
                <?php write_html($this->formPassword('password', null, array('class' => 'passView jsPassView', 'placeholder' => 'パスワードの登録'))); ?>
            </li>
            <li class="btn3"><a href="javascript:void(0);" class="large1 jsSubmitForm">登録する</a></li>
        </ul>
        </form>

    <p class="signupAttention"><a href="https://allied-id.com/maintenance" target=”_blank”>パスワード管理に関する注意</a></p>
    <!-- /.mailEntryWrap --></div>
