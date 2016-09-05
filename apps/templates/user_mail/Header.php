<!doctype html>
<html lang="ja">
<head>
    <meta charset="shift_jis">
    <title>モニプラ mail</title>

    <style>
        <!--
        body {
            font-family: Verdana, '游ゴシック', YuGothic, 'Hiragino Kaku Gothic ProN', Meiryo, sans-serif;
            background: #ECEBF0;
            color: #333;
            line-height: 1.6;
            min-width: 600px;
            font-size: 14px;
            margin: 0;
            padding: 30px 0;
        }
        a {
            color: #395897;
        }
        body,td,th {
            font-family: Verdana, '游ゴシック', YuGothic, 'Hiragino Kaku Gothic ProN', Meiryo, sans-serif;
        }
        -->
    </style>

</head>
<body style="font-family: Verdana, '游ゴシック', YuGothic, 'Hiragino Kaku Gothic ProN', Meiryo, sans-serif; background: #ECEBF0; color: #333; line-height: 1.6; min-width: 600px; font-size: 14px; margin: 0; padding: 30px 0;">
<center>
    <div style="width: 600px;">
        <table width="600" cellpadding="0" cellspacing="0" border="0" style="background: #FFF;">
            <tbody>
            <tr>
                <td rowspan="10" width="20" height="15"></td>
                <td colspan="3" width="560" height="15"></td>
                <td rowspan="10" width="20" height="15"></td>
            </tr>
            <tr>
                <th colspan="3" width="560" height="30" style="text-align: center;">
                    <img src="<?php assign(config('Protocol.Secure') . ':' . $this->setVersion('/img/mail/welcome/logoMonipla_01.png')); ?>" alt="monipla" width="130" height="30" style="display: block; border: none; margin: auto;">
                </th>
            </tr>
            <tr>
                <td colspan="3" width="560" height="5"></td>
            </tr>
            <tr>
                <td colspan="3" width="560" height="30" style="font-size: 14px; color: #000; text-align: center; margin:0; padding:0;">
                    <?php assign($data['title']); ?>ありがとうございます！
                </td>
            </tr>
            <tr>
                <td colspan="3" width="560" height="5"></td>
            </tr>
            <tr>
                <td colspan="3" width="560" height="5" style="border-top: 1px solid #DDD;"></td>
            </tr>
            <tr>
                <td colspan="3" width="560" height="30" style="font-size: 10px; color: #000; text-align: center; margin:0; padding:0;"><?php assign($data['entry_cp_info'] ? $data['sub_title'] : ''); ?></td>
            </tr>

            <?php if ($data['template_id'] === UserMailService::TEMPLATE_ID_WELCOME) : ?>
                <tr>
                    <td colspan="3" width="560" align="center">
                        <table width="420" cellpadding="0" cellspacing="0" border="0" style="background: #FFF;">
                            <?php if ($data['entry_cp_info']): ?>
                                <tr>
                                    <td rowspan="2" width="50"><img src="<?php assign($data['entry_cp_info']['cp_image_url']); ?>" height="50" width="50" alt="<?php assign($data['entry_cp_info']['cp_title']); ?>"></td>
                                    <td rowspan="2" width="15"></td>
                                    <th width="443" style="font-size: 18px; font-weight: normal; text-align:left; vertical-align:middle;"><?php assign($data['entry_cp_info']['cp_title']); ?></th>
                                </tr>
                                <tr>
                                    <td width="355" style="font-size: 11px; color:#888888; text-align:left; vertical-align:middle;"><?php assign($data['entry_cp_info']['brand']->enterprise_name); ?></td>
                                </tr>
                                <tr>
                                    <td colspan="3" width="420" height="15"></td>
                                </tr>
                                <tr>
                                    <td colspan="3" width="420" height="10" style="border-top: 1px solid #DDD;"></td>
                                </tr>
                            <?php endif; ?>
                            <tr>
                                <td colspan="3" width="420" style="font-size:12px; color:#000;">この度は、<a href="<?php assign($data['monipla_media_url']); ?>" target="_blank">モニプラ</a>に登録いただきありがとうございます。<br>あなたのご登録情報は以下となります。<br>連携アカウント・メールアドレスを用いてログインをお願いします。</td>
                            </tr>
                            <tr>
                                <td colspan="3" width="420" height="15"></td>
                            </tr>
                            <?php write_html($this->parseTemplate('user_mail/UserAccountInfo.php', $data['user_account_info'])); ?>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td colspan="3" width="560" height="50"></td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
