<tr>
    <td colspan="3" align="center">
        <table width="420" cellpadding="5" cellspacing="0" border="0" style="font-size:13px; border:1px solid #DDD;">
            <tbody>
            <tr>
                <td width="160" rowspan="4" align="center" style="border-right:1px solid #DDD; text-align:center;"><p><img src="<?php assign($data['profile_image']) ?>" width="100" height="100" alt="ユーザー"></p>
                    <p><?php assign(Util::cutTextByWidth($data['user_name'], 130));?></p></td>
                <?php if ($data['social_accounts']): ?>
                    <td width="260" bgcolor="#DDDDDD" style="background:#DDD;">以下のアカウントと連携しています</td>
                <?php endif;?>
            </tr>
            <?php if ($data['social_accounts']): ?>
                <tr>
                    <td style="line-height:1;">
                        <?php foreach ($data['social_accounts'] as $social_account): ?>
                            <p><img src="<?php assign(config('Protocol.Secure') . ':' . $this->setVersion('/img/mail/welcome/iconSns' . SocialAccount::$socialMediaTypeIcon[$social_account->social_media_id] . '4.png')); ?>" width="15" height="15" alt="<?php assign(SocialAccount::$socialMediaTypeName[$social_account->social_media_id]); ?>" style="vertical-align:middle; padding-right:5px;"><?php assign($social_account->name); ?></p>
                        <?php endforeach; ?>
                    </td>
                </tr>
            <?php endif;?>
            <?php if ($data['has_mail_address']): ?>
                <tr>
                    <td bgcolor="#DDDDDD" style="background:#DDD;">ログイン用メールアドレス</td>
                </tr>
                <tr>
                    <td><?php assign($data['mail_address']) ?></td>
                </tr>
            <?php endif;?>
            </tbody>
        </table>
    </td>
</tr>