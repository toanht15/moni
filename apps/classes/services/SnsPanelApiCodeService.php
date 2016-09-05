<?php
AAFW::import('jp.aainc.lib.base.aafwServiceBase');

class SnsPanelApiCodeService extends aafwServiceBase {

    const DEFAULT_LIMIT = 20;

    protected $api_end_point_url = "sns_panels";
    protected $sns_panel_api_codes;

    public function __construct() {
        $this->sns_panel_api_codes = $this->getModel('SnsPanelApiCodes');
    }

    /**
     * @return mixed
     */
    public function createEmptyApiCode() {
        return $this->sns_panel_api_codes->createEmptyObject();
    }

    /**
     * @param $api_code
     */
    public function createApiCode($api_code) {
        $this->sns_panel_api_codes->save($api_code);
    }

    /**
     * @param $api_code
     */
    public function updateApiCode($api_code) {
        $this->sns_panel_api_codes->save($api_code);
    }

    /**
     * @param $brand_id
     * @return mixed
     */
    public function getApiCodeByBrandId($brand_id) {
        $filter = array(
            'brand_id' => $brand_id
        );

        return $this->sns_panel_api_codes->findOne($filter);
    }

    /**
     * @param $code
     * @return mixed
     */
    public function getApiCodeByCode($code) {
        $filter = array(
            'code' => $code
        );

        return $this->sns_panel_api_codes->findOne($filter);
    }

    /**
     * @param $prefix
     * @return string
     */
    public function generateCode($prefix) {
        return md5(uniqid($prefix, true));
    }

    public function getUrl($code, $limit = self::DEFAULT_LIMIT, $p = 1, $page = 0, $link = 0, $photo = 0, $brand_social_account_ids = array()) {
        $api_params = $this->getApiQueryParams($code, $limit, $p, $page, $link, $photo, $brand_social_account_ids);
        $api_end_point_url = $this->api_end_point_url . '.json';

        return Util::rewriteUrl('api', $api_end_point_url, array(), $api_params);
    }

    public function getApiQueryParams($code, $limit = self::DEFAULT_LIMIT, $p = 1, $page, $link = 0, $photo = 0, $brand_social_account_ids = array()) {
        $query_params = array();

        $query_params['code'] = $code;

        if ($p != 1) {
            $query_params['p'] = $p;
        }

        if ($limit != self::DEFAULT_LIMIT) {
            $query_params['limit'] = $limit;
        }

        if($page){
            $query_params['page'] = $page;
        }

        if($link) {
            $query_params['link'] = $link;
        }

        if($photo) {
            $query_params['photo'] = $photo;
        }

        if(count($brand_social_account_ids)){
            $query_params['sns_ids'] = urlencode(implode(',',$brand_social_account_ids));
        }

        return $query_params;
    }
}