<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');

class coupon_list extends BrandcoGETActionBase {

    public $NeedOption = array(BrandOptions::OPTION_CP);
    public $NeedAdminLogin = true;
    private $pageLimited = 20;

    public function doThisFirst() {
        $this->deleteErrorSession();
    }

    public function validate() {
        return true;
    }

    function doAction() {

        /** @var CouponService $coupon_service */
        $coupon_service = $this->createService('CouponService');
        $this->Data['totalCount'] = $coupon_service->countCouponByBrandId($this->brand->id);
        $this->p = Util::getCorrectPaging($this->p, $this->Data['totalCount']);

        $this->Data['coupons'] = $coupon_service->getCouponsByBrandId($this->brand->id, $this->p, $this->pageLimited);
        $this->Data['pageLimited'] = $this->pageLimited;

        return 'user/brandco/admin-coupon/coupon_list.php';
    }
}
