<div class="mailEntryWrap jsLoginAddressForm">
    <div class="prompt">
        <p class="promptText">このメールアドレスを利用して登録されているSNS等のアカウントが見つかりました。<span><?php assign($data['ActionForm']['mail_address']); ?></span></p>
        <div class="prompSet">
            <p>このメールアドレスを使うには<a href="javascript:void(0);" class="jsScrollToLoginSnsHeader">登録しているSNS等でログインする</a></p>
            <p class="centerdLine"><span>または</span></p>
            <form id="email_form" action="" data-url="<?php assign(Util::rewriteUrl('auth', "api_issue_password.json")); ?>" method="POST">
                <?php write_html($this->csrf_tag()); ?>
                <?php write_html($this->formHidden('page_type', $data['page_type'])); ?>
                <?php write_html($this->formHidden('mail_address', $data['ActionForm']['mail_address'])); ?>
                <p><a href="javascript:void(0);" class="jsConfirmPasswordIssue">仮パスワードを発行する</a></p>
            </form>
            <!-- /.prompSet --></div>
        <!-- /.prompt --></div>
    <p class="supplement1"><a href="javascript:void(0);" class="jsRetypeMailAddress" data-mail_address="<?php assign($data['ActionForm']['mail_address']); ?>" data-url="<?php assign(Util::rewriteUrl('my', "api_get_pre_login_form.json")); ?>">別のメールアドレスを使う</a></p>
    <!-- /.mailEntryWrap --></div>
