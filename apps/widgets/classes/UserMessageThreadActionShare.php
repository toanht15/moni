<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');
AAFW::import('jp.aainc.classes.RequestUserInfoContainer');
AAFW::import('jp.aainc.classes.CpInfoContainer');


class UserMessageThreadActionShare extends aafwWidgetBase {

    private $social_account_array;
    private $facebook_user;

    //TODO CP ID:3052の特例対応
    private $conversion_tag_for_cp_ary = array('3048','3052','3065');

    public function doService($params) {

        //TODO CP ID:3052の特例対応
        if( in_array($params['cp_user']->cp_id , $this->conversion_tag_for_cp_ary) ){
            /** @var ReplaceTagService $replace_tag_service */
            $replace_tag_service = $this->getService('ReplaceTagService');

            $tag = '<img src="https://www.cross-a.net/xa.php?adid=12072&rn=1&u1=<#ALLIED_ID>" width="1" height="1">';

            $params['message_info']["conversion_tag"] = $replace_tag_service->getTag(
                $tag,
                array(ReplaceTagService::TYPE_ALLIED_ID=>$params['pageStatus']['userInfo']->id)
            );
        }

        /** @var CpShareActionService $cp_share_action_service */
        $cp_share_action_service = $this->getService('CpShareActionService');
        $cp_share_action = $params['message_info']['concrete_action'];
        $params['cp_share_action'] = $cp_share_action;


        /** @var CpShareUserLogService $cp_share_user_log_service */
        $cp_share_user_log_service = $this->getService('CpShareUserLogService');

        $cp_share_user_log = $cp_share_user_log_service->getCpShareUserLog($params['cp_user']->id);

        //連携しているSNSアカウント一覧
        $this->social_account_array = $params['pageStatus']['userInfo']->socialAccounts;

        $params['shared_flg'] = $cp_share_user_log ? '1' : '0';
        $params['share_text'] = $cp_share_user_log ? $cp_share_user_log->text : '';

        try {
            $params['share_media_type'] = $this->getShareSns($params['cp_user']->join_sns);

            $cp = CpInfoContainer::getInstance()->getCpById($params['cp_user']->cp_id);

            if($params['share_media_type'] == SocialAccountService::SOCIAL_MEDIA_TWITTER){
                $params['placeholder'] = $cp_share_action_service->getShareTwitterPlaceholder($cp_share_action->cp_action_id,$cp->getReferenceUrl());
            }

            //permissionのpublic_actionがあるかどうかチェック
            if($params['share_media_type'] == SocialAccountService::SOCIAL_MEDIA_FACEBOOK) {
                if(!$this->checkPublishAction($params['pageStatus'])){
                    $params['share_media_type'] = 0;
                }
            }
        } catch (Exception $e) {
            aafwLog4phpLogger::getDefaultLogger()->error('UserMessageThreadActionShare#doService.' . $e);
        }

        if($cp_share_action->share_url){
            $params['meta_tags'] = json_decode($params['cp_share_action']->meta_data);
        }

        $cp = CpInfoContainer::getInstance()->getCpById($params['cp_user']->cp_id);
        $params['og_info'] = $cp->getReferenceOpenGraphInfo();

        // 最後のアクションかどうか判定する
        $params['is_last_action'] = $params['message_info']['cp_action']->isLastCpActionInGroup();

        return $params;
    }

    function getShareSns($join_sns){
        if($this->checkSnsConnect($join_sns)){
            return $join_sns;
        }else{
            foreach ($this->social_account_array as $social_account) {
                switch($social_account->socialMediaType){
                    case "Facebook":
                        return SocialAccountService::SOCIAL_MEDIA_FACEBOOK;
                }
            }
        }
        return 0;
    }

    function checkSnsConnect($social_type){
        foreach ($this->social_account_array as $social_account) {
            switch($social_account->socialMediaType){
                case "Facebook":
                    if($social_type == SocialAccountService::SOCIAL_MEDIA_FACEBOOK)return true;
                    break;
                default:
                    return false;
            }
        }
        return false;
    }

    private function checkPublishAction($pageStatus){
        AAFW::import('jp.aainc.classes.core.UserSnsAccountManager');
        $user_sns_account_manager = new UserSnsAccountManager($pageStatus["userInfo"], null, $pageStatus["brand"]->app_id);
        $sns_account_info = $user_sns_account_manager->getSnsAccountInfo($this->getSNSAccountId($pageStatus["userInfo"], 'Facebook'), 'Facebook');

         if(!$sns_account_info){
             return false;
         }

        $this->getFacebookUser()->setToken($sns_account_info['social_media_access_token']);
        $permission_array = $this->getFacebookUser()->getResponse('GET', "/me/permissions");

        foreach($permission_array as $permission){
            if($permission->permission == 'publish_actions' && $permission->status == 'granted'){
                return true;
            }
        }
        return false;
    }

    private function getFacebookUser () {
        if ( $this->facebook_user == null )  {
            AAFW::import('jp.aainc.classes.FacebookApiClient');
            $this->facebook_user = new FacebookApiClient(FacebookApiClient::BRANDCO_MODE_USER);
        }
        return $this->facebook_user;
    }
}
