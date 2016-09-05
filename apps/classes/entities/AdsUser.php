<?php
AAFW::import ( 'jp.aainc.aafw.base.aafwEntityBase' );
class AdsUser extends aafwEntityBase {

    public function isTwitterAds() {
        return $this->social_app_id == SocialApps::PROVIDER_TWITTER;
    }

    public function isFacebookAds() {
        return $this->social_app_id == SocialApps::PROVIDER_FACEBOOK;
    }

}
