<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');
AAFW::import('jp.aainc.classes.BrandInfoContainer');
AAFW::import('jp.aainc.classes.RequestUserInfoContainer');

class BrandcoTopHeader extends aafwWidgetBase {
    public function doService($params = array()) {

        $serviceFactory = new aafwServiceFactory();

        /**
         * Global menu
         */
        $globalMenus = BrandInfoContainer::getInstance()->getBrandGlobalMenus();
        if( $globalMenus ) {
            $params['globalMenus'] = $globalMenus->toArray();
        }

        if( $params['userInfo']->id ) {
            /** @var UserService $user_service */
            $user_service = $serviceFactory->create('UserService');
            $user = RequestUserInfoContainer::getInstance()->getByMoniplaUserId($params['userInfo']->id);

            $params['notifications_count'] = $user_service->getUnreadMessagesCount($params['brand']->id, $user->id);
        }

        //Syn事務局キャンペーン or SynキャンペーンかつSyn.のメニューから遷移してきた人に対しては、ラッキーくじ用のSynMenuに差し替える
        $params['isNeedReplaceSynMenu'] = $params['cp'] && ($params['cp']->isForSyndotOnly() || $params['cp']->isSynCpAndFromSynMenu($params['from_id']));
        $params['isSynCampaign'] =  ($params['cp'] && $params['cp']->getSynCp()) ? true : false;
        $brand_options = BrandInfoContainer::getInstance()->getBrandOptions();
        $brand_contract = BrandInfoContainer::getInstance()->getBrandContract();
        $params['has_top_option'] = $params['brand']->hasOption(BrandOptions::OPTION_TOP, $brand_options);
        $params['has_header_option'] = $params['brand']->hasOption(BrandOptions::OPTION_HEADER, $brand_options);
        $params['can_show_syn_menu'] = !$params['brand']->isDisallowedBrand();
        $params['is_promotion'] = $params['brand']->isPlan(BrandContract::PLAN_PROMOTION_BRAND, $brand_contract) || $params['brand']->isPlan(BrandContract::PLAN_PROMOTION_MONIPLA, $brand_contract);

        return $params;
    }
}