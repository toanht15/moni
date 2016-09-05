<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');
AAFW::import('jp.aainc.classes.services.BrandsUsersRelationService');
AAFW::import('jp.aainc.classes.services.UserService');
AAFW::import('jp.aainc.classes.clients.UtilityApiClient');
class AdEbis extends aafwWidgetBase {

    // clients_dbで定義しているID
    private $client_id = 2;

    public function doService($params = array()) {

        $logger = aafwLog4phpLogger::getDefaultLogger();

        try {
            // user_id と brand_id 取得
            if ($params['page_type'] == Cps::ADEBIS_CP_JOIN_FINISH) {
                /** @var CpUserService $cp_user_service */
                $cp_user_service = $this->getService('CpUserService');
                $params['cp_user'] = $cp_user_service->getCpUserById($params['cp_user_id']);

                $cp = $params['cp_user']->getCp();
                $params['cp_id'] = $cp->id;

                $brand_id = $cp->brand_id;
                $user_id = $params['cp_user']->getUser()->id;
                if (!$user_id){
                    $logger->info('Adebis log,page_type=' . Cps::ADEBIS_CP_JOIN_FINISH . ',brand_id=' . $brand_id . ',user_id=' .$user_id);
                }

            }else if($params['page_type'] == Cps::ADEBIS_NEW_USER) {
                $brand_id = $params['brand_id'];
                /** @var UserService $user_service */
                $user_service = $this->getService('UserService');
                $user_id = $user_service->getUserByMoniplaUserId($params['platform_user_id'])->id;
                if (!$user_id){
                    $logger->info('Adebis log,page_type=' . Cps::ADEBIS_NEW_USER . ',brand_id=' . $brand_id . ',user_id=' .$user_id);
                }
            }

            /** @var BrandGlobalSettingService $brand_global_settings_service */
            $brand_global_settings_service = $this->getService('BrandGlobalSettingService');
            // Brand 別 ADEBiS token 取得
            $params['cid'] = $brand_global_settings_service->getBrandGlobalSetting($brand_id, BrandGlobalSettingService::ADEBIS_BRAND_TOKEN)->content;

            if ($params['cid'] && $user_id){
                /** @var BrandsUsersRelationService $brands_users_relation_service */
                $brands_users_relation_service = $this->getService('BrandsUsersRelationService');
                $brands_users_relation = $brands_users_relation_service->getBrandsUsersRelation($brand_id, $user_id);
                // brands_users_relation_id の token 取得
                $params['token'] = UtilityApiClient::getInstance()->getBrandsUserRelationToken($this->client_id, $brands_users_relation->id);
            }

        } catch (Exception $e) {
            aafwLog4phpLogger::getDefaultLogger()->error("adebis#doAction() Exception :" . $e);
        }

        return $params;
    }
}
