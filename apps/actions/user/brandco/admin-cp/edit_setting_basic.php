<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');

class edit_setting_basic extends BrandcoGETActionBase {
    protected $ContainerName = 'save_setting_basic';

    public $NeedOption = array(BrandOptions::OPTION_CP);
    public $NeedAdminLogin = true;

    public function beforeValidate() {
        $this->deleteErrorSession();
    }

    public function validate()  {
        $cp_id = $this->GET['exts'][0];

        $brand = $this->getBrand();
        $validatorService = new CpValidator($brand->id);
        if (!$validatorService->isOwner($cp_id)) {
            return false;
        }

        $cp_service = $this->createService('CpFlowService');
        $this->Data['cp'] = $cp_service->getCpById($cp_id);
        return true;
    }

    function doAction() {
        $action_form = $this->Data['cp']->toArray();

        $cp_service = $this->createService('CpFlowService');
        $first_action = $cp_service->getFirstActionOfCp($this->Data['cp']->id);
        $action_form['title'] = $first_action->getCpActionData()->title;

        $cp_info_service = $this->createService('CpInfoService');
        $cp_info = $cp_info_service->getCpInfoByCpId($this->Data['cp']->id);
        $action_form['salesforce_id'] = $cp_info->salesforce_id;

        if ($action_form['public_date'] == '0000-00-00 00:00:00' && $action_form['start_date'] == '0000-00-00 00:00:00') {
            $action_form['use_public_date_flg'] = false;
        } else {
            $action_form['use_public_date_flg'] = ($action_form['public_date'] !== $action_form['start_date']);
        }

        if ($action_form['public_date'] == '0000-00-00 00:00:00') {
            $action_form['public_date'] = date('Y/m/d H:i:s', time() + 540);
        }
        $public_time = strtotime($action_form['public_date']);
        $action_form['public_date'] = date('Y/m/d', $public_time);
        $action_form['publicTimeHH'] = date('H', $public_time);
        $action_form['publicTimeMM'] = date('i', $public_time);

        if ($action_form['start_date'] == '0000-00-00 00:00:00') {
            $action_form['start_date'] = date('Y/m/d H:i:s', time() + 600);
        }
        $start_time = strtotime($action_form['start_date']);
        $action_form['start_date'] = date('Y/m/d', $start_time);
        $action_form['openTimeHH'] = date('H', $start_time);
        $action_form['openTimeMM'] = date('i', $start_time);

        if ($action_form['end_date'] == '0000-00-00 00:00:00') {
            $action_form['end_date'] = date('Y/m/d');
            $action_form['closeTimeHH'] = '23';
            $action_form['closeTimeMM'] = '59';
        } else {
            $end_time = strtotime($action_form['end_date']);
            $action_form['end_date'] = date('Y/m/d', $end_time);
            $action_form['closeTimeHH'] = date('H', $end_time);
            $action_form['closeTimeMM'] = date('i', $end_time);
        }

        if ($action_form['closeTimeHH'] == '23' && $action_form['closeTimeMM'] == '59') {
            $action_form['closeTimeDate'] = '1';
        }

        if ($action_form['announce_date'] == '0000-00-00 00:00:00') {
            $action_form['announce_date'] = date('Y/m/d H:i:s', time() + 87000);
        }
        $announce_time = strtotime($action_form['announce_date']);
        $action_form['announce_date'] = date('Y/m/d', $announce_time);

        if ($action_form['publish_date'] == '0000-00-00 00:00:00') {
            $action_form['publish_date'] = date('Y/m/d H:i:s', time() + 600);
        }
        $publish_time = strtotime($action_form['publish_date']);
        $action_form['publish_date'] = date('Y/m/d', $publish_time);
        $action_form['pubTimeHH'] = date('H', $publish_time);
        $action_form['pubTimeMM'] = date('i', $publish_time);

        if ($action_form['cp_page_close_date'] == '0000-00-00 00:00:00') {
            $action_form['cp_page_close_date'] = '';
        } else {
            $close_time = strtotime($action_form['cp_page_close_date']);
            $action_form['cp_page_close_date'] = date('Y/m/d', $close_time);
            $action_form['cpPageCloseTimeHH']  = date('H', $close_time);
            $action_form['cpPageCloseTimeMM']  = date('i', $close_time);
        }

        if($action_form['join_limit_sns_flg'] == cp::JOIN_LIMIT_SNS_ON) {
            if (is_array($this->Data['ActionForm'])) {
                $action_form['join_limit_sns'] = $this->Data['ActionForm']['join_limit_sns'];
            } else {
                $action_form['join_limit_sns'] = $this->Data['cp']->getJoinLimitSns();
            }
        }
        
        $this->Data['can_login_by_linked_in'] = $this->canLoginByLinkedIn();

        if (!$action_form['reference_url']) {
            $action_form['reference_url'] = $this->Data['cp']->getUrlPath($this->getBrand());
        }
        $path = explode('/', $action_form['reference_url']);
        if ($path[2] == 'page' && !Util::isNullOrEmpty($path[3])) {
            $this->Data['reference_url_type'] = Cp::REFERENCE_URL_TYPE_LP;
            $action_form['page_url'] = $path[3];
        } else {
            $this->Data['reference_url_type'] = Cp::REFERENCE_URL_TYPE_CP;
            $action_form['page_url'] = "";
        }
        $this->Data['reference_url_type'] = is_array($this->Data['ActionForm']) ? $this->Data['ActionForm']['reference_url_type'] : $this->Data['reference_url_type'];

        $action_form['restricted_address_flg'] = is_array($this->Data['ActionForm']) ? $this->Data['ActionForm']['restricted_address_flg'] : $action_form['restricted_address_flg'];
        if ($action_form['restricted_address_flg'] == Cp::CP_RESTRICTED_ADDRESS_FLG_ON) {
            $action_form['restricted_addresses'] = is_array($this->Data['ActionForm']) ? $this->Data['ActionForm']['restricted_addresses'] : $this->Data['cp']->getRestrictedAddresses();
        }

        $action_form['rectangle_flg'] = $action_form['image_rectangle_url'] ? Cp::FLAG_SHOW_VALUE : Cp::FLAG_HIDE_VALUE;

        $this->Data['status'] = $this->Data['cp']->fix_basic_flg;
        $this->Data['CpStatus'] = $this->Data['cp']->getStatus();
        $this->Data['isManager'] = $this->isLoginManager();

        $prefecture_service = $this->getService('PrefectureService');
        $this->Data['prefectures'] = $prefecture_service->getPrefecturesByRegion();

        $this->assign('ActionForm', $action_form);

        return 'user/brandco/admin-cp/edit_setting_basic.php';
    }

    public static function canShowByManager($cp_status, $is_manager) {
        return self::canEditCp($cp_status) && $is_manager;
    }

    public static function canEditCp($cp_status) {
        return in_array($cp_status, array(Cp::CAMPAIGN_STATUS_OPEN, Cp::CAMPAIGN_STATUS_WAIT_ANNOUNCE, Cp::CAMPAIGN_STATUS_CLOSE));
    }
}
