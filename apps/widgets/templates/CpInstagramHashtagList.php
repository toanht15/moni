<?php $service_factory = new aafwServiceFactory();
$cp_flow_service = $service_factory->create('CpFlowService'); ?>

<div class="campaignPhotoSearch">
    <ul class="tablink1">
        <?php foreach($data['instagram_hashtag_actions'] as $instagram_hashtag_action): ?>
            <?php $min_step_no = $cp_flow_service->getMinOrderOfActionInGroup($instagram_hashtag_action->cp_action_group_id); ?>
            <li <?php if ($instagram_hashtag_action->id == $data['action_id']): ?>class="current"<?php endif; ?>>
                <a href="<?php write_html(Util::rewriteUrl('admin-cp', 'instagram_hashtags', array($instagram_hashtag_action->id))) ?>">Step <?php assign($min_step_no + $instagram_hashtag_action->order_no) ?>：<?php assign($instagram_hashtag_action->getCpActionData()->title) ?></a>
            </li>
        <?php endforeach; ?>
        <!-- /.tablink1 --></ul>
    <div class="itemsSortingDetail">
        <dl>
            <dt>検閲</dt><dd><?php write_html($this->formRadio('approval_flg', $data['cp_instagram_hashtag_action']->approval_flg, array('class' => 'jsInstagramHashtagApprovalFlg'), array(CpInstagramHashtagAction::APPROVAL_ON => 'あり', CpInstagramHashtagAction::APPROVAL_OFF => 'なし<small>(投稿と同時に承認となり写真がページに表示されます)</small>'), array(),'',false)); ?></dd>
        </dl>
        <p class="btnSet"><span class="btn3"><a href="javascript:void(0);" class="small1 jsInstagramHashtagApprovalFlg">適用</a></span></p>
        <!-- /.itemsSortingDetail --></div>
    <!-- /.campaignPhotoSearch --></div>
<div class="campaignPhotoSearch">
    <div class="itemsSortingDetail">
        <dl>
            <dt>絞り込み</dt><dd><?php write_html($this->formRadio('approval_status', $data['approval_status'] ? $data['approval_status'] : 1, array('class' => 'jsInstagramHashtagApprovalStatus'), array('1' => '全て', '2' => '未承認', '3' => '承認', '4' => '非承認'))); ?></dd>
            <dt>並び替え</dt>
            <dd>
                <?php write_html($this->formSelect('order_kind', $data['order_kind'] ? $data['order_kind'] : 1, array('class' => 'jsInstagramHashtagOrderKind'), array('1' => '投稿順', '2' => 'ユーザーID順'))); ?>&nbsp;&nbsp;&nbsp;
                <?php write_html($this->formRadio('order_type', $data['order_type'] ? $data['order_type'] : 1, array('class' => 'jsInstagramHashtagOrderType'), array('1' => '[A-Z↓] 昇順', '2' => '[Z-A↑] 降順'))); ?>
            </dd>
            <dt>ユーザネーム重複</dt>
            <dd><?php write_html($this->formRadio('duplicate_flg', $data['duplicate_flg'] ? $data['duplicate_flg'] : 1, array('class' => 'jsInstagramHashtagDuplicateFlg'), array('1' => '全て', '2' => 'なし', '3' => 'あり'))) ?></dd>
            <dt>登録投稿順序</dt>
            <dd><?php write_html($this->formRadio('reverse_post_time_flg', $data['reverse_post_time_flg'] ? $data['reverse_post_time_flg'] : 1, array('class' => 'jsInstagramHashtagReversePostTimeFlg'), array('1' => '全て', '2' => '登録後投稿', '3' => '投稿後登録'))) ?></dd>
        </dl>
        <p class="btnSet"><span class="btn2"><a href="javascript:void(0);" class="small1 jsInstagramHashtagSearchReset">リセット</a></span><span class="btn3"><a href="javascript:void(0);" class="small1 jsInstagramHashtagSearchBtn">適用</a></span></p>
    <!-- /.itemsSortingDetail --></div>

<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoCpDataListPager')->render(array(
    'TotalCount' => $data['total_instagram_hashtag_count'],
    'CurrentPage' => $data['page'],
    'Count' => $data['page_limited'],
))) ?>

<?php write_html($this->parseTemplate('BrandcoInstagramHashtagActionMenu.php',  array('menu_order' => '1'))) ?>

<div class="outputApi">
    <p class="labelModeAllied">
        <span class="confirmed">承認済<strong><?php assign($data['approved_instagram_hashtag_count']) ?></strong>件</span>
        <?php if ($data['api_url'] != ''): ?>
            <span class="btn2 jsExportAPIBtn"><span class="large2">外部出力APIのURL作成</span></span>
            <span class="url jsExportAPIUrl">URL：<?php assign($data['api_url']) ?></span>
        <?php else: ?>
            <span class="btn2 jsExportAPIBtn"><a href="javascript:void(0);" class="large2 jsExportAPI">外部出力APIのURL作成</a></span>
            <span class="url jsExportAPIUrl">URL：なし</span>
        <?php endif ?>
        <!-- /.labelModeAllied --></p>
    <!-- /.outputApi --></div>

<form method="POST" name="instagram_hashtag_action_form" action="<?php assign(Util::rewriteUrl('admin-cp', 'update_multi_instagram_hashtag_status')) ?>">
    <?php write_html($this->csrf_tag()); ?>
    <?php write_html($this->formHidden('multi_instagram_hashtag_approval_status', InstagramHashtagUserPost::APPROVAL_STATUS_APPROVE)) ?>
    <?php write_html($this->formHidden('action_id', $data['action_id'])); ?>

    <ul class="campaignPhoto">
        <?php if ($data['instagram_hashtag_user_posts'] && $data['instagram_hashtag_user_posts']->total() != 0): ?>
            <?php foreach($data['instagram_hashtag_user_posts'] as $instagram_hashtag_user_post): ?>
                <?php $user = $instagram_hashtag_user_post->getInstagramHashtagUser()->getCpUser()->getUser(); ?>
                <li>
                    <?php if ($instagram_hashtag_user_post->approval_status == InstagramHashtagUserPost::APPROVAL_STATUS_PRIVATE): ?>
                        <span class="labels">
                        <span style="margin-top: 20px;" class="<?php assign($instagram_hashtag_user_post->getApprovalStatusClass()) ?>"><?php assign($instagram_hashtag_user_post->getApprovalStatus()) ?></span>
                        <!-- /.labels --></span><p class="noModal">
                        <span class="thumb"><img src="<?php assign($this->setVersion('/img/campaign/imgDeletePhoto.png')); ?>" width="80" height="80" alt="<?php assign(json_decode($instagram_hashtag_user_post->detail_data)->caption->text); ?>"></span>
                        <span class="user"><img src="<?php assign($user->profile_image_url); ?>" width="20" height="20" alt="<?php assign($user->profile_image_url); ?>"><?php assign(!empty($data['is_hide_personal_info']) ? '' : $this->cutLongText($user->name, 15)); ?></span>
                        <span class="title"><?php assign($this->cutLongText(json_decode($instagram_hashtag_user_post->detail_data)->caption->text, 20)); ?></span>
                        <span class="post"><?php assign(date('Y/m/d', strtotime($instagram_hashtag_user_post->created_at))); ?></span></p>
                    <?php else: ?>
                        <span class="labels">
                        <input type="checkbox" class="jsInstagramHashtagCheck" name="instagram_hashtag_user_post_ids[]" value="<?php assign($instagram_hashtag_user_post->id) ?>">
                        <span class="<?php assign($instagram_hashtag_user_post->getApprovalStatusClass()) ?>"><?php assign($instagram_hashtag_user_post->getApprovalStatus()) ?></span>
                        <!-- /.labels --></span><a href="#instagram_hashtag_edit_modal" class="jsOpenInstagramHashtagModal" data-instagram_hashtag_user_post_id=<?php assign($instagram_hashtag_user_post->id) ?>>
                            <span class="thumb"><img src="<?php assign($instagram_hashtag_user_post->thumbnail); ?>" onerror="this.src='<?php assign($this->setVersion('/img/campaign/imgDeletePhoto.png')) ?>'" width="80" height="80" alt="<?php assign(json_decode($instagram_hashtag_user_post->detail_data)->caption->text); ?>"></span>
                            <span class="user"><img src="<?php assign($user->profile_image_url); ?>" width="20" height="20" alt="<?php assign($user->profile_image_url); ?>"><?php assign(!empty($data['is_hide_personal_info']) ? '' : $this->cutLongText($user->name, 15)); ?></span>
                            <span class="title"><?php assign($this->cutLongText(json_decode($instagram_hashtag_user_post->detail_data)->caption->text, 20)); ?></span>
                            <span class="post"><?php assign(date('Y/m/d', strtotime($instagram_hashtag_user_post->created_at))); ?></span></a>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        <?php endif; ?>
        <!-- /.campaignPhoto --></ul>
</form>

<?php write_html($this->parseTemplate('BrandcoInstagramHashtagActionMenu.php', array('menu_order' => '2'))) ?>

<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoCpDataListPager')->render(array(
    'TotalCount' => $data['total_instagram_hashtag_count'],
    'CurrentPage' => $data['page'],
    'Count' => $data['page_limited'],
))) ?>

