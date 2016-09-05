<section class="inquiryTableWrap">
    <table class="inquiryTable1">
        <tbody>
        <tr>
            <th class="title1">名前</th>
            <td>
                <?php assign($data['inquiry_user']['user_name']); ?>
            </td>
        </tr>
        <tr>
            <th class="title1">会員No</th>
            <td><?php assign($data['inquiry_user']['no']); ?></td>
        </tr>
        <?php if (InquiryRoom::isManager($data['operator_type'])): ?>
            <tr>
                <th class="title1">アライド ID</th>
                <td><?php assign($data['inquiry_user']['monipla_user_id']); ?></td>
            </tr>
            <tr>
                <th class="title1">メールアドレス</th>
                <td><?php assign($data['inquiry_user']['mail_address']); ?></td>
            </tr>
            <tr>
                <th class="title1">参加状況</th>
                <td>
                    <a href="<?php assign(Util::rewriteUrl('users', 'index', array(), array('search_type' => 1, 'platform_user_id' => $data['inquiry_user']['monipla_user_id']))); ?>" target="_brank"><?php assign(Util::rewriteUrl('users', 'index', array(), array('search_type' => 1, 'platform_user_id' => $data['inquiry_user']['monipla_user_id']))); ?></a>
                </td>
            </tr>
        <?php endif; ?>
        <tr>
            <th class="title1">キャンペーン名</th>
            <td><?php assign($data['inquiry_user']['cp_title']); ?></td>
        </tr>
        <?php if (InquiryRoom::isManager($data['operator_type'])): ?>
            <tr>
                <th class="title1">UA</th>
                <td><?php assign($data['inquiry_user']['user_agent']); ?></td>
            </tr>
        <?php endif; ?>
        </tbody>
        <!-- /.inquiryTable1 --></table>
</section>
