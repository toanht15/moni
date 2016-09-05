<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.classes.services.CpCreateSqlService');
AAFW::import('jp.aainc.classes.services.SegmentService');

class ads_audience extends BrandcoGETActionBase {

    protected $ContainerName = 'ads_audience';

    public $NeedOption = array(BrandOptions::OPTION_FACEBOOK_ADS, BrandOptions::OPTION_TWITTER_ADS);
    public $NeedAdminLogin = true;

    public function doThisFirst() {
        $this->Data['audience_id'] = $this->GET['exts'][0];
        $this->deleteErrorSession();
    }

    public function validate () {
        $ads_validator = new AdsValidator($this->getBrandsUsersRelation()->id);

        if (!$ads_validator->isValidAdsAudienceId($this->Data['audience_id'])) {
            return '404';
        }

        return true;
    }

    function doAction() {

        /** @var AdsService $ads_service */
        $ads_service = $this->createService('AdsService');
        $this->Data["audience"] = $ads_service->findAdsAudiencesById($this->Data['audience_id']);

        if (!$this->Data['ActionForm']) {
            $action_form["audience_name"] = $this->Data["audience"]->name;
            $action_form["audience_description"] = $this->Data["audience"]->description;
            $action_form['description_flg'] = !Util::isNullOrEmpty($this->Data["audience"]->description) ? 1 : 0;
            $this->assign("ActionForm", $action_form);
        }

        $ads_users = $ads_service->findAdsUsersByBrandUserRelationId($this->getBrandsUsersRelation()->id);
        $ads_service->updateAdsAccountInfo($ads_users);

        $this->Data['ads_accounts'] = $ads_service->findValidAdsAccountByBrandUserRelationId($this->getBrandsUsersRelation()->id);

        if($this->Data["audience"]->search_type == AdsAudience::SEACH_TYPE_SEGMENT) {
            /** @var SegmentActionLogService $segment_log_service */
            $segment_log_service = $this->createService('SegmentActionLogService');
            $segment_log_id = $this->Data["audience"]->search_condition;
            $segment_log = $segment_log_service->findSegmentActionLogById($segment_log_id);
            $this->Data['provision_ids'] = $segment_log_service->convertSegmentProvisionIdsToProvisionIdArray($segment_log->segment_provison_ids);
        }

        return 'user/brandco/admin-fan/ads_audience.php';
    }
}