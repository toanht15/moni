<table width="600" cellpadding="0" cellspacing="0" border="0" style="background: #FFF;">
    <tbody>
    <tr>
        <td colspan="3" align="top" bgcolor="#F8F8F8" style="text-align:center; background:#F8F8F8; vertical-align:top;">
            <img src="<?php assign($data['cp']->image_rectangle_url ?: $data['cp']->image_url); ?>" style="vertical-align: top; border:none; <?php if ($data['cp']->image_rectangle_url) { write_html('display:block; max-width:600px;'); } ?>" <?php if ($data['cp']->image_rectangle_url) { write_html('height="314"');} ?> alt="<?php assign($data['cp_title']); ?>">
        </td>
    </tr>
    <tr>
        <td width="20" height="20"></td>
        <td width="560" height="20"></td>
        <td width="20" height="20"></td>
    </tr>
    <tr>
        <td width="20" height="20"></td>
        <td align="center">
            <table width="560" cellpadding="0" cellspacing="0" border="0" style="background: #FFF; border:1px solid #ddd;">
                <tbody>
                <tr>
                    <td width="140" height="37" bgcolor="#F8F8F8" style="background:#F8F8F8; border-right:1px solid #ddd; border-bottom:1px solid #ddd; verticla-align:middle; padding-left:12px;">タイトル</td>
                    <td width="420" bgcolor="#FFFFFF" style="background:#fff; border-bottom:1px solid #ddd; padding-left:12px;"><?php assign($data['cp_title']); ?></td>
                </tr>
                <tr>
                    <td width="140" height="37" bgcolor="#F8F8F8" style="background:#F8F8F8; border-right:1px solid #ddd; border-bottom:1px solid #ddd; verticla-align:middle; padding-left:12px;">開催</td>
                    <td width="420" height="37" bgcolor="#FFFFFF" style="background:#fff; border-bottom:1px solid #ddd; verticla-align:middle; padding-left:12px;"><?php assign($data['brand']->enterprise_name); ?></td>
                </tr>
                <tr>
                    <td width="140" height="37"  bgcolor="#F8F8F8" style="background:#F8F8F8; border-right:1px solid #ddd; border-bottom:1px solid #ddd; verticla-align:middle; padding-left:12px;">期間</td>
                    <td width="420" height="37"  bgcolor="#FFFFFF" style="background:#fff; border-bottom:1px solid #ddd; verticla-align:middle; verticla-align:middle; padding-left:12px;"><?php assign(Util::getFormatDateString($data['cp']->start_date)) ?> 〜 <?php if (!$data['cp']->isNonIncentiveCp()) { assign(Util::getFormatDateString($data['cp']->end_date)); } ?> </td>
                </tr>
                <?php if (!$data['cp']->isNonIncentiveCp()): ?>
                    <tr>
                        <td width="140" height="37"  bgcolor="#F8F8F8" style="background:#F8F8F8; border-right:1px solid #ddd; verticla-align:middle; padding-left:12px;">発表日</td>
                        <td width="420" height="37"  bgcolor="#FFFFFF" style="background:#fff; verticla-align:middle; padding-left:12px;">
                            <?php if ($data['cp']->announce_display_label_use_flg == 1): ?>
                                <?php assign($data['cp']->announce_display_label) ?>
                            <?php elseif ($data["cp"]->shipping_method == Cp::SHIPPING_METHOD_PRESENT): ?>
                                賞品の発送をもって発表
                            <?php else: ?>
                                <?php assign(Util::getFormatDateString($data["cp"]->announce_date)); ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </td>
        <td width="20" height="20"></td>
    </tr>
    <tr>
        <td width="600" height="20" colspan="3"></td>
    </tr>
    <tr>
        <td colspan="3" align="center" style="text-align:center;"><a href="<?php assign($data['cp']->getThreadUrl() . '?fid=' . $data['fid']); ?>" target="_blank"><img src="<?php assign(config('Protocol.Secure') . ':' . $this->setVersion('/img/mail/finish/btnMoniplaNosite_01.png')); ?>" height="54" width="300" alt="企画ページへ" style="border:none; vertical-align:top;"></a></td>
    </tr>
    <tr>
        <td width="600" height="20" colspan="3"></td>
    </tr>
    </tbody>
</table>
