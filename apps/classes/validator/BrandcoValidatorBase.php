<?php

abstract class BrandcoValidatorBase extends aafwObject {

    protected $brandId;
    protected $service;

    const SERVICE_NAME_FACEBOOK = 'FacebookStreamService';
    const SERVICE_NAME_TWITTER = 'TwitterStreamService';
    const SERVICE_NAME_YOUTUBE = 'YoutubeStreamService';
    const SERVICE_NAME_RSS = 'RssStreamService';
    const SERVICE_NAME_LINK = 'LinkEntryService';
    const SERVICE_NAME_INSTAGRAM = 'InstagramStreamService';
    const SERVICE_NAME_STATIC_HTML = 'StaticHtmlEntryService';
    const SERVICE_NAME_FREE_AREA = 'FreeAreaEntryService';
    const SERVICE_NAME_BRAND_SOCIAL_ACCOUNT = 'BrandSocialAccountService';
    const SERVICE_NAME_GLOBAL_MENU = 'BrandGlobalMenuService';
    const SERVICE_NAME_SIDE_MENU = 'BrandSideMenuService';
    const SERVICE_NAME_PHOTO = 'PhotoStreamService';
    const SERVICE_NAME_PAGE = 'PageStreamService';

    private static $panel_service_name_array = array(
        self::SERVICE_NAME_FACEBOOK,
        self::SERVICE_NAME_TWITTER,
        self::SERVICE_NAME_YOUTUBE,
        self::SERVICE_NAME_RSS,
        self::SERVICE_NAME_LINK,
        self::SERVICE_NAME_INSTAGRAM,
        self::SERVICE_NAME_PHOTO,
        self::SERVICE_NAME_PAGE
    );

    private static $service_names = array(
        self::SERVICE_NAME_FACEBOOK,
        self::SERVICE_NAME_TWITTER,
        self::SERVICE_NAME_YOUTUBE,
        self::SERVICE_NAME_RSS,
        self::SERVICE_NAME_LINK,
        self::SERVICE_NAME_INSTAGRAM,
        self::SERVICE_NAME_STATIC_HTML,
        self::SERVICE_NAME_FREE_AREA,
        self::SERVICE_NAME_BRAND_SOCIAL_ACCOUNT,
        self::SERVICE_NAME_GLOBAL_MENU,
        self::SERVICE_NAME_SIDE_MENU,
        self::SERVICE_NAME_PHOTO,
        self::SERVICE_NAME_PAGE
    );

    private static $stream_validator_based_service = array(
        self::SERVICE_NAME_RSS,
        self::SERVICE_NAME_PHOTO,
        self::SERVICE_NAME_PAGE
    );

    /**
     * @param $service_name
     * @param $brandId
     */
    public function __construct($service_name, $brandId) {
        parent::__construct();
        $this->setBrandId($brandId);
        if (in_array($service_name, self::$service_names)) {
            $this->setService($service_name);
        } else {
            $this->sevice = null;
        }
    }

    /**
     * @param $id
     */
    public function setBrandId($id){
        $this->brandId = $id;
    }

    /**
     * @param $name
     * @return $this|void
     */
    public function setService($name) {
        $this->service = $this->_ServiceFactory->create($name);
    }

    /**
     * @param $id
     * @return mixed
     */
    public abstract function isOwner($id);

    /**
     * @param $name
     * @return bool
     */
    public function isPanelServiceName($name){
        if(!in_array($name, self::$panel_service_name_array)) return false;
        return true;
    }

    /**
     * @param $service_name
     * @return bool
     */
    public static function isStreamValidatorBasedService($service_name) {
        return in_array($service_name, self::$stream_validator_based_service);
    }
}
