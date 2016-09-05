<?php
AAFW::import ( 'jp.aainc.aafw.base.aafwEntityBase' );
class FacebookMarketingAudience extends aafwEntityBase {

    const STATUS_DRAFT = 0;
    const STATUS_ACTIVE = 1;

    const DESCRIPTION_FLG_OFF = 0;
    const DESCRIPTION_FLG_ON = 1;

    public static $operation_status = array(
        "0"   => "Status not available",
        "200" => "Normal", // there is no updating or issues found
        "400" => "Warning", // there is some message we would like advertisers to know
        "410" => "No upload", // no file has been uploaded
        "411" => "Low match rate", // low rate of matched people
        "412" => "High invalid rate", // high rate of invalid people
        "421" => "No pixel", // Your Custom Audience pixel hasn't been installed on your website yet
        "422" => "Pixel not firing", // Your Custom Audience pixel isn't firing
        "423" => "Invalid pixel", // Your Custom Audience pixel is invalid
        "431" => "Lookalike Audience refresh failed",
        "432" => "Lookalike Audience build failed",
        "433" => "Lookalike Audience build failed",
        "434" => "Lookalike Audience build retrying",
        "500" => "Error", // there is some error and advertisers need to take action items to fix the error
    );

    protected $_Relations = array(

        'FacebookMarketingAccounts' => array(
            'account_id' => 'id',
        ),

        'FacebookMarketingSearchFanHistories' => array(
            'id' => 'audience_id',
        ),

    );

    public function getMarketingUser() {
        $account = $this->getFacebookMarketingAccount();
        if (!$account) {
            return null;
        }
        return $account->getFacebookMarketingUser();
    }

    public function isActiveAudience() {
        return $this->status == self::STATUS_ACTIVE;
    }
}
