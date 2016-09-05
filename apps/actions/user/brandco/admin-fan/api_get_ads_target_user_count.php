<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');

class api_get_ads_target_user_count extends BrandcoPOSTActionBase {

    protected $ContainerName = 'create_ads_audience';
    protected $AllowContent = array('JSON');
    public $NeedAdminLogin = true;

    public $NeedOption = array(BrandOptions::OPTION_FACEBOOK_ADS, BrandOptions::OPTION_TWITTER_ADS);
    public $CsrfProtect = true;

    public function validate() {
        return true;
    }

    public function doAction() {
        /** @var SegmentingUserDataService $segmenting_data_service */
        $segmenting_data_service = $this->getService('SegmentingUserDataService');
        /** @var SegmentService $segment_service */
        $segment_service = $this->getService('SegmentService');

        parse_str($this->POST['condition_value'], $condition_value);

        $segment_provision_condition = $condition_value['spc'][0];

        $page_info = array('brand_id' => $this->getBrand()->id);

        $segmenting_data_service->createTmpSegmentingUsers();

        $cur_segment_provision = $segment_service->getSegmentProvisionArray($segment_provision_condition);
        $segmenting_users = $segmenting_data_service->getSegmentingUsers($page_info, $cur_segment_provision);

        $data['spc_user_count'] = count($segmenting_users);

        $segmenting_data_service->dropTmpSegmentingUsers();

        $json_data = $this->createAjaxResponse("ok", $data);
        $this->assign('json_data', $json_data);
        return 'dummy.php';
    }
}
