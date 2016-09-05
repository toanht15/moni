<section class="userListWrap">
    <?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoInquiryListPager')->render(array(
        'TotalCount' => $data['total_count'],
        'CurrentPage' => $data['page'],
        'Count' => InquiryService::N_INQUIRIES_PER_PAGE,
    ))) ?>
    <table class="itemTable">
        <thead>
        <tr>
            <th>受信日時</th>
            <th>名前</th>
            <th>カテゴリ</th>
            <th>問い合わせ元</th>
            <th>最新の内容</th>
            <th>備考</th>
            <th>ステータス</th>
            <th>担当者</th>
            <th>詳細</th>
        </tr>
        </thead>
        <tbody>
        <?php if (count($data['inquiry_list'])) : ?>
            <?php foreach ($data['inquiry_list'] as $inquiry) : ?>
                <tr>
                    <td><?php assign(Util::getFormatDateTimeString($inquiry['created_at'])); ?></td>
                    <td><?php assign($inquiry['user_name']) ?></td>
                    <td><?php assign(Inquiry::getCategory($inquiry['category'])); ?></td>
                    <td><?php assign($inquiry['brand_name']); ?></td>
                    <td title="<?php assign($inquiry['content']) ?>"><?php assign(Util::cutTextByWidth($inquiry['content'], 300)); ?></td>
                    <td title="<?php assign($inquiry['remarks']) ?>"><?php assign(Util::cutTextByWidth($inquiry['remarks'], 240)); ?></td>
                    <td><?php assign(InquiryRoom::getStatus($inquiry['status'])); ?></td>
                    <td><?php assign(($inquiry['operator_name']) ?: '-'); ?></td>
                    <td><span class="btn3"><a
                                href="<?php assign(Util::rewriteUrl(InquiryRoom::getDir($data['operator_type']), 'show_inquiry', array($inquiry['id']))); ?>"
                                class="small1">詳細</a></span></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="9" class="noList">0件です</td>
            </tr>
        <?php endif; ?>
        </tbody>
        <!-- /.itemTable --></table>

    <?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoInquiryListPager')->render(array(
        'TotalCount' => $data['total_count'],
        'CurrentPage' => $data['page'],
        'Count' => InquiryService::N_INQUIRIES_PER_PAGE,
    ))) ?>
</section>
