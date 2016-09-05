<table width="600" cellpadding="0" cellspacing="0" border="0" style="text-align: center; background: #FFF;">
    <tbody>
    <tr>
        <td colspan="3" height="20"></td>
    </tr>
    <tr>
        <th colspan="3" style="font-size: 24px; font-weight: bold; text-align: center;"><img src="<?php assign(config('Protocol.Secure') . ':' . $this->setVersion('/img/mail/welcome/iconTitleHot.png')); ?>" width="18" height="31" alt="hot" style="vertical-align:middle; padding-right:5px;"><?php assign($data['media_cp_type_name']); ?></th>
    </tr>
    <tr>
        <td colspan="3" height="20"></td>
    </tr>
    <tr>
        <td colspan="3" align="center" width="600" style="font-size: 12px;">
            <table width="560" cellpadding="0" cellspacing="0" border="0">
                <?php foreach ($data['media_cps'] as $media_cp) : ?>
                <tr>
                    <td style="border:1px solid #DDD;padding:14px;">
                        <table cellpadding="0" cellspacing="0" border="0">
                            <tbody>
                            <tr>
                                <td width="200" height="104" bgcolor="#F8F8F8" style="text-align:center; background:#F8F8F8;"><a href="<?php assign($media_cp['url']); ?>?fid=mpwelml" target="_blank" bgcolor="#F8F8F8" style="text-align:center; background:#F8F8F8; border:none;"><img src="<?php assign($media_cp['image']); ?>" alt="<?php assign($media_cp['name']); ?>" height="104" style="border:none; vertical-align:middle; width:auto; height:104px;"></a></td>
                                <td width="15" height="15"></td>
                                <td style="vertical-align:top;">
                                    <p style="padding:0; margin:0; color: #888; line-height:1.5; margin-bottom:10px;"><img src="<?php assign(config('Protocol.Secure') . ':' . $this->setVersion('/img/mail/welcome/iconEnjoy.png')); ?>" height="20" width="20" alt="プレゼント" style="vertical-align:middle;padding-right:5px;"><?php assign($media_cp['winningLabel']); ?><span style="float:right;padding-left:5px;"><img src="<?php assign(config('Protocol.Secure') . ':' . $this->setVersion('/img/mail/welcome/iconHot.png')); ?>" height="18" width="34" alt="HOT"></span></p>
                                    <p style="padding:0; margin:0; font-size:16px; color:#555; line-height:1.2; font-weight:bold; margin-bottom:10px;"><a href="<?php assign($media_cp['url']); ?>?fid=mpwelml" target="_blank"><?php assign($media_cp['name']); ?></a></p>
                                    <p style="padding:0; margin:0; color: #888;"><?php assign($media_cp['enterpriseName']); ?></p>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <?php endforeach; ?>
                <tr>
                    <td height="20"></td>
                </tr>
                <tr>
                    <td align="center"><a href="<?php assign($data['monipla_media_url']); ?>" target="_blank" style="border:none; width:201px; height:105px;" target="_blank" style="border:none; width:300px; height:54px;"><img src="<?php assign(config('Protocol.Secure') . ':' . $this->setVersion('/img/mail/welcome/btnMonipla_01.png')); ?>" height="54" width="300" alt="モニプラでもっと見る" style="border:none;"></a></td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td colspan="3" height="45"></td>
    </tr>
    </tbody>
</table>
