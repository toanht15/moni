<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.classes.services.CpCreateSqlService');
AAFW::import('jp.aainc.classes.services.FanListDownloadService');

class fan_list_download extends BrandcoGETActionBase {
    const SEARCH_BRAND_SESSION = "searchBrandCondition";

    protected $ContainerName = 'fan_list_download';

    public $NeedOption = array(BrandOptions::OPTION_CP, BrandOptions::OPTION_CRM);
    public $NeedAdminLogin = true;
    /** @var CpFlowService $cp_flow_service */
    protected $cp_flow_service;
    protected $can_download_brand_fan_list;

    public function doThisFirst() {
        $this->Data['cp_id'] = $this->GET['exts'][0];
        $this->Data['brand'] = $this->getBrand();

        /** @var CpFlowService $cp_flow_service */
        $this->cp_flow_service = $this->getService('CpFlowService');

        /** BrandGlobalSettingService $brand_global_settings_service */
        $brand_global_setting_service = $this->getService('BrandGlobalSettingService');
        $this->can_download_brand_fan_list = $brand_global_setting_service->getBrandGlobalSettingByName(BrandInfoContainer::getInstance()->getBrandGlobalSettings(), BrandGlobalSettingService::CAN_DOWNLOAD_BRAND_FAN_LIST);

        if($this->Data['cp_id']){
            $this->Data['is_cp_data_download_mode'] = true;
        }
        //選択したcp_idを取得する
        if(!$this->Data['is_cp_data_download_mode'] && $this->GET['cpId']){
            $this->Data['is_brand_download_mode_with_cp_id'] = true;
            $this->Data['cp_id'] = $this->GET['cpId'];
        }
    }

    public function validate() {
        $cp_validator = new CpValidator($this->Data['brand']->id);

        if (!$this->Data['is_cp_data_download_mode'] && !$this->can_download_brand_fan_list) {
            return false;
        }

        if ($this->Data['is_cp_data_download_mode']  || $this->Data['is_brand_download_mode_with_cp_id']) {
            if(!$cp_validator->isOwner($this->Data['cp_id'])) return false;

            $cp = $this->cp_flow_service->getCpById($this->Data['cp_id']);
            if ($cp->status == Cp::STATUS_DRAFT) return '404';
        }

        return true;
    }

    function doAction() {

        if ($this->r) {
            // セッション削除
            if($this->Data['is_cp_data_download_mode'] || $this->Data['is_brand_download_mode_with_cp_id']){
                $this->setSearchConditionSession($this->Data['cp_id'],null);
            }else{
                $this->setBrandSession(self::SEARCH_BRAND_SESSION, null);
            }
            $this->Data['search_condition'] = null;
        }

        $this->Data['dl_date'] = date('Ymd');

        if($this->Data['is_cp_data_download_mode'] || $this->Data['is_brand_download_mode_with_cp_id']){
            $search_condition = $this->getSearchConditionSession($this->Data['cp_id']);
            $search_condition['cp_id'] = $this->Data['cp_id'];
            $this->setSearchConditionSession($this->Data['cp_id'], $search_condition);

            $this->Data['first_cp_action'] = $this->cp_flow_service->getEntryActionByCpId($this->Data['cp_id']);

            $brand_global_setting_service = $this->getService('BrandGlobalSettingService');
            $this->Data['can_use_aaid_hash_tag'] = $brand_global_setting_service->getBrandGlobalSetting(
                $this->Data['brand']->id,
                BrandGlobalSettingService::CAN_USE_AAID_HASH_TAG
            );

            // ダウンロード可能なデータリストを用意
            $this->getDownloadableActionList($this->Data['cp_id']);

        } else {
            $search_condition = $this->getBrandSession(self::SEARCH_BRAND_SESSION);
        }

        $this->Data['search_condition'] = $search_condition;
        $this->Data['search_condition']['search_no'] = "1";
        $this->Data['search_condition']['brand_id'] = $this->Data['brand']->id;

        // 個人情報端末からもDLする必要が出てくる。ただし、一部のJSは実行できなくしている。
        $this->Data['personal_pc'] = Util::isPersonalMachine();

        return 'user/brandco/admin-cp/fan_list_download.php';
    }

    /**
     * ダウンロード可能なデータリストを用意
     * @param $cp_id
     */
    private function getDownloadableActionList($cp_id){
        $cp_actions = $this->cp_flow_service->getCpActionsOrderByStepNoByCpId($cp_id);
        foreach ($cp_actions as $step_no => $cp_action) {
            if (FanListDownloadService::$download_file_name[$cp_action['type']]) {
                $this->Data["download_file_list"][$cp_action['id']]['type'] = $cp_action['type'];
                $this->Data['download_file_list'][$cp_action['id']]['file_name'] =
                    '(' . $this->Data['dl_date']
                    . '_Step' . $step_no . '_'
                    . FanListDownloadService::$download_file_name[$cp_action['type']]
                    . '.csv)';
                $this->Data["download_file_list"][$cp_action['id']]['file_info'] =
                    'Step' . $step_no . ' '
                    . $this->cp_flow_service->getCpActionById($cp_action['id'])->getCpActionDetail()['title'];
            }
        }
    }
}
