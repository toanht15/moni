<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.classes.services.SegmentService');

class segment_list extends BrandcoGETActionBase {

    public $NeedOption = array(BrandOptions::OPTION_SEGMENT);
    public $NeedAdminLogin = true;

    private $sp_ids_array;

    public function doThisFirst() {
        $this->setBrandSession(SegmentService::SEGMENT_CONDITION_SESSION_KEY, null);

        //Ads アクション
        if($this->GET['showModal']) {

            $ads_session = $this->getBrandSession(SegmentService::SEGMENT_ADS_CONDITION_SESSION_KEY);

            foreach ($ads_session as $key => $value) {
                if (strpos($key, 'sp_ids_') !== false) {
                    $temp_rs = explode('sp_ids_', $key);
                    $this->sp_ids_array[$temp_rs[1]] = $value;
                }
            }

        } else {
            $this->setBrandSession(SegmentService::SEGMENT_ADS_CONDITION_SESSION_KEY, null);
        }
    }

    public function validate() {
        return true;
    }

    public function doAction() {

        $this->Data['default_data'] = array(
            'brand_id' => $this->getBrand()->id,
            'sp_ids_array' => $this->sp_ids_array,
        );

        $this->Data['can_use_ads_action'] = $this->hasAdsOption();

        $segment_service = $this->getService('SegmentService');
        $this->Data['count_list'] = $segment_service->getSegmentCountList($this->getBrand()->id);

        return 'user/brandco/admin-segment/segment_list.php';
    }

    private function hasAdsOption() {

        $options = BrandInfoContainer::getInstance()->getBrandOptions();

        return $this->getBrand()->hasOption(BrandOptions::OPTION_FACEBOOK_ADS, $options) || $this->getBrand()->hasOption(BrandOptions::OPTION_TWITTER_ADS, $options);
    }
}