<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');

abstract class AdminCpListBase extends aafwWidgetBase {

    protected $order_kinds = array(
        1 => 'created_at',
        2 => 'cp_user_id'
    );

    protected $order_types = array(
        1 => 'asc',
        2 => 'desc'
    );

    protected $cp_flow_service;
    protected $cp_action_service;
    protected $user_service;

    public function doService($params = array()) {
        $params['page'] = $params['page'] ?: 1;

        $api_code_service = $this->getService('ContentApiCodeService');
        $api_code = $api_code_service->getApiCodeByCpIdAndCpActionType($params['cp_id'], $this->getCurCpActionType());
        $params['api_url'] = $api_code ? $api_code_service->getApiUrl($api_code->code, $this->getCurCpActionType()) : '';

        return $this->doSubService($params);
    }

    public function getUserDataOrder($params) {
        return $order = array(
            'name' => $this->order_kinds[$params['order_kind']],
            'direction' => $this->order_types[$params['order_type']]
        );
    }

    abstract public function doSubService($params);

    abstract public function getSearchParams($params);

    abstract public function getCurCpActionType();
}