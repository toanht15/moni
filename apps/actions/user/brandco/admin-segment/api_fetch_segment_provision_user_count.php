<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');

class api_fetch_segment_provision_user_count extends BrandcoPOSTActionBase {

    protected $ContainerName = 'segment_list';
    protected $AllowContent = array('JSON');

    public $NeedOption = array(BrandOptions::OPTION_SEGMENT);
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

        $segment_provision_condition_array = $condition_value['spc'];

        //セグメントグループ場合、$unclassified_segment_provisionを作成
        if($condition_value['segment_type'] == Segment::TYPE_SEGMENT_GROUP) {
            $unclassified_segment_provision = array_slice($segment_provision_condition_array, -1, 1, true);
            unset($segment_provision_condition_array[array_keys($unclassified_segment_provision)[0]]);
        }

        $spc_user_count_list = array();
        $page_info = array('brand_id' => $this->getBrand()->id);

        $segmenting_data_service->createTmpSegmentingUsers();

        // Segmenting unclassified segment provision
        if ($condition_value['unclassified_flg'] == Segment::UNCLASSIFIED_SEGMENT_FLG_ON) {

            $cur_segment_provision = $segment_service->getSegmentProvisionArray(array_values($unclassified_segment_provision)[0]);
            $segmenting_users = $segmenting_data_service->getSegmentingUsers($page_info, $cur_segment_provision);
            $data['unclassified_user_count'] = count($segmenting_users);

            $segmenting_data_service->insertTmpSegmentingUsersByQuery($segmenting_users);
        }

        // Segmenting default segment provisions
        foreach ($segment_provision_condition_array as $segment_provision_condition) {
            $cur_segment_provision = $segment_service->getSegmentProvisionArray($segment_provision_condition);

            $segmenting_users = $segmenting_data_service->getSegmentingUsers($page_info, $cur_segment_provision);
            $spc_user_count_list[] = count($segmenting_users);

            $segmenting_data_service->insertTmpSegmentingUsersByQuery($segmenting_users);
        }
        $data['spc_user_count'] = $spc_user_count_list;

        // Segmenting remaining users
        $remaining_user_count = $segmenting_data_service->countRemainingUsers($page_info);

        if ($condition_value['unconditional_flg'] == Segment::UNCONDITIONAL_SEGMENT_FLG_ON) {
            $data['remaining_user_count'] = 0;
            $data['unconditional_user_count'] = $remaining_user_count[0]['total_count'];
        } else {
            $data['remaining_user_count'] = $remaining_user_count[0]['total_count'];
            $data['unconditional_user_count'] = 0;
        }

        $segmenting_data_service->dropTmpSegmentingUsers();

        $json_data = $this->createAjaxResponse("ok", $data);
        $this->assign('json_data', $json_data);
        return 'dummy.php';
    }
}
