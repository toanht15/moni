<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');

class coupon_codes extends BrandcoGETActionBase {

    public $NeedOption = array(BrandOptions::OPTION_CP);
    public $NeedAdminLogin = true;
    private $pageLimit = 20;
    /** @var CouponService $coupon_service */
    private $coupon_service;

    public function doThisFirst() {
        $this->Data['coupon_id'] = $this->GET['exts'][0];
    }

    public function validate() {

        if (!$this->Data['coupon_id']) {
            return '404';
        }

        $this->coupon_service = $this->createService('CouponService');
        $this->Data['coupon'] = $this->coupon_service->getCouponById($this->Data['coupon_id']);
        if (!$this->Data['coupon'] || $this->Data['coupon']->brand_id != $this->brand->id) {
            return '404';
        }

        return true;
    }

    function doAction() {

        $this->Data['totalCount'] = $this->coupon_service->countCouponCodeByCouponId($this->Data['coupon_id']);
        $total_page = floor ( $this->Data['totalCount'] / $this->pageLimit ) + ( $this->Data['totalCount'] % $this->pageLimit > 0 );
        $this->p = Util::getCorrectPaging($this->p, $total_page);
        $order = array(
            'name' => 'id',
            'direction' => "asc"
        );
        $this->Data['coupon_codes'] = $this->coupon_service->getCouponCodeByCouponId($this->Data['coupon_id'], $this->p, $this->pageLimit, $order);
        $this->Data['pageLimited'] = $this->pageLimit;

        list($reserved_num, $total_num) = $this->coupon_service->getCouponStatisticByCouponId($this->Data['coupon_id']);
        $this->Data['coupon_limit'] = $total_num;
        $this->Data['coupon_reserved'] = $reserved_num;

        return 'user/brandco/admin-coupon/coupon_codes.php';
    }
}
