    <table width="600" cellpadding="0" cellspacing="0" border="0" style="text-align: center; font-size: 10px; color: #A0A0A0; background: #FFF;">
        <tbody>
        <?php if (!$data['is_whitelist']): ?>
            <tr>
                <td rowspan="16" width="30"></td>
                <td colspan="3" width="560" height="50"></td>
                <td rowspan="16" width="30"></td>
            </tr>
            <tr>
                <td width="141"></td>
                <th wdth="258" height="60"><a href="<?php assign($data['monipla_media_url']); ?>" targe="_blank" style="border:none; width:258px; height:60px;"><img src="<?php assign($data['monipla_logo_url']); ?>" width="258" height="60" alt="monipla" style="display: block; border: none; margin: auto;"></a></th>
                <td width="141"></td>
            </tr>
            <tr>
                <td colspan="3" width="560" height="15"></td>
            </tr>
            <tr>
                <td colspan="3" width="560"><a href="<?php assign($data['monipla_media_url']); ?>" target="_blank">http://cp.monipla.com/</a></td>
            </tr>
            <tr>
                <td colspan="3" width="560" height="50"></td>
            </tr>
        <?php endif; ?>
        <tr>
            <?php if ($data['is_whitelist']): ?>
                <td rowspan="16" width="30"></td>
            <?php endif; ?>
            <td colspan="3" width="560" height="30" style="border-top: 1px solid #DDD;"></td>
            <?php if ($data['is_whitelist']): ?>
                <td rowspan="16" width="30"></td>
            <?php endif; ?>
        </tr>
        <tr>
            <td colspan="3" width="560">本メールはモニプラを利用して開催された企画に参加された方にお送りしております。配信専用のメールアドレスよりお送りしております。本メールに返信されてもお答えできませんのでご了承ください。</td>
        </tr>
        <tr>
            <td colspan="3" width="560" height="15"></td>
        </tr>
        <tr>
            <td colspan="3">
                <table width="560" height="50" cellpadding="0" cellspacing="0" border="0" style="text-align: center;">
                    <tbody>
                    <tr>
                        <td width="270" height="50" style="background: #F4F4F7; font-size: 13px;"><a href="<?php assign($data['aaid_faq_url']); ?>" target="_blank">よくある質問</a></td>
                        <td width="270" height="50" style="padding: 0 15px; background: #F4F4F7; font-size: 13px;"><a href="<?php assign($data['aaid_inquiry_url']); ?>" target="_blank">お問い合わせ</a></td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="3" width="560" height="20"></td>
        </tr>
        <tr>
            <td colspan="3" width="560" style="text-align: center;">発行元：<a href="<?php assign($data['monipla_media_url']); ?>" target="_blank">モニプラ</a><br />運営会社：<a href="http://www.aainc.co.jp/" target="_blank">アライドアーキテクツ株式会社</a></td>
        </tr>
        <tr>
            <td colspan="3" width="560" height="30"></td>
        </tr>
        </tbody>
    </table>
</div>
</center>

</body>
</html>
