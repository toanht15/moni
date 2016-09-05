<section class="noticeBar1 jsNoticeBarArea1">
    <p class="error1 jsNoticeBarClose" id="jsError1"><a href="#" class="btnDelete jsNoticeBarClose1">閉じる</a><span class="noticeBarWrap"> 公開するために全ての利用規約に同意しなければなりません。</span></p>
</section>

<?php write_html(aafwWidgets::getInstance()->loadWidget('CpActionTitle')->render(array('cp' => $data['cp'])));?>

<?php
$service_factory = new aafwServiceFactory();
/** @var CpFlowService $cp_flow_service */
$cp_flow_service = $service_factory->create('CpFlowService');
$manager_service = $service_factory->create('ManagerService');
?>
<?php if($data['cp']->status == Cp::STATUS_DRAFT): ?>
<section class="skeletonSet">
    <div class="stepListWrap">
        <ul class="stepList">
            <li class="stepDetail_base">
                <h1>基本情報<span class="attention1">※</span></h1>
                <ul class="moduleList">
                    <li class="moduleDetail1">
                        <?php if($data['setting_id'] == Cp::CP_SETTING_BASIC):?>
                            <span class="current <?php assign($data['cp']->fix_basic_flg == CpAction::STATUS_FIX ? 'finished' : '') ?>">
                                <img src="<?php assign($this->setVersion('/img/module/setting1.png'))?>" height="25" width="25" alt="基本設定"><span class="textBalloon1"><span>基本設定</span></span>
                            </span>
                        <?php else: ?>
                            <a class="moduleIcon <?php assign($data['cp']->fix_basic_flg == CpAction::STATUS_FIX ? 'finished' : '') ?>" href="<?php assign(Util::rewriteUrl('admin-cp', 'edit_setting_basic', array($data['cp_id']), null))?>">
                                <img src="<?php assign($this->setVersion('/img/module/setting1.png')) ?>" height="25" width="25" alt="基本設定"><span class="textBalloon1"><span>基本設定</span></span>
                            </a>
                        <?php endif; ?>
                    </li>
                    <?php if($data['cp']->join_limit_flg == CP::JOIN_LIMIT_OFF):?>
                        <li class="moduleDetail1 jsAttractModule">
                            <?php if($data['setting_id'] == Cp::CP_SETTING_ATTRACT): ?>
                                <span class="current <?php assign($data['cp']->fix_attract_flg == CpAction::STATUS_FIX ? 'finished' : '') ?>">
                                    <img src="<?php assign($this->setVersion('/img/module/attract1.png'))?>" height="25" width="25" alt="集客設定"><span class="textBalloon1"><span>集客設定</span></span>
                                </span>
                            <?php else: ?>
                                <a class="moduleIcon <?php assign($data['cp']->fix_attract_flg == CpAction::STATUS_FIX ? 'finished' : '') ?>" href="<?php assign(Util::rewriteUrl('admin-cp', 'edit_setting_attract', array($data['cp_id']), null))?>">
                                    <img src="<?php assign($this->setVersion('/img/module/attract1.png')) ?>" height="25" width="25" alt="集客設定"><span class="textBalloon1"><span>集客設定</span></span>
                                </a>
                            <?php endif; ?>
                        </li>
                    <?php endif;?>
                <!-- /.moduleList --></ul>
            <!-- /.stepDetail_base --></li>
<?php foreach($data['groups'] as $group): ?>
    <?php $actions = $cp_flow_service->getCpActionsByCpActionGroupId($group->id);?>
    <?php if($group->order_no == 1): ?>
        <li class="stepDetail_require">
            <?php if($cp_flow_service->getCpActionsByCpActionGroupId($group->id)->total() > 1): ?>
                <h1><?php assign('STEP'.($cp_flow_service->getMinStepNo($group->id)->order_no + $last_order_no).'-'.($cp_flow_service->getMaxStepNo($group->id)->order_no + $last_order_no))?><span class="attention1">※</span></h1>
                <?php $last_order_no += $cp_flow_service->getMaxStepNo($group->id)->order_no?>
            <?php else: ?>
                <h1><?php assign('STEP'.($cp_flow_service->getMinStepNo($group->id)->order_no + $last_order_no))?><span class="attention1">※</span></h1>
                <?php $last_order_no += $cp_flow_service->getMinStepNo($group->id)->order_no?>
            <?php endif; ?>
    <?php else: ?>
        <li class="stepDetail">
            <?php if($cp_flow_service->getCpActionsByCpActionGroupId($group->id)->total() > 1): ?>
                <h1><?php assign('STEP'.($cp_flow_service->getMinStepNo($group->id)->order_no + $last_order_no).'-'.($cp_flow_service->getMaxStepNo($group->id)->order_no + $last_order_no))?></h1>
                <?php $last_order_no += $cp_flow_service->getMaxStepNo($group->id)->order_no?>
            <?php else: ?>
                <h1><?php assign('STEP'.($cp_flow_service->getMinStepNo($group->id)->order_no + $last_order_no))?></h1>
                <?php $last_order_no += $cp_flow_service->getMinStepNo($group->id)->order_no?>
            <?php endif; ?>
    <?php endif; ?>
            <ul class="moduleList">
                <?php foreach($actions as $action):?>
                    <?php if($action->id == $data['current_id']) { $current_action = $action; } ?>
                    <?php write_html($this->parseTemplate('CpTopActionMenu.php', array(
                        'action' => $action,
                        'current_id' => $data['current_id'],
                    ))) ?>
                <?php endforeach; ?>
            <!-- /.moduleList --></ul>

    <?php if($group->order_no == 1): ?>
        <!-- /.stepDetail_require --></li>
    <?php else: ?>
        <!-- /.stepDetail --></li>
    <?php endif; ?>
<?php endforeach; ?>
        <!-- /.stepList --></ul>
        <?php if($data['cp']->status != Cp::STATUS_CLOSE): ?>
            <p class="campaignSettingBase"><a href="#SkeletonModal" class="font-edit jsOpenModal">フロー編集</a></p>
        <?php endif; ?>
    <!-- /.stepListWrap --></div>
<?php if($data['isReady']): ?>
    <ul class="openButtons">
        <li class="demoOpen">
            <span class="btn1"><a href="#modal_demo_confirm" class="round1 jsOpenModal">デモ公開</a></span>
            <!-- /.demoOpen --></li
            ><li class="flowOpen">
    <?php if(!$manager_service->isAgentLogin()): ?>
        <?php if( $data['cp']->join_limit_flg == cp::JOIN_LIMIT_OFF ):?>
                <span class="btn3"><a href="#modal1" class="round2 jsOpenModal" id="scheduleButton">公開</a></span>
            <?PHP elseif( $data['cp']->join_limit_flg == cp::JOIN_LIMIT_ON ):?>
                <span class="btn3"><a href="javascript:void(0)" id="scheduleLimitedCp" data-cp="<?php assign($data['cp']->id) ?>" data-cp_action="<?php assign($data['first_action']->id) ?>" data-url="<?php assign(Util::rewriteUrl('admin-cp', 'api_schedule_cp.json'))?>" class="round2">次へ</a></span>
            <?php endif;?>
    <?php else: ?>
        <span class="btn3"><span class="round2 jsOpenModal">公開</span></span>
    <?php endif ?>
        </li>
        <!-- /.openButtons --></ul>
<?php else: ?>

    <ul class="openButtons">
        <li class="demoOpen">
            <span class="btn1"><span class="round1">デモ公開</span></span>
            <!-- /.demoOpen --></li
            ><li class="flowOpen">
            <span class="btn3"><span class="round2 jsOpenModal">非公開</span></span>
            <!-- /.flowOpen --></li>
        <small class="attention1">「※」が付いている箇所を確定させてください</small>
        <!-- /.openButtons --></ul>
<?php endif; ?>
<!-- /.stepListWrap --></section>
<?php endif; ?>

<?php if (!$data['setting_id']): ?>
    <?php $cp_action_detail = $current_action !== null ? $current_action->getCpActionDetail() : array(); ?>
    <h1 class="hd1"><img src="<?php assign($this->setVersion('/img/module/'. $cp_action_detail['icon']))?>" width="25" height="25" alt="<?php assign($cp_action_detail['title'])?>" class="moduleIcon">
        <?php assign($cp_action_detail['title'])?>
    </h1>
<?php endif; ?>
