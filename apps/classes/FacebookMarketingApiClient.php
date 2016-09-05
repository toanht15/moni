<?php
AAFW::import('jp.aainc.vendor.autoload');

use FacebookAds\Api;
use FacebookAds\Object\Fields\AdAccountFields;
use FacebookAds\Object\Fields\CustomAudienceFields;
use FacebookAds\Object\CustomAudience;
use FacebookAds\Object\Values\CustomAudienceTypes;
use FacebookAds\Object\Values\CustomAudienceSubtypes;
use FacebookAds\Object\AdAccount;
use FacebookAds\Object\AdUser;

class FacebookMarketingApiClient extends aafwObject{
    /** @var  Api */
    private $api;

    public function __construct($access_token) {
        $this->api = Api::init(config("@facebook.MarketingAdmin.AppId"), config("@facebook.MarketingAdmin.AppSecretKey"), $access_token);

        if(config("@facebook.MarketingAdmin.ApiVersion")){
            $this->api->setDefaultGraphVersion(config("@facebook.MarketingAdmin.ApiVersion"));
        }
    }

    public function fetchMarketingAccountsInfo($account_ids, $ads_user) {

        $batch_param = $this->createUpdateAccountBatchParams($account_ids);
        $responses = $this->api->executeRequest($this->api->prepareRequest("", "POST", array("batch" => $batch_param)))->getContent();

        $return_data = array();

        foreach ($responses as $response) {
            $data = json_decode($response["body"], true);

            if ($response["code"] != 200) {
                $return_data[] = array('error' => $data["error"]);
                continue;
            }

            foreach ($data["users"]["data"] as $value) {
                if ($value["id"] == $ads_user->social_account_id) {
                    $role = $value["role"];
                    break;
                }
            }

            $extra_data = array(
                "role" => $role,
                "status" => $data["account_status"],
                "custom_audience_tos" => $data[AdAccountFields::TOS_ACCEPTED]["custom_audience_tos"],
                "web_custom_audience_tos" => $data[AdAccountFields::TOS_ACCEPTED]["web_custom_audience_tos"],
            );

            $account_info = array(
                "ads_user_id" => $ads_user->id,
                "account_id" => $data["id"],
                "account_name" => $data[AdAccountFields::NAME],
                "social_app_id" => SocialApps::PROVIDER_FACEBOOK,
                "extra_data" => json_encode($extra_data),
            );

            $return_data[] = $account_info;
        }

        return $return_data;
    }

    public function getCustomAudienceInfo($audience_list) {

        $batch_param = $this->createUpdateAudienceBatchParams($audience_list);

        $responses = $this->api->executeRequest($this->api->prepareRequest("", "POST", array("batch" => $batch_param)))->getContent();

        $return_data = array();
        foreach ($responses as $response) {
            $data = json_decode($response["body"], true);

            if ($response["code"] != 200) {
                $return_data[] = array('error' => $data["error"]);
                continue;
            }

            $return_data[] = $data;
        }

        return $return_data;
    }

    public function createUpdateAccountBatchParams($account_ids) {
        $param = '[';

        foreach ($account_ids as $account_id) {
            $param .= '{"method":"GET", "relative_url": "/'.$account_id;
            $param .= '?fields='.AdAccountFields::TOS_ACCEPTED.','.AdAccountFields::USERS.','.AdAccountFields::ACCOUNT_STATUS.','.AdAccountFields::NAME.'"},';
        }

        $param .= ']';

        return $param;
    }

    private function createUpdateAudienceBatchParams($audiences) {
        $param = '[';

        foreach ($audiences as $audience) {
            $param .= '{"method":"GET", "relative_url": "/'.$audience->audience_id.'?fields=';
            $param .= CustomAudienceFields::NAME.','.CustomAudienceFields::DESCRIPTION.','.CustomAudienceFields::DELIVERY_STATUS.',';
            $param .= CustomAudienceFields::ACCOUNT_ID.','.CustomAudienceFields::DATA_SOURCE.','.CustomAudienceFields::OPERATION_STATUS.'"},';
        }

        $param .= ']';

        return $param;
    }

    public function apiRemoveTarget ($audience_id, $fb_ids = array(), $emails = array()) {

        $audience = new CustomAudience($audience_id);

        if ($fb_ids && count($fb_ids) > 0) {
            $result = $audience->removeUsers($fb_ids, CustomAudienceTypes::ID, array(config("@facebook.User.AppId")));
            aafwLog4phpLogger::getDefaultLogger()->info("facebook_marketing_submit_fans remove fb_id audience_id=".$this->Data["audience_id"]);
            aafwLog4phpLogger::getDefaultLogger()->info($result);
        }

        if ($emails && count($emails) > 0) {
            $result = $audience->removeUsers($emails, CustomAudienceTypes::EMAIL, array(config("@facebook.User.AppId")));
            aafwLog4phpLogger::getDefaultLogger()->info("facebook_marketing_submit_fans remove email audience_id=".$this->Data["audience_id"]);
            aafwLog4phpLogger::getDefaultLogger()->info($result);
        }
    }

    public function apiAddTarget ($audience_id, $fb_ids = array(), $emails = array()) {

        $audience = new CustomAudience($audience_id);
        if ($fb_ids && count($fb_ids) > 0) {
            $result = $audience->addUsers($fb_ids, CustomAudienceTypes::ID, array(config("@facebook.User.AppId")));
            aafwLog4phpLogger::getDefaultLogger()->info("facebook_marketing_submit_fans add fb_id audience_id=".$this->Data["audience_id"]);
            aafwLog4phpLogger::getDefaultLogger()->info($result);
        }

        if ($emails && count($emails) > 0) {
            $result = $audience->addUsers($emails, CustomAudienceTypes::EMAIL, array(config("@facebook.User.AppId")));
            aafwLog4phpLogger::getDefaultLogger()->info("facebook_marketing_submit_fans add email audience_id=".$this->Data["audience_id"]);
            aafwLog4phpLogger::getDefaultLogger()->info($result);
        }
    }

    public function createOrUpdateCustomAudience($account_id, $audience_id, $data) {
        if ($audience_id) {
            $audience = new CustomAudience($audience_id);
        } else {
            $audience = new CustomAudience(null, $account_id);
        }

        $params = array(
            CustomAudienceFields::NAME => $data["name"],
            CustomAUdienceFields::DESCRIPTION => $data["description"]);

        if (!$audience_id) {
            $params[CustomAudienceFields::SUBTYPE] = CustomAudienceSubtypes::CUSTOM;
        }

        $audience->setData($params);

        if ($audience_id) {
            $created_audience = $audience->update();
        } else {
            $created_audience = $audience->create();
        }

        $audience->read(array(
            CustomAudienceFields::DELIVERY_STATUS,
            CustomAudienceFields::ACCOUNT_ID,
            CustomAudienceFields::NAME,
            CustomAudienceFields::DESCRIPTION,
            CustomAudienceFields::DATA_SOURCE,
            CustomAudienceFields::OPERATION_STATUS
        ));

        $audience_data = $created_audience->getData();

        return $audience_data;
    }

    public function getMarketingAccounts($after = '') {
        $user = new AdUser('me');
        if($after) {
            return $user->getAdAccounts(array(AdAccountFields::NAME, AdAccountFields::ACCOUNT_STATUS, AdAccountFields::USERS),array('after' => $after));
        }
        return $user->getAdAccounts(array(AdAccountFields::NAME, AdAccountFields::ACCOUNT_STATUS, AdAccountFields::USERS));
    }
}