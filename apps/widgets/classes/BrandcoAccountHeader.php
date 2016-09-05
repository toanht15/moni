<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');
class BrandcoAccountHeader extends aafwWidgetBase {
	public function doService( $params = array() ){

		$serviceFactory = new aafwServiceFactory();
		/** @var UserService $user_service */
		$user_service = $serviceFactory->create('UserService');
		$user = $user_service->getUserByMoniplaUserId($params['userInfo']->id);

		$params['notifications_count'] = $user_service->getUnreadMessagesCount($params['brand']->id, $user->id);

		return $params;
	}
}
