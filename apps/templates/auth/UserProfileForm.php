<h1 class="<?php assign($data['parent_class_name']); ?>Hd1">アカウント情報登録<span class="poweredByMonipla">powerd by<img src="<?php assign($this->setVersion('/img/base/imgLogo_lg.png')); ?>" width="60" height="14" alt="モニプラ"></span></h1>
<p class="entryText">参加には、以下の情報の確認が必要です。</p>
<form method="POST" class="jsUserProfileForm" action="<?php assign($data['is_api'] ? Util::rewriteUrl('messages', 'api_update_user_profile.json') : Util::rewriteUrl('auth', 'pre_signup_post'));?>">
    <?php write_html($this->csrf_tag()); ?>
    <?php write_html($this->formHidden('cp_id', $data['is_api'] ? $data['cp_id'] : -1)); ?>
    <?php write_html($this->formHidden('cp_user_id', $data['is_api'] ? $data['cp_user_id'] : -1)); ?>
    <?php write_html($this->formHidden('cp_action_id', $data['is_api'] ? $data['cp_action_id'] : -1)); ?>
    <?php if ($data['is_api']): ?>
        <?php write_html($this->formHidden('need_display_personal_form', $data['need_display_personal_form'] ? 1 : 0)); ?>
    <?php endif; ?>

    <ul class="commonTableList1">
        <li>
            <p class="title1">
                <span class="require1">ニックネーム</span>
                <!-- /.title1 --></p>
            <p class="itemEdit" data-input_name="name">
                <?php if ($this->ActionError && !$this->ActionError->isValid('name')):?>
                    <span class="iconError1"><?php write_html($this->ActionError->getMessage('name'))?></span>
                <?php endif; ?>
                <span class="editInput">
                <?php write_html($this->formText('name', PHPParser::ACTION_FORM))?>
                    <!-- /.editInput --></span>
                <!-- /.itemEdit --></p>
        </li>
        <li>
            <p class="title1">
                <span class="require1">メールアドレス</span>
                <!-- /.title1 --></p>
            <p class="itemEdit" data-input_name="mail_address">
                <?php if ($this->ActionError && !$this->ActionError->isValid('mail_address')):?>
                    <span class="iconError1"><?php write_html(sprintf($this->ActionError->getMessage('mail_address'), Util::rewriteUrl('inquiry', 'index')))?></span>
                <?php endif; ?>
                <span class="editInput">
                    <?php write_html($this->formEmail('mail_address', PHPParser::ACTION_FORM))?>
                    <!-- /.editInput --></span>
                <small class="supplement1">※当選メールなどをお届けしますので、普段ご利用のものをご入力ください</small>

                <?php
                // FIXME: template呼び出し元に書くと各所にか必要が有るため、ここで呼び出す
                $service_factory = new aafwServiceFactory();
                /** @var BrandGlobalSettingService $brand_global_setting_service */
                $brand_global_setting_service = $service_factory->create('BrandGlobalSettingService');
                $brand_global_setting = $brand_global_setting_service->getBrandGlobalSettingByName(BrandInfoContainer::getInstance()->getBrandGlobalSettings(), BrandGlobalSettingService::HIDE_OPTIN_CHECKBOX);
                ?>
                <?php if (Util::isNullOrEmpty($brand_global_setting)): ?>
                    <small><label for="optin_1"><input type="checkbox" id="optin_1" name="optin" value="1" checked="checked">モニプラ・アライドIDからのお知らせを受け取る</label></small>
                <?php else: ?>
                    <?php write_html($this->formHidden('optin', '0')); ?>
                <?php endif; ?>
                <!-- /.itemEdit --></p>
        </li>
    </ul>
    <ul class="btnSet">
        <li class="btn3"><a href="javascript:void(0);" class="large1 jsUpdateUserProfile">確認して次へ</a></li>
        <!-- /.btnSet --></ul>
</form>

<?php write_html($this->scriptTag('user/UserProfileService')) ?>


