<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.ExecuteActionBase');
AAFW::import('jp.aainc.classes.validator.user.UserEntryActionValidator');
AAFW::import('jp.aainc.classes.brandco.cp.trait.CpActionTrait');
AAFW::import('jp.aainc.classes.core.UserSnsAccountManager');

class api_execute_youtube_channel_action extends ExecuteActionBase {

    use CpActionTrait;

    public $NeedOption = array();
    protected $ContainerName = 'api_execute_youtube_channel_action';

    public function validate() {

        $validator = new UserEntryActionValidator($this->cp_user_id, $this->cp_action_id);
        $validator->validate();

        if (!$validator->isValid()) {
            $errors = $validator->getErrors();
            $json_data = $this->createAjaxResponse("ng", array(), $errors);
            $this->assign('json_data', $json_data);

            return false;
        }

        return true;
    }

    function saveData() {

        /** @var CpYoutubeChannelUserLogService $cp_yt_channel_user_log_service */
        $cp_yt_channel_user_log_service = $this->getService('CpYoutubeChannelUserLogService');

        if ($this->auto_follow) {
            try {
                // アカウント情報取得
                /** @var CpUserService $cp_user_service */
                $cp_user_service = $this->getService('CpUserService');
                $user = $cp_user_service->getUserByCpUserId($this->cp_user_id);
                /** @var BrandcoSocialAccountService $brandco_social_account_service */
                $brandco_social_account_service = $this->getService('BrandcoSocialAccountService');
                $brandco_social_account = $brandco_social_account_service->getBrandcoSocialAccount($user->id, SocialApps::PROVIDER_GOOGLE);

                // チャンネル登録
                $status = $cp_yt_channel_user_log_service->subscribeYoutubeChannel($brandco_social_account->access_token, $this->channel_id);

                // 失敗したら例外発生
                if ($status == CpYoutubeChannelUserLog::STATUS_ERROR) {
                    throw new Exception('STATUS_ERROR');
                }

                // ログを取る
                $cp_yt_channel_user_log_service->setLog($this->cp_action_id, $this->cp_user_id, $status);

            } catch (Exception $e) {
                aafwLog4phpLogger::getDefaultLogger()->error("api_execute_youtube_channel_actions#saveData() Exception cp_user_id = " . $this->cp_user_id);
                aafwLog4phpLogger::getDefaultLogger()->error($e);
                throw $e;
            }
        } else {
            // ログを取る
            $cp_yt_channel_user_log_service->setLog($this->cp_action_id, $this->cp_user_id, CpYoutubeChannelUserLog::STATUS_SKIP);
        }
    }

}
