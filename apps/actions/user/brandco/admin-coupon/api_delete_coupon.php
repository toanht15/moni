<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');
class api_delete_coupon extends BrandcoPOSTActionBase {
    protected $ContainerName = 'api_delete_coupon';
    protected $AllowContent = array('JSON');

    public $NeedOption = array();
    public $NeedAdminLogin = true;
    public $CsrfProtect = true;

    public function beforeValidate () {
    }

    public function validate () {
        $coupon_validator = new CouponValidator($this->POST['coupon_id'], $this->brand->id);
        if (!$coupon_validator->isValidCouponId()) {
            return false;
        }
        return true;
    }

    function doAction() {
        /** @var CouponService $coupon_service */
        $coupon_service = $this->createService('CouponService');
        $coupon_action_manager = new CpCouponActionManager();
        $coupon_actions = $coupon_action_manager->getCpCouponActionsByCouponId($this->POST['coupon_id']);
        if ($coupon_actions) {
            $json_data = $this->createAjaxResponse("ng");
            $this->assign('json_data', $json_data);
            return 'dummy.php';
        } else {
            $coupon_service->deleteCouponAndCouponCodes($this->POST['coupon_id']);
        }

        $json_data = $this->createAjaxResponse("ok");
        $this->assign('json_data', $json_data);

        return 'dummy.php';
    }
}