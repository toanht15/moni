<?php
$service_factory = new aafwServiceFactory();
/** CpFlowService $cp_flow_service */
$cp_flow_service = $service_factory->create('CpFlowService');
$cp = $cp_flow_service->getCpById($data['cp_id']);

$brand_page_setting_service = $service_factory->create('BrandPageSettingService');
$is_public_brand = $brand_page_setting_service->isPublic($cp->brand_id);
?>

<?php if( $cp->join_limit_flg == cp::JOIN_LIMIT_OFF):?>
    <div class="modal1 jsModal" id="modal1">
        <section class="modalCont-medium jsModalCont">
            <h1>キャンペーン公開前に下記項目をご確認ください</h1>
            <ul class="adminRuleCheck">
                <li><label><input type="checkbox" name="condition1" id="condition1_1" class="licenceBox" value="1">「いいね！」や「フォロー」、「配送先情報取得」など参加フローに不足はありませんか？</label></li>
                <?php if ($cp->isNonIncentiveCp()): ?>
                    <?php if ($cp->isPermanent()): ?>
                        <li><label><input type="checkbox" name="condition2" id="condition2_1" class="licenceBox" value="1"><?php assign(date('Y年n月j日 H時i分', strtotime($cp->start_date))); ?>から公開する常設キャンペーンとなりますが、よろしいですか？</label></li>
                    <?php else: ?>
                        <li><label><input type="checkbox" name="condition2" id="condition2_1" class="licenceBox" value="1"><?php assign(date('Y年n月j日 H時i分', strtotime($cp->start_date))); ?>～<?php assign(date('Y年n月j日 H時i分', strtotime($cp->end_date))) ?>の常設キャンペーンとなりますが、よろしいですか？</label></li>
                    <?php endif ?>
                    <?php $public_rule_index = 3; ?>
                <?php else: ?>
                    <li><label><input type="checkbox" name="condition2" id="condition2_1" class="licenceBox" value="1"><?php assign(date('Y年n月j日 H時i分', strtotime($cp->start_date))); ?>～<?php assign(date('Y年n月j日 H時i分', strtotime($cp->end_date))) ?>のキャンペーンとなりますが、よろしいですか？</label></li>
                    <li><label><input type="checkbox" name="condition3" id="condition3_1" class="licenceBox" value="1">賞品の使用許諾を得ていますか？</label></li>
                    <li><label><input type="checkbox" name="condition4" id="condition4_1" class="licenceBox" value="1">景品表示法を順守したキャンペーンであることを確認しましたか？</label></li>
                    <?php $public_rule_index = 5; ?>
                <?php endif ?>
                <li><label><input type="checkbox" name="condition<?php assign($public_rule_index) ?>" id="condition<?php assign($public_rule_index) ?>_1" class="licenceBox" value="1">キャンペーン公開後は設定変更ができませんが、よろしいですか？</label></li>

                <?php $public_rule_index++; ?>

                <?php if (!$is_public_brand): ?>
                    <li><label><input type="checkbox" name="condition<?php assign($public_rule_index) ?>" id="condition<?php assign($public_rule_index) ?>_1" class="licenceBox" value="1">ファンサイト（企業ページ）が公開状態になっていないとユーザーはキャンペーンに参加ができません。よろしいですか？</label></li>
                <?php endif ?>
            </ul>
            <p class="admincCampaignTime"><strong><span class="attention1"><?php assign(date('Y/m/d H:i', strtotime($cp->public_date))) ?></span>に公開</strong></p>
            <ul class="btnSet">
                <li class="btn2"><a href="#closeModal" class="large1">キャンセル</a></li>
                <li class="btn3 disableButton"><span class="large1">予約公開</span></li>
                <li class="btn3 enableButton" style="display: none"><a href="javascript:void(0)" class="large1" id="scheduleCp" data-cp="<?php assign($cp->id) ?>" data-url="<?php assign(Util::rewriteUrl('admin-cp', 'api_schedule_cp.json'))?>">予約公開</a></li>
            </ul>

        </section>
        <!-- /.modal1 --></div>
<?php endif;?>

<div class="modal1 jsModal" id="modal_demo_confirm">
    <section class="modalCont-medium jsModalCont">
        <h1>デモキャンペーン公開前に下記項目をご確認ください</h1>
        <ul class="adminRuleCheck">
            <li><label><input type="checkbox" id="demo_condition_1" class="demoLicence" value="1">デモキャンペーンではURLを拡散しないために集客設定は動作しません。</label></li>
            <li><label><input type="checkbox" id="demo_condition_2" class="demoLicence" value="1">デモキャンペーンには基本設定で設定した応募期間に関係なく参加できます。</label></li>
            <li><label><input type="checkbox" id="demo_condition_3" class="demoLicence" value="1">各種SNSへのフォローやいいね！のアクションは実際に反映されますのでご注意ください。挙動確認時はテストアカウントをご利用ください。
                </label></li>
            <!-- /.adminRuleCheck --></ul>
        <p class="btnSet">
            <span class="btn2"><a href="#closeModal" class="large1">キャンセル</a></span>
            <span class="btn1 demoDisableButton"><span class="large1">デモ公開</span></span>
            <span class="btn1 demoEnableButton" style="display: none"><a href="javascript:void(0)" class="large1" id="demoConfirmButton" data-cp="<?php assign($cp->id) ?>" data-url="<?php assign(Util::rewriteUrl('admin-cp', 'api_demo_cp.json'))?>">デモ公開</a></span>
        </p>
    </section>
    <!-- /.modal1 --></div>

<?php write_html($this->parseTemplate('CpDemoConfirmBoxTemplate.php')) ?>