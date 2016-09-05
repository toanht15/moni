<div class="makeStepTypeCont1 jsUpdateSkeleton">
    <section class="skeletonWrap">
        <form id="updateSkeletonForm" name="updateSkeletonForm" target="_top" action="<?php assign(Util::rewriteUrl( 'admin-cp', 'save_setting_skeleton')); ?>" method="POST">
            <?php write_html($this->csrf_tag()); ?>
            <?php write_html($this->formHidden('cps_type', $data['cp']->type == Cp::TYPE_CAMPAIGN ? Cp::TYPE_CAMPAIGN : Cp::TYPE_MESSAGE)) ?>
            <?php write_html($this->formHidden('skeleton_type', Cp::SKELETON_ADD)) ?>
            <?php write_html($this->formHidden('cp_id', $data['cp']->id)) ?>
            <?php write_html($this->formHidden('groupCount', '', array("id" => "newSkeletonGroupCount"))) ?>
        </form>
        <?php $step_plus = 0; ?>
        <div class="stepListEdit">
            <div class="stepListWrap">
                <ul class="stepList newSkeletonTag">
                    <?php $firstStepInGroup=1; ?>
                    <?php foreach($data['groups'] as $group): ?>
                        <?php $announce_required_group = ($data['cp']->selection_method == CpCreator::ANNOUNCE_SELECTION && $group->order_no == 2) ||
                            (($data['cp']->selection_method == CpCreator::ANNOUNCE_FIRST || $data['cp']->selection_method == CpCreator::ANNOUNCE_LOTTERY) && $group->order_no == 1)?>
                        <?php $not_editable = in_array($group->id, $data['not_editable_groups']); ?>
                        <?php
                            $actions = $data['cp_flow_service']->getCpActionsByCpActionGroupId($group->id);
                            if(Util::isNullOrEmpty($actions)) {
                                continue;
                            }
                        ?>
                        <li class="stepDetail_require newSkeletonGroup" data-group-id="<?php assign($group->id)?>">
                            <h1>STEP<?php $actions->total() == 1 ? assign($firstStepInGroup) : assign($firstStepInGroup . "-" . ($firstStepInGroup + $actions->total() - 1)) ?></h1>
                            <?php if($not_editable): ?>
                                <ul class="moduleList" data-disable-actions="all" data-required-announce="<?php assign($announce_required_group)?>">
                            <?php else: ?>
                                <?php if (!$data['can_set_coupon_for_non_incentive_cp']): ?>
                                    <?php $disable_actions = $data['cp']->selection_method != CpCreator::ANNOUNCE_FIRST ? CpAction::TYPE_COUPON.','.CpAction::TYPE_GIFT.','.CpAction::TYPE_ANNOUNCE_DELIVERY.' ' : CpAction::TYPE_ANNOUNCE_DELIVERY.', ' ?>
                                <?php else: ?>
                                    <?php $disable_actions = $data['cp']->selection_method != CpCreator::ANNOUNCE_FIRST ? CpAction::TYPE_GIFT.','.CpAction::TYPE_ANNOUNCE_DELIVERY.' ' : CpAction::TYPE_ANNOUNCE_DELIVERY.', ' ?>
                                <?php endif; ?>
                                <ul class="moduleList" <?php assign($group->order_no == 1 ? 'data-disable-actions='.$disable_actions : '')?>  data-required-announce="<?php assign($announce_required_group)?>"
                                    <?php assign($data['cp']->selection_method == CpCreator::ANNOUNCE_LOTTERY ? 'data-disable-before=instant_win ' : ' ') ?>>
                            <?php endif; ?>
                                <?php foreach($actions as $action): ?>
                                    <?php $action_detail = $action->getCpActionDetail();?>
                                    <?php $detail_data = $action->getCpActionData();?>
                                    <?php $not_shift_action = $not_editable || !$data['cp_flow_service']->canShiftAction($data['cp'], $group, $action); ?>
                                    <?php $sortable_action = $data['cp_flow_service']->canSortAction($data['cp'], $group, $action);?>
                                    <?php $instant_win = $action->type == CpAction::TYPE_INSTANT_WIN ? 'instant_win' : ''?>
                                    <?php if($not_shift_action): ?>
                                        <li class="moduleDetail1 jsLockSortable jsLockShift <?php assign($instant_win)?>"
                                    <?php elseif(!$sortable_action): ?>
                                        <li class="moduleDetail1 jsLockSortable <?php assign($instant_win)?>"
                                    <?php else: ?>
                                        <li class="moduleDetail1 <?php assign($instant_win)?>"
                                    <?php endif; ?>
                                        data-action-type="<?php assign($action->type) ?>" data-action-id="<?php assign($action->id) ?>"
                                        data-type-name="<?php assign($action_detail['title']) ?>" data-created="<?php assign($detail_data->created_at) ?>" data-title-text="<?php assign($detail_data->title) ?>"
                                        data-opening_flg="<?php assign(($data['cp']->type == Cp::TYPE_CAMPAIGN && $group->order_no == 1 && $action->order_no == 1) ? 1 : 0); ?>">
                                        <?php //キャンペーンで一番最初に配置されているもの、発送をもって発表、編集不可のもの以外はaddModuleLがでる ?>
                                        <?php if(($data['cp']->type == Cp::TYPE_CAMPAIGN && ($group->order_no != 1 || $action->order_no != 1) && $action->type != CpAction::TYPE_ANNOUNCE_DELIVERY && !$not_editable) ||
                                            ($data['cp']->type == Cp::TYPE_MESSAGE && !$not_editable)):?>
                                            <span class="addModuleL" style="display: none">追加する</span>
                                        <?php endif; ?>
                                        <span class="moduleIcon <?php assign(($not_editable || $not_shift_action || !$sortable_action) ? 'lock' : '')?>">
                                            <img src="<?php assign($this->setVersion('/img/module/'.$action_detail['icon']))?>" height="33" width="33" alt="<?php assign($action_detail['title']);?>">
                                            <span class="textBalloon1">
                                                <span><?php assign($action_detail['title']) ?><br><?php assign(Util::cutTextByWidth($detail_data->title, 150)) ?></span>
                                            </span>
                                        </span>
                                        <?php if($action->type !== CpAction::TYPE_ANNOUNCE_DELIVERY && !$not_editable): ?>
                                            <span class="addModuleR" style="display: none">追加する</span>
                                        <?php endif; ?>
                                    </li>
                                    <?php $firstStepInGroup++;?>
                                <?php endforeach; ?>
                            <!-- /.moduleList --></ul>
                        <!-- /.stepDetail_require --></li>
                    <?php endforeach; ?>
                    <?php if($data['cp']->selection_method != CpCreator::ANNOUNCE_NON_INCENTIVE): ?>
                        <li class="stepDetail_require">
                            <h1>STEP N</h1>
                            <ul class="moduleList">
                                <li class="addModuleDetail1"><span>追加する</span></li>
                            <!-- /.moduleList --></ul>
                        <!-- /.stepDetail_require --></li>
                    <?php else: ?>
                        <li class="jsDummyDetail1" style="display:none;"></li>
                    <?php endif; ?>
                <!-- /.stepList --></ul>
            <!-- /.stepListWrap --></div>
            <div class="deleteModule">
                <p class="">削除する</p>
            <!-- /.deleteModule --></div>
            <p class="supplement1">アイコンをドラッグ&ドロップすることでフローをカスタマイズできます。</p>
        <!-- /.stepListEdit --></div>
    <!-- /.stepListWrap --></section>

    <ul class="selectModuleList">
        <?php foreach($data['CpActionDetail'] as $key=>$value): ?>
            <?php if(!in_array($key, $data['invisible_types'])): ?>
                <li class="moduleDetail2" data-action-type="<?php assign($key) ?>">
                    <span class="hdModuleIcon">
                        <img src="<?php assign($this->setVersion('/img/module/'.$value['icon']))?>" width="55" height="55" alt="<?php assign($value['title']) ?>">
                    </span>
                    <span class="moduleName"><?php assign($value['title']) ?></span>
                </li>
            <?php endif; ?>
        <?php endforeach; ?>
    </ul>

<!-- /.makeStepTypeCont --></div>
<div class="modal2 jsModal" id="modal_confirm_action_delete">
    <section class="modalCont-small jsModalCont">
        <h1>確認</h1>
        <p><strong class="attention1">以下のアクションを削除してもよろしいですか？</strong><br></p>
        <ul>
            <li><span>種別：</span><span class="jsActionType"></span></li>
            <li><span>作成日時：</span><span class="jsActionCreate"></span></li>
            <li><span>タイトル：</span><span class="jsActionTitle"></span></li>
        </ul>
        <p class="btnSet">
            <span class="btn2">
                <a href="#closeModal" class="middle1">キャンセル</a>
            </span>
            <span class="btn4">
                <a class="middle1" href="javascript:void(0)" id="executeActionDelete">削除</a>
            </span>
        </p>
    </section>
</div>
<div class="modal2 jsModal" id="modal_confirm_cancel">
    <section class="modalCont-small jsModalCont">
        <h1>確認</h1>
        <p><strong class="attention1">変更内容が保存されていませんがよろしいですか？</strong><br></p>
        <p class="btnSet">
            <span class="btn2">
                <a href="#closeModal" class="middle1">キャンセル</a>
            </span>
            <span class="btn4">
                <a class="middle1" href="javascript:void(0)" id="executeCancel">OK</a>
            </span>
        </p>
    </section>
</div>
