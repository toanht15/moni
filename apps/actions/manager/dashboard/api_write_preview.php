<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoManagerGETActionBase');
AAFW::import('jp.aainc.classes.entities.BrandContract');

class api_write_preview extends BrandcoManagerGETActionBase {
    protected $AllowContent = array('JSON');

    public $NeedManagerLogin = true;

    public function validate() {
        return true;
    }

    public function doAction() {
        try {
            $redis = aafwRedisManager::getRedisInstance();
            $key = BrandContract::PREVIEW_PREFIX . ':' . $this->POST['brand_id'] . ':' . BrandContract::CLOSED_PAGE_PREVIEW_KEY;

            $redis->set($key, json_encode($this->POST));
            $redis->setTimeout($key, BrandContract::SESSION_TIMEOUT);
        } catch (Exception $e) {
            $logger = aafwLog4phpLogger::getDefaultLogger();
            $logger->error($e);
        } finally {
            if ($redis) {
                $redis->close();
            }
        }

        $brand_service = $this->createService('BrandService');
        $brand = $brand_service->getBrandById($this->POST['brand_id']);
        $brand_url = $brand->getUrl();

        $preview_page = $brand_url . 'closed?preview=' . BrandContract::SESSION_PREVIEW_MODE;
        $data = array(
            'preview_url' => $brand_url . 'preview?preview_url=' . base64_encode($preview_page)
        );

        $json_data = $this->createAjaxResponse('ok', $data);
        $this->assign('json_data', $json_data);
        return 'dummy.php';
    }
}