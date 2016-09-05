<?php
AAFW::import('jp.aainc.lib.base.aafwServiceBase');
AAFW::import ( 'jp.aainc.aafw.db.aafwDataBuilder' );

class CouponService extends aafwServiceBase {
    /** @var aafwEntityStoreBase $coupons */
    public $coupons;
    /** @var aafwEntityStoreBase $coupon_codes */
    private $coupon_codes;

    private $logger;

    private $db;

    public function __construct ()
    {
        $this->coupons = $this->getModel("Coupons");
        $this->coupon_codes = $this->getModel("CouponCodes");
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
        $this->db = aafwDataBuilder::newBuilder();
    }

    /**
     * @param $brand_id
     * @param $name
     * @param $description
     */
    public function createCoupon ($brand_id, $name, $description) {
        $coupon = $this->coupons->createEmptyObject();
        $coupon->brand_id = $brand_id;
        $coupon->name = $name;
        $coupon->description = $description;
        return $this->coupons->save($coupon);
    }

    /**
     * @param $coupon_id
     * @param $code
     * @param $expire_date
     * @param $limit
     * @return mixed
     */
    public function createCouponCode ($coupon_id, $code, $expire_date, $limit) {
        $coupon_code = $this->coupon_codes->createEmptyObject();
        $coupon_code->coupon_id = $coupon_id;
        $coupon_code->code = $code;
        $coupon_code->expire_date = $expire_date;
        $coupon_code->max_num = $limit;
        return $this->coupon_codes->save($coupon_code);
    }

    /**
     * @param $coupon_id
     * @param $coupon_codes
     */
    public function createCouponCodes ($coupon_id, $code_array) {
        if (count($code_array) === 0) {
            return;
        }
        try {
            $this->coupon_codes->begin();
            foreach ($code_array as $code) {
                if (!trim($code)) {
                    continue;
                }
                $pattern = explode(",", trim($code));
                if (!$pattern[1]) {
                    $pattern[1] = 1;
                }
                if (!$pattern[2]) {
                    $this->createCouponCode ($coupon_id, $pattern[0], '0000-00-00 00:00:00', $pattern[1]);
                } else {
                    $this->createCouponCode ($coupon_id, $pattern[0], date_create($pattern[2])->format('Y-m-d H:i:s'), $pattern[1]);
                }
            }
            $this->coupon_codes->commit();
        } catch (Exception $e) {
            $this->coupon_codes->rollback();
            $this->logger->error('Cant create coupon code coupon_id = '.$coupon_id);
            $this->logger->error($e);
        }
    }

    /**
     * @param $coupon_id
     * @param $name
     * @param $description
     */
    public function updateCoupon($coupon_id, $name, $description, $distribution_type) {
        $coupon = $this->getCouponById($coupon_id);
        $coupon->name = $name;
        $coupon->description = $description;
        $coupon->distribution_type = $distribution_type;
        $this->coupons->save($coupon);
    }

    /**
     * @param $coupon
     */
    public function saveCoupon($coupon) {
        $this->coupons->save($coupon);
    }

    /**
     * @param $coupon_code
     */
    public function updateCouponCode($coupon_code) {
        $this->coupon_codes->save($coupon_code);
    }
    /**
     * @param $brand_id
     * @param $page
     * @param $limit
     * @param $order
     * @return aafwEntityContainer|array
     */
    public function getCouponsByBrandId ($brand_id, $page, $limit, $order = null) {
        $filter = array(
            'conditions' => array(
                'brand_id' => $brand_id
            ),
            'order' => $order,
            'pager' => array(
                'page' => $page,
                'count' => $limit,
            )
        );
        return $this->coupons->find($filter);
    }

    /**
     * @param $brand_id
     * @return aafwEntityContainer|array
     */
    public function getAllCouponsByBrandId($brand_id) {
        return $this->coupons->find(array('brand_id' => $brand_id));
    }

    /**
     * @param $brand_id
     * @param $winner_count
     * @return array
     */
    public function getCpCouponByBrandIdAndWinnerCount ($brand_id, $winner_count) {
        $coupons = $this->getAllCouponsByBrandId($brand_id);

        $available_coupon = array();
        foreach ($coupons as $coupon) {
            list ($reserved, $total) = $this->getCouponStatisticByCouponId($coupon->id);
            if (($total - $coupon->countReservedNum()) >= $winner_count) {
                $available_coupon[] = $coupon;
            }
        }
        return $available_coupon;
    }

    /**
     * @param $coupon_id
     * @param $page
     * @param $limit
     * @param null $order
     * @return aafwEntityContainer|array
     */
    public function getCouponCodeByCouponId ($coupon_id, $page, $limit, $order = null) {
        $filter = array(
            'conditions' => array(
                'coupon_id' => $coupon_id
            ),
            'pager' => array(
                'page' => $page,
                'count' => $limit
            ),
            'order' => $order
        );
        return $this->coupon_codes->find($filter);
    }

    /**
     * @param $id
     * @return entity
     */
    public function getCouponById ($id) {
        return $this->coupons->findOne($id);
    }

    /**
     * @param $coupon_id
     * @return mixed
     */
    public function getRandomCouponCodeByCouponId($coupon_id) {
        $count_sql = 'SELECT cc.id FROM coupon_codes cc WHERE cc.coupon_id = '.$coupon_id.' && cc.max_num > cc.reserved_num && cc.del_flg = 0 ORDER BY RAND() LIMIT 1';

        $args = array('__NOFETCH__');
        $rs = $this->db->getBySQL($count_sql, $args);

        return $this->db->fetch($rs)['id'];
    }

    /**
     * クーポン配布のため、クーポンコードを取得する
     * @param $coupon_id
     * @return mixed
     */
    public function getCouponCodeForDistribute($coupon_id) {
        $coupon = $this->getCouponById($coupon_id);

        //クーポンの配布種類はランダムの場合は
        if ($coupon->distribution_type == Coupon::DISTRIBUTION_TYPE_RANDOM) {
            return $this->getRandomCouponCodeByCouponId($coupon_id);
        }

        //クーポンの配布種類は登録順の場合は
        return $this->getCouponCodeByRegisterOrder($coupon_id);
    }

    /**
     * 登録順でクーポンコードを取得する
     * @param $coupon_id
     * @return mixed
     */
    public function getCouponCodeByRegisterOrder ($coupon_id) {
        $conditions = array(
            'coupon_id'   => $coupon_id,
            '__NOFETCH__' => true
        );

        $order = array(
            'name' => 'id',
            'direction' => 'asc'
        );

        $rs = $this->db->getAvailableCouponCodeByCouponId($conditions, $order);

        return $this->db->fetch($rs)['id'];
    }

    /**
     * @param $coupon_code_id
     * @return mixed
     */
    public function incrementReservedNum($coupon_code_id) {
        if (!$coupon_code_id) return null;
        $coupon_code = $this->getCouponCodeById($coupon_code_id);
        $coupon_code->reserved_num += 1;
        return $this->coupon_codes->save($coupon_code);
    }

    /**
     * @param $coupon_id
     * @return array
     */
    public function getCouponStatisticByCouponId($coupon_id) {

        $total = $this->getSumOfCouponCodeColumn($coupon_id, 'max_num');
        $reserved_num = $this->getSumOfCouponCodeColumn($coupon_id, 'reserved_num');;
        
        return array($reserved_num, $total);
    }

    /**
     * @param $codes
     * @param $coupon_id
     * @return aafwEntityContainer|array
     */
    public function getCouponCodeByCodeAndCouponId($codes, $coupon_id) {
        $filter = array(
            'conditions' => array(
                'code' => $codes,
                'coupon_id' => $coupon_id
            )
        );
        return $this->coupon_codes->find($filter);
    }

    /**
     * @param $coupon_id
     * @param $column_name
     * @return 合計
     */
    public function getSumOfCouponCodeColumn($coupon_id, $column_name) {
        $filter = array(
            'conditions' => array(
                'coupon_id' => $coupon_id
            )
        );
        return $this->coupon_codes->getSum($column_name, $filter);
    }

    /**
     * @param $coupon_code_id
     * @return entity
     */
    public function getCouponCodeById($coupon_code_id) {
        return $this->coupon_codes->findOne($coupon_code_id);
    }

    /**
     * @param $brand_id
     * @return 件数
     */
    public function countCouponByBrandId ($brand_id) {
        return $this->coupons->count(array('brand_id' => $brand_id));
    }

    /**
     * @param $coupon_id
     * @return 件数
     */
    public function countCouponCodeByCouponId($coupon_id) {
        return $this->coupon_codes->count(array('conditions' => array('coupon_id' => $coupon_id)));
    }

    /**
     * @param $coupon_id
     * @throws aafwException
     */
    public function deleteCouponAndCouponCodes($coupon_id) {
        try {
            $coupon_codes = $this->getCouponCodeByCouponId($coupon_id, null, null);
            $this->coupon_codes->begin();

            foreach ($coupon_codes as $coupon_codes) {
                $this->coupon_codes->delete($coupon_codes);
            }
            $coupon = $this->getCouponById($coupon_id);
            $this->coupons->delete($coupon);

            $this->coupon_codes->commit();
        } catch (Exception $e) {
            $this->coupon_codes->rollback();
            $this->logger->error($e);
        }
    }
}