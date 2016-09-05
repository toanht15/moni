<div class="mailEntryWrap jsSliderContent">
    <div class="prompt">
        <p class="promptText">このメールアドレスを利用して登録されているSNS等のアカウントが見つかりました。<span><?php assign($data['ActionForm']['mail_address']); ?></span></p>
        <div class="prompSet">
            <p>このメールアドレスを使うには<a href="javascript:void(0);" class="jsCallAuthForm">登録しているSNS等でログインする</a></p>
            <p class="centerdLine"><span>または</span></p>
            <form action="" data-url="<?php assign(Util::rewriteUrl('auth', "api_issue_password.json")); ?>" method="POST">
                <?php write_html($this->csrf_tag()); ?>
                <?php write_html($this->formHidden('mail_address', $data['ActionForm']['mail_address'])); ?>
                <p><a href="javascript:void(0);" class="jsIssuePassword">仮パスワードを発行する</a></p>
            </form>
            <!-- /.prompSet --></div>
        <!-- /.prompt --></div>
    <form method="POST">
        <?php write_html($this->csrf_tag()); ?>
        <p class="supplement1"><a href="javascript:void(0);" class="jsCallMailAuthForm">別のメールアドレスを使う</a></p>
    </form>
    <!-- /.mailEntryWrap --></div>
