<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');

class api_check_fix_target  extends BrandcoGETActionBase {

    protected $ContainerName = 'api_check_fix_target';
    protected $AllowContent = array('JSON');

    public $NeedOption = array();
    public $NeedUserLogin = true;

    protected $cp_id;
    protected $action_id;

    public function doThisFirst() {
        $this->cp_id = $this->GET['cp_id'];
        $this->action_id = $this->GET['action_id'];
    }

    public function validate() {
        $cp_validator = new CpValidator($this->brand->id);
        if (!$cp_validator->isOwner($this->cp_id)) {
            $json_data = $this->createAjaxResponse('ng');
            $this->assign('json_data', $json_data);
            return false;
        }
        if (!$cp_validator->isOwnerOfAction($this->action_id)) {
            $json_data = $this->createAjaxResponse('ng');
            $this->assign('json_data', $json_data);
            return false;
        }
        return true;
    }

    public function doAction() {
        if(!Util::isAcceptRemote() && !Util::isPersonalMachine()){
            $error['message'] = 'お使いの環境ではダウンロードできません。';
            $json_data = $this->createAjaxResponse('ng', array(), $error);
            $this->assign('json_data', $json_data);
            return 'dummy.php';
        }

        $data_builder = aafwDataBuilder::newBuilder();

        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = $this->createService('CpFlowService');
        $is_instant_win_cp = $cp_flow_service->checkInstantWinCpByCpId($this->cp_id);

        $pager = array(
            'page' => 1,
            'count' => 1
        );

        if ($is_instant_win_cp) {
            $param = array(
                'brand_id' => $this->brand->id,
                'GET_ANNOUNCED_USER' => '__ON__',
                'cp_action_id' => $this->action_id
            );

            $shipping_users = $data_builder->getShippingInfo($param, array(), $pager);
        } else {
            $param = array(
                'cp_id' => $this->cp_id,
                'brand_id' => $this->brand->id,
                'GET_FIXED_TARGET' => '__ON__',
                'cp_action_id' => $this->action_id
            );

            $shipping_users = $data_builder->getShippingAddressInfoByTarget($param, array(), $pager);
        }

        if(count($shipping_users) == 0) {
            $error['message'] = 'ダウンロード可能な配送先情報がありません。';
            $json_data = $this->createAjaxResponse('ng', array(), $error);
            $this->assign('json_data', $json_data);
            return 'dummy.php';
        }

        $json_data = $this->createAjaxResponse('ok');
        $this->assign('json_data', $json_data);
        return 'dummy.php';
    }
}