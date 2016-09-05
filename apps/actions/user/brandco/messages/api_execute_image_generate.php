<?php

AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');
AAFW::import('jp.aainc.classes.services.GiftCardConfigService');
AAFW::import('jp.aainc.classes.services.GiftCardUploadService');
AAFW::import('jp.aainc.classes.services.GiftMessageService');
AAFW::import('jp.aainc.classes.services.CpGiftActionService');
AAFW::import('jp.aainc.classes.services.GreetingCardGenerateService');
AAFW::import('jp.aainc.classes.validator.user.UserEntryActionValidator');

class api_execute_image_generate extends BrandcoPOSTActionBase {

    public $NeedUserLogin = true;
    public $CsrfProtect = true;
    protected $ContainerName = 'api_execute_image_generate';
    protected $AllowContent = array('JSON');

    private $gift_card_config_service;
    private $gift_card_upload_service;
    private $greeting_card_generate_service;
    private $gift_message_service;
    private $gift_card_config;
    private $cp_gift_action;

    public function doThisFirst() {
        $this->gift_card_config_service         = $this->createService('GiftCardConfigService');
        $this->gift_card_upload_service         = $this->createService('GiftCardUploadService');
        $this->greeting_card_generate_service   = $this->createService('GreetingCardGenerateService');
        $this->gift_message_service             = $this->createService('GiftMessageService');
    }
    public function validate() {

        //参加ユーザを検証する
        $validator = new UserEntryActionValidator($this->cp_user_id, $this->cp_action_id);
        $validator->validate();

        if (!$validator->isValid()) {
            $errors = $validator->getErrors();
            $json_data = $this->createAjaxResponse("ng", array(), $errors);
            $this->assign('json_data', $json_data);

            return false;
        }

        //グリーティングカードを作成するための情報を検証する
        $errors = array();
        if (!$this->image_url || !$this->receiver_text || !$this->sender_text || !$this->content_text || !$this->content_width) {
            $errors[] = "入力のエラーが発生しました";
        }

        $cp_gift_action_service = $this->createService('CpGiftActionService');
        $this->cp_gift_action   = $cp_gift_action_service->getCpGiftAction($this->cp_action_id);

        $this->gift_card_config = $this->gift_card_config_service->getGiftCardConfig($this->cp_gift_action->id);

        if (!$this->gift_card_config) {
            $errors[] = "カード設定のエラーが発生しました";
        } else {
            if(!$this->gift_card_upload_service->validCardUpload($this->gift_card_config->id, $this->image_url)){
                $errors[] = "画像のエラーが発生しました";
            }
        }

        if(!empty($errors)) {
            $json_data = $this->createAjaxResponse("ng", array(), $errors, '');
            $this->assign('json_data', $json_data);
            return false;
        }

        return true;
    }
    public function doAction() {
        $message_info = array(
            'image_url'         => $this->image_url,
            'receiver_text'     => $this->receiver_text,
            'sender_text'       => $this->sender_text,
            'content_text'      => $this->content_text,
            'content_width'     => $this->content_width,
            'content_height'    => $this->content_height,
            'sender_height'     => $this->sender_height,
            'receiver_height'   => $this->receiver_height
        );

        //グリーティングカードを作成する
        $image_file = $this->greeting_card_generate_service->makeCard($this->gift_card_config, $message_info);

        $gift_message = $this->gift_message_service->getGiftMessageByCpUserIdAndCpGiftActionId($this->cp_user_id, $this->cp_gift_action->id);

        if ($gift_message->image_url) {
            StorageClient::getInstance()->deleteObject(StorageClient::getInstance()->getImageKey($gift_message->image_url));
        }
        $image_url = StorageClient::getInstance()->putObject( StorageClient::toHash('brand/' . $this->getBrand()->id . '/gift_card_upload/user/' . StorageClient::getUniqueId()), array('path' => $image_file, 'extension' => 'png'));

        //ユーザのグリーティングカード画像を更新する
        $this->gift_message_service->updateGreetingCardImage($this->cp_user_id, $this->cp_gift_action->id, $image_url, $this->sender_text, $this->receiver_text, $this->content_text);

        $json_data = $this->createAjaxResponse("ok", array('card_image_url' => $image_url), array(), '');
        $this->assign('json_data', $json_data);
        return 'dummy.php';

    }
}