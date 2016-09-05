<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');

class edit_coupon_codes extends BrandcoGETActionBase {
    protected $ContainerName = 'update_coupon';

    public $NeedOption = array(BrandOptions::OPTION_CP);
    public $NeedAdminLogin = true;
    private $pageLimit = 20;
    /** @var CouponService $coupon_service */
    private $coupon_service;

    public function doThisFirst() {
        $this->Data['coupon_id'] = $this->GET['exts'][0];
        $this->deleteErrorSession();
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

        if (!$this->getActionContainer('Errors')) {
            $data = array();
            $data['name'] = $this->Data['coupon']->name;
            $data['description'] = $this->Data['coupon']->description;
            $data['distribution_type'] = $this->Data['coupon']->distribution_type;

            foreach($this->Data['coupon_codes'] as $coupon_code) {
                $data['max_num/'.$coupon_code->id] = $coupon_code->max_num;
                if ($coupon_code->expire_date == '0000-00-00 00:00:00') {
                    $data['non_expire_date/'.$coupon_code->id] = '1';
                } else {
                    $data['expire_date/'.$coupon_code->id] = date_create($coupon_code->expire_date)->format('Y/m/d');
                }
            }
            $this->assign('ActionForm', $data);
        }

        $coupon_action_manager = new CpCouponActionManager();
        $coupon_action = $coupon_action_manager->getCpCouponActionsByCouponId($this->Data['coupon_id']);
        $this->Data['can_delete'] = $coupon_action ? false : true;

        list($reserved_num, $total_num) = $this->coupon_service->getCouponStatisticByCouponId($this->Data['coupon_id']);
        $this->Data['coupon_limit'] = $total_num;
        $this->Data['coupon_reserved'] = $reserved_num;

        return 'user/brandco/admin-coupon/edit_coupon_codes.php';
    }
}
