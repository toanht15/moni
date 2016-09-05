<?php
$service_factory = new aafwServiceFactory();
/** @var CpFlowService $cp_flow_service */
$cp_flow_service = $service_factory->create('CpFlowService');
$cp = $cp_flow_service->getCpById($data['cp_id']);
$action = $cp_flow_service->getCpActionById($data['action_id']);
$cp_member_count = $action->getMemberCount();
?>
    <section class="campaignEditCont">
        <div class="howtoSend">
            <?php if ($action->isAnnounceDelivery()): ?>
                <h1>当選者を選択し、賞品を発送します</h1>
                <table>
                    <thead>
                    <tr>
                        <th>当選数</th>
                        <th>配送数</th>
                        <th>残り</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td><?php assign(number_format($cp->winner_count)) ?>人</td>
                        <td>
                            <span><?php assign(number_format($cp_member_count[CpUserService::CACHE_TYPE_FINISH_ACTION])) ?>人</span>
                        </td>
                        <td><?php assign(number_format($cp->winner_count - $cp_member_count[CpUserService::CACHE_TYPE_FINISH_ACTION])) ?>人</td>
                    </tr>
                    </tbody>
                </table>
                <p class="imgHowtoShipping"><img src="<?php assign($this->setVersion('/img/campaign/imgHowtoShipping.jpg')) ?>" width="365" height="228" alt="賞品の送り方"></p>
                <form action="<?php assign(Util::rewriteUrl('admin-cp', 'api_change_manual_announce_delivery.json')) ?>" class="jsChangeAnnounceDeliveryManualForm" method="POST">
                    <?php write_html($this->csrf_tag()); ?>
                    <p class="howtoCheck">
                        <?php write_html($this->formCheckbox('hide_manual', false, array('class' => 'jsChangeAnnounceDeliveryManual'), array('1' => '次回から表示しない'))) ?>
                    </p>
                </form>
            <?php else: ?>
                <h1>当選者を選択し、発表のメッセージを作成します</h1>
                <table>
                    <thead>
                    <tr>
                        <th>当選数</th>
                        <th>送信数</th>
                        <th>残り</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td><?php assign(number_format($cp->winner_count)) ?>人</td>
                        <td><a href="<?php assign(Util::rewriteUrl( 'admin-cp', 'show_user_list', array($data['cp_id'], $data['action_id']), array('sent_target'=>true))) ?>">
                                <?php assign(number_format($cp_member_count[CpUserService::CACHE_TYPE_SEND_MESSAGE]))?>人</a></td>
                        <td><?php assign(number_format($cp->winner_count-$cp_member_count[CpUserService::CACHE_TYPE_SEND_MESSAGE])) ?>人</td>
                    </tr>
                    </tbody>
                </table>
                <p><img src="<?php assign($this->setVersion('/img/campaign/imgHowto.jpg'))?>" width="930" height="229" alt="メッセージの送り方"></p>
                <form action="<?php assign(Util::rewriteUrl( 'admin-cp', 'api_change_hide_manual.json')); ?>" class="executeChangeHideManualAction" method="POST">
                    <?php write_html($this->csrf_tag()); ?>
                    <p class="howtoCheck"><?php write_html($this->formCheckbox("chk_hide_manual", false, array('class' => 'cmd_execute_change_hide_manual_action'), array('1' => '次回から表示しない')));?></p>
                </form>
            <?php endif; ?>
        <!-- /.howtoSend --></div>
    <!-- /.campaignEditCont  --></section>
