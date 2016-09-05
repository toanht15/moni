<?php

AAFW::import('jp.aainc.lib.base.aafwServiceBase');

abstract class BrandSocialAccountProfileService extends aafwServiceBase {

    protected $client;
    protected $brandSocialAccountService;
    protected $logger;
    protected $config;

    public function __construct(){
        $this->brandSocialAccountService = $this->getService('BrandSocialAccountService');
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
        $this->config = aafwApplicationConfig::getInstance();
        $this->initClient();
    }

    abstract public function initClient();

    abstract public function getProfile($brandSocialAccount);

    abstract public function parserProfileData($sourceData);

    public function updateBrandSocialAccount($brandSocialAccount, $newProfile){

        if(Util::isNullOrEmpty($brandSocialAccount)){
            return;
        }

        $brandSocialAccount->name = $newProfile['name'];
        $brandSocialAccount->screen_name = $newProfile['screen_name'];
        $brandSocialAccount->about = $newProfile['about'];
        $brandSocialAccount->picture_url = $newProfile['picture_url'];
        $brandSocialAccount->store = $newProfile['store'];

        $this->brandSocialAccountService->updateBrandSocialAccount($brandSocialAccount);
    }
}

class FacebookProfileService extends BrandSocialAccountProfileService {

    public function initClient() {
        AAFW::import('jp.aainc.classes.FacebookApiClient');
        $this->client = new FacebookApiClient();
    }

    public function getProfile($brandSocialAccount) {

        if(Util::isNullOrEmpty($brandSocialAccount)) {
            return null;
        }

        if($brandSocialAccount->token_expired_flg) {
            return null;
        }

        $accountProfile = null;

        try {

            $this->client->setToken($brandSocialAccount->token);

            $param = '/' . $brandSocialAccount->social_media_account_id;
            $response = $this->client->getPageInfo($param, array('fields' => 'id,about,can_post,category,checkins,country_page_likes,cover,has_added_app,is_community_page,is_published,new_like_count,likes,link,location,name,offer_eligible,promotion_eligible,talking_about_count,unread_message_count,unread_notif_count,unseen_message_count,username,were_here_count'));
            $accountProfile = $this->parserProfileData($response);

            //Get Profile Image
            $pictureUrl = $this->client->getPageInfo($param, array('fields' => 'picture.width(200).height(200)'));
            $pictureUrl = $pictureUrl['picture']->data->url;

            $accountProfile['picture_url'] = $pictureUrl;

        } catch (Exception $e){
            $this->brandSocialAccountService->getErrorMessage($brandSocialAccount, $e);
            $this->logger->error('FacebookProfileService#getProfile() Exception brand_social_account_id = ' . $brandSocialAccount->id);
            $this->logger->error("Error Message: ".$e);
        }

        return $accountProfile;
    }

    public function parserProfileData($sourceData){

        $profile = array();

        $profile['name'] = $sourceData['name'];
        $profile['about'] = $sourceData['about'];
        $profile['store'] = json_encode($sourceData);

        return $profile;
    }
}

class TwitterProfileService extends BrandSocialAccountProfileService {

    CONST CODEBIRD_RETURNFORMAT_ARRAY = 1;

    public function initClient() {
        require_once('vendor/codebird-php/src/codebird.php');

        \Codebird\Codebird::setConsumerKey(
            $this->config->query('@twitter.Admin.ConsumerKey'),
            $this->config->query('@twitter.Admin.ConsumerSecret')
        );

        $this->client = \Codebird\Codebird::getInstance();
        $this->client->setReturnFormat(self::CODEBIRD_RETURNFORMAT_ARRAY);
    }

    public function getProfile($brandSocialAccount) {

        if(Util::isNullOrEmpty($brandSocialAccount)) {
            return null;
        }

        if($brandSocialAccount->token_expired_flg) {
            return null;
        }

        $accountProfile = null;

        try{

            $this->client->setToken($brandSocialAccount->token, $brandSocialAccount->token_secret);
            $response = $this->client->account_verifyCredentials();

            //check errors
            $errorMessage = $this->brandSocialAccountService->getErrorMessage($brandSocialAccount, $response);
            if ($errorMessage) {
                $this->logger->error("TwitterProfileService#getProfile() Exception brand_social_account_id = " . $brandSocialAccount->id);
                $this->logger->error($response);

                return null;
            }

            $accountProfile = $this->parserProfileData($response);

        } catch(Exception $e){
            $this->brandSocialAccountService->getErrorMessage($brandSocialAccount, $e);
            $this->logger->error('TwitterProfileService#getProfile() Exception brand_social_account_id = ' . $brandSocialAccount->id);
            $this->logger->error("Error Message: ".$e);
        }

        return $accountProfile;
    }

    public function parserProfileData($sourceData){

        $profile = array();

        $profile['name'] = $sourceData['name'];
        $profile['screen_name'] = $sourceData['screen_name'];
        $profile['about'] = $sourceData['description'];
        $profile['picture_url'] = $this->getOriginalProfileImage($sourceData['profile_image_url_https']);
        $profile['store'] = json_encode($sourceData);

        return $profile;
    }

    private function getOriginalProfileImage($img_url) {
        if (substr_count($img_url, '_bigger.') == 1) {
            $img_url = str_replace("_bigger.", ".", $img_url);

        } elseif (substr_count($img_url, '_normal.') == 1) {
            $img_url = str_replace("_normal.", ".", $img_url);

        } elseif (substr_count($img_url, '_mini.') == 1) {
            $img_url = str_replace("_mini.", ".", $img_url);
        }
        return $img_url;
    }
}

class InstagramProfileService extends BrandSocialAccountProfileService {

    public function initClient() {
        AAFW::import('jp.aainc.vendor.instagram.Instagram');

        $this->client = new Instagram();
        $this->client->setClientId($this->config->query('@instagram.Admin.ClientID'));
        $this->client->setClientSecret($this->config->query('@instagram.Admin.ClientSecret'));
        $this->client->setScope($this->config->query('@instagram.Admin.Scopes'));
    }

    public function getProfile($brandSocialAccount) {

        if(Util::isNullOrEmpty($brandSocialAccount)) {
            return null;
        }

        if($brandSocialAccount->token_expired_flg) {
            return null;
        }

        $accountProfile = null;

        try {

            $response = $this->client->getAccountInfo($brandSocialAccount->social_media_account_id, $brandSocialAccount->token);
            $accountProfile = $this->parserProfileData($response);

        } catch (Exception $e){
            $this->brandSocialAccountService->getErrorMessage($brandSocialAccount, $e);
            $this->logger->error('InstagramProfileService#getProfile() Exception brand_social_account_id = ' . $brandSocialAccount->id);
            $this->logger->error("Error Message: ".$e);
        }

        return $accountProfile;
    }

    public function parserProfileData($sourceData){
        $profile = array();

        if($sourceData->data){

            $profile['name'] = $sourceData->data->username;
            $profile['screen_name'] = $sourceData->data->full_name ? : $sourceData->data->username;
            $profile['picture_url'] = $sourceData->data->profile_picture;
            $profile['store'] = json_encode($sourceData->data);
        }

        return $profile;
    }
}

class YoutubeProfileService extends BrandSocialAccountProfileService {

    public function initClient() {

        require_once('vendor/google/Google_Client.php');
        require_once('vendor/google/contrib/Google_YouTubeService.php');
        require_once('vendor/google/contrib/Google_Oauth2Service.php');

        $this->client = new Google_Client();
        $this->client->setClientId($this->config->query('@google.Google.ClientID'));
        $this->client->setClientSecret($this->config->query('@google.Google.ClientSecret'));
        $scope = array();
        $apiBase = $this->config->query('@google.Google.ApiBaseUrl');
        foreach ($this->config->query('@google.Google.Scope') as $url) {
            array_push($scope, $apiBase.'/'.$url);
        }

        $this->client->setScopes($scope);
    }

    public function getProfile($brandSocialAccount) {

        if(Util::isNullOrEmpty($brandSocialAccount)) {
            return null;
        }

        if($brandSocialAccount->token_expired_flg) {
            return null;
        }

        $accountProfile = null;

        try {
            $this->client->setAccessToken($brandSocialAccount->token);
            $plus = new Google_Oauth2Service($this->client);
            $response = $plus->userinfo->get();

            $youtube = new Google_YouTubeService($this->client);

            $channelsResponse = $youtube->channels->listChannels('contentDetails', array(
                'mine' => 'true',
            ));

            $response['channelId'] = $channelsResponse['items'][0]['id'];

            $accountProfile = $this->parserProfileData($response);

        } catch (Exception $e){
            $this->brandSocialAccountService->getErrorMessage($brandSocialAccount, $e);
            $this->logger->error('YoutubeProfileService#getProfile() Exception brand_social_account_id = ' . $brandSocialAccount->id);
            $this->logger->error("Error Message: ".$e);
        }

        return $accountProfile;
    }

    public function parserProfileData($sourceData){
        $profile = array();

        $profile['name'] = $sourceData['name'];
        $profile['screen_name'] = $sourceData['name'];
        $profile['picture_url'] = $sourceData['picture'].'?sz=200';
        $profile['store'] = json_encode($sourceData);

        return $profile;
    }
}

class BrandSocialAccountProfileServiceFactory {

    public function creatBrandSocialAccountProfileService($type){
        $brandSocialProfileService = null;

        switch($type){
            case SocialApps::PROVIDER_FACEBOOK:
                $brandSocialProfileService = new FacebookProfileService();
                break;
            case SocialApps::PROVIDER_TWITTER:
                $brandSocialProfileService = new TwitterProfileService();
                break;
            case SocialApps::PROVIDER_GOOGLE:
                $brandSocialProfileService = new YoutubeProfileService();
                break;
            case SocialApps::PROVIDER_INSTAGRAM:
                $brandSocialProfileService = new InstagramProfileService();
                break;
        }

        return $brandSocialProfileService;
    }

}
