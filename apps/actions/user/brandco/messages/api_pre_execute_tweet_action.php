<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');
AAFW::import('jp.aainc.classes.validator.user.UserEntryActionValidator');
AAFW::import('jp.aainc.classes.services.TweetMessageService');
AAFW::import('jp.aainc.classes.core.UserSnsAccountManager');
AAFW::import('jp.aainc.vendor.twitter.twitter_text_counter.Extractor');

class api_pre_execute_tweet_action extends BrandcoPOSTActionBase {

    public $CsrfProtect = true;
    public $NeedUserLogin = true;
    protected $ContainerName = 'api_pre_execute_tweet_action';
    protected $AllowContent = array('JSON');
    protected $photo_uploads = array();
    protected $cp_tweet_action;

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

        foreach ($this->FILES as $key=>$value) {
            if (strpos($key, 'tweet_photo_upload') !== false) {
                $fileValidator = new FileValidator($value, FileValidator::FILE_TYPE_IMAGE);
                if (!$fileValidator->isValidFile()) {
                    $this->ajaxResponseError($fileValidator->getErrorMessage());
                    return false;
                } else {
                    $file_info = $fileValidator->getFileInfo();
                    // 画像サイズの上限は3MBまで
                    if ($file_info['size'] > 3072000) {
                        $this->ajaxResponseError('画像サイズが上限 (3MB) を超えています。');
                        return false;
                    }
                    $this->photo_uploads[] = $fileValidator->getFileInfo();
                }
            }
        }

        $cp_tweet_action_service    = $this->createService('CpTweetActionService');
        $this->cp_tweet_action      = $cp_tweet_action_service->getCpTweetAction($this->cp_action_id);

        // 画像アップロードが必須の時に
        if ($this->cp_tweet_action->photo_flg == CpTweetAction::PHOTO_REQUIRE && empty($this->photo_uploads)) {
            $this->ajaxResponseError('画像を必ずアップロードしてください。');
            return false;
        }

        // 画像アップロードが非表示の時に
        if ($this->cp_tweet_action->photo_flg == CpTweetAction::PHOTO_OPTION_HIDE && !empty($this->photo_uploads)) {
            $this->ajaxResponseError('エラーが発生しました。');
            return false;
        }

        if (count($this->photo_uploads) > CpTweetAction::MAX_PHOTO_NUM) {
            $this->ajaxResponseError('画像のアップロード数は最大4枚です。');
            return false;
        }

        $tweet_length = 0;
        $tweet_length += $this->POST['tweet_default_text'] ? $this->getTwitterStringLength($this->POST['tweet_default_text']) : 0 ;
        $tweet_length += $this->cp_tweet_action->tweet_fixed_text != '' ? $this->getTwitterStringLength($this->cp_tweet_action->tweet_fixed_text) : 0 ;
        if ($tweet_length == 0) {
            $this->ajaxResponseError('ツイート内容を入力してください。');
            return false;
        }

        $tweet_length += !empty($this->photo_uploads) ? CpTweetAction::PHOTO_TEXT_LENGTH : 0 ;
        // 文字数オーバーを検討する
        if ($tweet_length > CpTweetAction::MAX_TEXT_LENGTH) {
            $this->ajaxResponseError('ツイートできる文字数は最大140文字です。');
            return false;
        }

        return true;
    }

    function doAction() {

        $image_urls = array();
        foreach ($this->photo_uploads as $element) {
            $image_url = StorageClient::getInstance()->putObject(
                StorageClient::toHash('brand/'.$this->Data['brand']->id . '/tweet_photo/' . StorageClient::getUniqueId()), $element
            );
            $image_urls[] = $image_url;
        }
        $tweet_status = $this->POST['tweet_default_text'] . ($this->cp_tweet_action->tweet_fixed_text != '' ? "\r\n" . $this->cp_tweet_action->tweet_fixed_text : '') ;

        //ツイートを投稿する
        $cp_tweet_message_service = $this->getCpTweetMessageService();

        //デモモードで外部SNSにAPIを投げないようにチェックする
        /** @var CpFlowService $cp_follow_service */
        $cp_follow_service = $this->createService('CpFlowService');
        $is_demo_cp = $cp_follow_service->isDemoCpByCpActionId($this->cp_action_id);

        if($is_demo_cp) {
            $post_tweet = 'ここにツイートのURLが表示されます。';
        } elseif ($cp_tweet_message_service) {
            $post_tweet = $cp_tweet_message_service->postTweet($tweet_status, $image_urls);
        } else {
            $post_tweet = '';
        }
        if ($post_tweet == 'api_error') {
            $json_data = $this->createAjaxResponse("ok", array('post_tweet'=>$post_tweet));
            $this->assign('json_data', $json_data);
            return 'dummy.php';
        }

        /** @var TweetMessageService $tweet_message_service */
        $tweet_message_service = $this->createService('TweetMessageService');

        $tweet_message_data = array();
        $tweet_message_data['cp_user_id']           = $this->cp_user_id;
        $tweet_message_data['cp_tweet_action_id']   = $this->cp_tweet_action->id;
        $tweet_message_data['tweet_text']           = $this->POST['tweet_default_text'];
        $tweet_message_data['has_photo']            = !empty($image_urls);
        $tweet_message_data['tweet_content_url']    = $post_tweet;

        if ($this->cp_tweet_action->panel_hidden_flg == CpTweetAction::PANEL_TYPE_HIDDEN) {
            $tweet_message_data['approval_status'] = TweetMessage::APPROVAL_STATUS_REJECT;
        }

        //ツイートメッセージの内容を保存する
        $tweet_message = $tweet_message_service->updateTweetMessage($tweet_message_data);

        //アップロード画像があれば、消す
        $tweet_message_service->deleteTweetPhotos($tweet_message->id);

        //アップロード画像を保存する
        foreach ($image_urls as $image_url) {
            $tweet_message_service->createTweetPhoto($tweet_message->id, $image_url);
        }

        $json_data = $this->createAjaxResponse("ok", array('post_tweet'=>$post_tweet));
        $this->assign('json_data', $json_data);
        return 'dummy.php';
    }

    private function ajaxResponseError($error__message) {
        $errors['tweet_error'] = $error__message;
        $json_data = $this->createAjaxResponse("ng", array(), $errors);
        $this->assign('json_data', $json_data);
    }

    public function getCpTweetMessageService() {
        $user_sns_account_manager   = new UserSnsAccountManager($this->Data['pageStatus']['userInfo'], null, $this->Data['pageStatus']['brand']->app_id);
        $user_sns_account_id        = $this->getSNSAccountId($this->Data['pageStatus']['userInfo'], self::SOCIAL_TYPE_TWITTER);
        if ($user_sns_account_id == -1) return null;

        $sns_account_info = $user_sns_account_manager->getSnsAccountInfo($user_sns_account_id, self::SOCIAL_TYPE_TWITTER);

        $cp_tweet_message_service = $this->getService('CpTweetMessageService',
            array(
                $sns_account_info['social_media_access_token'],
                $sns_account_info['social_media_access_refresh_token']
            ));
        return $cp_tweet_message_service;
    }

    private function getTwitterStringLength($tweet_text) {
        $tweet_text = str_replace("\r\n", "\n", $tweet_text);
        $tweet_text_length = mb_strlen($tweet_text, 'UTF-8');

        $tweet_urls = $this->getTweetUrls($tweet_text);

        if (is_array($tweet_urls) && count($tweet_urls) == 0) {
            return $tweet_text_length;
        }

        foreach ($tweet_urls as $tweet_url) {
            $tweet_text_length += CpTweetAction::URL_TEXT_LENGTH - mb_strlen($tweet_url['url'], 'UTF-8');
        }

        return $tweet_text_length;
    }

    private function getTweetUrls($tweet_text) {
        $tweet_extractor = new Twitter_Extractor($tweet_text);
        $tweet_urls = $tweet_extractor->extractURLsWithIndices();

        return $tweet_urls;
    }
}