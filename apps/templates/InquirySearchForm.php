<section class="inquirySearch jsSearchForm">
    <form method="POST">
        <?php write_html($this->csrf_tag()); ?>
        <?php write_html($this->formHidden('total_count', $data['total_count'])); ?>
        <?php write_html($this->formHidden('page', 1)); ?>
        <table class="inquiryTable1">
            <tbody>
            <tr class="jsCheckToggleWrap">
                <th class="title1">期間<span class="range"><?php write_html($this->formCheckbox('period_flg', 0, array('class' => 'jsCheckToggle'), array('1' => '範囲'))); ?></span></th>
                <td class="edit1">
                    <?php write_html($this->formText('date_begin', '', array('class' => 'jsDate inputDate', 'placeholder' => '年/月/日'))) ?>
                    <span class="jsCheckToggleTarget" style="display:none;">
                            <span class="dash">～</span>
                        <?php write_html($this->formText('date_end', '', array('class' => 'jsDate inputDate', 'placeholder' => '年/月/日'))) ?>
                        </span>
                </td>
                <th class="title2">担当者</th>
                <td><?php write_html($this->formText('operator_name', '', array('placeholder' => '例)新井戸'))) ?></td>
            </tr>
            <tr>
                <th class="title1">カテゴリ</th>
                <td colspan="3">
                    <?php write_html($this->formSelect('category', Inquiry::TYPE_DEFAULT, array('class' => 'select1'), Inquiry::$category_options)); ?>
                </td>
            </tr><tr>
                <th class="title1">対応状況</th>
                <td colspan="3">
                    <?php write_html($this->formCheckbox('status', array(InquiryRoom::STATUS_OPEN, InquiryRoom::STATUS_IN_PROGRESS), array(), InquiryRoom::$status_options)); ?>
                </td>
            </tr>
            <?php if (InquiryRoom::isManager($data['operator_type'])) : ?>
            <tr>
                <th class="title1"> メールアドレス</th>
                <td colspan="3"><?php write_html($this->formEmail('mail_address', '', array('class' => 'widthFull', 'placeholder' => 'sample@monipla.com'))) ?></td>
            </tr>
            <?php endif; ?>
            <tr>
                <th class="title1">フリーワード</th>
                <td colspan="3"><?php write_html($this->formText('keywords', '', array('class' => 'widthFull', 'placeholder' => ''))) ?></td>
            </tr>
            <tr>
                <td colspan="4">
                        <span class="btnSet">
                            <span class="btn3"><a href="javascript:void(0);" class="jsSearch" data-form_action="<?php assign(Util::rewriteUrl(InquiryRoom::getDir($data['operator_type']), 'api_search_inquiry.json')); ?>">検索する</a></span>
                            <?php if (InquiryRoom::isManager($data['operator_type'])) : ?>
                                <span class="btn3"><a href="javascript:void(0)" class="jsDownload" data-form_action="<?php assign(Util::rewriteUrl(InquiryRoom::getDir($data['operator_type']), 'download_inquiry_csv')); ?>">CSV出力</a></span>
                            <?php endif; ?>
                        <!-- /.btnSet --></span>
                </td>
            </tr>
            </tbody>
            <!-- /.inquiryTable1 --></table>

    </form>
    <!-- /.inquirySearch --></section>
