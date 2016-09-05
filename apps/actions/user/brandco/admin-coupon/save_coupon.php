<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');

class save_coupon extends BrandcoPOSTActionBase {
    protected $ContainerName = 'create_coupon';
    protected $Form = array(
        'package' => 'admin-coupon',
        'action' => 'create_coupon',
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

        /** @var CouponService $coupon_service */
        $coupon_service = $this->createService('CouponService');
        $coupon = $coupon_service->createCoupon($this->brand->id, $this->POST['name'], $this->POST['description']);

        $this->Data['saved'] = 1;

        $return = 'redirect: '. Util::rewriteUrl('admin-coupon', 'edit_coupon_codes', array($coupon->id));

        return $return;
    }
}
