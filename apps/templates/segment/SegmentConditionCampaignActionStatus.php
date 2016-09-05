<?php
$service_factory = new aafwServiceFactory();
/** @var CpUserActionStatusService $cp_user_action_status_service */
$cp_user_action_status_service = $service_factory->create('CpUserActionStatusService');
$cp_flow_service = $service_factory->create('CpFlowService');
$action = $cp_flow_service->getCpActionById($data['target_id']);
?>
<ul class="status">
    <li>
        <?php write_html($this->formCheckbox(
            'search_participate_condition/' . $action->id . '/' . SegmentCreateSqlService::PARTICIPATE_COMPLETE,
            1,
            array('checked' => $data['condition_data']['search_participate_condition/' . $action->id . '/' . SegmentCreateSqlService::PARTICIPATE_COMPLETE] ? 'checked' : ''),
            array('1' => $cp_user_action_status_service->getQueryItem($action)[0])
        )) ?>
    </li>

    <?php if ($action->type != CpAction::TYPE_COUPON && $action->type != CpAction::TYPE_ANNOUNCE_DELIVERY): ?>
        <li>
            <?php write_html($this->formCheckbox(
                'search_participate_condition/' . $action->id . '/' . SegmentCreateSqlService::PARTICIPATE_READ,
                1,
                array('checked' => $data['condition_data']['search_participate_condition/' . $action->id . '/' . SegmentCreateSqlService::PARTICIPATE_READ] ? 'checked' : ''),
                array('1' => $cp_user_action_status_service->getQueryItem($action)[1])
            )) ?>
        </li>
    <?php endif; ?>

    <?php if ($action->type != CpAction::TYPE_ANNOUNCE_DELIVERY): ?>
        <li>
            <?php write_html($this->formCheckbox(
                'search_participate_condition/' . $action->id . '/' . SegmentCreateSqlService::PARTICIPATE_NOT_READ,
                1,
                array('checked' => $data['condition_data']['search_participate_condition/' . $action->id . '/' . SegmentCreateSqlService::PARTICIPATE_NOT_READ] ? 'checked' : ''),
                array('1' => $cp_user_action_status_service->getQueryItem($action)[2])
            )) ?>
        </li>

        <li>
            <?php write_html($this->formCheckbox(
                'search_participate_condition/' . $action->id . '/' . SegmentCreateSqlService::PARTICIPATE_NOT_SEND,
                1,
                array('checked' => $data['condition_data']['search_participate_condition/' . $action->id . '/' . SegmentCreateSqlService::PARTICIPATE_NOT_SEND] ? 'checked' : ''),
                array('1' => $cp_user_action_status_service->getQueryItem($action)[3])
            )) ?>
        </li>
    <?php endif; ?>

    <?php if ($action->type == CpAction::TYPE_INSTANT_WIN): ?>
        <li>
            <?php write_html($this->formCheckbox(
                'search_participate_condition/' . $action->id . '/' . SegmentCreateSqlService::PARTICIPATE_COUNT_INSTANT_WIN,
                1,
                array('class' => 'jsCheckToggle', 'checked' => $data['condition_data']['search_participate_condition/' . $action->id . '/' . SegmentCreateSqlService::PARTICIPATE_COUNT_INSTANT_WIN] ? 'checked' : ''),
                array('1' => $cp_user_action_status_service->getQueryItem($action)[4])
            )) ?>
        </li>
        <p class="jsCheckToggleTarget" style="display: none;">
            <?php write_html($this->formText(
                'search_count_instant_win_from/' . $action->id,
                1,
                array('maxlength' => '3', 'class' => 'inputNum', 'placeholder' => '0')
            )); ?>回
            <span class="dash">～</span>
            <?php write_html($this->formText(
                'search_count_instant_win_to/' . $action->id,
                1,
                array('maxlength' => '3', 'class' => 'inputNum', 'placeholder' => '99')
            )); ?>回
        </p>
    <?php elseif ($action->isOpeningCpAction()): ?>
        <li>
            <?php write_html($this->formCheckbox(
                'search_participate_condition/' . $action->id . '/' . SegmentCreateSqlService::PARTICIPATE_REJECTED,
                1,
                array('checked' => $data['condition_data']['search_participate_condition/' . $action->id . '/' . SegmentCreateSqlService::PARTICIPATE_REJECTED] ? 'checked' : ''),
                array('1' => $cp_user_action_status_service->getQueryItem($action)[4])
            )) ?>
        </li>
    <?php endif ?>
    <!-- /.status --></ul>
