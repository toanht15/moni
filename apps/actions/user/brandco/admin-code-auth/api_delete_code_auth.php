<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');

class api_delete_code_auth extends BrandcoPOSTActionBase {
    protected $ContainerName = 'api_delete_code_auth';
    protected $AllowContent = array('JSON');

    public $NeedOption = array();
    public $NeedAdminLogin = true;
    public $CsrfProtect = true;

    public function validate () {
        $code_auth_validator = new CodeAuthValidator($this->POST['code_auth_id'], $this->getBrand()->id);

        return $code_auth_validator->isValidCodeAuthId();
    }

    function doAction() {
        /** @var CpCodeAuthActionManager $code_auth_action_manager */
        $code_auth_action_manager = $this->createService('CpCodeAuthActionManager');

        $code_auth_actions = $code_auth_action_manager->getCpConcreteActionByCodeAuthId($this->POST['code_auth_id']);

        if ($code_auth_actions) {
            $json_data = $this->createAjaxResponse('ng');
        } else {
            /** @var CodeAuthenticationService $code_auth_service */
            $code_auth_service = $this->createService('CodeAuthenticationService');

            $code_auth_service->deleteCodeAuthAndCodeAuthCodes($this->POST['code_auth_id']);
            $json_data = $this->createAjaxResponse("ok");
        }

        $this->assign('json_data', $json_data);
        return 'dummy.php';
    }
}