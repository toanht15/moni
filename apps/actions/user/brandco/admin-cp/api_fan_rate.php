<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');
AAFW::import('jp.aainc.classes.services.BrandsUsersRelationService');

class api_fan_rate extends BrandcoPOSTActionBase {

    protected $ContainerName = 'api_fan_rate';

    protected $AllowContent = array('JSON');
    public $NeedOption = array();
    public $NeedAdminLogin = true;
    public $CsrfProtect = true;

    public function validate() {
        return true;
    }

    function doAction() {
        /** @var BrandsUsersRelationService $brandsUsersRelationService */
        $brandsUsersRelationService = $this->getService('BrandsUsersRelationService');

        if($brandsUsersRelationService->changeFanRate($this->POST['brand_user_id'],$this->POST['rate'])) {
            $rate_info = $brandsUsersRelationService->getMemberRate($this->POST['rate']);
            $parser = new PHPParser();
            $html = $parser->parseTemplate('MemberRate.php', array(
                'image_url'     => $rate_info['image_url'],
                'rate_value'    => $rate_info['rate'],
                'brand_user_id' => $this->POST['brand_user_id'],
                'rate'          => $this->POST['rate'],
            ));
            $json_data = $this->createAjaxResponse("ok",array(),array(),$html);
        } else {
            $json_data = $this->createAjaxResponse("ng");
        }
        $this->assign('json_data', $json_data);
        return 'dummy.php';
    }
}
