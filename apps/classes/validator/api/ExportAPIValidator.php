<?php
AAFW::import('jp.aainc.classes.validator.BaseValidator');

class ExportAPIValidator extends BaseValidator {

    const TYPE_CONTENT_API = 1;
    const TYPE_SNS_PANEL_API = 2;

    private $brand_id;
    private $code;

    private $api_code;

    private $service_factory;

    private $type;

    public function __construct($brand_id, $code, $type) {
        $this->brand_id = $brand_id;
        $this->code = $code;
        $this->type = $type;

        $this->service_factory = new aafwServiceFactory();
    }

    public function validate() {
        if (!$this->isValidParameter()) {
            return false;
        }

        if (!$this->isValidCode()) {
            return false;
        }

        return true;
    }

    private function isValidParameter() {
        if (!$this->brand_id) {
            $this->errors['message'][] = 'ブランドが存在しません';
            return false;
        }

        if (!$this->code) {
            $this->errors['message'][] = 'コードが存在しません';
            return false;
        }

        return true;
    }

    private function isValidCode() {
        if($this->type == self::TYPE_CONTENT_API){
            $api_code_service = $this->service_factory->create('ContentApiCodeService');
            $this->api_code = $api_code_service->getApiCodeByCode($this->code);
            $api_code_brand_id = $this->api_code->getCp()->brand_id;
        } else {
            $api_code_service = $this->service_factory->create('SnsPanelApiCodeService');
            $this->api_code = $api_code_service->getApiCodeByCode($this->code);
            $api_code_brand_id = $this->api_code->brand_id;
        }

        if (!$this->api_code) {
            $this->errors['message'][] = 'コードが存在しません';
            return false;
        }

        if ($api_code_brand_id != $this->brand_id) {
            $this->errors['message'][] = '正しいコードを入力して下さい';
            return false;
        }

        return true;
    }

    public function getApiCode() {
        return $this->api_code;
    }
}