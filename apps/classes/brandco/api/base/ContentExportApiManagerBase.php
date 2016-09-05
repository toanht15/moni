<?php
AAFW::import('jp.aainc.classes.validator.api.ExportAPIValidator');
AAFW::import('jp.aainc.classes.services.ContentApiCodeService');

abstract class ContentExportApiManagerBase {

    const DEFAULT_PAGE = 1;
    const RESPONSE_TIME_LIMIT = 2;   //APIのレスポンスタイムリミット

    private $brand;
    protected $api_code;
    protected $service_factory;

    protected $code;
    protected $limit;
    protected $max_id;
    protected $callback;
    protected $directory_name;
    protected $hipchat_logger;

    public function __construct($init_data) {
        $this->service_factory = new aafwServiceFactory();

        $this->code             = $init_data['code'];
        $this->limit            = $init_data['limit'] && is_numeric($init_data['limit']) ? $init_data['limit'] : ContentApiCodeService::DEFAULT_LIMIT;
        $this->max_id           = $init_data['next_id'] && is_numeric($init_data['next_id']) ? $init_data['next_id'] : 0;
        $this->callback         = $init_data['callback'] ? $init_data['callback'] : '';
        $this->directory_name   = $init_data['directory_name'] ? $init_data['directory_name'] : '';
        $this->hipchat_logger   = aafwLog4phpLogger::getHipChatLogger();
    }

    protected function validate() {
        if ($this->callback && strlen($this->callback) > 512) {
            $json_data = $this->createResponseData('ng', array(), array('message' => 'Invalid callback: ' . $this->callback));
            return $json_data;
        }

        $export_api_validator = new ExportAPIValidator($this->getBrand()->id, $this->code, ExportAPIValidator::TYPE_CONTENT_API);

        if (!$export_api_validator->validate()) {
            $json_data = $this->createResponseData('ng', array(), $export_api_validator->getErrors());
            return $json_data;
        }

        $this->api_code = $export_api_validator->getApiCode();
        return true;
    }

    /**
     * @return string
     */
    public function doProgress() {
        if ($this->getBrand() && $this->getBrand()->test_page == Brand::BRAND_TEST_PAGE) {
            $page_settings_service = $this->service_factory->create('BrandPageSettingService');
            $brand_page_setting = $page_settings_service->getPageSettingsByBrandId($this->getBrand()->id);

            switch (true) {
                case !$brand_page_setting->test_id || !$brand_page_setting->test_pass:
                case !isset($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']):
                case $_SERVER['PHP_AUTH_USER'] != $brand_page_setting->test_id:
                case $_SERVER['PHP_AUTH_PW'] != $brand_page_setting->test_pass:
                    header('WWW-Authenticate: Basic realm="Please log in with brand\'s account"');
                    header('Content-Type: text/plain; charset=utf-8');

                    die('このページを見るにはログインが必要です');
            }
        }

        $validate_result = $this->validate();
        if ($validate_result !== true) {
            return $validate_result;
        }

        $begin_time = time();
        $response_data = $this->doSubProgress();
        $end_time = time();

        $response_time = $end_time - $begin_time;

        //APIのレスポンスタイムはリミットを超える場合、hipchatに通知します。
        if ($response_time > self::RESPONSE_TIME_LIMIT) {
            $this->hipchat_logger->warn(get_class($this)." : APIのレスポンスタイムが".self::RESPONSE_TIME_LIMIT."秒を超えました({$response_time}秒)");
        }

        return $response_data;
    }

    /**
     * @param $result
     * @param array $data
     * @param array $errors
     * @param array $pagination
     * @return string
     */
    protected function createResponseData($result, $data = array(), $errors = array(), $pagination = array()) {
        $response_data = json_encode(array('result' => $result, 'pagination' => $pagination, 'data' => $data, 'errors' => $errors, 'html' => ""));

        if ($this->callback) {
            return $this->callback . '(' . $response_data . ')';
        }

        return $response_data;
    }

    /**
     * @return mixed
     */
    protected function getBrand() {
        if ($this->brand == null) {
            $brand_service = $this->service_factory->create('BrandService');

            $mapped_brand_id = Util::getMappedBrandId();
            if ( $mapped_brand_id === Util::NOT_MAPPED_BRAND ) {
                $this->brand = $brand_service->getBrandByDirectoryName($this->directory_name);
            } else {
                $this->brand = $brand_service->getBrandById($mapped_brand_id);
            }
        }

        return $this->brand;
    }

    abstract public function doSubProgress();

    abstract public function getApiExportData($data, $brand = null);
}