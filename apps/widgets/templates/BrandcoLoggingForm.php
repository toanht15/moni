<?php if (!$data['sns_limited']) : ?>
    <?php if ($data['pageContents']) write_html($data['pageContents']); ?>
    <?php if(!(count($data['available_sns_accounts']) == 1 && $data['available_sns_accounts'][0] == SocialAccountService::SOCIAL_MEDIA_PLATFORM)):?>
        <h1 class="hd1 jsLoginSnsHeader">お持ちのアカウントで無料登録・ログイン</h1>
        <?php write_html($this->parseTemplate('auth/SnsLoginForm.php', $data)); ?>
    <?php endif; ?>

    <?php if (in_array(SocialAccountService::SOCIAL_MEDIA_PLATFORM, $data['available_sns_accounts'])): ?>
    <h1 class="hd1 jsLoginAddressHeader">メールアドレスで無料登録・ログイン</h1>
    <div class="addressJoin jsLoginAddressWrap" data-required_agreement="1" style="position: relative; overflow: hidden;">
        <?php write_html($this->parseTemplate($data['template_file'], $data)); ?>
        <!-- /.loginAddress --></div>
    <?php endif ?>

    <?php write_html( $this->parseTemplate('Cooperation.php', array('brand' => $data['pageStatus']['brand'], 'action' => '登録'))) ?>
<?php else: ?>
    <h1 class="singleWrapHd1">ログインしてメッセージを読む</h1>
    <p class="supplement1">※連携済のアカウント・メールアドレスは、メール内「ご登録中の情報」よりご確認ください</p>

    <?php if (in_array(SocialAccount::SOCIAL_MEDIA_GDO, $data['limited_accounts'])): ?>
        <?php unset($data['available_sns_accounts'][array_search(SocialAccount::SOCIAL_MEDIA_GDO, $data['limited_accounts'])]); ?>
        <?php if ($data['pageContents']) write_html($data['pageContents']); ?>
    <?php endif; ?>

    <?php if (in_array(SocialAccountService::SOCIAL_MEDIA_PLATFORM, $data['available_sns_accounts'])): ?>
    <?php if (count($data['available_sns_accounts']) > 0): ?>
        <h2 class="hd1 jsLoginSnsHeader">お持ちのアカウント</h2>
        <?php write_html($this->parseTemplate('auth/SnsLoginForm.php', $data)); ?>
    <?php endif; ?>
    <h2 class="hd1 jsLoginAddressHeader">メールアドレス</h2>
    <div class="addressJoin jsLoginAddressWrap" data-required_agreement="1">
        <?php write_html($this->parseTemplate($data['template_file'], $data)); ?>
        <!-- /.loginAddress --></div>
    <?php endif; ?>
<?php endif; ?>

<?php write_html($this->scriptTag('BrandcoLoggingFormService'))?>
