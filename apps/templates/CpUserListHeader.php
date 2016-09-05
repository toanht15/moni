<?php
$service_factory = new aafwServiceFactory();
/** @var CpFlowService $cp_flow_service */
$cp_flow_service = $service_factory->create('CpFlowService');
$cp = $cp_flow_service->getCpById($data['cp_id']);
$is_message_type = $cp->type == cp::TYPE_MESSAGE ? 1 : 0;
$action = $cp_flow_service->getCpActionById($data['action_id']);
$action_group = $action->getCpActionGroup();
$is_check_shipping_address = ($action_group->order_no > 1 && $cp_flow_service->isExistShippingAddressActionInGroup($action_group->id)) ? 1 : 0;
$cp_member_count = $action->getMemberCount();
/** @var CpMessageDeliveryService $message_delivery_service */
$message_delivery_service = $service_factory->create('CpMessageDeliveryService');

$cp_actions = $cp_flow_service->getCpActionsByCpActionGroupId($action_group->id);

$has_announce_module = false;
$has_instant_win_module = false;
foreach ($cp_actions as $cp_action) {
    if($cp_action->type == CpAction::TYPE_ANNOUNCE){
        $has_announce_module = true;
    }
    if($cp_action->type == CpAction::TYPE_INSTANT_WIN){
        $has_instant_win_module = true;
    }
}

$has_fix_target_step = false;
if($action_group->order_no != 1 && $has_announce_module && !$has_instant_win_module){
    $has_fix_target_step = true;
}
//対象に入れたユーザー数または当選者確定ユーザー数
$targets_count = $message_delivery_service->getTargetsCount($data['brand']->id, $data['reservation']->id, $has_fix_target_step);

//対象に入れたユーザーカウント
$set_target_count = $message_delivery_service->getTargetsCount($data['brand']->id, $data['reservation']->id);

/** @var ManagerService $manager_service */
$manager_service = $service_factory->create('ManagerService');
?>

<?php if ($action->isAnnounceDelivery()): ?>
    <section class="campaignEditWrapShipping">
        <header class="campaignEditHeaderShipping">
            <?php if($data['current_page'] == Cp::PAGE_USER_LIST): ?>
                <div data-header_type="userList" class="<?php assign($targets_count ? 'campaignEditItemShipping_finished' : 'campaignEditItemShipping') ?>">
                    <p class="select" data-query_user="<?php assign(CpCreateSqlService::QUERY_USER_SENT) ?>">
                        <span class="stepNum">1</span>
                        <span class="stepTitle"
                              data-winner_count="<?php assign(number_format($cp->winner_count)) ?>"
                              data-sent_count=<?php assign(number_format($cp_member_count[CpUserService::CACHE_TYPE_FINISH_ACTION]))?>>
                            配送確定ユーザー<small>当選者数：<?php assign(number_format($cp->winner_count)) ?>人<br>
                        <?php if($cp_member_count[CpUserService::CACHE_TYPE_FINISH_ACTION] != 0 && !$data['sent_target']):?>
                            <a href="javascript:void(0)" data-query_user="<?php assign(CpCreateSqlService::QUERY_USER_SENT) ?>">配送確定済：<?php assign(number_format($cp_member_count[CpUserService::CACHE_TYPE_FINISH_ACTION]))?>人</a></small>
                            </span>
                        <?php else: ?>
                            配送確定済：<?php assign(number_format($cp_member_count[CpUserService::CACHE_TYPE_FINISH_ACTION]))?>人</small>
                            </span>
                        <?php endif; ?>
                    <!-- /.select--></p>

                    <p class="selectedUser" data-query_user="<?php assign(CpCreateSqlService::QUERY_USER_TARGET) ?>">
                        <?php if($targets_count): ?>
                            <a href="javascript:void(0)" data-query_user="<?php assign(CpCreateSqlService::QUERY_USER_TARGET) ?>">
                                <small>配送対象</small><br><span class="userNum"><?php assign($targets_count ? number_format($targets_count) : '--')?></span>人
                            </a>
                        <?php else: ?>
                            <span>
                            <small>配送対象</small><br><span class="userNum"><?php assign($targets_count ? number_format($targets_count) : '--')?></span>人
                        </span>
                        <?php endif; ?>
                    <!-- /.selectedUser --></p>
                <!-- /.campaignEditItemShipping --></div>
            <?php else: ?>
                <?php if($data['reservation']->isOverScheduled()): ?>
                    <?php $first_url = 'javascript:void(0);'; ?>
                <?php else: ?>
                    <?php $first_url = Util::rewriteUrl('admin-cp','show_user_list', array($data['cp_id'], $cp_flow_service->getFirstActionInGroupByAction($action)->id), array()); ?>
                <?php endif; ?>

                <a href="<?php write_html($first_url) ?>" class="<?php assign($targets_count ? 'campaignEditItemShipping_finished' : 'campaignEditItemShipping') ?>" data-header_type="userList">
                    <p class="select">
                        <span class="stepNum">1</span>
                        <span class="stepTitle"
                            data-winner_count="<?php assign(number_format($cp->winner_count)) ?>"
                            data-sent_count=<?php assign(number_format($cp_member_count[CpUserService::CACHE_TYPE_FINISH_ACTION]))?>>
                            配送確定ユーザー<small>当選者数：<?php assign(number_format($cp->winner_count)) ?>人<br>
                            配送確定済：<?php assign(number_format($cp_member_count[CpUserService::CACHE_TYPE_FINISH_ACTION]))?>人</small>
                        </span>
                    <!-- /.select--></p>

                    <p class="selectedUser">
                        <span>
                            <small>配送対象</small><br><span class="userNum"><?php assign($targets_count ? number_format($targets_count) : '--')?></span>人
                        </span>
                    <!-- /.selectedUser --></p>
                <!-- /.campaignEditItemShipping --></a>
            <?php endif; ?>

            <?php // Fixしてるまたはターゲット数が0ならリンクなし ?>
            <?php if ($data['reservation']->isScheduled()): ?>
                <p class="btn3_area"><span>確定処理中</span></p>
            <?php elseif ($data['reservation']->isFixedAnnounceDeliveryUser() || !$targets_count || $manager_service->isAgentLogin()): ?>
                <p style="display: none" class="btn3_area" data-btn_type="send_mail"><a href="#jsAnnounceDeliveryFix" class="jsOpenModal jsAnnounceBtn">確定</a></p>
                <p class="btn3_area" data-btn_type="send_mail"><span class="jsAnnounceBtn">確定</span></p>
            <?php elseif (!$data['reservation']->isFixedAnnounceDeliveryUser()): ?>
                <p style="display:none" class="btn3_area" data-btn_type="send_mail"><span class="jsAnnounceBtn">確定</span></p>
                <p class="btn3_area" data-btn_type="send_mail"><a href="#jsAnnounceDeliveryFix" class="jsOpenModal jsAnnounceBtn">確定</a></p>
            <?php endif; ?>
        <!-- /.campaignEditHeaderShipping --></header>

        <?php write_html($this->formHidden('has_send_message_permission', $manager_service->isAgentLogin() ? 0 : 1)) ?>

        <?php if($data['current_page'] == Cp::PAGE_USER_LIST): ?>
        <section class="campaignEditCont"></section>
    <?php endif;?>

<?php else: ?>

    <section class="campaignEditWrap">
        <header class="campaignEditHeader">

        <?php $message_option_value = $data['is_group_fixed'] ? "campaignEditItem_finished" : "campaignEditItem"; ?>

        <?php if($data['current_page'] == Cp::PAGE_EDIT_MESSAGE): ?>
            <div class="<?php assign($message_option_value) ?>" data-header_type="message">
        <?php else: ?>
            <?php if($data['reservation']->isOverScheduled()) {
                $message_url = 'javascript:void(0);';
            } else {
                $message_url = Util::rewriteUrl('admin-cp','edit_action', array($data['cp_id'], $data['action_id']), array('in_action'=>true));
            } ?>
            <a href="<?php write_html($message_url) ?>" class="<?php assign($message_option_value) ?>" data-header_type="message">
        <?php endif; ?>
            <p class="messageEdit">
                <span class="stepNum">1</span><span class="stepTitle">メッセージ作成</span>
            <!-- /.messageEdit --></p>
        <?php if($data['current_page'] == Cp::PAGE_EDIT_MESSAGE): ?>
            <!-- /.campaignEditItem --></div>
        <?php else: ?>
            <!-- /.campaignEditItem --></a>
        <?php endif; ?>

        <?php if($data['current_page'] == Cp::PAGE_USER_LIST): ?>
            <div class="<?php assign(($targets_count || $set_target_count) ? 'campaignEditItem_finished' : 'campaignEditItem') ?>" data-header_type="userList">
        <?php else: ?>
            <?php if($data['reservation']->isOverScheduled()) {
                $first_url = 'javascript:void(0);';
            } else {
                $first_url = Util::rewriteUrl('admin-cp','show_user_list', array($data['cp_id'], $cp_flow_service->getFirstActionInGroupByAction($action)->id), array());
            } ?>
            <a href="<?php write_html($first_url) ?>" class="<?php assign(($targets_count || $set_target_count) ? 'campaignEditItem_finished' : 'campaignEditItem') ?>" data-header_type="userList">
        <?php endif; ?>

        <?php if($data['current_page'] == Cp::PAGE_USER_LIST): ?>
                <div class="userListHeaderCont_current">
                    <div data-header_type="userList">
                        <p class="select" data-query_user="<?php assign(CpCreateSqlService::QUERY_USER_SENT) ?>">
                            <?php if ($action->type === CpAction::TYPE_ANNOUNCE || $data['is_include_type_announce']): ?>
                                <span class="stepNum">2</span><span class="stepTitle" data-winner_count=<?php assign(number_format($cp->winner_count)) ?> data-sent_count=<?php assign(number_format($cp_member_count[CpUserService::CACHE_TYPE_SEND_MESSAGE]))?>>対象ユーザー選択<small>当選者数：<?php assign(number_format($cp->winner_count)) ?>人<br>
                            <?php else:?>
                                <span class="stepNum">2</span><span class="stepTitle" data-sent_count=<?php assign(number_format($cp_member_count[CpUserService::CACHE_TYPE_SEND_MESSAGE]))?>>対象ユーザー選択<small>
                            <?php endif;?>

                            <?php if($cp_member_count[CpUserService::CACHE_TYPE_SEND_MESSAGE] != 0 && !$data['sent_target']):?>
                                <a href="javascript:void(0)" data-query_user="<?php assign(CpCreateSqlService::QUERY_USER_SENT) ?>">送信済：<?php assign(number_format($cp_member_count[CpUserService::CACHE_TYPE_SEND_MESSAGE]))?>人</a></small></span>
                            <?php else: ?>
                                送信済：<?php assign(number_format($cp_member_count[CpUserService::CACHE_TYPE_SEND_MESSAGE]))?>人</small></span>
                            <?php endif; ?>

                        <!-- /.select--></p>
                        <p class="selectedUser" data-query_user="<?php assign(CpCreateSqlService::QUERY_USER_TARGET) ?>">
                            <?php if ($targets_count && $has_fix_target_step): ?>
                                <a class="showTarget" href="javascript:void(0)" data-query_user="<?php assign(CpCreateSqlService::QUERY_USER_TARGET) ?>">
                                    <small>送信対象(確定)</small><br>
                                    <span class="userNum"><?php assign($targets_count ? number_format($targets_count) : '--') ?></span>人</a>
                            <?php elseif($targets_count): ?>
                                <a class="showTarget" href="javascript:void(0)" data-query_user="<?php assign(CpCreateSqlService::QUERY_USER_TARGET) ?>">
                                    <small>送信対象</small><br>
                                    <span class="userNum"><?php assign($targets_count ? number_format($targets_count) : '--') ?></span>人</a>
                            <?php elseif ($set_target_count && $has_fix_target_step): ?>
                                <a class="showTarget" href="javascript:void(0)"
                                   data-query_user="<?php assign(CpCreateSqlService::QUERY_USER_TARGET) ?>">
                                    <small>送信対象(候補)</small><br>
                                    <span class="setTargetNum"><?php assign($set_target_count ? number_format($set_target_count) : '--') ?></span>人</a>
                            <?php else: ?>
                                <span class="showTarget">
                                    <small>送信対象</small><br><span class="userNum"><?php assign($targets_count ? number_format($targets_count) : '--')?></span>人
                                </span>
                            <?php endif;?>
                        <!-- /.selectedUser --></p>
                    </div>
                <!-- /.userListHeaderCont_current --></div>
            <!-- /.campaignEditItem --></div>
        <?php else: ?>
                <p class="select">
                    <?php if ($action->type === CpAction::TYPE_ANNOUNCE || $data['is_include_type_announce']): ?>
                        <span class="stepNum">2</span><span class="stepTitle" data-winner_count=<?php assign(number_format($cp->winner_count)) ?> data-sent_count=<?php assign(number_format($cp_member_count[CpUserService::CACHE_TYPE_SEND_MESSAGE]))?>>対象ユーザー選択<small>当選者数：<?php assign(number_format($cp->winner_count)) ?>人中<br>
                    <?php else:?>
                        <span class="stepNum">2</span><span class="stepTitle" data-sent_count=<?php assign(number_format($cp_member_count[CpUserService::CACHE_TYPE_SEND_MESSAGE]))?>>対象ユーザー選択<small>
                    <?php endif;?>
                    送信済：<?php assign(number_format($cp_member_count[CpUserService::CACHE_TYPE_SEND_MESSAGE]))?>人</small></span>
                <!-- /.select--></p>
                <p class="selectedUser">
                    <?php if($set_target_count && $has_fix_target_step): ?>
                        <span>
                            <small>送信対象(候補)</small><br><span class="setTargetNum"><?php assign($set_target_count ? number_format($set_target_count) : '--')?></span>人
                        </span>
                    <?php else: ?>
                    <span>
                        <small>送信対象</small><br><span class="userNum"><?php assign($targets_count ? number_format($targets_count) : '--')?></span>人
                    </span>
                    <?php endif; ?>
                <!-- /.selectedUser --></p>
            <!-- /.campaignEditItem --></a>
        <?php endif; ?>

        <?php $option_value = $data['reservation']->canEdit() ? "campaignEditItem" : "campaignEditItem_finished"; ?>

        <?php if($data['current_page'] == Cp::PAGE_SETTING_OPTION): ?>
            <div class="<?php assign($option_value) ?>" data-header_type="option">
        <?php else: ?>
            <?php if($data['reservation']->isOverScheduled()) {
                $option_url = 'javascript:void(0);';
            } else {
                $option_url = Util::rewriteUrl('admin-cp','setting_message_option', array($cp_flow_service->getFirstActionInGroupByAction($action)->id));
            } ?>
            <a href="<?php write_html($option_url) ?>" class="<?php assign($option_value) ?>" data-header_type="option">
        <?php endif; ?>

            <p class="option">
                <span class="stepTitle">オプション設定<br><small>・<?php assign($data['reservation']->deliveryTypeString()); ?><br>・<?php assign($data['reservation']->sendMailFlgString()); ?></small></span>
            <!-- /.option --></p>

        <?php if($data['current_page'] == Cp::PAGE_SETTING_OPTION): ?>
            <!-- /.campaignEditItem --></div>
        <?php else: ?>
            <!-- /.campaignEditItem --></a>
        <?php endif; ?>

        <?php $shipping_method_present = $cp->shipping_method && $action->type == CpAction::TYPE_ANNOUNCE ?>
        <?php write_html($this->formHidden('shipping_method_present', $shipping_method_present ? 1 : 0)) ?>
        <?php write_html($this->formHidden('coupon_action', $action->type == CpAction::TYPE_COUPON ? 1 : 0)) ?>
        <?php write_html($this->formHidden('is_check_shipping_address', $is_check_shipping_address)) ?>
        <?php write_html($this->formHidden('is_message_type', $is_message_type)) ?>
        <?php write_html($this->formHidden('has_fix_target_step', $has_fix_target_step ? 1 : 0)) ?>
        <?php write_html($this->formHidden('set_target_count', $set_target_count ? number_format($set_target_count) : 0)) ?>
        <?php write_html($this->formHidden('has_send_message_permission', $manager_service->isAgentLogin() ? 0 : 1)) ?>

        <?php if($data['reservation']->isScheduled()): ?>
            <p class="btn3_area"><span>配信予約済み</span></p>
        <?php elseif($data['reservation']->isDelivering()): ?>
            <p class="btn4_area"><span>配信中</span></p>
        <?php elseif($data['reservation']->isFailedDelivering()): ?>
            <p class="btn4_area"><span>配信失敗</span></p>
        <?php elseif($data['reservation']->canSchedule() && $targets_count && $data['is_group_fixed'] && !$manager_service->isAgentLogin() && !$shipping_method_present && $action->type != CpAction::TYPE_COUPON && ($data['fixed_target'] || !$has_fix_target_step)): ?>
            <p class="btn3_area" data-btn_type="send_mail"><a href="#modal1" class="jsOpenModal jsAnnounceBtn">送信</a></p>
            <p style="display: none" class="btn3_area" data-btn_type="send_mail"><span class="jsAnnounceBtn">送信</span></p>
        <?php else: ?>
            <p style="display: none" class="btn3_area" data-btn_type="send_mail"><a href="#modal1" class="jsOpenModal jsAnnounceBtn">送信</a></p>
            <p class="btn3_area" data-btn_type="send_mail"><span class="jsAnnounceBtn">送信</span></p>
        <?php endif; ?>
        <!-- /.campaignEditHeader --></header>

        <?php if($data['current_page'] == Cp::PAGE_USER_LIST): ?>
        <section class="campaignEditCont"></section>
        <?php endif;?>

<?php endif; ?>
