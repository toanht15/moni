<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');
AAFW::import('jp.aainc.classes.core.ShippingAddressManager');

class UserMessageThreadActionShippingAddress extends aafwWidgetBase {
    public function doService($params) {
        $service_factory = new aafwServiceFactory();
        
        $shipping_address_action_service = $service_factory->create('CpShippingAddressActionService');
        $params['shipping_address_action'] = $shipping_address_action_service->getCpShippingAddressAction($params['message_info']["cp_action"]->id);
        
        //都道府県情報取得
        /** @var PrefectureService $prefectureService */
        $prefectureService = $service_factory->create('PrefectureService');
        $params['prefectures'] = $prefectureService->getPrefecturesKeyValue();
        
        //ユーザーの配送情報
        $answered = $params["message_info"]["action_status"]->status;
        if($answered) {
            //回答済み
            $shipping_address_user_service = $service_factory->create('ShippingAddressUserService');
            $shipping_address_user = $shipping_address_user_service->getShippingAddressUserByCpUserId($params['cp_user']->id);
            if(!$shipping_address_user) {
                aafwLog4phpLogger::getDefaultLogger()->error("UserMessageThreadActionShippingAddress#doService() cp_user_id :" . $params['cp_user']->id);
            } else{
                $params['userShippingAddress'] = $shipping_address_user->toArray();
            }
        } else {
            //未回答
            $shippingAddressManager = new ShippingAddressManager($params['pageStatus']['userInfo'], $this->getMoniplaCore());
            $shippingAddress = $shippingAddressManager->getShippingAddress();

            foreach ($shippingAddress as $key => $value) {
                $params['userShippingAddress'][ShippingAddressManager::$AddressParams[$key]] = $value;
            }
        }

        return $params;
    }

}
