<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');

class save_coupon_codes extends BrandcoPOSTActionBase {
    protected $ContainerName = 'update_coupon';
    protected $Form = array(
        'package' => 'admin-coupon',
        'action' => 'edit_coupon_codes/{coupon_id}',
    );

    public $NeedOption = array();
    public $NeedAdminLogin = true;
    public $CsrfProtect = true;

    protected $ValidatorDefinition = array(
        'coupon_codes' => array(
            'type' => 'str'
        ),
        'csv_file' => array(
            'type' => 'file'
        )
    );

    public function doThisFirst() {
        set_time_limit(1800);
        if (!$this->coupon_codes && !$this->FILES['csv_file']) {
            $this->ValidatorDefinition['coupon_codes']['required'] = true;
        }

    }

    public function validate() {

        $coupon_validator = new CouponValidator($this->POST['coupon_id'], $this->brand->id);

        if (!$coupon_validator->isValidCouponId()) {
            return '404';
        }

        $coupon_validator->validate($this->POST['coupon_codes']);
        if (!$coupon_validator->isValid()) {
            $this->Validator->setError('coupon_codes', $coupon_validator->getErrors()[0]);
        }

        // csvファイルチェック
        if ($this->FILES['csv_file']) {
            $fileValidator = new FileValidator($this->FILES['csv_file'], FileValidator::FILE_TYPE_CSV);
            if (!$fileValidator->isValidFile()) {
                $this->Validator->setError('csv_file', 'NOT_MATCHES');
            } else {
                $coupon_validator->validate(null, $this->FILES['csv_file']['name']);
                if (!$coupon_validator->isValid()) {
                    $this->Validator->setError('csv_coupon_codes', $coupon_validator->getErrors()[0]);
                }
            }
        }

        if (!$this->Validator->isValid()) {
            return false;
        }
        return true;
    }

    function doAction() {

        /** @var CouponService $coupon_service */
        $coupon_service = $this->createService('CouponService');
        if (!$this->isEmpty($this->POST['coupon_codes'])) {
            $coupon_service->createCouponCodes($this->POST['coupon_id'], Util::cutStringByLineBreak($this->POST['coupon_codes']));
        }

        if ($this->FILES['csv_file']) {
            $data = '';
            try {
                $f = fopen($this->FILES['csv_file']['name'], 'rb');
                while (!feof($f)) {
                    $data .= fread($f, filesize($this->FILES['csv_file']['name']));
                }
                fclose($f);

                $data = Util::convertEncoding($data);
                $code_array = Util::cutStringByLineBreak($data);
                array_shift($code_array);//1行目は項目名のため削除

                $coupon_service->createCouponCodes($this->POST['coupon_id'], $code_array);
            } catch (Exception $e) {
                $logger = aafwLog4phpLogger::getDefaultLogger();
                $logger->error($e);
            }
        }

        $this->Data['saved'] = 1;

        $return = 'redirect: '. Util::rewriteUrl('admin-coupon', 'edit_coupon_codes', array($this->POST['coupon_id']), array('mid' => 'updated'));

        return $return;
    }
}
