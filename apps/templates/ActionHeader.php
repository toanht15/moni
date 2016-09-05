<?php
$service_factory = new aafwServiceFactory();
/** @var CpFlowService $cp_flow_service */
$cp_flow_service = $service_factory->create('CpFlowService');
$cp = $cp_flow_service->getCpById($data['cp_id']);
if($data['action_id']) {
    $action = $cp_flow_service->getCpActionById($data['action_id']);
    $group = $cp_flow_service->getCpActionGroupByAction($data['action_id']);
}
switch($cp->type) {
    case cp::TYPE_CAMPAIGN:
        $widgetPath = "CpActionHeader";
        break;
    case cp::TYPE_MESSAGE:
        $widgetPath = "MsgActionHeader";
        break;
    default:
        $widgetPath = "CpActionHeader";
}
write_html(aafwWidgets::getInstance()->loadWidget($widgetPath)->render(
    array(
        'cp' => $cp,
        'group_array' => $data['group_array'],
        'action' => $action,
        'group' => $group,
        'user_list_page' => $data['user_list_page'],
        'pageStatus' => $data['pageStatus'],
        'enable_archive' => $data['enable_archive'],
        'isHideDemoFunction' => $data['isHideDemoFunction'],
    )
));