<div class="makeStepTypeCont">
    <form id="deleteCpForm" name="deleteCpForm" action="" method="POST" data-delete-url="<?php assign(Util::rewriteUrl( 'admin-cp', 'save_draft_campaign_into_archive')); ?>"
          data-create-url="<?php assign(Util::rewriteUrl( 'admin-cp', 'save_setting_skeleton')); ?>">
        <?php write_html($this->formHidden('cp_id', 0)) ?>
        <?php write_html($this->formHidden('cps_type', Cp::TYPE_CAMPAIGN)) ?>
        <?php write_html($this->formHidden('skeleton_type', Cp::SKELETON_COPY)) ?>
    <section id="<?php if($data['type'] == Cp::SKELETON_DRAFT) assign('DraftCpContainer'); elseif($data['type'] == Cp::SKELETON_COPY) assign('CopyCpContainer')?>">

        <?php foreach ($data['cps'] as $cp):?>
            <?php
            $service_factory = new aafwServiceFactory();
            /** @var CpFlowService $cp_flow_service */
            $cp_flow_service = $service_factory->create('CpFlowService');
            $groups = $cp_flow_service->getCpActionGroupsByCpId($cp->id);
            $step1 = $groups->current();
            $step1_actions = $cp_flow_service->getCpActionsByCpActionGroupId($step1->id);
            ?>

            <section class="skeletonWrap <?php if($data['type'] == Cp::SKELETON_DRAFT) assign(' draftCp'); elseif($data['type'] == Cp::SKELETON_COPY) assign(' copyCp')?>" data-cp-id="<?php assign($cp->id)?>">
                <p class="skeltonDetail">
                    <?php if($data['type'] != Cp::SKELETON_COPY): ?>
                        <span class="name"><?php assign(Util::cutTextByWidth($cp->getTitle(), 611))?></span>
                        <span class="actionWrap">
                            <span class="btn4"><a href="javascript:void(0)" class="middle1 submitDeleteCpForm">削除</a></span>
                            <span class="btn3"><a href="<?php assign(Util::rewriteUrl('admin-cp', 'edit_setting_basic', array($cp->id))) ?>" class="middle1">選択</a></span>
                        </span>
                    <?php else: ?>
                        <span class="name"><?php assign(Util::cutTextByWidth($cp->getTitle(), 740))?></span>
                        <span class="actionWrap">
                            <span class="btn3"><a href="javascript:void(0)" class="middle1 copyCP">選択</a></span>
                        </span>
                    <?php endif; ?>
                </p>
                <div class="stepListWrap">
                    <ul class="stepList">
                        <li class="stepDetail_require">
                            <?php $last_order_no = 0;?>
                            <?php if($cp_flow_service->getCpActionsByCpActionGroupId($step1->id)->total() > 1): ?>
                                <h1><?php assign('STEP'.($cp_flow_service->getMinStepNo($step1->id)->order_no + $last_order_no).'-'.($cp_flow_service->getMaxStepNo($step1->id)->order_no + $last_order_no))?></h1>
                                <?php $last_order_no += $cp_flow_service->getMaxStepNo($step1->id)->order_no?>
                            <?php else: ?>
                                <h1><?php assign('STEP'.($cp_flow_service->getMinStepNo($step1->id)->order_no + $last_order_no))?></h1>
                                <?php $last_order_no += $cp_flow_service->getMinStepNo($step1->id)->order_no?>
                            <?php endif; ?>
                            <ul class="moduleList">
                                <?php foreach ($step1_actions as $action):?>
                                    <li class="moduleDetail1"><span <?php if ($action->status == CpAction::STATUS_FIX && $data['type'] != Cp::SKELETON_COPY) write_html('class="finished"') ?>><img class="moduleIcon" src="<?php assign($this->setVersion('/img/module/'.$action->getCpActionDetail()['icon']))?>" height="33" width="33" alt="<?php assign($action->getCpActionDetail()['title'])?>"></span></li>
                                <?php endforeach; ?>
                            <!-- /.moduleList --></ul>
                        <!-- /.stepDetail_require --></li>
                        <?php foreach ($groups as $group): ?>
                            <?php if ($group->id != $step1->id):?>
                                <li class="stepDetail">
                                    <?php $cp_action_array = $cp_flow_service->getCpActionsByCpActionGroupId($group->id); ?>
                                    <?php if($cp_action_array && $cp_action_array->total() > 1): ?>
                                        <h1><?php assign('STEP'.($cp_flow_service->getMinStepNo($group->id)->order_no + $last_order_no).'-'.($cp_flow_service->getMaxStepNo($group->id)->order_no + $last_order_no))?></h1>
                                        <?php $last_order_no += $cp_flow_service->getMaxStepNo($group->id)->order_no?>
                                    <?php else: ?>
                                        <h1><?php assign('STEP'.($cp_flow_service->getMinStepNo($group->id)->order_no + $last_order_no))?></h1>
                                        <?php $last_order_no += $cp_flow_service->getMinStepNo($group->id)->order_no?>
                                    <?php endif; ?>
                                    <?php $actions = $cp_flow_service->getCpActionsByCpActionGroupId($group->id)?>
                                    <ul class="moduleList">
                                        <?php foreach ($actions as $action): ?>
                                            <li class="moduleDetail1"><span <?php if ($action->status == CpAction::STATUS_FIX && $data['type'] != Cp::SKELETON_COPY) write_html('class="finished"') ?>><img class="moduleIcon" src="<?php assign($this->setVersion('/img/module/'.$action->getCpActionDetail()['icon']))?>" height="33" width="33" alt="<?php assign($action->getCpActionDetail()['title'])?>"></span></li>
                                        <?php endforeach; ?>
                                    <!-- /.moduleList --></ul>
                                <!-- /.stepDetail_require --></li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <!-- /.stepList --></ul>
                <!-- /.stepListWrap --></div>
            <!-- /.skeletonWrap --></section>
        <?php endforeach; ?>
    </section>
    </form>

    <?php if ($data['type'] == Cp::SKELETON_DRAFT): ?>
        <div class="pager1" id="draftPage" data-url="<?php assign(Util::rewriteUrl('admin-cp','api_get_draft_cps.json'))?>"
             data-totalpage = "<?php assign($data['totalPages']) ?>"
             data-pagelimit = "<?php assign($data['pageLimit']) ?>"
             data-totalitem = "<?php assign($data['cp_count']) ?>">
        </div>
     <?php elseif ($data['type'] == Cp::SKELETON_COPY): ?>
        <div class="pager1" id="copyCpPage" data-url="<?php assign(Util::rewriteUrl('admin-cp','api_get_copy_cps.json'))?>"
             data-totalpage = "<?php assign($data['totalPages']) ?>"
             data-pagelimit = "<?php assign($data['pageLimit']) ?>"
             data-totalitem = "<?php assign($data['cp_count']) ?>">
        </div>
    <?php endif; ?>

    <!-- /.makeStepTypeCont --></div>
