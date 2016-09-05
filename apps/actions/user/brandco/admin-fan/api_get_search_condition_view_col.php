<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');
AAFW::import('jp.aainc.classes.services.CpCreateSqlService');
AAFW::import('jp.aainc.aafw.parsers.PHPParser');

class api_get_search_condition_view_col extends BrandcoPOSTActionBase {

    protected $ContainerName = 'api_get_search_condition_view_col';

    public $NeedOption = array();
    public $NeedAdminLogin = true;
    protected $AllowContent = array('JSON');

    private $search_conditions;

    public function validate() {
        $validator = new FacebookMarketingValidator($this->getBrandsUsersRelation()->id);

        if (!$this->cpdl_flg && !$validator->isValidMarketingAudienceId($this->audience_id)) {
            return '404';
        }

        if ($this->cpdl_flg) {
            if(!Util::isNullOrEmpty($this->POST['cp_id'])){
                $this->search_conditions = $this->getSearchConditionSession($this->POST['cp_id']);
            }else{
                $this->search_conditions = $this->getBrandSession('searchBrandCondition');
            }
        } else {
            $this->search_conditions = $this->getBrandSession('searchConditionFacebookMarketing');
        }

        return true;
    }

    public function doAction() {

        /** @var CpCreateSqlService $create_sql_service */
        $create_sql_service = $this->getService("CpCreateSqlService");
        $page_info = array('brand_id' => $this->getBrand()->id);
        if ($this->search_conditions["cp_id"]) {
            $page_info['cp_id'] = $this->search_conditions["cp_id"];
            /** @var CpFlowService $cp_flow_service */
            $cp_flow_service = $this->getService("CpFlowService");
            $first_action = $cp_flow_service->getFirstActionOfCp($this->search_conditions["cp_id"]);
            $page_info["action_id"] = $first_action->id;
        }

        $count_sql = $create_sql_service->getUserSql($page_info, $this->search_conditions, '', true, null);

        $db = aafwDataBuilder::newBuilder();
        $user_count = $db->getBySQL($count_sql, array());

        $html = "";
        if (!$this->cpdl_flg) {
            $this->search_conditions["audience_id"] = $this->audience_id;
            $this->search_conditions["brand_id"] = $this->getBrand()->id;

            $html = aafwWidgets::getInstance()->loadWidget('SearchConditionViewCol')->render(array('search_conditions' => $this->search_conditions));
        }

        $json_data = $this->createAjaxResponse("ok", array('target_count' => $user_count[0]['total_count'] ? $user_count[0]['total_count'] : 0), array(), $html);
        $this->assign('json_data', $json_data);
        return 'dummy.php';
    }
}
