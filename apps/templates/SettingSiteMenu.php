<aside class="adminSideCol">
    <nav class="adminSettingWrap">
        <ul>
            <?php if ($this->values['ActionForm']['action'] === 'administrator_settings_form'): ?>
                <li class="admin"><span class="current">管理者設定</span></li>
            <?php else: ?>
                <li class="admin"><a href="admin-settings/administrator_settings_form">管理者設定</a></li>
            <?php endif; ?>

            <?php if ($this->values['ActionForm']['action'] === 'user_settings_form'): ?>
                <li class="user"><span class="current">ユーザー設定</span></li>
            <?php else: ?>
                <li class="user"><a href="admin-settings/user_settings_form">ユーザー設定</a></li>
            <?php endif; ?>

            <?php if($data['can_set_sign_up_mail']): ?>
                <?php if ($this->values['ActionForm']['action'] === 'signup_mail_settings_form'): ?>
                    <li class="mail"><span class="current">登録メール設定</span></li>
                <?php else: ?>
                    <li class="mail"><a href="admin-settings/signup_mail_settings_form">登録メール設定</a></li>
                <?php endif; ?>
            <?php endif; ?>

            <?php if ($this->values['ActionForm']['action'] === 'page_settings_form'): ?>
                <li class="page"><span class="current">ページ設定</span></li>
            <?php else: ?>
                <li class="page"><a href="admin-settings/page_settings_form">ページ設定</a></li>
            <?php endif; ?>

            <?php if (Util::isDefaultBRANDCoDomain()): ?>
                <?php if (in_array($this->values['ActionForm']['action'], array('conversion_setting_form', 'edit_conversion_form'))): ?>
                    <li class="cvtag"><span class="current">コンバージョンタグ作成</span></li>
                <?php else: ?>
                    <li class="cvtag"><a href="admin-settings/conversion_setting_form">コンバージョンタグ作成</a></li>
                <?php endif; ?>
            <?php endif; ?>
            <?php if (in_array($this->values['ActionForm']['action'], array('redirector_settings_form', 'edit_redirector_form'))): ?>
                <li class="redirect"><span class="current">リダイレクトURL作成</span></li>
            <?php else: ?>
                <li class="redirect"><a href="admin-settings/redirector_settings_form">リダイレクトURL作成</a></li>
            <?php endif; ?>

            <?php if ($this->values['ActionForm']['action'] === 'inquiry_settings_form'): ?>
                <li class="contact"><span class="current">通知先メールアドレス設定</span></li>
            <?php else: ?>
                <li class="contact"><a href="admin-settings/inquiry_settings_form">通知先メールアドレス設定</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</aside>