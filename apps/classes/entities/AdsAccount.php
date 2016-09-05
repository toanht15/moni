<?php
AAFW::import ( 'jp.aainc.aafw.base.aafwEntityBase' );
class AdsAccount extends aafwEntityBase {

    public static $sns_icon_class_2 = array(
        SocialApps::PROVIDER_FACEBOOK => 'iconFB2',
        SocialApps::PROVIDER_TWITTER => 'iconTW2_2',
    );

    public static $sns_icon_class_1 = array(
        SocialApps::PROVIDER_FACEBOOK => 'iconFB1',
        SocialApps::PROVIDER_TWITTER => 'iconTW1_2'
    );

    public static $sns_label = array(
        SocialApps::PROVIDER_FACEBOOK => 'Facebook',
        SocialApps::PROVIDER_TWITTER => 'Twitter',
    );

    public static $fb_account_permissions = array(
        "1" => "ACCOUNT_ADMIN",
        "2" => "ADMANAGER_READ",
        "3" => "ADMANAGER_WRITE",
        "4" => "BILLING_READ",
        "5" => "BILLING_WRITE",
        "7" => "REPORTS"
    );

    public static $fb_account_roles = array(
        "1001" => "Administrator access",
        "1002" => "Advertiser (ad manager) access",
        "1003" => "Analyst access",
        "1004" => "Direct sales access"
    );

    public static $fb_account_statuses = array(
        "1"     => "ACTIVE",
        "2"     => "DISABLED",
        "3"     => "UNSETTLED",
        "7"     => "PENDING_RISK_REVIEW",
        "9"     => "IN_GRACE_PERIOD",
        "100"   => "PENDING_CLOSURE",
        "101"   => "CLOSED",
        "102"   => "PENDING_SETTLEMENT",
        "201"   => "ANY_ACTIVE",
        "202"   => "ANY_CLOSED"
    );

    public function isFacebookAccount() {
        return $this->social_app_id == SocialApps::PROVIDER_FACEBOOK;
    }

    public function isTwitterAccount() {
        return $this->social_app_id == SocialApps::PROVIDER_TWITTER;
    }

    public function isValidAccount() {

        if($this->isFacebookAccount()) {
            return $this->isValidFacebookAccount();
        }

        if($this->isTwitterAccount()) {
            return $this->isValidTwitterAccount();
        }

        return false;
    }

    private function isValidFacebookAccount() {

        $extra_data = json_decode($this->extra_data, true);

        if($extra_data['custom_audience_tos'] == 1 && $extra_data['web_custom_audience_tos'] == 1) {
            return true;
        }

        return false;
    }

    private function isValidTwitterAccount() {

        $extra_data = json_decode($this->extra_data, true);

        if($extra_data['approval_status'] == 'ACCEPTED') {
            return true;
        }

        return false;
    }
}
