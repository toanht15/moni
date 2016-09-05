<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');
AAFW::import('jp.aainc.classes.batch.UserMessageDeliveryManager');

class UserMessageThreadActionAnnounce extends aafwWidgetBase {

    public function doService($params = array()) {
        $brandcoAuthService = $this->getService('BrandcoAuthService');
        $userInfo = $brandcoAuthService->getUserInfoByQuery($params['pageStatus']['userInfo']->id);
        $mailAddress = $userInfo->mailAddress;
        if( !$mailAddress ) {
            /** @var UserService $user_service */
            $user_service = $this->getService('UserService');
            $user = $user_service->getUserByMoniplaUserId($params['pageStatus']['userInfo']->id);
            $mailAddress = $user->mail_address;
        }

        if($mailAddress && $this->canSendMail($params) ) {
            /** @var UserMessageDeliveryManager $userMessageDeliveryManager */
            $user = $params['cp_user']->getUser();
            $userMessageDeliveryManager = $this->getService('UserMessageDeliveryManager');
            $userMessageDeliveryManager->sendNow(
                array(
                    'cp_action_id' => $params['message_info']['cp_action']->id,
                    'user_id' => $params['cp_user']->user_id,
                    'mail_address' => $mailAddress,
                    'name' => $user->name,
                    'profile_image_url' => $user->profile_image_url
                ),
                $params['pageStatus']['cp'],
                $params['pageStatus']['brand'],
                $params['message_info']['cp_action'],
                $params['message_info']['concrete_action']->title
            );
        }

        if ($params['message_info']["concrete_action"]->html_content) {
            /** @var BrandGlobalSettingService $brandGlobalSettingService */
            $brandGlobalSettingService = $this->getService('BrandGlobalSettingService');
            $brand_global_setting = $brandGlobalSettingService->getBrandGlobalSettingByName(BrandInfoContainer::getInstance()->getBrandGlobalSettings(), BrandGlobalSettingService::CAN_USE_AAID_HASH_TAG);

            if(!Util::isNullOrEmpty($brand_global_setting)){
                /** @var ReplaceTagService $replace_tag_service */
                $replace_tag_service = $this->getService('ReplaceTagService');

                $params['message_info']["concrete_action"]->html_content = $replace_tag_service->getTag(
                    $params['message_info']["concrete_action"]->html_content,
                    array(ReplaceTagService::TYPE_ANNOUNCE_TAG => $params['pageStatus']['userInfo']->id)
                );
            }
        }

        return $params;
    }

    public function canSendMail($params) {
        return $params['canSendAnnounceMail'] &&
        $params['message_info']['cp_action']->order_no > 1;
    }

    //TODO: ざっくさん後で確認して消す
    public function sendAnnounceMail($platformUserId, $cpActionMessage) {
        /** @var UserMailService $userMailService */
        $userMailService = $this->getService('UserMailService');
        $userMailService->sendAnnounceMail($platformUserId, $cpActionMessage);
    }
}