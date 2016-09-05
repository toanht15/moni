<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');

class update_coupon extends BrandcoPOSTActionBase {
    protected $ContainerName = 'update_coupon';
    protected $Form = array(
        'package' => 'admin-coupon',
        'action' => 'edit_coupon_codes/{coupon_id}?p={page}',
    );

    public $NeedOption = array();
    public $NeedAdminLogin = true;
    public $CsrfProtect = true;
    /** @var CouponService $coupon_service */
    protected $coupon_service;
    protected $coupon_codes;

    protected $ValidatorDefinition = array(
        'name' => array(
            'required' => true,
            'type' => 'str',
            'length' => 255
        ),
        'page' => array(
            'required' => true,
            'type' => 'num',
            'range' => array(
                '>' => 0,
            )
        ),
        'coupon_id' => array(
            'required' => true,
            'type' => 'num',
            'range' => array(
                '>' => 0,
            )
        ),
        'limit' => array(
            'required' => true,
            'type' => 'num',
            'range' => array(
                '>' => 0,
            )
        )
    );

    public function validate() {
        $coupon_validator = new CouponValidator($this->POST['coupon_id'], $this->brand->id);
        if (!$coupon_validator->isValidCouponId()) {
            return '404';
        }

        $this->coupon_service = $this->createService('CouponService');
        $order = array(
            'name' => 'id',
            'direction' => "asc"
        );
        $this->coupon_codes = $this->coupon_service->getCouponCodeByCouponId($this->POST['coupon_id'], $this->POST['page'], $this->POST['limit'], $order);
        foreach ($this->coupon_codes as $coupon_code) {
            $max_plus = $this->POST['max_num_plus/'.$coupon_code->id];
            if ($max_plus && (!$this->isNumeric($max_plus) || $max_plus < 0 || ($coupon_code->max_num + $max_plus)  >= CouponCode::MAX_NUM_LIMIT)) {
                $this->Validator->setError('max_num_plus/'.$coupon_code->id, 'INVALID_LIMIT');
            }
            if (!$this->POST['expire_date/'.$coupon_code->id]) {
                if (!$this->POST['non_expire_date/'.$coupon_code->id]) {
                    $this->Validator->setError('expire_date/'.$coupon_code->id, 'NOT_REQUIRED');
                }
            } else {
                $expire_date = strtotime($this->POST['expire_date/'.$coupon_code->id]);
                if (!$expire_date || ($expire_date < strtotime('today'))) {
                    $this->Validator->setError('expire_date/'.$coupon_code->id, 'INVALID_DATE');
                }
            }
        }

        if (!$this->Validator->isValid()) {
            return false;
        }

        return true;
    }

    function doAction() {

        try{
            $this->coupon_service->coupons->begin();

            $this->coupon_service->updateCoupon($this->POST['coupon_id'], $this->POST['name'], $this->POST['description'], $this->POST['distribution_type']);
            foreach ($this->coupon_codes as $coupon) {
                $coupon->expire_date = $this->POST['non_expire_date/'.$coupon->id] ? '0000-00-00 00:00:00' : date_create($this->POST['expire_date/'.$coupon->id])->format('Y-m-d H:i:s');
                $coupon->max_num += $this->POST['max_num_plus/'.$coupon->id] ? $this->POST['max_num_plus/'.$coupon->id] : 0;
                $this->coupon_service->updateCouponCode($coupon);
            }

            $this->coupon_service->coupons->commit();
        } catch (Exception $e) {
            $this->coupon_service->coupons->rollback();
            $logger = aafwLog4phpLogger::getDefaultLogger();
            $logger->error($e);
            return 'redirect: '. Util::rewriteUrl('admin-coupon', 'coupon_codes', array($this->POST['coupon_id'], array('mid' => 'failed', 'p' => $this->POST['page'])));
        }

        $this->Data['saved'] = 1;

        return 'redirect: '. Util::rewriteUrl('admin-coupon', 'coupon_codes', array($this->POST['coupon_id']), array('mid' => 'updated', 'p' => $this->POST['page']));
    }
}
