<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');

class save_code_auth extends BrandcoPOSTActionBase {
    protected $ContainerName = 'create_code_auth';
    protected $Form = array(
        'package' => 'admin-code-auth',
        'action' => 'create_code_auth',
    );

    public $NeedOption = array();
    public $NeedAdminLogin = true;
    public $CsrfProtect = true;

    protected $ValidatorDefinition = array(
        'name' => array(
            'required' => true,
            'type' => 'str',
            'length' => 255
        )
    );

    public function validate() {
        return true;
    }

    function doAction() {
        $code_auth_service = $this->createService('CodeAuthenticationService');

        $code_auth = $code_auth_service->createEmptyCodeAuth();
        $code_auth->brand_id = $this->getBrand()->id;
        $code_auth->name = $this->POST['name'];
        $code_auth->description = $this->POST['description'];
        $code_auth_service->createCodeAuth($code_auth);

        $this->Data['saved'] = 1;
        return 'redirect: ' . Util::rewriteUrl('admin-code-auth', 'edit_code_auth_codes', array($code_auth->id));
    }
}
