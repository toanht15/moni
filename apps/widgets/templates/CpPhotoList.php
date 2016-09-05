<?php $service_factory = new aafwServiceFactory();
$cp_flow_service = $service_factory->create('CpFlowService');
$manager_service = $service_factory->create('ManagerService'); ?>

<div class="campaignPhotoSearch">
    <ul class="tablink1">
        <?php foreach($data['photo_actions'] as $photo_action): ?>
            <?php $min_step_no = $cp_flow_service->getMinOrderOfActionInGroup($photo_action->cp_action_group_id); ?>
            <li <?php if ($photo_action->id == $data['action_id']): ?>class="current"<?php endif; ?>>
                <a href="<?php write_html(Util::rewriteUrl('admin-cp', 'photo_campaign', array($photo_action->id))) ?>">Step <?php assign($min_step_no + $photo_action->order_no) ?>：<?php assign($photo_action->getCpActionData()->title) ?></a>
            </li>
        <?php endforeach; ?>
        <!-- /.tablink1 --></ul>
    <?php if(!$manager_service->isAgentLogin()): ?>
    <div class="itemsSortingDetail">
        <dl>
            <dt>検閲</dt><dd><?php write_html($this->formRadio('panel_hidden_flg', $data['cur_photo_action']->panel_hidden_flg, array('class' => 'jsPhotoPanelHiddenFlg'), array(PhotoStreamService::PANEL_TYPE_HIDDEN => 'する', PhotoStreamService::PANEL_TYPE_AVAILABLE => 'しない<small>(投稿と同時に承認となり写真がページに表示されます)</small>'), array(),'',false)); ?></dd>
            <dt>投稿一覧URL</dt><dd><a target="_blank" href="<?php assign(Util::rewriteUrl('photo', 'cp_actions', array($data['action_id']))); ?> "><?php assign(Util::rewriteUrl('photo', 'cp_actions', array($data['action_id']))); ?></a></dd>
        </dl>
        <p class="btnSet"><span class="btn3"><a href="javascript:void(0);" class="small1 jsPhotoPanelHiddenConfirm">適用</a></span></p>
        <!-- /.itemsSortingDetail --></div>
    <!-- /.campaignPhotoSearch --></div>
    <?php endif ?>
<div class="campaignPhotoSearch">
    <div class="itemsSortingDetail">
        <dl>
            <dt>絞り込み</dt><dd><?php write_html($this->formRadio('approval_status', $data['approval_status'] ? $data['approval_status'] : 1, array('class' => 'jsPhotoApprovalStatus'), array('1' => '全て', '2' => '未承認', '3' => '承認', '4' => '非承認'))); ?></dd><dt>並び替え</dt><dd>
                <?php write_html($this->formSelect('order_kind', $data['order_kind'] ? $data['order_kind'] : 1, array('class' => 'jsPhotoOrderKind'), array('1' => '投稿順', '2' => 'ユーザーID順'))); ?>&nbsp;&nbsp;&nbsp;
                <?php write_html($this->formRadio('order_type', $data['order_type'] ? $data['order_type'] : 1, array('class' => 'jsPhotoOrderType'), array('1' => '[A-Z↓] 昇順', '2' => '[Z-A↑] 降順'))); ?></dd>
        </dl>
        <p class="btnSet"><span class="btn2"><a href="javascript:void(0);" class="small1 jsPhotoSearchReset">リセット</a></span><span class="btn3"><a href="javascript:void(0);" class="small1 jsPhotoSearchBtn">適用</a></span></p>
    <!-- /.itemsSortingDetail --></div>
<!-- /.campaignPhotoSearch --></div>

<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoCpDataListPager')->render(array(
    'TotalCount' => $data['total_photo_count'],
    'CurrentPage' => $data['page'],
    'Count' => $data['page_limited'],
))) ?>

<?php write_html($this->parseTemplate('BrandcoPhotoActionMenu.php',  array('menu_order' => '1'))) ?>

<div class="outputApi">
    <p class="labelModeAllied">
        <span class="confirmed">承認済<strong><?php assign($data['approved_photo_count']) ?></strong>件</span>
        <?php if ($data['api_url'] != ''): ?>
            <span class="btn2 jsExportAPIBtn"><span class="large2">外部出力APIのURL作成</span></span>
            <span class="url jsExportAPIUrl">URL：<?php assign($data['api_url']) ?></span>
        <?php else: ?>
            <span class="btn2 jsExportAPIBtn"><a href="javascript:void(0);" class="large2 jsExportAPI">外部出力APIのURL作成</a></span>
            <span class="url jsExportAPIUrl">URL：なし</span>
        <?php endif ?>
        <!-- /.labelModeAllied --></p>
    <!-- /.outputApi --></div>

<form method="POST" name="photo_action_form" action="<?php assign(Util::rewriteUrl('admin-cp', 'update_multi_photo_status')) ?>">
    <?php write_html($this->csrf_tag()); ?>
    <?php write_html($this->formHidden('multi_photo_approval_status', PhotoUser::APPROVAL_STATUS_APPROVE)) ?>
    <?php write_html($this->formHidden('multi_photo_top_status', PhotoEntry::TOP_STATUS_HIDDEN)) ?>
    <?php write_html($this->formHidden('action_id', $data['action_id'])); ?>

    <ul class="campaignPhoto">
        <?php if ($data['photo_posts']['data']): ?>
            <?php foreach($data['photo_posts']['data'] as $photo_data): ?>
                <li>
                    <span class="labels">
                        <input type="checkbox" class="jsPhotoCheck" name="photo_user_ids[]" value="<?php assign($photo_data['photo_post']->id) ?>">
                        <span class="<?php assign($photo_data['approval_status_class']) ?>"><?php assign($photo_data['approval_status']) ?></span>
                        <?php if ($photo_data['photo_post']->hidden_flg == 0): ?>
                            <span class="label3">TOP</span>
                        <?php endif; ?>
                        <!-- /.labels --></span><a href="#photo_edit_modal" class="jsOpenPhotoModal" data-photo_user_id=<?php assign($photo_data['photo_post']->id) ?>>
                        <span class="thumb"><img src="<?php assign($photo_data['photo_url']); ?>" width="80" height="80" alt="<?php assign($photo_data['photo_post']->photo_title); ?>" onerror="this.src='<?php assign($photo_data['photo_post']->photo_url); ?>';"></span>
                        <span class="title"><?php assign($this->cutLongText($photo_data['photo_post']->photo_title, 20)); ?></span>
                        <span class="post"><?php assign(date('Y/m/d', strtotime($photo_data['photo_post']->created_at))); ?></span>
                        <span class="userid">(No.<?php assign($this->cutLongText($photo_data['photo_post']->no, 20)); ?>)</span>
                    </a>
                </li>
            <?php endforeach; ?>
        <?php endif; ?>
        <!-- /.campaignPhoto --></ul>
</form>

<?php write_html($this->parseTemplate('BrandcoPhotoActionMenu.php', array('menu_order' => '2'))) ?>

<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoCpDataListPager')->render(array(
    'TotalCount' => $data['total_photo_count'],
    'CurrentPage' => $data['page'],
    'Count' => $data['page_limited'],
))) ?>


<div class="campaignPhotoSearch">
    <div class="itemsSortingDetail">
        <dl>
            <dt>
                表示件数
                <?php write_html($this->formSelect('limit', $data['page_limited'] ? $data['page_limited'] : 1, array('class' => 'jsPhotoLimit'), array('18' => '18', '30' => '30', '60' => '60', '200' => '200'))); ?> 件
            </dt>
        </dl>
        <p class="btnSet"><span class="btn3"><a href="javascript:void(0);" class="small1 jsPhotoSearchBtn">反映</a></span></p>
        <!-- /.itemsSortingDetail --></div>
    <!-- /.campaignPhotoSearch --></div>
