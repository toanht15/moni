<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');
AAFW::import('jp.aainc.classes.validator.user.UserEntryActionValidator');
AAFW::import('jp.aainc.classes.services.RetweetMessageService');
AAFW::import('jp.aainc.classes.core.UserSnsAccountManager');

class api_pre_execute_retweet_action extends BrandcoPOSTActionBase {

    public $CsrfProtect = true;
    public $NeedUserLogin = true;
    protected $ContainerName = 'api_pre_execute_retweet_action';
    protected $AllowContent = array('JSON');
    protected $cp_retweet_action;

    const SOCIAL_TYPE_TWITTER = 'Twitter';

    public function validate() {
        $validator = new UserEntryActionValidator($this->cp_user_id, $this->cp_action_id);
        $validator->validate();

        // 共通validate
        if (!$validator->isValid()) {
            $errors = $validator->getErrors();
            $json_data = $this->createAjaxResponse("ng", array(), $errors);
            $this->assign('json_data', $json_data);
            return false;
        }

        return true;
    }

    function doAction() {

        $cp_retweet_action_service    = $this->createService('CpRetweetActionService');
        $this->cp_retweet_action      = $cp_retweet_action_service->getCpRetweetAction($this->cp_action_id);

        //リツイートを投稿する
        $cp_retweet_message_service = $this->getCpRetweetMessageService();
        //デモモードで外部SNSにAPIを投げないようにチェックする
        /** @var CpFlowService $cp_follow_service */
        $cp_follow_service = $this->createService('CpFlowService');
        $is_demo_cp = $cp_follow_service->isDemoCpByCpActionId($this->cp_action_id);
        if($is_demo_cp) {
            $post_retweet = 'success';
        }elseif ($cp_retweet_message_service) {
            $post_retweet = $cp_retweet_message_service->postRetweet($this->cp_retweet_action->tweet_id);
        } else {
            $post_retweet = '';
        }
        if ($post_retweet == 'api_error') {
            $json_data = $this->createAjaxResponse("ok", array('post_retweet'=>$post_retweet));
            $this->assign('json_data', $json_data);
            return 'dummy.php';
        }

        /** @var RetweetMessageService $retweet_message_service */
        $retweet_message_service = $this->createService('RetweetMessageService');

        $retweet_message_data = array();
        $retweet_message_data['cp_user_id']             = $this->cp_user_id;
        $retweet_message_data['cp_retweet_action_id']   = $this->cp_retweet_action->id;
        $retweet_message_data['retweeted']              = $post_retweet == 'success' ? ($this->post_retweet ? CpRetweetAction::CONNECT_AND_POSTED_RETWEET :CpRetweetAction::POSTED_RETWEET) : CpRetweetAction::POST_RETWEET;

        //リツイートアクションを保存する
        $retweet_message_service->updateRetweetMessage($retweet_message_data);

        $json_data = $this->createAjaxResponse("ok", array('post_retweet'=>$post_retweet));
        $this->assign('json_data', $json_data);
        return 'dummy.php';
    }


    public function getCpRetweetMessageService() {
        $user_sns_account_manager   = new UserSnsAccountManager($this->Data['pageStatus']['userInfo'], null, $this->Data['pageStatus']['brand']->app_id);
        $user_sns_account_id        = $this->getSNSAccountId($this->Data['pageStatus']['userInfo'], self::SOCIAL_TYPE_TWITTER);
        if ($user_sns_account_id == -1) return null;

        $sns_account_info = $user_sns_account_manager->getSnsAccountInfo($user_sns_account_id, self::SOCIAL_TYPE_TWITTER);
        if (!$sns_account_info['social_media_access_token']) return null;

        $cp_retweet_message_service = $this->getService('CpRetweetMessageService',
            array(
                $sns_account_info['social_media_access_token'],
                $sns_account_info['social_media_access_refresh_token']
            ));
        return $cp_retweet_message_service;
    }
}