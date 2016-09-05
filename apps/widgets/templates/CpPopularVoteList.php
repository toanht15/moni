<?php $service_factory = new aafwServiceFactory();
$cp_flow_service = $service_factory->create('CpFlowService');
?>
<div class="campaignPhotoSearch">
    <ul class="tablink1">
        <?php foreach($data['popular_vote_actions'] as $popular_vote_action): ?>
            <?php $min_step_no = $cp_flow_service->getMinOrderOfActionInGroup($popular_vote_action->cp_action_group_id); ?>
            <?php if ($popular_vote_action->id == $data['action_id']): ?>
                <li class="current"><span>STEP <?php assign($min_step_no + $popular_vote_action->order_no) ?>：<?php assign($popular_vote_action->getCpActionData()->title) ?></span></li>
            <?php else: ?>
                <li><a href="<?php write_html(Util::rewriteUrl('admin-cp', 'popular_votes', array($popular_vote_action->id))) ?>">STEP <?php assign($min_step_no + $popular_vote_action->order_no) ?>：<?php assign($popular_vote_action->getCpActionData()->title) ?></a></li>
            <?php endif ?>
        <?php endforeach; ?>
        <!-- /.tablink1 --></ul>
    <!-- /.campaignPhotoSearch --></div>

<div class="outputApi">
    <p class="labelModeAllied">
        <?php if ($data['api_url'] != ''): ?>
            <span class="btn2 jsExportAPIBtn"><span class="large2">外部出力APIのURL作成</span></span>
            <span class="url jsExportAPIUrl">URL：<?php assign($data['api_url']) ?></span>
        <?php else: ?>
            <span class="btn2 jsExportAPIBtn"><a href="javascript:void(0);" class="large2 jsExportAPI">外部出力APIのURL作成</a></span>
            <span class="url jsExportAPIUrl">URL：なし</span>
        <?php endif ?>
        <!-- /.labelModeAllied --></p>
    <!-- /.outputApi --></div>

