<?php

AAFW::import('jp.aainc.aafw.base.aafwEntityBase');
AAFW::import('jp.aainc.classes.brandco.cp.interface.CpCreator');
AAFW::import('jp.aainc.classes.brandco.cp.CpInstantWinActionManager');
AAFW::import('jp.aainc.classes.CacheManager');

class Cp extends aafwEntityBase {

    //種別
    const TYPE_CAMPAIGN      = "1";  // キャンペーン
    const TYPE_MESSAGE       = "2";  // メッセージ

    const STATUS_DRAFT       = "1";  // 下書き
    const STATUS_SCHEDULE    = "2";  // 予約済み
    const STATUS_FIX         = "3";  // 確定
    const STATUS_DEMO        = "4";  //デモモード
    const STATUS_CLOSE       = "5";  // クローズ

    const PAGE_USER_LIST           = "1";  //対象ユーザー選択ページ
    const PAGE_EDIT_MESSAGE        = "2";  //メッセージ作成ページ
    const PAGE_SETTING_OPTION      = "3";  //オプション設定ページ
    const PAGE_SHOW_MESSAGE_MANUAL = "4";  //マニュアルページ
    const PAGE_SHOW_RESERVATION_INFO = "5"; //配信予約ページ

    //アーカイブ
    const ARCHIVE_OFF = "0";
    const ARCHIVE_ON = "1";

    //キャンペンの基本設定の状態
    const SETTING_DRAFT = "0";
    const SETTING_FIX   = "1";

    //スケルトン種類
//    const SKELETON_BASIC     = "1";
    const SKELETON_COPY      = "2";
    const SKELETON_DRAFT     = "3";
    const SKELETON_NEW       = "4";
    const SKELETON_ADD       = "5";

    //キャンペーン状態
    const CAMPAIGN_STATUS_DRAFT            = "1";
    const CAMPAIGN_STATUS_SCHEDULE         = "2";
    const CAMPAIGN_STATUS_OPEN             = "3";
    const CAMPAIGN_STATUS_WAIT_ANNOUNCE    = "4";
    const CAMPAIGN_STATUS_CLOSE            = "5";
    const CAMPAIGN_STATUS_DEMO             = "6";
    const CAMPAIGN_STATUS_CP_PAGE_CLOSED   = "7";


    //メッセージ状態
    const MESSAGE_STATUS_DRAFT            = "1";
    const MESSAGE_STATUS_SCHEDULE         = "2";
    const MESSAGE_STATUS_OPEN             = "3";

    //発表方法
    const SHIPPING_METHOD_MESSAGE = '0';
    const SHIPPING_METHOD_PRESENT = '1';

    //基本スケルトン種類
    //基本セットの組み合わせなのでCpActionIDとは関連性なし
    const BASIC_SKELETON_PRESENT = 1;
    const BASIC_SKELETON_PHOTO = 2;
    const BASIC_SKELETON_QUESTIONNAIRE = 3;
    const BASIC_SKELETON_COUPON = 4;
    const BASIC_SKELETON_INSTANT_WIN = 5;
    const BASIC_SKELETON_MOVIE = 6;
    const BASIC_SKELETON_GIFT = 7;
    const BASIC_SKELETON_PAYMENT = 8;

    //オススメキャンペーンセットの為
    const TEMPLATE_SKELETON_RETWEET = 101;
    const TEMPLATE_SKELETON_TWEET = 102;
    const TEMPLATE_SKELETON_POPULAR_VOTE = 103;
    const TEMPLATE_SKELETON_QUESTIONNAIRE = 104;
    const TEMPLATE_SKELETON_DOUBLE_QUESTIONNAIRE = 105;
    const TEMPLATE_SKELETON_PHOTO_COLLECTION = 106;
    const TEMPLATE_SKELETON_INSTAGRAM_HASHTAG = 107;
    const TEMPLATE_SKELETON_TWITTER_FOLLOW = 108;
    const TEMPLATE_SKELETON_MOVIE_YOUTUBE_CHANNEL = 109;
    const TEMPLATE_SKELETON_INSTANT_WIN = 110;
    const TEMPLATE_SKELETON_COUPON = 111;
    const TEMPLATE_SKELETON_CODE_AUTHENTICATION = 112;
    const TEMPLATE_SKELETON_PHOTO_MUSTBUY = 113;

    // Permanent campaign's basic type
    const PERMANENT_SKELETON_QUESTIONNAIRE = 201;
    const PERMANENT_SKELETON_PAYMENT = 202;

    //キャンペンの基本設定種類
    const CP_SETTING_BASIC = 1;
    const CP_SETTING_ATTRACT = 2;

    //キャンペーンフラグ設定
    const SHOW_LP_PUBLIC     = "1";  // 公開
    const SHOW_LP_PRIVATE    = "2";  // 非公開

    const FLAG_SHOW_VALUE = "1";
    const FLAG_HIDE_VALUE = "0";

    const SHOW_RECRUITMENT_NOTE = "1";
    const HIDE_RECRUITMENT_NOTE = "0";

    const JOIN_LIMIT_ON = '1';  //限定
    const JOIN_LIMIT_OFF = '0'; //公開

    const JOIN_LIMIT_SNS_ON = '1';
    const JOIN_LIMIT_SNS_OFF = '0';

    const CP_RESTRICTED_AGE_FLG_ON  = '1';
    const CP_RESTRICTED_AGE_FLG_OFF = '0';

    const CP_RESTRICTED_GENDER_FLG_ON  = '1';
    const CP_RESTRICTED_GENDER_FLG_OFF = '0';

    const CP_RESTRICTED_GENDER_FEMALE   = '1';
    const CP_RESTRICTED_GENDER_MALE     = '2';

    public static $cp_restricted_gender = array(
        self::CP_RESTRICTED_GENDER_FEMALE  => '女性',
        self::CP_RESTRICTED_GENDER_MALE    => '男性'
    );

    public static $cp_restricted_brief_gender = array(
        self::CP_RESTRICTED_GENDER_FEMALE => 'f',
        self::CP_RESTRICTED_GENDER_MALE   => 'm'
    );

    const CP_RESTRICTED_ADDRESS_FLG_ON  = '1';
    const CP_RESTRICTED_ADDRESS_FLG_OFF = '0';

    const PUBLIC_DATE_ON  = '1';
    const PUBLIC_DATE_OFF = '0';

    const CLOSE_DATE_ON  = '1';
    const CLOSE_DATE_OFF = '0';

    const AU_FLG_ON  = '1';
    const AU_FLG_OFF = '0';

    const REFERENCE_URL_TYPE_CP = 0;
    const REFERENCE_URL_TYPE_LP = 1;

    const PERMANENT_FLG_ON  = '1';
    const PERMANENT_FLG_OFF = '0';

    const CRM_SEND_TEXT_MAIL_FLG_ON = '1';
    const CRM_SEND_TEXT_MAIL_FLG_OFF = '0';

    public static $basic_skeleton_type = [
        self::BASIC_SKELETON_PRESENT,
        self::BASIC_SKELETON_PHOTO,
        self::BASIC_SKELETON_QUESTIONNAIRE,
        self::BASIC_SKELETON_COUPON,
        self::BASIC_SKELETON_INSTANT_WIN,
        self::BASIC_SKELETON_MOVIE,
        self::BASIC_SKELETON_GIFT,
        self::BASIC_SKELETON_PAYMENT
    ];

    public static $template_skeleton_type = [
        self::TEMPLATE_SKELETON_RETWEET,
        self::TEMPLATE_SKELETON_TWEET,
        self::TEMPLATE_SKELETON_POPULAR_VOTE,
        self::TEMPLATE_SKELETON_QUESTIONNAIRE,
        self::TEMPLATE_SKELETON_DOUBLE_QUESTIONNAIRE,
        self::TEMPLATE_SKELETON_PHOTO_COLLECTION,
        self::TEMPLATE_SKELETON_INSTAGRAM_HASHTAG,
        self::TEMPLATE_SKELETON_TWITTER_FOLLOW,
        self::TEMPLATE_SKELETON_MOVIE_YOUTUBE_CHANNEL,
        self::TEMPLATE_SKELETON_INSTANT_WIN,
        self::TEMPLATE_SKELETON_COUPON,
        self::TEMPLATE_SKELETON_CODE_AUTHENTICATION,
        self::TEMPLATE_SKELETON_PHOTO_MUSTBUY
    ];

    public static $permanent_skeleton_type = [
        self::PERMANENT_SKELETON_QUESTIONNAIRE,
        self::PERMANENT_SKELETON_PAYMENT
    ];

    public static $send_message_array =  [
        self::CAMPAIGN_STATUS_SCHEDULE,
        self::CAMPAIGN_STATUS_OPEN,
        self::CAMPAIGN_STATUS_WAIT_ANNOUNCE,
        self::CAMPAIGN_STATUS_CLOSE,
        self::CAMPAIGN_STATUS_DEMO
    ];

    public static $cp_type_array =  [
        self::TYPE_CAMPAIGN => "キャンペーン",
        self::TYPE_MESSAGE => "メッセージ"
    ];

    public static $join_limit_array =  [
        self::JOIN_LIMIT_OFF => "公開",
        self::JOIN_LIMIT_ON => "限定"
    ];

    protected $_Relations = array(
        'Brands' => array(
            'brand_id' => 'id',
        ),
        'CpJoinLimitSnses' => array(
            'id' => 'cp_id',
        ),
        'CpActionGroups' => array(
            'id' => 'cp_id'
        ),
        'CpUsers' => array(
            'id' => 'cp_id'
        ),
        'CpRestrictedAddresses' => array(
            'id' => 'cp_id'
        ),
        'SynCps' => array(
            'id' => 'cp_id'
        ),
        'Products' => array(
            'id' => 'cp_id'
        )
    );

    /**
     * @param null $brand
     * @return string
     */
    public function getUrlPath($brand = null) {
        if (!$brand) {
            $brand = $this->getBrand();
        }

        return '/' . $brand->directory_name . '/campaigns/' . $this->id;
    }

    /**
     * @param bool $secure
     * @param null $brand
     * @return string
     */
    public function getUrl($secure = false, $brand = null) {
        if (!$brand) {
            $brand = $this->getBrand();
        }
        $base_url = $brand->getUrl($secure);
        return Util::rewriteUrl("", "campaigns", array("cp_id" => $this->id), array(), $base_url);
    }

    /**
     * @param bool|false $secure
     * @param null $brand
     * @return string
     */
    public function getThreadUrl($secure = false, $brand = null) {
        if (!$brand) {
            $brand = $this->getBrand();
        }
        $base_url = $brand->getUrl($secure);
        return Util::rewriteUrl("messages", "thread", array("cp_id" => $this->id), array(), $base_url);
    }

    /**
     * @param bool $secure
     * @param null $brand
     * @return string
     */
    public function getReferenceUrl($secure = false, $brand = null) {
        if (!$this->reference_url) {
            return $this->getUrl($secure, $brand);
        }

        $path = array_slice(explode('/', $this->reference_url), 2);

        if (!$brand) {
            $brand = $this->getBrand();
        }
        $base_url = $brand->getUrl($secure);

        return $base_url . implode('/', $path);
    }

    public function getDemoUrl($secure = false, $brand = null) {
        return $this->getUrl($secure, $brand) . '?demo_token=' . hash("sha256", $this->created_at);
    }

    public function getOpenGraphImgUrl(){
        return $this->image_rectangle_url ?: $this->image_url;
    }

    public function getReferenceOpenGraphImgUrl($reference_page) {
        if ($reference_page && $reference_page->og_image_url) {
            return $reference_page->og_image_url;
        }

        return $this->getOpenGraphImgUrl();
    }

    public function getOpenGraphDescription(){
        $service_factory = new aafwServiceFactory();
        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = $service_factory->create('CpFlowService');
        list(,$entry_action) = $cp_flow_service->getEntryActionInfoByCpId($this->id);

        return strip_tags($entry_action->html_content);
    }

    public function getReferenceOpenGraphDescription($reference_page) {
        if ($reference_page && $reference_page->meta_description) {
            return $reference_page->meta_description;
        }

        return $this->getOpenGraphDescription();
    }

    public function getOpenGraphInfo($brand = null){
        if ($brand === null) {
            $brand = $this->getBrand();
        }
        $og_info = array();
        $og_info['title']       = $this->getTitle() . ' / ' . $brand->name;
        $og_info['image']       = $this->getOpenGraphImgUrl();
        $og_info['description'] = $this->getOpenGraphDescription();
        $og_info['url']         = $this->getUrl(true, $brand);
        $og_info['brand_image'] = $brand->profile_img_url;
        $og_info['brand_name']  = $brand->name;

        return $og_info;
    }

    public function getReferenceOpenGraphInfo() {
        $path = explode('/', $this->reference_url);

        if (!$path[2] || $path[2] != 'page') return $this->getOpenGraphInfo();

        $service_factory = new aafwServiceFactory();
        $static_html_entry_service = $service_factory->create('StaticHtmlEntryService');

        $reference_page = $static_html_entry_service->getEntryByBrandIdAndPageUrl($this->brand_id, $path[3]);

        if (!$reference_page) return $this->getOpenGraphInfo();

        $brand = $this->getBrand();
        $og_info = array();

        $og_info['title'] = $this->getReferenceTitle($reference_page) . ' / ' . $brand->name;
        $og_info['image']       = $this->getReferenceOpenGraphImgUrl($reference_page);
        $og_info['description'] = $this->getReferenceOpenGraphDescription($reference_page);
        $og_info['url']         = $this->getReferenceUrl(true);
        $og_info['brand_image'] = $brand->profile_img_url;
        $og_info['brand_name']  = $brand->name;

        return $og_info;
    }

    /**
     * @return string
     */
    public function getTitle($first_action = null) {
        if ($first_action === null) {
            $service_factory = new aafwServiceFactory();
            /** @var CpFlowService $service */
            $service = $service_factory->create('CpFlowService');
            $first_action = $service->getFirstActionOfCp($this->id);
            if ($first_action) {
                $first_action = $first_action->getCpActionData();
            }
        }
        return $first_action->title ? $first_action->title : '名称未設定のキャンペーン';
    }

    /**
     * @return string
     */
    public function getTitleAndLpImageUrl($first_action = null) {
        if ($first_action === null) {
            $service_factory = new aafwServiceFactory();
            /** @var CpFlowService $service */
            $service = $service_factory->create('CpFlowService');
            $first_action = $service->getFirstActionOfCp($this->id);
            if ($first_action) {
                $first_action = $first_action->getCpActionData();
            }
        }

        $title = $first_action->title ?: '名称未設定のキャンペーン';
        $lp_image_url = $first_action->image_url ?: '';

        return array($title, $lp_image_url);
    }

    /**
     * @param $reference_page
     * @param null $first_action
     * @return string
     */
    public function getReferenceTitle($reference_page, $first_action = null) {
        if ($reference_page && $reference_page->meta_title) {
            return $reference_page->meta_title;
        }

        return $this->getTitle($first_action);
    }

    public function getIcon() {
        if ($this->type == self::TYPE_CAMPAIGN) {
            return $this->image_url ? $this->image_url : '';
        } else {
            $service_factory = new aafwServiceFactory();
            /** @var CpFlowService $service */
            $service = $service_factory->create('CpFlowService');
            $first_action = $service->getFirstActionOfCp($this->id);
            $basic_url = aafwApplicationConfig::getInstance()->query('Static.Url');
            return $basic_url.'/img/module/'.$first_action->getCpActionDetail()['icon'];
        }
    }

    /**
     * 2日以内
     * @return bool
     */
    public function isNew() {
        return (time() - strtotime($this->start_date)) <= (60 * 60 * 48 * 1);
    }

    /**
     * @return bool
     */
    public function canSendMessage() {

        $status = $this->getStatus();
        if (in_array($status, self::$send_message_array)) {
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function canEntry($status = null) {
        if ($status === null) {
            $status = $this->getStatus();
        }

        if ($status != self::CAMPAIGN_STATUS_OPEN && $status != self::CAMPAIGN_STATUS_DEMO) {
            return false;
        }

        if ($this->isOverTime() || $this->isOverLimitWinner()) {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    public function isOverLimitWinner() {
        // 他の要件をチェックする
        $service_factory = new aafwServiceFactory();
        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = $service_factory->create('CpFlowService');

        if ($this->selection_method == CpCreator::ANNOUNCE_FIRST) {
            $entry_action = $cp_flow_service->getFirstActionOfCp($this->id);

            /** @var CpUserActionStatusService $cp_user_action_status_service */
            $cp_user_action_status_service = $service_factory->create('CpUserActionStatusService');
            $joined_user_count = $cp_user_action_status_service->countCpUserActionStatusByCpActionAndStatus($entry_action->id, 1);

            if ($joined_user_count && $joined_user_count >= $this->winner_count) {
                return true;
            }
        } elseif ($this->selection_method == CpCreator::ANNOUNCE_LOTTERY) {
            $cp_action = $cp_flow_service->getInstantWinActionByCpId($this->id);

            /** @var CpInstantWinActionManager $cp_instant_win_action_manager */
            $cp_instant_win_action_manager = new CpInstantWinActionManager();
            $cp_instant_win_action = $cp_instant_win_action_manager->getCpConcreteActionByCpActionId($cp_action['id']);

            /** @var InstantWinPrizeService $instant_win_prize_service */
            $instant_win_prize_service = $service_factory->create('InstantWinPrizeService');
            $instant_win_prize = $instant_win_prize_service->getInstantWinPrizeByPrizeStatus($cp_instant_win_action->id, InstantWinPrizes::PRIZE_STATUS_PASS);

            if ($instant_win_prize->winner_count >= $this->winner_count) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return bool
     */
    public function isOverTime() {

        if ($this->status == self::STATUS_DEMO) {
            return false;
        }

        $dt = new DateTime();
        $now = strtotime($dt->format('Y-m-d H:i:s'));

        if (strtotime($this->start_date) > $now) {
            return true;
        }

        if (!$this->isPermanent() && $now >= strtotime($this->end_date)) {
            return true;
        }
        return false;
    }

    /**
     * 当選発表日を過ぎているかどうか
     * @return bool
     */
    public function isOverAnnounceDate() {
        $now = strtotime(date('Y-m-d 00:00:00'));
        return $now >= strtotime(date('Y-m-d 00:00:00', strtotime($this->announce_date)));
    }

    public function isHasAction($action_type) {
        $service_factory = new aafwServiceFactory();
        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = $service_factory->create('CpFlowService');
        $actions = $cp_flow_service->getCpActionsByCpId($this->id);
        foreach ($actions as $action) {
            if ($action->type == $action_type) {
                return true;
            }
        }
        return false;
    }

    /**
     * 非常に重い処理なので呼出には要注意! 基本的にRequestuserInfoContainer->getStatusByCpでキャッシュしましょう。
     *
     * キャンペーンのステータスを返す。
     * @return string
     */
    public function getStatus() {

        if ($this->status == self::STATUS_DRAFT) {

            return self::CAMPAIGN_STATUS_DRAFT;

        } elseif ($this->status == self::STATUS_SCHEDULE) {

            return self::CAMPAIGN_STATUS_SCHEDULE;

        } elseif ($this->status == self::STATUS_DEMO) {

            return self::CAMPAIGN_STATUS_DEMO;

        } elseif ($this->status == self::STATUS_CLOSE) {

            return self::CAMPAIGN_STATUS_CP_PAGE_CLOSED;

        } else {
            if( $this->selection_method === CpNewSkeletonCreator::ANNOUNCE_FIRST ||
                $this->selection_method === CpNewSkeletonCreator::ANNOUNCE_LOTTERY) {
                if($this->isOverLimitWinner()) {
                    return self::CAMPAIGN_STATUS_CLOSE;
                }
            }

            $dt = new DateTime();
            $now = strtotime($dt->format('Y-m-d H:i:s'));

            if ($this->isPermanent() && strtotime($this->public_date) < $now) {
                return self::CAMPAIGN_STATUS_OPEN;
            }

            if (strtotime($this->public_date) < $now && $now < strtotime($this->end_date)) {

                return self::CAMPAIGN_STATUS_OPEN;

            } elseif ($this->isNonIncentiveCp() && $now > strtotime($this->end_date)) {

                return self::CAMPAIGN_STATUS_CLOSE;

            } elseif ($now > strtotime($this->end_date) && $now < strtotime($this->announce_date)) {

                return self::CAMPAIGN_STATUS_WAIT_ANNOUNCE;

            } elseif ($now > strtotime($this->announce_date)) {

                return self::CAMPAIGN_STATUS_CLOSE;

            } else {

                // 不正な値のためidをログに出力する
                $logger = aafwLog4phpLogger::getDefaultLogger();
                $logger->warn("Cp status is invalid. cp_id = " . $this->id . " ");

                return self::CAMPAIGN_STATUS_DRAFT;
            }
        }
    }

    public function refreshJoinLimitSns($snsArray) {

        $joinLimitSnses = $this->getModel("CpJoinLimitSnses");
        $list = $joinLimitSnses->find(array('cp_id' => $this->id));
        foreach($list as $sns) {
            $joinLimitSnses->delete($sns);
        }
        foreach($snsArray as $social_media_id) {
            $joinLimitSns = $joinLimitSnses->createEmptyObject();
            $joinLimitSns->cp_id = $this->id;
            $joinLimitSns->social_media_id = $social_media_id;
            $joinLimitSnses->save($joinLimitSns);
        }

        $cache_manager = new CacheManager();
        $cache_manager->clearCampaignLPInfo($this->id);
    }

    public function getJoinLimitSns($list = null) {
        $ret = array();
        if ($list === null) {
            $list = $this->getCpJoinLimitSnses();
        }
        foreach($list as $item) {
            $ret[] = $item->social_media_id;
        }
        return $ret;
    }

    public function hasJoinLimitSnsWithoutPlatform($list = null) {
        if (!$this->join_limit_sns_flg) {
            return false;
        }

        if ($list === null) {
            $list = $this->getCpJoinLimitSnses();
        }
        foreach($list as $item) {
            if($item != SocialAccountService::SOCIAL_MEDIA_PLATFORM) {
                return true;
            }
        }
        return false;
    }

    /**
     * 種別がキャンペーンであるか
     *
     * @return boolean
     */
    public function isCpTypeCampaign() {
        return $this->type == self::TYPE_CAMPAIGN;
    }

    /**
     * 種別がメッセージであるか
     *
     * @return boolean
     */
    public function isCpTypeMessage() {
        return $this->type == self::TYPE_MESSAGE;
    }

    /**
     * @return bool
     */
    public function isAuCampaign() {
        return $this->au_flg == self::AU_FLG_ON;
    }

    /**
     * @return true
     */
    public function isRestrictedCampaign() {
        return $this->restricted_age_flg || $this->restricted_gender_flg || $this->restricted_address_flg;
    }

    public function requireRestriction($cp_user) {
        return $this->isRestrictedCampaign() && $cp_user->isIncompleteDemography();
    }

    /**
     * @param $restricted_addresses
     */
    public function updateCpRestrictedAddress($restricted_addresses) {
        $cp_restricted_addresses = $this->getModel('CpRestrictedAddresses');

        $cur_addresses = $cp_restricted_addresses->find(array('cp_id' => $this->id));
        foreach ($cur_addresses as $cur_address) {
            $cp_restricted_addresses->delete($cur_address);
        }

        foreach ($restricted_addresses as $restricted_address) {
            $cp_restricted_address = $cp_restricted_addresses->createEmptyObject();
            $cp_restricted_address->cp_id = $this->id;
            $cp_restricted_address->pref_id = $restricted_address;
            $cp_restricted_addresses->save($cp_restricted_address);
        }
    }

    /**
     * @return array
     */
    public function getRestrictedAddresses() {
        $restricted_addresses = array();
        $list = $this->getCpRestrictedAddresses();

        foreach($list as $item) {
            $restricted_addresses[] = $item->pref_id;
        }

        return $restricted_addresses;
    }

    /**
     * クローズしたキャンペーン用の画像取得
     * @return string
     */
    public static function getClosedCampaignImage() {
        $config = aafwApplicationConfig::getInstance();
        $domain = $config->query('Static.Url');
        return $domain . '/img/appIcon/iconMPLogo360.png';
    }

    /**
     * @return boolean
     */
    public function isCampaignStatusOpen() {
        $dt = new DateTime();
        $now = strtotime($dt->format('Y-m-d H:i:s'));

        if (strtotime($this->public_date) > $now) return false;

        if (!$this->isPermanent() && strtotime($this->end_date) <= $now) return false;

        return true;
    }

    /**
     * @return boolean
     */
    public function isDemo() {
        return $this->status == self::STATUS_DEMO;
    }

    /**
     * @return bool
     */
    public function isFixed() {
        return $this->status === self::STATUS_FIX;
    }

    /**
     * キャンペーン期間が終了しているかどうか
     * @return bool
     */
    public function isCampaignTermFinished($cp_status = null) {
        if ($cp_status === null) {
            $cp_status = $this->getStatus();
        }
        return ($cp_status == self::CAMPAIGN_STATUS_WAIT_ANNOUNCE || $cp_status == self::CAMPAIGN_STATUS_CLOSE);
    }

    /**
     * 常設キャンペーンであるかどうか
     * @return bool
     */
    public function isPermanent() {
        return $this->permanent_flg == self::PERMANENT_FLG_ON;
    }

    /**
     * インセンティブありキャンペーンであるかどうか
     * @return bool
     */
    public function isIncentiveCp() {
        return !$this->isNonIncentiveCp();
    }

    /**
     * インセンティブなしキャンペーンであるかどうか
     * @return bool
     */
    public function isNonIncentiveCp() {
        return $this->selection_method == CpNewSkeletonCreator::ANNOUNCE_NON_INCENTIVE;
    }

    /**
     * 限定キャンペーンかどうか
     * @return bool
     */
    public function isLimitCp() {
        return $this->join_limit_flg == self::JOIN_LIMIT_ON;
    }

    /**
     * アーカイブフラグが立っているかどうか
     * @return bool
     */
    public function isArchive() {
        return $this->archive_flg == self::ARCHIVE_ON;
    }

    public function getCpRectangleImage(){

        if($this->image_rectangle_url){
            return $this->image_rectangle_url;
        }

        $config = aafwApplicationConfig::getInstance();
        $domain = $config->query('Static.Url');
        return $domain . '/img/base/imgCpDummy1000.png';
    }

    /**
     * 公開後キャンペーンかどうか
     * @return bool
     */
    public function isActivedCp(){
        $cpStatus = $this->getStatus();
        if($cpStatus != Cp::CAMPAIGN_STATUS_DEMO && $cpStatus != Cp::CAMPAIGN_STATUS_DRAFT && $cpStatus != Cp::CAMPAIGN_STATUS_SCHEDULE){
            return true;
        }
        return false;
    }

    /**
     * キャンペーンは公開日や終了日が設定されたかどうか
     * @return bool
     */
    public function hasStartEndDate(){
        if(Util::isValidDate($this->start_date) && Util::isValidDate($this->end_date)){
            return true;
        }
        return false;
    }

    /**
     * Syn専用のキャンペーンかどうかの判定
     * @return bool
     */
    public function isForSyndotOnly(){
        return $this->brand_id == config('SynBrandId') && $this->getSynCp();
    }

    /** SynキャンペーンでかつSyn.メニューから遷移してきたか判定
     * @param $fromId
     * @return bool
     */
    public function isSynCpAndFromSynMenu($fromId){
        return $fromId == 'syncp001' && $this->getSynCp();
    }

    /** Synキャンペーンかどうか判定
    * @return bool
    */
    public function isSynCp(){
        return $this->getSynCp() ? true: false;
    }
}
