<div class="mailEntryWrap jsLoginAddressForm">
    <div class="accoutWrap">
        <?php if ($data['is_limited']): ?>
            <p class="address"><?php assign($data['ActionForm']['mail_address']); ?></p>
        <?php else: ?>
            <p class="address"><?php assign($data['ActionForm']['mail_address']); ?><span class="supplement1"><a href="javascript:void(0);" class="jsRetypeMailAddress" data-mail_address="<?php assign($data['ActionForm']['mail_address']); ?>" data-url="<?php assign(Util::rewriteUrl('my', "api_get_pre_login_form.json")); ?>">[変更]</a></span></p>
        <?php endif; ?>
        <!-- /.accoutWrap --></div>

    <form id="email_form" name="email_form" method="POST" class="jsCampaignJoinForm" action="<?php assign(Util::rewriteUrl('my', 'save_login')); ?>" data-slide_skip="1" data-url="<?php assign(Util::rewriteUrl('auth', "api_issue_password.json")); ?>">
        <?php write_html($this->csrf_tag()); ?>
        <?php write_html($this->formHidden('mail_type', 'login')); ?>
        <?php write_html($this->formHidden('page_type', $data['page_type'])); ?>
        <?php write_html($this->formHidden('mail_address', $data['ActionForm']['mail_address'])); ?>
        <ul>
            <li class="pass">
                <?php if ($this->ActionError && !$this->ActionError->isValid('password')): ?>
                    <span class="iconError1"><?php assign($this->ActionError->getMessage('password')); ?></span>
                <?php endif; ?>
                <p class="passBtn"><a href="javascript:void(0);" class="jsPassViewBtn" data-visible="0">表示</a></p>
                <?php write_html($this->formPassword('password', null, array('class' => 'passView jsPassView', 'placeholder' => 'パスワード'))); ?>
            </li>
            <li class="pass">
                <small class="supplement1"><a href="javascript:void(0);" class="jsConfirmPasswordIssue">パスワードをお忘れの方はこちら</a></small>
            </li>
            <li class="btn3"><a href="javascript:void(0);" class="large1 jsSubmitForm">ログイン</a></li>
        </ul>
        <!-- /.inputAddress --></form>

    <p class="signupAttention"><a href="https://allied-id.com/maintenance" target=”_blank”>パスワード管理に関する注意</a></p>
    <!-- /.mailEntryWrap --></div>
