<?php
AAFW::import('jp.aainc.lib.base.aafwServiceBase');

class SocialAccountFollowerService extends aafwServiceBase {

    /** @var SocialAccountFollowers $social_account_followers */
    protected $social_account_followers;

    public function __construct() {
        $this->social_account_followers = $this->getModel("SocialAccountFollowers");
    }

    public function createEmptyBrandSocialAccount() {
        return $this->social_account_followers->createEmptyObject();
    }

    public function createSocialAccountFollowerCount(SocialAccountFollower $social_account_follower) {
        $this->social_account_followers->save($social_account_follower);
    }
}

