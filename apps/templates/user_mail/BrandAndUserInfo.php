<table width="600" cellpadding="0" cellspacing="0" border="0" style="background: #FFF;">
    <tbody>
    <?php if ($data['is_manager']): ?>
        <tr>
            <th colspan="3" width="600" height="60" align="center" bgcolor="#F8F8F8" style="text-align:center;color:#555;line-height:60px; background: #F8F8F8; font-size:14px; font-weight:normal;">あなたが登録したサイトはこちら！</th>
        </tr>
        <tr>
            <td width="20" height="20"></td>
            <td width="560" height="20"></td>
            <td width="20" height="20"></td>
        </tr>
    <?php endif;?>
    <tr>
        <td width="20"></td>
        <td align="center">
            <table width="560" cellpadding="0" cellspacing="0" border="0" style="background: #FFF; border:1px solid #ddd; border-radius:6px;">
                <tbody>
                <tr>
                    <td width="20" height="20" rowspan="5"></td>
                    <td width="520" height="20" colspan="3"></td>
                    <td width="20" height="20" rowspan="5"></td>
                </tr>

                <?php if ($data['is_manager']): ?>
                    <tr>
                        <td width="118"><img src="<?php assign($data['brand']->getProfileImage()); ?>" height="118" width="118" alt="<?php assign($data['brand']->name); ?>" style="vertical-align:top;"></td>
                        <td width="20">&nbsp;</td>
                        <td width="382" height="20" style="vertical-align:middle;">
                            <p style="font-size:26px; font-weight:bold;margin:0; padding:0;line-height:1.1;"><?php assign($data['brand']->name); ?></p>
                            <p style="font-size:14px; color:#888; margin:0; padding:0;line-height:1.2;"><?php assign($data['brand']->enterprise_name); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <td height="20" colspan="3"></td>
                    </tr>
                <?php endif;?>
                <tr>
                    <td height="15" colspan="3" style="border-top: 1px solid #DDD;"></td>
                </tr>
                <?php write_html($this->parseTemplate('user_mail/UserAccountInfo.php', $data['user_account_info'])); ?>
                <tr>
                    <td height="20"></td>
                </tr>
                </tbody>
            </table>
        </td>
        <td width="20"></td>
    </tr>
    <?php if ($data['is_manager']): ?>
        <tr>
            <td colspan="3" width="600" height="20"></td>
        </tr>
        <tr>
            <td colspan="3" align="center" style="text-align:center;">
                <a href="<?php assign($data['fan_site_url']); ?>" target="_blank" ><img src="<?php assign(config('Protocol.Secure') . ':' . $this->setVersion('/img/mail/finish/btnMoniplaNosite_02.png')); ?>" height="54" width="300" alt="サイトヘ" style="border:none; vertical-align:top;"></a>
            </td>
        </tr>
    <?php endif;?>
    <tr>
        <td colspan="3" width="600" height="20"></td>
    </tr>
    </tbody>
</table>
