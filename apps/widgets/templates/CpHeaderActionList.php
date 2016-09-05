<section class="campaignFlowWrap">
    <div class="campaignFlowArea">
        <div class="campaignFlow">
            <ul class="campaignFlowCont">
                <?php $last_order_no = 0; ?>
                <?php foreach($data['group_array'] as $group_key => $group): ?>
                    <?php $group_order_no = $group['group_order_no']; unset($group['group_order_no']);?>
                    <?php $delivered_rsv_logs = $data['cp_list_service']->getDeliveredLogs($group);?>
                    <?php if($group_order_no == 1 || $delivered_rsv_logs): ?>
                        <li class="flowDetail_finished <?php assign(($data['action'] && $data['action']->cp_action_group_id == $group_key) ? 'flowDetail_focus' : '');?>">
                    <?php else: ?>
                        <li class="flowDetail <?php assign(($data['action'] && $data['action']->cp_action_group_id == $group_key) ? 'flowDetail_focus' : '');?>">
                    <?php endif; ?>

                    <?php list($minStepNo, $maxStepNo) = $data['cp_list_service']->getStepNo($group);?>
                    <?php if(count($group) > 1): ?>
                        <h1><?php assign('STEP '.($minStepNo + $last_order_no).'-'.($maxStepNo + $last_order_no))?>
                            <span class="iconHelp">
                                <span class="text"></span>
                                <span class="textBalloon1">
                                    <span>
                                        <?php if($data['exist_announce_actions'][$group_key]): ?>
                                            別パターンの当選発表をする場合は<br>新たに当選通知を追加してください
                                        <?php else: ?>
                                            STEPグループごとに編集画面が開きます
                                        <?php endif; ?>
                                    </span>
                                <!-- /.textBalloon1 --></span>
                            <!-- /.iconHelp --></span>
                        </h1>
                        <?php $last_order_no += $maxStepNo?>
                    <?php else: ?>
                        <h1><?php assign('STEP '.($minStepNo + $last_order_no))?>
                            <?php if($data['exist_announce_actions'][$group_key]): ?>
                                <span class="iconHelp">
                                    <span class="text"></span>
                                    <span class="textBalloon1">
                                        <span>
                                            別パターンの当選発表をする場合は<br>新たに当選通知を追加してください
                                        </span>
                                    <!-- /.textBalloon1 --></span>
                                <!-- /.iconHelp --></span>
                            <?php endif; ?>
                        </h1>
                        <?php $last_order_no += $minStepNo?>
                    <?php endif; ?>

                    <?php if ($group_order_no == $data['group']->order_no): ?>
                        <div class="moduleWrap">
                    <?php else: ?>
                        <?php reset($group); ?>
                        <a href="<?php write_html(Util::rewriteUrl('admin-cp', 'edit_action', array($params['cp']->id, key($group)))) ?>" class="moduleWrap">
                    <?php endif; ?>
                    <?php foreach($group as $action_key => $action): ?>
                        <?php list($cp_action_detail, $cp_action_data) = $data['cp_list_service']->getActionData($params['cp'], $action_key, $action, $group_order_no); ?>

                        <dl class="campaignModule1">
                            <dt><img src="<?php assign($this->setVersion('/img/module/'.$cp_action_detail['icon'])) ?>" width="16" height="16" alt="<?php assign($cp_action_detail['title']) ?>" class="hdModuleIcon">
                                <?php assign(Util::cutTextByWidth($cp_action_data->title, 130)) ?></dt>
                            <?php if ($action['type'] == CpAction::TYPE_ANNOUNCE_DELIVERY): ?>
                                <?php $cp_message_delivery_service = $this->getService('CpMessageDeliveryService'); ?>
                                <dd></dd>
                                <dd></dd>
                                <dd><span class="shipping2"><strong class="num" id="finishCount<?php assign($action_key)?>"><img id="loading<?php assign($action_key)?>" src="<?php assign($this->setVersion('/img/base/amimeLoading.gif'))?>" alt="loading" class="loadingImg"></strong>人</span></dd>
                            <?php else: ?>
                                <dd>
                                    <?php if($action['action_order_no'] == 1):?>
                                        <span class="iconMail1"><strong class="num" id="sendCount<?php assign($action_key)?>"><img id="loading<?php assign($action_key)?>" src="<?php assign($this->setVersion('/img/base/amimeLoading.gif'))?>" alt="loading" class="loadingImg"></strong>人</span>
                                    <?php endif;?>
                                </dd>
                                <dd><span class="iconMail2"><strong class="num" id="readCount<?php assign($action_key)?>"><img id="loading<?php assign($action_key)?>" src="<?php assign($this->setVersion('/img/base/amimeLoading.gif'))?>" alt="loading" class="loadingImg"></strong>人</span></dd>
                                <dd><span class="iconCheck3"><strong class="num" id="finishCount<?php assign($action_key)?>"><img id="loading<?php assign($action_key)?>" src="<?php assign($this->setVersion('/img/base/amimeLoading.gif'))?>" alt="loading" class="loadingImg"></strong>人</span></dd>
                            <?php endif; ?>
                        <!-- /.campaignModule1 --></dl>
                    <?php endforeach; ?>
                    <?php if ($group_order_no == $data['group']->order_no): ?>
                        </div>
                    <?php else: ?>
                        </a>
                    <?php endif; ?>
                    <?php if($delivered_rsv_logs && count($delivered_rsv_logs) > 0): ?>
                        <div class="changeHistory">
                            <p class="iconHistory">変更履歴</p>
                            <p class="textBalloon1">
                                <span>
                                    <?php foreach ($delivered_rsv_logs as $log): ?>
                                        <?php if ($log['count'] > 0): ?>
                                            [<?php assign(date('Y/m/d H:i', strtotime($log['date']))) ?>] <?php assign(number_format($log['count'])) ?>名に配信済<br>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </span>
                            <!-- /.textBalloon1 --></p>
                        <!-- /.changeHistory --></div>
                    <?php endif; ?>
                    <!-- /.campaignFlowDetail --></li>
                <?php endforeach; ?>

                <?php $last_order_no++;?>
            <!-- /.campaignFlowCont --></ul>
        <!-- /.campaignFlow --></div>

        <ul class="campaignFlowScroll">
            <li class="flowPrev">←</li>
            <li class="flowNext">→</li>
        </ul>
    <!-- /.campaignFlowArea --></div>
    <?php if($data['user_list_page']):?>
        <p class="campaignSettingBase">
            <?php if($data['cp']->type == Cp::TYPE_CAMPAIGN): ?>
                <a href="<?php assign(Util::rewriteUrl('admin-cp', 'edit_setting_basic', array($data['cp']->id))) ?>" class="font-gear">基本設定</a>
                <?php if ($data['cp']->join_limit_flg == cp::JOIN_LIMIT_OFF): ?>
                    <a href="<?php assign(Util::rewriteUrl('admin-cp', 'edit_setting_attract', array($data['cp']->id))) ?>" class="font-attract">集客設定</a>
                <?php endif; ?>
            <?php endif; ?>
            <?php if($data['cp']->status != Cp::STATUS_CLOSE): ?>
                <a id="checkExistUser" href="javascript:void(0)" class="font-edit" data-cp-id="<?php assign($data['cp']->id); ?>">フロー編集</a>
            <?php endif; ?>
        </p>
    <?php endif; ?>
<!-- /.campaignFlowWrap --></section>
<script type="text/javascript">
    $(document).ready(function(){
        CpHeaderActionListService.countActionMessages(<?php assign($params['cp']->id)?>,function(result){
            for(actionId in result){
                for(key in result[actionId]){
                    $("#"+key+actionId).text(result[actionId][key]);
                    $("[id=loading"+actionId+"]").hide()
                }
            }
        });
    });
</script>
