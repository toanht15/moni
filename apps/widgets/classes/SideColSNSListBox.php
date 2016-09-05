<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');
AAFW::import('jp.aainc.classes.services.SnsPageService');

class SideColSNSListBox extends aafwWidgetBase {

    public function doService( $params = array() ){
        $service_factory = new aafwServiceFactory ();

        $data['brand'] = $params['brand'];
        foreach($params['brand_social_accounts'] as $param) {
            if ($param->social_app_id && in_array($param->social_app_id, SocialApps::$social_pages)) {
                $sns_page_service = $service_factory->create('SnsPageService', array($param->id));
                $total_count = $sns_page_service->getTotalCount();

                if ($total_count > 0) {
                    $param->page_link = Util::rewriteUrl('sns', 'category', array($param->id));
                    $param->target_blank = false;
                } else {
                    $param->target_blank = true;

                    if ($param->social_app_id == SocialApps::PROVIDER_TWITTER) {
                        $param->page_link = '//twitter.com/' . json_decode($param->store)->screen_name;
                    } elseif ($param->social_app_id == SocialApps::PROVIDER_FACEBOOK) {
                        $param->page_link = json_decode($param->store)->link;
                    } elseif ($param->social_app_id == SocialApps::PROVIDER_GOOGLE) {
                        $param->page_link = '//www.youtube.com/channel/' . json_decode($param->store)->channelId;
                    } elseif ($param->social_app_id == SocialApps::PROVIDER_INSTAGRAM) {
                        $param->page_link = '//instagram.com/' . $param->name;
                    }
                }
            }
            $data['brand_social_accounts'][] = $param;
        }

        return $data;
    }
}