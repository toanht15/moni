<?php
AAFW::import('jp.aainc.classes.validator.BaseValidator');
AAFW::import('jp.aainc.classes.entities.CouponCode');

class CouponValidator extends BaseValidator {
    const CODE_LENGTH_LIMIT = 255;

    protected $coupon_id;
    protected $brand_id;

    public function __construct($coupon_id = null, $brand_id = null) {
        parent::__construct();
        $this->brand_id = $brand_id;
        $this->setCouponId($coupon_id);
    }

    public function validate ($codes = null, $csv_file = null) {
        if ($codes) {
            $code_array = Util::cutStringByLineBreak($codes);
            $coupon_codes = array();
            foreach ($code_array as $code) {
                $split = $this->splitTextToCode($code);
                if ($split) {
                    $coupon_codes[] = $split[0];
                }
            }
            $this->isValidCouponCodes($coupon_codes);
        }

        if ($csv_file) {
            $this->isValidCouponCsvFile($csv_file);
        }
    }

    public function setCouponId ($coupon_id) {
        $this->coupon_id = $coupon_id;
    }

    /**
     * @param $code
     * @return bool
     */
    public function splitTextToCode ($code) {

        if (!trim($code)) {
            return null;
        }

        $object = new aafwObject();

        $pattern = explode(",", trim($code));
        if (count($pattern) < 1 || count($pattern) > 3) {
            $this->errors[] = 'INVALID_COUPON_CODE';
            return null;
        }

        $pattern[1] = trim($pattern[1]);
        if ($pattern[1] && (!$object->isNumeric($pattern[1]) || $pattern[1] <= 0 || $pattern[1] >= CouponCode::MAX_NUM_LIMIT)) {
            $this->errors[] = 'INVALID_LIMIT';
            return null;
        }

        if ($pattern[2] && (!date_create($pattern[2]) || (strtotime($pattern[2]) < strtotime('today')))) {
            $this->errors[] = 'INVALID_DATE';
            return null;
        }

        return $pattern;
    }

    public function isValidCouponCsvFile ($filename) {
        try {
            $data = '';
            $f = fopen($filename, 'rb');
            while (!feof($f)) {
                $data .= fread($f, filesize($filename));
            }

            $code_array = Util::cutStringByLineBreak($data);

            array_shift($code_array);//先頭の１行を削除
            if (!$code_array || count($code_array) === 0) {
                $this->errors[] = 'EMPTY_FILE';
            } else {
                $coupon_codes = array();
                foreach($code_array as $code) {
                    $split = $this->splitTextToCode($code);
                    if ($split) {
                        $coupon_codes[] = $split[0];
                    }
                }
                $this->isValidCouponCodes($coupon_codes);
            }

            fclose($f);
        } catch (Exception $e) {
            $this->errors[] = 'OPEN_FILE_ERROR';
            return false;
        }
        return true;
    }

    public function isValidCouponCodes($codes) {
        if (is_array($codes) && count($codes) <= 0) {
            return true;
        }
        foreach (array_count_values($codes) as $key => $value) {
            if ($value >= 2) {
                $this->errors[] = 'DOUBLE_CODE';
                return false;
            }
        }
        $service_factory = new aafwServiceFactory();
        /** @var CouponService $coupon_service */
        $coupon_service = $service_factory->create('CouponService');
        $coupon_code = $coupon_service->getCouponCodeByCodeAndCouponId($codes, $this->coupon_id);
        if ($coupon_code) {
            $this->errors[] = 'EXISTED_CODE';
            return false;
        }

        $object = new aafwObject();

        foreach ($codes as $code) {
            if (!$object->inStrLen($code, self::CODE_LENGTH_LIMIT, true)) {
                $this->errors[] = 'CODE_TOO_LONG';
                return false;
            }
        }
    }

    public function isValidCouponId() {
        if (!$this->coupon_id) {
            return false;
        }
        $service_factory = new aafwServiceFactory();
        /** @var CouponService $coupon_service */
        $coupon_service = $service_factory->create('CouponService');
        $coupon = $coupon_service->getCouponById($this->coupon_id);
        if (!$coupon || $coupon->brand_id != $this->brand_id) {
            return false;
        }
        return true;
    }
}