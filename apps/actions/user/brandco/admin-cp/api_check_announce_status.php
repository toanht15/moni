<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');

class api_check_announce_status extends BrandcoGETActionBase {

    protected $ContainerName = 'api_check_announce_status';
    protected $AllowContent = array('JSON');

    public $NeedOption = array();
    public $NeedUserLogin = true;

    protected $cp_id;
    protected $action_id;

    function doThisFirst() {
        $this->cp_id = $this->GET['cp_id'];
        $this->action_id = $this->GET['action_id'];
    }

    function validate() {
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

    function doAction() {
        if(!Util::isAcceptRemote() && !Util::isPersonalMachine()){
            $error['message'] = 'お使いの環境ではダウンロードできません。';
            $json_data = $this->createAjaxResponse('ng', array(), $error);
            $this->assign('json_data', $json_data);
            return 'dummy.php';
        }

        $data_builder = aafwDataBuilder::newBuilder();

        $param = array(
            'brand_id' => $this->brand->id,
            'GET_ANNOUNCED_USER' => '__ON__',
            'cp_action_id' => $this->action_id
        );

        $shipping_users = $data_builder->getShippingInfo($param);
        if(!$shipping_users) {
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