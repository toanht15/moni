<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');

class api_get_ads_list_audience extends BrandcoGETActionBase {

    protected $ContainerName = 'ads_list';
    protected $AllowContent = array('JSON');
    public $NeedAdminLogin = true;

    public $NeedOption = array(BrandOptions::OPTION_FACEBOOK_ADS, BrandOptions::OPTION_TWITTER_ADS);
    const LIMIT_QUERY_COUNT = 10;

    private $target_ads_account_ids;

    public function doThisFirst() {
        parse_str($this->target_account_ids, $ads_account_id_parameter_array);

        foreach($ads_account_id_parameter_array as $key => $value) {
            if (strpos($key, 'ads_account') !== false) {
                $this->target_ads_account_ids = $value;
            }
        }
    }

    public function validate() {

        $validator = new AdsValidator($this->getBrandsUsersRelation()->id);

        foreach($this->target_ads_account_ids as $account_id) {

            if(!$validator->isValidAdsAccountId($account_id)) {
                return false;
            }
        }

        return true;
    }

    public function getFormURL () {
        $json_data = $this->createAjaxResponse("ng");
        $this->assign('json_data', $json_data);
        return 'dummy.php';
    }

    public function doAction() {

        $audiences = $this->fetchListAudience();

        $html = aafwWidgets::getInstance()->loadWidget('AdsAudienceList')->render(
            array(
                'brand_user_relation_id' => $this->getBrandsUsersRelation()->id,
                'target_account_ids' => $this->target_ads_account_ids,
                'audiences' => $audiences['list'],
                'count' => $audiences['pager']['count'],
                'page_no' => $this->page_no,
                'count_per_page' => self::LIMIT_QUERY_COUNT,
            ));

        $json_data = $this->createAjaxResponse("ok", array(), array(), $html);
        $this->assign('json_data', $json_data);

        return 'dummy.php';
    }

    private function fetchListAudience() {

        $db = aafwDataBuilder::newBuilder();

        $conditions = $this->getConditions();
        $pager = $this->getPager();
        $order = $this->getOrder();

        $result = $db->getAdsAudiences($conditions,$order,$pager,true);

        return $result;
    }

    private function getConditions() {
        $conditions = array();

        $conditions['brand_user_relation_id'] = $this->getBrandsUsersRelation()->id;

        if($this->target_ads_account_ids) {
            $conditions['SEARCH_BY_ACCOUNT'] = '__ON__';
            $conditions['target_account_ids'] = $this->target_ads_account_ids;
        }

        return $conditions;
    }

    private function getPager() {

        if(Util::isNullOrEmpty($this->page_no)) {
            $this->page_no = 1;
        }

        $pager = array(
            'page'  => $this->page_no,
            'count' => self::LIMIT_QUERY_COUNT,
        );

        return $pager;
    }

    private function getOrder() {
        return array(
            'name' => 'r.updated_at',
            'direction' => 'DESC',
        );
    }
}
