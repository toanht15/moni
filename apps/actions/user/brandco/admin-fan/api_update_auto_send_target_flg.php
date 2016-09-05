<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');

class api_update_auto_send_target_flg extends BrandcoPOSTActionBase {

    protected $ContainerName = 'ads_list';
    protected $AllowContent = array('JSON');

    public $NeedOption = array(BrandOptions::OPTION_FACEBOOK_ADS, BrandOptions::OPTION_TWITTER_ADS);
    public $NeedAdminLogin = true;
    public $CsrfProtect = true;

    private $relation;

    public function validate() {

        if(Util::isNullOrEmpty($this->relation_id)) {
            return false;
        }

        /** @var AdsService $ads_service */
        $ads_service = $this->getService('AdsService');

        $this->relation = $ads_service->findAudiencesAccountsRelationById($this->relation_id);

        if(!$this->relation) {
            return false;
        }

        return true;
    }

    public function getFormURL () {
        $json_data = $this->createAjaxResponse("ng");
        $this->assign('json_data', $json_data);
        return 'dummy.php';
    }

    public function doAction() {

        $this->relation->auto_send_target_flg = $this->send_target_flg == AdsAudiencesAccountsRelation::AUTO_SEND_TARGET_FLG_ON ?: AdsAudiencesAccountsRelation::AUTO_SEND_TARGET_FLG_OFF;

        /** @var AdsService $ads_service */
        $ads_service = $this->getService('AdsService');

        $ads_service->updateAudiencesAccountsRelation($this->relation);

        //Twitterアカウント場合、IDも更新が必要です
        if($this->relation->type == AdsAudiencesAccountsRelation::SEND_MAIL_TYPE) {
            $email_relation = $ads_service->findRelationByAccountIdAndAudienceIdAndType($this->relation->ads_account_id,$this->relation->ads_audience_id,AdsAudiencesAccountsRelation::SEND_ID_TYPE);

            if($email_relation) {
                $email_relation->auto_send_target_flg = $this->relation->auto_send_target_flg;
            }

            $ads_service->updateAudiencesAccountsRelation($email_relation);
        }

        $json_data = $this->createAjaxResponse("ok");
        $this->assign('json_data', $json_data);

        return 'dummy.php';
    }
}
