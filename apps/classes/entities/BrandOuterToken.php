<?php

AAFW::import('jp.aainc.aafw.base.aafwEntityBase');

class BrandOuterToken extends aafwEntityBase {

    protected $_Relations = array(
        'Brands' => array (
            'brand_id' => 'id',
        ),
        'SocialApps' => array(
            'social_app_id' => 'id'
        ),
    );

    public function isFacebook() {
        return $this->social_app_id == SocialApps::PROVIDER_FACEBOOK;
    }

    public function isTwitter() {
        return $this->social_app_id == SocialApps::PROVIDER_TWITTER;
    }

    public function isGoogle() {
        return $this->social_app_id == SocialApps::PROVIDER_GOOGLE;
    }

    public function isInstagram() {
        return $this->social_app_id == SocialApps::PROVIDER_INSTAGRAM;
    }
}
