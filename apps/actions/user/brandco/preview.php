<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');

class preview extends BrandcoGETActionBase {

    public $NeedOption = array();
    public $NeedAdminLogin = true;

    public function validate() {
        return true;
    }

    function doAction() {
        $this->Data['preview_url'] = $this->getPreviewUrl(base64_decode($this->preview_url));
        $this->Data['brand'] = $this->getBrand();

        return 'user/brandco/preview.php';
    }

    /**
     * @param $url
     * @return string
     */
    private function getPreviewUrl($url) {
        $parsed_url = parse_url($url);

        parse_str($parsed_url['query'], $query_params);
        foreach ($query_params as $query_key => $query_param) {
            if ($query_key == 'preview') continue;

            unset($query_params[$query_key]);
        }
        $query = http_build_query($query_params);

        return sprintf('%s://%s%s?%s', $parsed_url['scheme'], $parsed_url['host'], $parsed_url['path'], $query);
    }
}
