<div class="adAccountWrap">
    <div class="customaudienceTableHeader">
        <h2 class="hd2">広告アカウント一覧</h2>
        <p class="create"><span class="btn1"><a href="#selectAdsSnSType" class="middle1 jsOpenModal">アカウントの追加</a></span></p>
        <!-- /.customaudienceTableHeader --></div>
    <?php if(count($data['ads_accounts']) > 0):?>
        <table class="customaudienceTable">
            <thead>
            <tr>
                <th>SNS</th>
                <th>アカウント名</th>
                <th>登録数</th>
                <th>最終送信日</th>
                <th>カスタムオーディエンス規約</th>
                <th>Facebook以外のサービスから<br>カスタムオーディエンス修正の規約</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach($data['ads_accounts'] as $account): ?>

                <?php $account_name = $account['account_name'] ? $account['account_name'] : '未設定'; ?>

                <tr class="<?php assign($this->isDisableAccount($account) ? 'disabled' : '')?>">

                    <td class="sns">
                    <span class="<?php assign(AdsAccount::$sns_icon_class_2[$account['social_app_id']]) ?>">
                        <?php assign(AdsAccount::$sns_label[$account['social_app_id']]) ?>
                    </span>
                    </td>

                    <td class="accountName">
                        <span <?php write_html($account['error'] ? 'style="text-decoration: line-through"' : '')?>><?php assign($account_name) ?></span>
                    </td>

                    <td <?php write_html($account['error'] ? 'style="text-decoration: line-through"' : '')?>><?php assign($account['audience_count']) ?></td>
                    <td <?php write_html($account['error'] ? 'style="text-decoration: line-through"' : '')?>><?php assign($account['last_send_target']) ?></td>

                    <?php if($account['social_app_id'] == SocialApps::PROVIDER_FACEBOOK && !$account['error']): ?>
                        <?php $account_id_num = explode("act_", $account['account_id'])[1]; ?>
                        <td><?php if (!$account['custom_audience_tos']) write_html('<span class="notyet">未確認</span>') ?><a href="https://www.facebook.com/ads/manage/customaudiences/tos.php?account_id=<?php assign($account_id_num) ?>" target="_blank"><?php write_html($account['custom_audience_tos'] ? "確認済" : "（確認する）") ?></a></td>
                        <td><?php if (!$account['web_custom_audience_tos']) write_html('<span class="notyet">未確認</span>') ?><a href="https://www.facebook.com/customaudiences/app/tos?account_id=<?php assign($account_id_num) ?>" target="_blank"><?php write_html($account['web_custom_audience_tos'] ? "確認済" : "（確認する）") ?></a></td>
                    <?php else: ?>
                        <td></td>
                        <td></td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
            </tbody>
            <!-- /.customaudienceTable --></table>
    <?php endif; ?>
<!-- /.adAccountWrap --></div>