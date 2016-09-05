<?php
AAFW::import ( 'jp.aainc.aafw.base.aafwEntityBase' );
class SocialApp extends aafwEntityBase {

    public function isFacebook() {
        return $this->provider == SocialApps::PROVIDER_FACEBOOK;
    }

    public function isTwitter() {
        return $this->provider == SocialApps::PROVIDER_TWITTER;
    }

    public function isGoogle() {
        return $this->provider == SocialApps::PROVIDER_GOOGLE;
    }

    public function isInstagram() {
        return $this->provider == SocialApps::PROVIDER_INSTAGRAM;
    }
}
