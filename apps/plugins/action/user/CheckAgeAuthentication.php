<?php
AAFW::import('jp.aainc.aafw.base.aafwActionPluginBase');
AAFW::import('jp.aainc.classes.core.UserAttributeManager');

class CheckAgeAuthentication extends aafwActionPluginBase {
    const FB_CRAWLER_USER_AGENT = "facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)";
    const TW_CRAWLER_USER_AGENT = "Twitterbot/1.0";
    const GGL_BOT_CRAWLER_USER_AGENT = "+http://www.google.com/bot.html";
    const GGL_ADSBOT_CRAWLER_USER_AGENT = "+http://www.google.com/adsbot.html";
    const BING_BOT_CRAWLER_USER_AGENT = "+http://www.bing.com/bingbot.htm";

    protected $user_agent_white_list = array(
        self::FB_CRAWLER_USER_AGENT,
        self::TW_CRAWLER_USER_AGENT,
        self::GGL_BOT_CRAWLER_USER_AGENT,
        self::GGL_ADSBOT_CRAWLER_USER_AGENT,
        self::BING_BOT_CRAWLER_USER_AGENT
    );

    protected $HookPoint = 'First';
    protected $Priority = 2; // SetFactoriesのあと

    public function doService() {

        if ($this->Action->SkipAgeAuthenticate) {
            return;
        }

        list($p, $g, $s, $c, $f, $e, $sv, $r) = $this->Action->getParams();

        if ($c['restrict_age']) {
            return;
        }

        /** @var BrandGlobalSettingService $brand_global_setting_service */
        $brand_global_setting_service = $this->Action->createService('BrandGlobalSettingService');
        $brand_global_setting = $brand_global_setting_service->getBrandGlobalSettingByName(BrandInfoContainer::getInstance()->getBrandGlobalSettings(), BrandGlobalSettingService::AUTHENTICATION_PAGE);

        if (Util::isNullOrEmpty($brand_global_setting)) {
            return;
        }

        $brand_page_setting = BrandInfoContainer::getInstance()->getBrandPageSetting();
        if (!$brand_page_setting->privacy_required_restricted || !$brand_page_setting->age_authentication_flg) {
            return;
        }

        if (method_exists($this->Action, 'isLoginAdmin') && $this->Action->isLoginAdmin()) {
            return;
        }

        if (method_exists($this->Action, 'isLogin') && $this->Action->isLogin()) {

            $user_info = (object)$s['pl_monipla_userInfo'];

            $userAttributeManager = new UserAttributeManager($user_info, $this->Action->getMoniplaCore());

            $birthday = $userAttributeManager->getBirthDay();

            if ($birthday) {
                $user_age = Util::getUserAge($birthday);
                if ($user_age >= $brand_page_setting->restricted_age) {
                    return;
                }
            }

        }

        if ($this->isUserAgentWhitelist()) {
            return;
        }

        return 'redirect: ' . Util::rewriteUrl('', 'authentication_page', array(), array('callback' => Util::getCurrentUrl()));
    }

    /**
     * @return bool
     */
    private function isUserAgentWhitelist() {
        if (!$_SERVER['HTTP_USER_AGENT']) return false;

        foreach ($this->user_agent_white_list as $user_agent) {
            if (strpos($_SERVER['HTTP_USER_AGENT'], $user_agent) !== false) {
                return true;
            }
        }

        return false;
    }
}