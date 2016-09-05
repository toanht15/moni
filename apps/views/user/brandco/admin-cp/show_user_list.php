<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoHeader')->render($data['pageStatus']))?>
<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoAccountHeader')->render($data['pageStatus'])) ?>

<article>

<?php if($data['segment_condition_session']): ?>
    <div class="segmentPresetInfo jsSegmentPresetInfo">
        <p>セグメント機能からメッセージ作成中です</p>
        <!-- /.segmentPresetInfo --></div>
<?php endif; ?>

<?php write_html($this->parseTemplate('ActionHeader.php',array(
    'cp_id' => $data['cp_id'],
    'action_id' => $data['action_id'],
    'user_list_page' => true,
    'pageStatus' => $data['pageStatus'],
    'enable_archive' => false,
    'isHideDemoFunction' => false
))); ?>

<?php write_html($this->parseTemplate('CpUserListHeader.php',array(
    'cp_id' => $data['cp_id'],
    'action_id' => $data['action_id'],
    'current_page'=>Cp::PAGE_USER_LIST,
    'reservation' => $data['reservation'],
    'is_group_fixed' => $data['is_group_fixed'],
    'sent_target' => $data['sent_target'],
    'brand' => $data['brand'],
    'is_include_type_announce' => $data['is_include_type_announce'],
    'fixed_target' => $data['fixed_target']
))); ?>

<?php write_html($this->formHidden('action_id', $data['action_id'])) ?>
<?php write_html($this->formHidden('cp_id', $data['cp_id'])) ?>
<?php write_html($this->formHidden('list_url', Util::rewriteUrl('admin-cp', 'api_get_search_fan.json'))) ?>
<?php write_html($this->formHidden('search_url', Util::rewriteUrl('admin-cp', 'api_search_fan.json'))) ?>
<?php write_html($this->formHidden('update_target_url', Util::rewriteUrl('admin-cp', 'update_fan_target.json'))) ?>
<?php write_html($this->formHidden('update_target_url_for_random', Util::rewriteUrl('admin-cp', 'update_fan_target_for_random.json'))) ?>
<?php write_html($this->formHidden('api_check_shipping_address_action_status_url', Util::rewriteUrl('admin-cp', 'api_check_shipping_address_action_status.json'))) ?>
<?php write_html($this->formHidden('api_check_answer_status_url', Util::rewriteUrl('admin-cp', 'api_check_answer_status.json'))) ?>
<?php write_html($this->formHidden('isManager', $data['isManager'])) ?>
<?php write_html($this->formHidden('update_rate', Util::rewriteUrl('admin-cp', 'api_fan_rate.json'))) ?>
<?php write_html($this->formHidden('join_user', $data['join_user'])) ?>
<?php write_html($this->formHidden('segment_condition_session', json_encode($data['segment_condition_session']))) ?>

</article>

<div class="modal1 jsModal" id="modal3">
    <section class="modalCont-small jsModalCont">
        <h1>確認</h1>
        <p><span class="attention1">当選者数を超えています。<br>本当に対象に入れますか？</span></p>
        <p class="btnSet"><span class="btn2"><a href="javascript:void(0)" class="middle1" data-close_modal_id="3">キャンセル</a></span><span class="btn3"><a href="javascript:void(0)" class="middle1" data-update_type="announce_insert">対象に入れる</a></span></p>
    </section>
</div>

<div class="modal1 jsModal" id="modal4">
    <section class="modalCont-small jsModalCont">
        <h1>確認</h1>
        <p><span class="attention1"></span><span class="attention1">人を本当に対象に入れますか？<br>処理には時間がかかることがあります。<br>また、処理開始時に再度絞り込みを行うため<br>人数が変動することもあります。</span></p>
        <p class="btnSet"><span class="btn2"><a href="javascript:void(0)" class="middle1" data-close_modal_id="4">キャンセル</a></span><span class="btn3"><a href="javascript:void(0)" class="middle1" data-update_type="insert_all_users">対象に入れる</a></span></p>
    </section>
</div>

<div class="modal1 jsModal" id="modal5">
    <section class="modalCont-small jsModalCont">
        <h1>確認</h1>
        <p><span class="attention1"></span><span class="attention1">チェック済のユーザを対象に入れないで画面遷移をすると、チェックが解除されます。続行しますか？</span></p>
        <p class="btnSet"><span class="btn2"><a href="javascript:void(0)" class="middle1" data-close_modal_id="5">キャンセル</a></span>
            <span class="btn3"><a href="javascript:void(0)" id="getFanConfirm" class="middle1" >続行する</a></span></p>
    </section>
</div>

<div class="modal1 jsModal" id="modal6">
    <section class="modalCont-small jsModalCont">
        <h1>確認</h1>
        <p><span class="attention1">送信対象に、直前のステップ「<span id='modal6_step_name'></span>」を完了していないユーザが<br /><span id='modal6_user_count'></span>名入っています。<br>本当に対象に入れますか？</span></p>
        <p class="btnSet"><span class="btn2"><a href="javascript:void(0)" class="middle1" data-close_modal_id="6">キャンセル</a></span><span class="btn3"><a href="javascript:void(0)" class="middle1" data-update_type="check_answer_status">対象に入れる</a></span></p>
    </section>
</div>

<div class="modal1 jsModal" id="modal7">
    <section class="modalCont-medium jsModalCont">
        <h1>抽選する</h1>
        <div class="modalUserRandomSelect">
          <p>当選者数<input id="random_text" type="text" value="" class="inputNum">名様<small>（候補：<strong id="random_target_count">0</strong>名）</small></p>
        <?php if ($data['is_include_type_shipping_address']): ?>
            <dl class="randomOption">
                <dt>抽選補助条件</dt>
                <dd><label><input id="duplicated_address_check" type="checkbox">住所重複を除外</label></dd>
            <!-- /.randomOption --></dl>
        <?php else: ?>
            <br />
        <?php endif; ?>
        <p class="supplement1">※抽選後、住所重複や退会などにより対象とならないユーザーが出た場合は設定人数に満たなくなります。再度の抽選をお願いします。</p>
        <!-- /.modalUserRandomSelect --></div>
        <p class="btnSet">
            <span class="btn2"><a href="#closeModal" data-close_modal_id="7">キャンセル</a></span>
            <span class="btn3"><a href="javascript:void(0)" class="middle1" data-update_type="random_select">この条件で抽選</a></span>
        </p>
    </section>
</div>

<div class="modal2 modal1 jsModal <?php assign($data['isSocialLikesEmpty'] ? 'jsShowModal' : '' );?>" id="socialLikeAlert">
    <section class="modalCont-small jsModalCont" id="jsModalCont">
        <p><span class="attention1">Facebookいいね！のデータにつきましては現在連携中です。</span></p>
        <p class="btnSet"><span class="btn3"><a href="#closeModal" class="middle1">OK</a></span></p>
    </section>
</div>

<div class="modal1 jsModal" id="modal8">
    <section class="modalCont-small jsModalCont">
        <h1>エラー</h1>
        <p><span class="attention1"></span><span class="attention1">未入力の配送先情報モジュールをもつユーザが含まれているため、対象に選択できません。</span></p>
        <p class="btnSet"><span class="btn2"><a href="javascript:void(0)" class="middle1" data-close_modal_id="8">閉じる</a></span></p>
    </section>
</div>

<div class="modal1 jsModal" id="modal9">
    <section class="modalCont-medium jsModalCont">
        <h1>当選者の確定前に下記項目をご確認ください</h1>
        <ul class="adminRuleCheck">
            <li>
                <label>
                    <input class="admin_rule_check" type="checkbox" name="admin_rule_check1">当選予定人数
                    <strong class="num" id="winner_num"></strong>名に対して、新たに
                    <strong class="num" id="fix_target_num"></strong>名の当選者を確定しますがよろしいですか？複数の当選グループを使用する場合は、合計人数にご注意ください。
                </label>
            </li>
            <li><label><input class="admin_rule_check" type="checkbox" name="admin_rule_check2">当選者確定後は対象者の変更ができませんがよろしいですか？</label></li>
        </ul>
        <p class="supplement1">※配送目的外のデータ抽出による個人情報漏洩を防止するため、当選者を一度確定した後は解除ができなくなっております。</p>
        <p class="btnSet">
            <span class="btn2"><a href="#closeModal">キャンセル</a></span>
            <span class="btn4" style="display: none" id="fixEnableButton"><a href="javascript:void(0)">確定する</a></span>
            <span class="btn4" style="display: none" id="fixDisableButton"><span>確定する</span></span>
        </p>
    </section>
</div>

<div class="modal1 jsModal" id="modal10">
    <section class="modalCont-small jsModalCont">
        <h1>当選者の解除</h1>
        <p class="attention1">当選者を解除する際にはクライアントと事前の了承を得てから行ってください。</p>
        <p class="btnSet">
            <span class="btn2"><a href="#closeModal" class="middle1">キャンセル</a></span>
            <span class="btn3"><a href="javascript:void(0)" class="middle1">解除する</a></span>
        </p>
    </section>
</div>

<div class="modal1 jsModal" id="modal11">
    <section class="modalCont-small jsModalCont">
        <h1>確認</h1>
        <p><span class="jsSPName"></span>の絞り込みを解除しますか？</p>
        <p class="btnSet">
            <span class="btn2"><a href="#closeModal" class="small1">キャンセル</a></span>
            <span class="btn4"><a href="javascript:void(0)" class="small1">削除する</a></span>
        </p>
        <!-- /.modalCont-small --></section>
<!-- /.modal1 --></div>

<link rel="stylesheet" href="<?php assign($this->setVersion('/css/jqueryUI.css'))?>">
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/i18n/jquery.ui.datepicker-ja.min.js"></script>

<script type="text/javascript" src="<?php assign($this->setVersion('/js/raty/jquery.raty.js'))?>"></script>
<?php write_html($this->parseTemplate('MessageDeliveryConfirmBox.php', array(
    'reservation' => $data['reservation'],
    'cp_id' => $data['cp_id'],
    'pageStatus' => $data['pageStatus'],
))) ?>
<?php write_html($this->parseTemplate('CpDownloadList.php', array(
    'brand_id' => $data['brand']->id,
    'cp_id' => $data['cp_id'],
    'pageStatus' => $data['pageStatus'],
))) ?>
<?php $script = array('admin-cp/FanRateService','admin-cp/ShowCpUserListService','admin-fan/ShowUserListService','admin-cp/CpMenuService','admin-segment/SegmentMessageActionService') ?>
<?php if ($data['is_include_type_instagram_hashtag']): ?>
    <?php $script[] = 'admin-cp/InstagramHashtagCampaignService' ?>
    <?php write_html($this->parseTemplate('BrandcoInstagramModal.php')) ?>
<?php endif; ?>
<?php if ($data['is_include_type_photo']): ?>
    <?php $script[] = 'admin-cp/PhotoCampaignService' ?>
    <div class="modal1 jsModal" id="photo_edit_modal">
        <section class="modalCont-large jsModalCont">

            <div class="modalCampaignPhoto jsPhotoEditModal">
                <!-- /.modalCampaignPhoto --></div>

            <p><a href="#closeModal" class="modalCloseBtn">キャンセル</a></p>
        </section>
        <!-- /.modal1 --></div>
<?php endif; ?>
<?php if ($data['is_include_type_tweet']): ?>
    <?php $script[] = 'admin-cp/TweetPhotoService' ?>
    <div class="modal1 jsModal" id="view_tweet_photo_modal">
        <section class="modalCont-medium jsModalCont">
            <div class="jsViewTweetPhotoModal"></div>
            <p><a href="#closeModal" class="modalCloseBtn">キャンセル</a></p>
        </section>
        <!-- /.modal1 --></div>
<?php endif; ?>
<?php $param = array_merge($data['pageStatus'], array('script' => $script)) ?>

<?php write_html($this->parseTemplate('BrandcoFooter.php', $param)); ?>
