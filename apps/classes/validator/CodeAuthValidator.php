<?php
AAFW::import('jp.aainc.classes.validator.BaseValidator');
AAFW::import('jp.aainc.classes.entities.CodeAuthenticationCode');

class CodeAuthValidator extends BaseValidator {
    const CODE_LENGTH_LIMIT = 255;

    protected $code_auth_id;
    protected $brand_id;

    public function __construct($code_auth_id = null, $brand_id = null) {
        parent::__construct();

        $this->brand_id = $brand_id;
        $this->code_auth_id = $code_auth_id;
    }

    public function validate ($codes = null, $csv_file = null) {
        if ($csv_file) {
            $this->isValidCodeAuthCsvFile($csv_file);
        }

        if ($codes) {
            $code_auth_codes = array();
            $code_array = Util::cutStringByLineBreak($codes);

            foreach ($code_array as $code) {
                $split_code = $this->splitTextToCode($code);

                if ($split_code) {
                    $code_auth_codes[] = $split_code[0];
                }
            }

            $this->isValidCodeAuthCodes($code_auth_codes);
        }
    }

    /**
     * @param $code
     * @return array|void
     */
    public function splitTextToCode ($code) {
        if (!trim($code)) return;

        $object = new aafwObject();

        $pattern = explode(",", trim($code));
        if (count($pattern) < 1 || count($pattern) > 3) {
            $this->errors[] = 'INVALID_COUPON_CODE';
            return null;
        }

        if ($pattern[1] && (!$object->isNumeric($pattern[1]) || $pattern[1] <= 0 || $pattern[1] >= CodeAuthenticationCode::MAX_NUM_LIMIT)) {
            $this->errors[] = 'INVALID_LIMIT';
            return null;
        }

        if ($pattern[2] && (!date_create($pattern[2]) || (strtotime($pattern[2]) < strtotime('today')))) {
            $this->errors[] = 'INVALID_DATE';
            return null;
        }

        return $pattern;
    }

    public function isValidCodeAuthCsvFile ($filename) {
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
                $code_auth_codes = array();

                foreach($code_array as $code) {
                    $split = $this->splitTextToCode($code);

                    if ($split) {
                        $code_auth_codes[] = $split[0];
                    }
                }

                $this->isValidCodeAuthCodes($code_auth_codes);
            }

            fclose($f);
        } catch (Exception $e) {
            $this->errors[] = 'OPEN_FILE_ERROR';
            return false;
        }
        return true;
    }

    public function isValidCodeAuthCodes($codes) {
        if (is_array($codes) && count($codes) <= 0) return true;

        foreach (array_count_values($codes) as $key => $value) {
            if ($value >= 2) {
                $this->errors[] = 'DOUBLE_CODE';
                return false;
            }
        }

        $service_factory = new aafwServiceFactory();
        $code_auth_service = $service_factory->create('CodeAuthenticationService');

        $code_auth_code = $code_auth_service->getCodeAuthCodeByCodeAndCodeAuthId($codes, $this->code_auth_id);
        if ($code_auth_code) {
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

        return true;
    }

    public function isValidCodeAuthId() {
        if (!$this->code_auth_id) return false;

        $service_factory = new aafwServiceFactory();
        $code_auth_service = $service_factory->create('CodeAuthenticationService');

        $code_auth = $code_auth_service->getCodeAuthById($this->code_auth_id);

        return $code_auth && $code_auth->brand_id == $this->brand_id;
    }
}