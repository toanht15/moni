<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');

abstract class TopSideColBase extends aafwWidgetBase {

    public function doService( $params = array() ) {

        // Official SNS Accounts
        $params['brand_social_accounts'] = $this->getBrandSocialAccounts($params['brand']->id);

        $params = $this->doAction($params);

        // User Information
        $params['side_col_info'] = array(
            'is_login' => $params['isLogin'],
            'is_show_sns_box' => $this->canShowSNSBox()
        );

        return $params;
    }

    abstract public function doAction($params = array());

    public function getBrandSocialAccounts($brand_id) {
        $brand_social_account_service = $this->getService('BrandSocialAccountService');
        $brand_social_accounts = array();

        $socialPanelKinds = $brand_social_account_service->getBrandSocialAccountByBrandId($brand_id);
        foreach ($socialPanelKinds as $social_panel_kind) {
            $brand_social_accounts[] = $social_panel_kind;
        }

        $rss_service = $this->getService('RssStreamService');
        $rssPanelKinds = $rss_service->getStreamByBrandId($brand_id);
        foreach ($rssPanelKinds as $social_panel_kind) {
            $brand_social_accounts[] = $social_panel_kind;
        }

        usort($brand_social_accounts, function ($a, $b) {
            if ($a->order_no == $b->order_no) {
                return 0;
            }
            return ($a->order_no < $b->order_no) ? -1 : 1;
        });

        return $brand_social_accounts;
    }

    abstract public function canShowSNSBox();
}