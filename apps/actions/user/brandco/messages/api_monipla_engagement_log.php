<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');

class api_monipla_engagement_log extends BrandcoPOSTActionBase{
    
    protected   $ContainerName  = 'api_monipla_engagement_log';
    public      $NeedOption     = array();
    protected   $AllowContent   = array('JSON');
    protected $monipla_engagement_logs_service;

    public function doThisFirst() {
        $this->monipla_engagement_logs_service = $this->createService('MoniplaEngagementLogService');
    }
    public function validate() {

        if( $this->POST['social_media_id'] !== (String)SocialAccount::SOCIAL_MEDIA_FACEBOOK &&
            $this->POST['social_media_id'] !== (String)SocialAccount::SOCIAL_MEDIA_TWITTER ) {

            return false;
        }

        if(!in_array($this->POST['value'],array("0","1","-1"),true)) {

            return false;
        }

        $user_service = $this->createService('UserService');
        if(!$user_service->getUserByBrandcoUserId($this->POST['user_id'])) {

            return false;
        }

        return true;
    }

    public function doAction() {
        
        $log = array(
            'social_media_id'    => $this->POST['social_media_id'],
            'locate_id'          => $this->POST['locate_id'],
            'value'              => $this->POST['value'],
            'user_id'            => $this->POST['user_id'],
        );
        
        $this->monipla_engagement_logs_service->createLog($log);
        
    }
}