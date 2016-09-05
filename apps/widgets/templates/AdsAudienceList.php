<table class="customaudienceTable">
    <thead>
    <tr class="jsAreaToggleWrap">
        <th class="customaudienceName">カスタムオーディエンス名</th>
        <th>送信先アカウント<a href="javascript:void(0)" class="btnArrowB1 jsAreaToggle">絞り込む</a>
            <?php write_html($this->parseTemplate('ads/AdsAccountFilter.php', array(
                'ads_accounts' => $data['ads_accounts'],
                'target_account_ids' => $data['target_account_ids'],
            ))) ?>
        </th>
        <th>オーディエンスID</th>
        <th>最終送信日</th>
        <th>対象数</th>
        <th>自動送信設定</th>
        <th></th>
    </tr>
    </thead>
    <tbody>
        <?php foreach($data['audiences'] as $audience): ?>
            <?php
            $error_account_decorate = $audience['error_account'] ? 'style="text-decoration: line-through"' : '';
            ?>
            <tr>
                <th>
                    <label class="itemSelect">
                        <input type="checkbox" name="target_relation_ids[]" <?php assign( ($audience['audience_status'] == AdsAudience::STATUS_ACTIVE && !$audience['error_account']) ? '' : 'disabled' ) ?>
                               value="<?php assign($audience['relation_id'])?>">
                    </label>
                    <span class="nameSet">
                      <a href="<?php assign(Util::rewriteUrl('admin-fan', 'ads_audience', array('audience_id' => $audience['audience_id'])))?>"><?php assign($audience['audience_name'])?></a>
                      <small class="choices"><?php assign($this->getConditionBrief($audience))?></small>
                    </span>
                </th>
                <td class="<?php assign($audience['error_account'] ? 'disabled' : '')?>"><?php write_html($audience['account_name'] ? '<span class="'.AdsAccount::$sns_icon_class_1[$audience['social_app_id']].'" '.$error_account_decorate.'>'.$audience['account_name'].'</span>' : '-')?></td>
                <td <?php write_html($error_account_decorate)?> class="<?php assign($audience['error_account'] ? 'disabled' : '')?>">
                    <?php if($audience['sns_audience_id']): ?>
                        <?php if($audience['social_app_id'] == SocialApps::PROVIDER_FACEBOOK):?>
                            <?php assign($audience['sns_audience_id'])?>
                        <?php elseif($audience['social_app_id'] == SocialApps::PROVIDER_TWITTER): ?>
                            <?php assign(implode(',', $audience['sns_audience_id'] ))?>
                        <?php endif; ?>
                    <?php else: ?>
                        <?php assign('-')?>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if($audience['last_send_target_date']):?>
                        <?php assign($audience['last_send_target_date'])?>
                        <span class="<?php assign($audience['last_send_target_status'] == AdsTargetLog::SEND_TARGET_SUCCESS ? 'success' : 'error')?>"><?php assign($audience['last_send_target_status'] == AdsTargetLog::SEND_TARGET_SUCCESS ? '成功' : '送信失敗')?></span>
                    <?php else: ?>
                        <?php assign('-')?>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if(!Util::isNullOrEmpty($audience['last_send_target_count'])):?>
                        <strong><?php assign($audience['last_send_target_count'])?><span>名</span></strong>
                    <?php else: ?>
                        <?php assign('-')?>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if($audience['audience_status'] == AdsAudience::STATUS_ACTIVE && !$audience['error_account']): ?>
                        <a href="javascript:void(0)" class="<?php assign($audience['auto_send_target_flg'] == AdsAudiencesAccountsRelation::AUTO_SEND_TARGET_FLG_ON ? 'switch on' : 'switch off') ?>" data-relation_id="<?php assign($audience['relation_id'])?>" ><span class="switchInner"><span class="selectON">ON</span><span class="selectOFF">OFF</span></span></a>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </td>
                <td>
                    <?php if($audience['audience_status'] == AdsAudience::STATUS_ACTIVE): ?>
                        <a href="javascript:void(0)" data-url="<?php write_html(Util::rewriteUrl('admin-fan','copy_ads_audience',array($audience['audience_id']))) ?>" data-name="<?php assign($audience['audience_name']) ?>" class="jsCopyCondition">条件をコピー</a>
                    <?php else: ?>
                        <span class="iconDraft2">下書き</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
    <!-- /.customaudienceTable --></table>
    <?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoDefaultListPager')->render(array(
        'TotalCount' => $data['count'],
        'CurrentPage' => $data['page_no'],
        'Count' => $data['count_per_page'],
    ))) ?>
<!-- /.pager1 --></div>
<?php write_html($this->formHidden('target_account_ids', json_encode($data['target_account_ids']))) ?>