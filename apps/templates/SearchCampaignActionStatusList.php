<?php
$service_factory = new aafwServiceFactory();
/** @var CpUserActionStatusService $cp_user_action_status_service */
$cp_user_action_status_service = $service_factory->create('CpUserActionStatusService');
/** @var CpFlowService $cp_flow_service */
$cp_flow_service = $service_factory->create('CpFlowService');

$cp_action_group = $cp_flow_service->getCpActionGroupByAction($data['action']->id);

//先着当選キャンペーンかどうかチェックする
$is_first_group_announce = ($cp_action_group->order_no == 1) ? true : false;
?>
<ul>
    <li>
        <?php write_html($this->formCheckbox(
            'search_participate_condition/'.$data['action']->id.'/'.CpCreateSqlService::PARTICIPATE_COMPLETE.'/'.$data["search_no"],
            $this->POST ? PHPParser::ACTION_FORM : $data['search_conditions'][CpCreateSqlService::PARTICIPATE_COMPLETE],
            array('checked' => $data['search_conditions']['search_participate_condition/'.$data['action']->id.'/'.CpCreateSqlService::PARTICIPATE_COMPLETE] ? 'checked' : ''),
            array('1' => $cp_user_action_status_service->getQueryItem($data['action'])[0])
        ))?>
    </li>

<?php if ($data['action']->type != CpAction::TYPE_COUPON && $data['action']->type != CpAction::TYPE_ANNOUNCE_DELIVERY): ?>
    <li>
        <?php write_html($this->formCheckbox(
            'search_participate_condition/'.$data['action']->id.'/'.CpCreateSqlService::PARTICIPATE_READ.'/'.$data["search_no"],
            $this->POST ? PHPParser::ACTION_FORM : $data['search_conditions'][CpCreateSqlService::PARTICIPATE_READ],
            array('checked' => $data['search_conditions']['search_participate_condition/'.$data['action']->id.'/'.CpCreateSqlService::PARTICIPATE_READ] ? 'checked' : ''),
            array('1' => $cp_user_action_status_service->getQueryItem($data['action'])[1])
        ))?>
    </li>
<?php endif; ?>

<?php if ($data['action']->type != CpAction::TYPE_ANNOUNCE_DELIVERY): ?>
    <li>
        <?php write_html($this->formCheckbox(
            'search_participate_condition/'.$data['action']->id.'/'.CpCreateSqlService::PARTICIPATE_NOT_READ.'/'.$data["search_no"],
            $this->POST ? PHPParser::ACTION_FORM : $data['search_conditions'][CpCreateSqlService::PARTICIPATE_NOT_READ],
            array('checked' => $data['search_conditions']['search_participate_condition/'.$data['action']->id.'/'.CpCreateSqlService::PARTICIPATE_NOT_READ] ? 'checked' : ''),
            array('1' => $cp_user_action_status_service->getQueryItem($data['action'])[2])
        ))?>
    </li>

    <li>
        <?php write_html($this->formCheckbox(
            'search_participate_condition/'.$data['action']->id.'/'.CpCreateSqlService::PARTICIPATE_NOT_SEND.'/'.$data["search_no"],
            $this->POST ? PHPParser::ACTION_FORM : $data['search_conditions'][CpCreateSqlService::PARTICIPATE_NOT_SEND],
            array('checked' => $data['search_conditions']['search_participate_condition/'.$data['action']->id.'/'.CpCreateSqlService::PARTICIPATE_NOT_SEND] ? 'checked' : ''),
            array('1' => $cp_user_action_status_service->getQueryItem($data['action'])[3])
        ))?>
    </li>
<?php endif; ?>

<?php if ($data['action']->type == CpAction::TYPE_INSTANT_WIN): ?>
    <li>
        <?php write_html($this->formCheckbox(
            'search_participate_condition/'.$data['action']->id.'/'.CpCreateSqlService::PARTICIPATE_COUNT_INSTANT_WIN.'/'.$data["search_no"],
            $this->POST ? PHPParser::ACTION_FORM : $data['search_conditions'][CpCreateSqlService::PARTICIPATE_COUNT_INSTANT_WIN],
            array('class' => 'jsCheckToggle', 'checked' => $data['search_conditions']['search_participate_condition/'.$data['action']->id.'/'.CpCreateSqlService::PARTICIPATE_COUNT_INSTANT_WIN] ? 'checked' : ''),
            array('1' => $cp_user_action_status_service->getQueryItem($data['action'])[4])
        ))?>
    </li>
    <p class="jsCheckToggleTarget" style="display: none;">
        <?php write_html($this->formText(
            'search_count_instant_win_from/'.$data['action']->id.'/'.$data["search_no"],
            $this->POST ? PHPParser::ACTION_FORM : $data['search_conditions']['search_count_instant_win_from/'.$data['action']->id],
            array('maxlength'=>'3', 'class'=>'inputNum', 'placeholder'=>'0')
        )); ?>回
        <span class="dash">～</span>
        <?php write_html($this->formText(
            'search_count_instant_win_to/'.$data['action']->id.'/'.$data["search_no"],
            $this->POST ? PHPParser::ACTION_FORM : $data['search_conditions']['search_count_instant_win_to/'.$data['action']->id],
            array('maxlength'=>'3', 'class'=>'inputNum', 'placeholder'=>'99')
        )); ?>回
    </p>
<?php elseif ($data['action']->isOpeningCpAction()): ?>
    <li>
        <?php write_html($this->formCheckbox(
            'search_participate_condition/'.$data['action']->id.'/'.CpCreateSqlService::PARTICIPATE_REJECTED.'/'.$data["search_no"],
            $this->POST ? PHPParser::ACTION_FORM : $data['search_conditions'][CpCreateSqlService::PARTICIPATE_REJECTED],
            array('checked' => $data['search_conditions']['search_participate_condition/'.$data['action']->id.'/'.CpCreateSqlService::PARTICIPATE_REJECTED] ? 'checked' : ''),
            array('1' => $cp_user_action_status_service->getQueryItem($data['action'])[4])
        )) ?>
    </li>
<?php endif ?>
</ul>

<?php if ($data['action']->type == CpAction::TYPE_ANNOUNCE && !$is_first_group_announce): ?>
    <hr>
    <ul>
        <li>
            <?php write_html($this->formCheckbox(
                'search_participate_condition/'.$data['action']->id.'/'.CpCreateSqlService::PARTICIPATE_TARGET.'/'.$data["search_no"],
                $this->POST ? PHPParser::ACTION_FORM : $data['search_conditions'][CpCreateSqlService::PARTICIPATE_TARGET],
                array('checked' => $data['search_conditions']['search_participate_condition/'.$data['action']->id.'/'.CpCreateSqlService::PARTICIPATE_TARGET] ? 'checked' : '', 'class' => 'jsParticipateTarget', 'data-cp_action_id' => $data['action']->id),
                array('1' => $cp_user_action_status_service->getQueryItem($data['action'])[4])
            ))?>
        </li>
        <li>
            <?php write_html($this->formCheckbox(
                'search_participate_condition/'.$data['action']->id.'/'.CpCreateSqlService::PARTICIPATE_NOT_TARGET.'/'.$data["search_no"],
                $this->POST ? PHPParser::ACTION_FORM : $data['search_conditions'][CpCreateSqlService::PARTICIPATE_NOT_TARGET],
                array('checked' => $data['search_conditions']['search_participate_condition/'.$data['action']->id.'/'.CpCreateSqlService::PARTICIPATE_NOT_TARGET] ? 'checked' : ''),
                array('1' => $cp_user_action_status_service->getQueryItem($data['action'])[5])
            ))?>
        </li>
    </ul>
<?php endif; ?>
