<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.classes.services.CpCreateSqlService');
AAFW::import('jp.aainc.aafw.parsers.PHPParser');

class api_get_search_brand_fan extends BrandcoGETActionBase {
    protected $ContainerName = 'api_get_search_brand_fan';

    public $NeedOption = array();
    protected $AllowContent = array('JSON');
    protected $search_condition;
    protected $order_condition;

    public function doThisFirst() {

        $this->Data['list_page'] = array(
            'page_no'  => $this->page_no ? $this->page_no : '1',
            'limit'    => $this->limit,
            'tab_no'   => CpCreateSqlService::TAB_PAGE_PROFILE,// タブのデフォルトは「プロフィール」
            'brand_id' => $this->getBrand()->id
        );

        $this->search_condition = $this->getBrandSession('searchBrandCondition');
        $this->order_condition = $this->getBrandSession('orderBrandCondition');
    }

    public function validate() {
        return true;
    }

    function doAction() {
        if (!$this->Data['list_page']['limit'] || !in_array($this->Data['list_page']['limit'], CpCreateSqlService::$display_items_range)) {
            $this->Data['list_page']['limit'] = CpCreateSqlService::DISPLAY_50_ITEMS;
        }

        /** @var CpUserListService $cp_user_list_service */
        $cp_user_list_service = $this->createService('CpUserListService');
        $fan_list = $cp_user_list_service->getDisplayFanListAndCount($this->Data['list_page'], $this->search_condition, $this->order_condition);

        /** @var BrandGlobalSettingService $brand_global_setting_service */
        $brand_global_setting_service = $this->getService('BrandGlobalSettingService');
        // ニックネーム等の個人情報非表示モード
        $brand_global_setting = $brand_global_setting_service->getBrandGlobalSettingByName(BrandInfoContainer::getInstance()->getBrandGlobalSettings(), BrandGlobalSettingService::HIDE_PERSONAL_INFO);
        $is_hide_personal_info = !Util::isNullOrEmpty($brand_global_setting) ? true : false;

        $parser = new PHPParser();
        $html = $this->sanitizeOutput($parser->parseTemplate(
            $this->getBrand()->id == 375 ? 'RecruitBrandUserList.php' : 'BrandUserList.php', array(
                'brand' => $this->getBrand(),
                'fan_list_users' => $fan_list,
                'list_page' => $this->Data['list_page'],
                'search_condition' => $this->search_condition,
                'is_hide_personal_info' => $is_hide_personal_info
            )
        ));

        $json_data = $this->createAjaxResponse("ok", array(), array(), $html);
        $this->assign('json_data', $json_data);
        return 'dummy.php';
    }


    function sanitizeOutput($html) {

        $search = array(
            '/\>[^\S ]+/s',  // strip whitespaces after tags, except space
            '/[^\S ]+\</s',  // strip whitespaces before tags, except space
            '/(\s)+/s'       // shorten multiple whitespace sequences
        );

        $replace = array(
            '>',
            '<',
            '\\1'
        );

        $html = preg_replace($search, $replace, $html);

        return $html;
    }
}
