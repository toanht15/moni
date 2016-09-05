<?php
AAFW::import ( 'jp.aainc.aafw.base.aafwEntityBase' );
class FacebookMarketingAccount extends aafwEntityBase {

    protected $_Relations = array(

        'FacebookMarketingUsers' => array(
            'marketing_user_id' => 'id',
        )

    );

    const TWITTER_ADS = 1;
    const FACEBOOK_ADS = 2;

    public static $sns_icon_class_2 = array(
        self::FACEBOOK_ADS => 'iconFB2'
    );

    public static $sns_icon_class_1 = array(
        self::FACEBOOK_ADS => 'iconFB1'
    );

    public static $sns_label = array(
        self::FACEBOOK_ADS => 'facebook'
    );

    public function isConfirmCustomAudienceTos() {
        return $this->custom_audience_tos == 1;
    }

    public function isConfirmWebCustomAudienceTos() {
        return $this->web_custom_audience_tos == 1;
    }
}
