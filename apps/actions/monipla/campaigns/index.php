<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.classes.brandco.cp.CpEntryActionManager');
AAFW::import('jp.aainc.classes.services.UserService');

class index extends aafwGETActionBase {

    public $Secure = false;

    /** @var CpFlowService cp_flow_service */
    protected $cp_flow_service;
    protected $AllowContent = array('JSON');
    private $api_data = array();

    public function validate () {
        if (!$this->app_id) {
            $error = 'app_id is required.';
            $json_data = $this->createApiResponse('false', $this->api_data, $error);
            $this->assign('json_data', $json_data);
            return false;
        }
        return true;
    }

    function doAction() {

        $application_key_array = array_keys(ApplicationService::$ApplicationMaster);

        $this->cp_flow_service = $this->createService('CpFlowService');
        $cps = $this->cp_flow_service->getPublicCpsForMonipla($application_key_array);

        $this->setApiData($cps);

        $json_data = $this->createApiResponse('true', $this->api_data);
        $this->assign('json_data', $json_data);
        return 'dummy.php';
    }

    private function setApiData($cps) {

        $cps_info = [];
        foreach ($cps as $cp) {

            list($cp_action, $entry_action) = $this->cp_flow_service->getEntryActionInfoByCpId($cp->id);
            $cp_info['id'] = $cp->id;
            $cp_info['cp_url'] = Util::getCpURL($cp->brand_id, $cp->directory_name, $cp->id);
            $cp_info['title'] = $entry_action->title;
            $cp_info['text'] = $entry_action->text;
            $cp_info['start_date'] = $cp->start_date;
            $cp_info['end_date'] = $cp->end_date;
            $cp_info['announce_date'] = $cp->announce_date;
            $cp_info['image_url'] = $cp->image_url;
            $cp_info['winner_count'] = $cp->winner_count;
            $cp_info['winner_label'] = $cp->show_winner_label ? $cp->winner_label : "";
            $cp_info['open_flg'] = $cp->show_monipla_com_flg;
            $cp_info['created_at'] = $cp->created_at;
            $cp_info['updated_at'] = $cp->updated_at;
            $cp_info['brand']["id"] = $cp->brand_id;
            $cp_info['brand']["app_id"] = $cp->app_id;
            $cp_info['brand']["enterprise_id"] = $cp->enterprise_id;
            $cp_info['brand']["name"] = $cp->name;
            $cp_info['brand']["profile_img_url"] = $cp->profile_img_url;
            $cps_info[] = $cp_info;
        }
        $this->api_data['created_at'] = date('Y-m-d H:i:s');
        $this->api_data['campaign'] = $cps_info;
    }
}