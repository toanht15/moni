<?php

AAFW::import('jp.aainc.aafw.base.aafwEntityBase');
AAFW::import('jp.aainc.classes.brandco.cp.interface.CpActionManager');
AAFW::import('jp.aainc.classes.brandco.cp.CpEntryActionManager');
AAFW::import('jp.aainc.classes.brandco.cp.CpMessageActionManager');
AAFW::import('jp.aainc.classes.brandco.cp.CpButtonsActionManager');
AAFW::import('jp.aainc.classes.brandco.cp.CpShippingAddressActionManager');
AAFW::import('jp.aainc.classes.brandco.cp.CpQuestionnaireActionManager');
AAFW::import('jp.aainc.classes.brandco.cp.CpEngagementActionManager');
AAFW::import('jp.aainc.classes.brandco.cp.CpAnnounceActionManager');
AAFW::import('jp.aainc.classes.brandco.cp.CpFreeAnswerActionManager');
AAFW::import('jp.aainc.classes.brandco.cp.CpInstantWinActionManager');
AAFW::import('jp.aainc.classes.brandco.cp.CpJoinFinishActionManager');
AAFW::import('jp.aainc.classes.brandco.cp.CpMovieActionManager');
AAFW::import('jp.aainc.classes.brandco.cp.CpTwitterFollowActionManager');
AAFW::import('jp.aainc.classes.brandco.cp.CpFacebookLikeActionManager');
AAFW::import('jp.aainc.classes.brandco.cp.CpCodeAuthActionManager');
AAFW::import('jp.aainc.classes.brandco.cp.CpYoutubeChannelActionManager');
AAFW::import('jp.aainc.classes.brandco.cp.CpAnnounceDeliveryActionManager');
AAFW::import('jp.aainc.classes.brandco.cp.CpLineAddFriendActionManager');
AAFW::import('jp.aainc.classes.services.CpUserService');

class CpAction extends aafwEntityBase {

    const STATUS_DRAFT  = "0";              // 下書き
    const STATUS_FIX    = "1";              // 確定

    const END_TYPE_DEFAULT = "-1";          // 初期状態
    const END_TYPE_NONE = "0";              // 指定なし
    const END_TYPE_CP   = "1";              // 応募終了にあわせる
    const END_TYPE_ORIGINAL = "2";          // 任意の日時指定

    const TYPE_ENTRY     = "0";             // 参加
    const TYPE_MESSAGE   = "1";             // メッセージ
    const TYPE_PHOTO     = "2";             // 写真
    const TYPE_ANNOUNCE  = "3";             // 当選発表
    const TYPE_SHIPPING_ADDRESS = "4";      // アドレス
    const TYPE_QUESTIONNAIRE = "5";         // アンケート
    const TYPE_BUTTONS    = "6";            // 複数ボタン
    const TYPE_FREE_ANSWER = "7";           // 自由回答
    const TYPE_ENGAGEMENT = "8";            // エンゲージメント
    const TYPE_JOIN_FINISH = "9";           // 参加完了アクション
    const TYPE_INSTANT_WIN = "10";          // スピードくじ
    const TYPE_COUPON      = "11";          // クーポンアクション
    const TYPE_MOVIE = "12";                // 動画視聴
    const TYPE_SHARE = "13";                // シェア
    const TYPE_TWITTER_FOLLOW = "14";       // Twitterエンゲージメント
    const TYPE_FACEBOOK_LIKE = "15";        // Facebookエンゲージメント
    const TYPE_GIFT = "16";                 // ギフト
    const TYPE_INSTAGRAM_FOLLOW = "17";     // Instagramフォロー
    const TYPE_TWEET = "18";                // ツイート
    const TYPE_CODE_AUTHENTICATION = "19";  // 認証コード
    const TYPE_INSTAGRAM_HASHTAG = "20";    // Instagramハッシュタグ投稿
    const TYPE_RETWEET = "21";              // リツイート
    const TYPE_YOUTUBE_CHANNEL = "22";      // YouTubeチャンネル登録
    const TYPE_POPULAR_VOTE = "23";         // 人気投票
    const TYPE_CONVERSION_TAG = "24";       // コンバージョンタグ
    const TYPE_ANNOUNCE_DELIVERY = "25";    // 賞品の発送をもって発表
    const TYPE_LINE_ADD_FRIEND = "26";      // Line友達追加
    const TYPE_PAYMENT = "27";              // 決済

    const PREFILL_FLG_IGNORE = 0;
    const PREFILL_FLG_FILL = 1;

    /** @var array  $action_type_detail*/
    protected $action_type_detail = array(

        self::TYPE_ENTRY => array(
            'title' => 'キャンペーントップ',
            'icon' => 'cpBase.png',
            'form_action' => 'save_action_entry',
            'widget_class' => 'EditActionEntry'
        ),
        self::TYPE_MESSAGE => array(
            'title' => 'メッセージ',
            'icon' => 'mail1.png',
            'form_action' => 'save_action_message',
            'widget_class' => 'EditActionMessage'
        ),
        self::TYPE_PHOTO => array(
            'title' => '写真投稿',
            'icon' => 'photo1.png',
            'form_action' => 'save_action_photo',
            'widget_class' => 'EditActionPhoto'
        ),
        self::TYPE_ANNOUNCE => array(
            'title' => '当選通知',
            'icon' => 'win1.png',
            'form_action' => 'save_action_announce',
            'widget_class' => 'EditActionAnnounce'
        ),
        self::TYPE_SHIPPING_ADDRESS => array(
            'title' => '配送先情報',
            'icon' => 'address1.png',
            'form_action' => 'save_action_shipping_address',
            'widget_class' => 'EditActionShippingAddress'
        ),
        //         self::TYPE_BUTTONS => array(
        //             'title' => '複数ボタン',
        //             'icon' => 'brunch1.png',
        //             'form_action' => 'save_action_buttons',
        //             'widget_class' => 'EditActionButtons'
        //         ),
        self::TYPE_QUESTIONNAIRE => array(
            'title' => 'アンケート',
            'icon' => 'enquete1.png',
            'form_action' => 'save_action_questionnaire',
            'widget_class' => 'EditActionQuestionnaire'
        ),
        self::TYPE_ENGAGEMENT => array(
            'title' => 'エンゲージメント',
            'icon' => 'engagement1.png',
            'form_action' => 'save_action_engagement',
            'widget_class' => 'EditActionEngagement',
        ),
        self::TYPE_FREE_ANSWER => array(
            'title' => '自由回答',
            'icon' => 'kakikomi1.png',
            'form_action' => 'save_action_free_answer',
            'widget_class' => 'EditActionFreeAnswer'
        ),
        self::TYPE_JOIN_FINISH => array(
            'title' => '参加完了',
            'icon' => 'finish1.png',
            'form_action' => 'save_action_join_finish',
            'widget_class' => 'EditActionJoinFinish'
        ),
        self::TYPE_COUPON => array(
            'title' => 'クーポン',
            'icon' => 'coupon1.png',
            'form_action' => 'save_action_coupon',
            'widget_class' => 'EditActionCoupon'
        ),
        self::TYPE_INSTANT_WIN => array(
            'title' => 'スピードくじ',
            'icon' => 'speedwin1.png',
            'form_action' => 'save_action_instant_win',
            'widget_class' => 'EditActionInstantWin'
        ),
        self::TYPE_MOVIE => array(
            'title' => '動画視聴',
            'icon' => 'movie1.png',
            'form_action' => 'save_action_movie',
            'widget_class' => 'EditActionMovie'
        ),
        self::TYPE_SHARE => array(
            'title' => 'シェア',
            'icon' => 'share1.png',
            'form_action' => 'save_action_share',
            'widget_class' => 'EditActionShare'
        ),
        self::TYPE_TWITTER_FOLLOW => array(
            'title' => 'Twitter フォロー',
            'icon' => 'twitterFollow1.png',
            'form_action' => 'save_action_twitter_follow',
            'widget_class' => 'EditActionTwitterFollow'
        ),
        self::TYPE_FACEBOOK_LIKE => array(
            'title' => 'Facebook いいね！',
            'icon' => 'facebookLike.png',
            'form_action' => 'save_action_facebook_like',
            'widget_class' => 'EditActionFacebookLike'
        ),
        self::TYPE_GIFT => array(
            'title'         => 'ギフト',
            'icon'          => 'gift1.png',
            'form_action'   => 'save_action_gift',
            'widget_class'  => 'EditActionGift'
        ),
        self::TYPE_INSTAGRAM_FOLLOW => array(
            'title' => 'Instagram フォロー',
            'icon' => 'instagramFollow1.png',
            'form_action' => 'save_action_instagram_follow',
            'widget_class' => 'EditActionInstagramFollow'
        ),
        self::TYPE_TWEET => array(
            'title' => 'ツイート',
            'icon' => 'twitterTweet1.png',
            'form_action' => 'save_action_tweet',
            'widget_class' => 'EditActionTweet'
        ),
        self::TYPE_CODE_AUTHENTICATION => array(
            'title' => 'コード認証',
            'icon' => 'code1.png',
            'form_action' => 'save_action_code_auth',
            'widget_class' => 'EditActionCodeAuth'
        ),
        self::TYPE_INSTAGRAM_HASHTAG => array(
            'title' => 'Instagram 投稿',
            'icon' => 'hastag1.png',
            'form_action' => 'save_action_instagram_hashtag',
            'widget_class' => 'EditActionInstagramHashtag'
        ),
        self::TYPE_RETWEET => array(
            'title' => 'リツイート',
            'icon' => 'twitterRetweet1.png',
            'form_action' => 'save_action_retweet',
            'widget_class' => 'EditActionRetweet'
        ),
        self::TYPE_YOUTUBE_CHANNEL => array(
            'title' => 'YouTubeチャンネル登録',
            'icon' => 'ytchannel1.png',
            'form_action' => 'save_action_youtube_channel',
            'widget_class' => 'EditActionYoutubeChannel'
        ),
        self::TYPE_POPULAR_VOTE => array(
            'title' => '人気投票',
            'icon' => 'ranking1.png',
            'form_action' => 'save_action_popular_vote',
            'widget_class' => 'EditActionPopularVote'
        ),
        self::TYPE_ANNOUNCE_DELIVERY => array(
            'title' => '賞品の発送をもって発表',
            'icon' => 'shipping1.png',
            'form_action' => '',
            'widget_class' => 'EditActionAnnounceDelivery'
        ),
        self::TYPE_CONVERSION_TAG => array(
            'title' => 'コンバージョンタグ',
            'icon' => 'conversion.png',
            'form_action' => 'save_action_conversion_tag',
            'widget_class' => 'EditActionConversionTag'
        ),
        self::TYPE_LINE_ADD_FRIEND => array(
            'title' => 'LINE 友だち追加',
            'icon' => 'line1.png',
            'form_action' => 'save_action_line_add_friend',
            'widget_class' => 'EditActionLineAddFriend'
        ),
        self::TYPE_PAYMENT => array(
            'title' => '決済',
            'icon' => '',
            'form_action' => 'save_action_payment',
            'widget_class' => 'EditActionPayment'
        )
    );

    /**
     * ToDo: BaseTest::newCampaign()をよく見てメソッドを追加してからTypeを追加
     * @var array
     */
    public static $concrete_action_list = array(
        CpAction::TYPE_ENTRY               => 'Entry',
        CpAction::TYPE_QUESTIONNAIRE       => 'Questionnaire',
        CpAction::TYPE_PHOTO               => 'Photo',
        CpAction::TYPE_FACEBOOK_LIKE       => 'FacebookLike',
        CpAction::TYPE_TWITTER_FOLLOW      => 'TwitterFollow',
        CpAction::TYPE_INSTANT_WIN         => 'InstantWin',
        CpAction::TYPE_FREE_ANSWER         => 'FreeAnswer',
        CpAction::TYPE_SHARE               => 'Share',
        CpAction::TYPE_GIFT                => 'Gift',
        CpAction::TYPE_COUPON              => 'Coupon',
        CpAction::TYPE_CODE_AUTHENTICATION => 'CodeAuthentication',
        CpAction::TYPE_TWEET               => 'Tweet',
        CpAction::TYPE_INSTAGRAM_HASHTAG   => 'InstagramHashTag',
        CpAction::TYPE_YOUTUBE_CHANNEL     => 'YoutubeChannel',
        CpAction::TYPE_RETWEET             => 'Retweet',
        CpAction::TYPE_POPULAR_VOTE        => 'PopularVote'
    );

    protected $cp_available_actions = array(
        self::TYPE_ENTRY,
        self::TYPE_FREE_ANSWER,
        self::TYPE_QUESTIONNAIRE,
        self::TYPE_COUPON,
        self::TYPE_PHOTO,
        self::TYPE_POPULAR_VOTE,
        self::TYPE_INSTANT_WIN,
        self::TYPE_FACEBOOK_LIKE,
        self::TYPE_TWITTER_FOLLOW,
        self::TYPE_TWEET,
        self::TYPE_RETWEET,
        self::TYPE_SHARE,
        self::TYPE_MOVIE,
        self::TYPE_YOUTUBE_CHANNEL,
        self::TYPE_INSTAGRAM_FOLLOW,
        self::TYPE_INSTAGRAM_HASHTAG,
        self::TYPE_GIFT,
        self::TYPE_CODE_AUTHENTICATION,
        self::TYPE_CONVERSION_TAG,
        self::TYPE_JOIN_FINISH,
        self::TYPE_MESSAGE,
        self::TYPE_SHIPPING_ADDRESS,
        self::TYPE_ANNOUNCE,
        self::TYPE_ANNOUNCE_DELIVERY,
        self::TYPE_LINE_ADD_FRIEND,
        self::TYPE_PAYMENT,
    );

    protected $msg_available_actions = array(
        self::TYPE_FREE_ANSWER,
        self::TYPE_QUESTIONNAIRE,
        self::TYPE_COUPON,
        self::TYPE_PHOTO,
        //        self::TYPE_POPULAR_VOTE,
        self::TYPE_FACEBOOK_LIKE,
        self::TYPE_TWITTER_FOLLOW,
        //        self::TYPE_SHIPPING_ADDRESS,
        self::TYPE_MOVIE,
        //        self::TYPE_YOUTUBE_CHANNEL
        self::TYPE_INSTAGRAM_FOLLOW,
        self::TYPE_INSTAGRAM_HASHTAG,
        self::TYPE_MESSAGE,
        self::TYPE_CODE_AUTHENTICATION,
        self::TYPE_CONVERSION_TAG,
        self::TYPE_LINE_ADD_FRIEND,
    );

    public static $legal_opening_cp_actions = array(
        self::TYPE_ENTRY,
        self::TYPE_QUESTIONNAIRE,
        self::TYPE_PAYMENT
    );

    protected $cp_action_manager;
    protected $cp_entry_action_manager;
    protected $cp_message_action_manager;
    protected $cp_photo_action_manager;
    protected $cp_button_action_manager;
    protected $cp_shipping_action_manager;
    protected $cp_questionnaire_action_manager;
    protected $cp_engagement_action_manager;
    protected $cp_announce_action_manager;
    protected $cp_free_action_manager;
    protected $cp_instant_action_manager;
    protected $cp_join_finish_action_manager;
    protected $cp_movie_action_manager;
    protected $cp_twitter_follow_action_manager;
    protected $cp_share_action_manager;
    protected $cp_facebook_like_action_manager;
    protected $cp_gift_action_manager;
    protected $cp_instagram_follow_action_manager;
    protected $cp_tweet_action_manager;
    protected $cp_code_auth_action_manager;
    protected $cp_instagram_hashtag_action_manager;
    protected $cp_retweet_action_manager;
    protected $cp_code_youtube_channel_manager;
    protected $cp_popular_vote_action_manager;
    protected $cp_announce_delivery_action_manager;
    protected $cp_conversion_tag_action_manager;
    protected $cp_line_add_friend_action_manager;
    protected $cp_payment_action_manager;

    protected $_Relations = array(
        'CpActionGroups' => array(
            'cp_action_group_id' => 'id',
        ),
        'CpMessageDeliveryReservations' => array(
            'id' => 'cp_action_id',
        ),
        'CpMessageDeliveryTargets' => array(
            'id' => 'cp_action_id'
        )
    );

    /**
     * CPのタイプをもとにアクションモジュールリストを取得するGETメソッド返す
     * @param $cp
     * @return array
     */
    public function getAvailableActions($cp){
        if($cp->type==CP::TYPE_CAMPAIGN){
            return $this->getAvailableCampaignActions();
        }else if($cp->type==CP::TYPE_MESSAGE){
            return $this->getAvailableMessageActions();
        }
    }

    public function getAvailableCampaignActions() {
        $ret = array();
        foreach($this->cp_available_actions as $item){
            $ret[$item] = $this->action_type_detail[$item];
        }
        return $ret;
    }

    public function getAvailableMessageActions() {
        $ret = array();
        foreach($this->msg_available_actions as $item){
            $ret[$item] = $this->action_type_detail[$item];
        }
        return $ret;
    }

    public function getCpActionDetail() {
        $cp_action_detail = $this->action_type_detail[$this->type];

        if ($this->type == CpAction::TYPE_QUESTIONNAIRE && $this->isOpeningCpAction()) {
            $cp_action_detail['icon'] = 'enqueteAndCp1.png';
        }

        return $cp_action_detail;
    }

    public function getCpActionDetailByType($type) {
        return $this->action_type_detail[$type];
    }

    public function getCpNextActions() {
        $cp_next_actions = $this->getModel('CpNextActions');
        return $cp_next_actions->find(array('cp_action_id' => $this->id));
    }

    public function getCpPrevActions() {
        $cp_next_actions = $this->getModel('CpNextActions');
        return $cp_next_actions->find(array('cp_next_action_id' => $this->id));
    }

    /**
     * @return CpEntryActionManager|CpMessageActionManager|null
     */
    public function getActionManagerClass() {
        switch ($this->type) {
            case self::TYPE_ENTRY: return new CpEntryActionManager();
            case self::TYPE_MESSAGE: return new CpMessageActionManager();
            case self::TYPE_PHOTO: return new CpPhotoActionManager();
            case self::TYPE_BUTTONS: return new CpButtonsActionManager();
            case self::TYPE_SHIPPING_ADDRESS: return new CpShippingAddressActionManager();
            case self::TYPE_QUESTIONNAIRE: return new CpQuestionnaireActionManager();
            case self::TYPE_ENGAGEMENT: return new CpEngagementActionManager();
            case self::TYPE_ANNOUNCE: return new CpAnnounceActionManager();
            case self::TYPE_FREE_ANSWER: return new CpFreeAnswerActionManager();
            case self::TYPE_JOIN_FINISH: return new CpJoinFinishActionManager();
            case self::TYPE_COUPON: return new CpCouponActionManager();
            case self::TYPE_INSTANT_WIN: return new CpInstantWinActionManager();
            case self::TYPE_MOVIE: return new CpMovieActionManager();
            case self::TYPE_SHARE: return new CpShareActionManager();
            case self::TYPE_TWITTER_FOLLOW: return new CpTwitterFollowActionManager();
            case self::TYPE_FACEBOOK_LIKE: return new CpFacebookLikeActionManager();
            case self::TYPE_GIFT: return new CpGiftActionManager();
            case self::TYPE_INSTAGRAM_FOLLOW: return new CpInstagramFollowActionManager();
            case self::TYPE_TWEET: return new CpTweetActionManager();
            case self::TYPE_CODE_AUTHENTICATION: return new CpCodeAuthActionManager();
            case self::TYPE_INSTAGRAM_HASHTAG: return new CpInstagramHashtagActionManager();
            case self::TYPE_RETWEET: return new CpRetweetActionManager();
            case self::TYPE_YOUTUBE_CHANNEL: return new CpYoutubeChannelActionManager();
            case self::TYPE_POPULAR_VOTE: return new CpPopularVoteActionManager();
            case self::TYPE_ANNOUNCE_DELIVERY: return new CpAnnounceDeliveryActionManager();
            case self::TYPE_CONVERSION_TAG: return new CpConversionTagActionManager();
            case self::TYPE_LINE_ADD_FRIEND: return new CpLineAddFriendActionManager();
            case self::TYPE_PAYMENT: return new CpPaymentActionManager();
            default: return null;
        }
    }

    public function getCpActionData() {
        switch ($this->type) {
            case self::TYPE_ENTRY:
                if(!$this->cp_action_manager) {
                    $this->cp_action_manager = new CpEntryActionManager();
                }
                break;
            case self::TYPE_MESSAGE:
                if(!$this->cp_action_manager) {
                    $this->cp_action_manager = new CpMessageActionManager();
                }
                break;
            case self::TYPE_BUTTONS:
                if(!$this->cp_action_manager) {
                    $this->cp_action_manager = new CpButtonsActionManager();
                }
                break;
            case self::TYPE_SHIPPING_ADDRESS:
                if(!$this->cp_action_manager) {
                    $this->cp_action_manager = new CpShippingAddressActionManager();
                }
                break;
            case self::TYPE_QUESTIONNAIRE:
                if(!$this->cp_action_manager) {
                    $this->cp_action_manager = new CpQuestionnaireActionManager();
                }
                break;
            case self::TYPE_ENGAGEMENT:
                if(!$this->cp_action_manager) {
                    $this->cp_action_manager = new CpEngagementActionManager();
                }
                break;
            case self::TYPE_ANNOUNCE:
                if(!$this->cp_action_manager) {
                    $this->cp_action_manager = new CpAnnounceActionManager();
                }
                break;
            case self::TYPE_FREE_ANSWER:
                if(!$this->cp_action_manager) {
                    $this->cp_action_manager = new CpFreeAnswerActionManager();
                }
                break;
            case self::TYPE_JOIN_FINISH:
                if(!$this->cp_action_manager) {
                    $this->cp_action_manager = new CpJoinFinishActionManager();
                }
                break;
            case self::TYPE_COUPON:
                if(!$this->cp_action_manager) {
                    $this->cp_action_manager = new CpCouponActionManager();
                }
                break;
            case self::TYPE_PHOTO:
                if(!$this->cp_action_manager) {
                    $this->cp_action_manager = new CpPhotoActionManager();
                }
                break;
            case self::TYPE_INSTANT_WIN:
                if(!$this->cp_instant_action_manager) $this->cp_instant_action_manager = new CpInstantWinActionManager();
                return $this->cp_instant_action_manager->getConcreteAction($this);
            case self::TYPE_MOVIE:
                if(!$this->cp_action_manager) {
                    $this->cp_action_manager = new CpMovieActionManager();
                }
                break;
            case self::TYPE_TWITTER_FOLLOW:
                if(!$this->cp_action_manager) {
                    $this->cp_action_manager = new CpTwitterFollowActionManager();
                }
                break;
            case self::TYPE_SHARE:
                if(!$this->cp_action_manager) {
                    $this->cp_action_manager = new CpShareActionManager();
                }
                break;
            case self::TYPE_FACEBOOK_LIKE:
                if(!$this->cp_action_manager) {
                    $this->cp_action_manager = new CpFacebookLikeActionManager();
                }
                break;
            case self::TYPE_GIFT:
                if(!$this->cp_action_manager) {
                    $this->cp_action_manager = new CpGiftActionManager();
                }
                break;
            case self::TYPE_INSTAGRAM_FOLLOW:
                if(!$this->cp_action_manager) {
                    $this->cp_action_manager = new CpInstagramFollowActionManager();
                }
                break;
            case self::TYPE_TWEET:
                if(!$this->cp_action_manager) {
                    $this->cp_action_manager = new CpTweetActionManager();
                }
                break;
            case self::TYPE_CODE_AUTHENTICATION:
                if (!$this->cp_action_manager) {
                    $this->cp_action_manager = new CpCodeAuthActionManager();
                }
                break;
            case self::TYPE_INSTAGRAM_HASHTAG:
                if (!$this->cp_action_manager) {
                    $this->cp_action_manager = new CpInstagramHashtagActionManager();
                }
                break;
            case self::TYPE_RETWEET:
                if (!$this->cp_action_manager) {
                    $this->cp_action_manager = new CpRetweetActionManager();
                }
                break;
            case self::TYPE_YOUTUBE_CHANNEL:
                if (!$this->cp_action_manager) {
                    $this->cp_action_manager = new CpYoutubeChannelActionManager();
                }
                break;
            case self::TYPE_POPULAR_VOTE:
                if (!$this->cp_action_manager) {
                    $this->cp_action_manager = new CpPopularVoteActionManager();
                }
                break;
            case self::TYPE_ANNOUNCE_DELIVERY:
                if (!$this->cp_action_manager) {
                    $this->cp_action_manager = new CpAnnounceDeliveryActionManager();
                }
                break;
            case self::TYPE_CONVERSION_TAG:
                if (!$this->cp_action_manager) {
                    $this->cp_action_manager = new CpConversionTagActionManager();
                }
                break;
            case self::TYPE_LINE_ADD_FRIEND:
                if (!$this->cp_action_manager) {
                    $this->cp_action_manager = new CpLineAddFriendActionManager();
                }
                break;
            case self::TYPE_PAYMENT:
                if (!$this->cp_payment_action_manager) {
                    $this->cp_action_manager = new CpPaymentActionManager();
                }
                break;
            default: return null;
        }
        return $this->cp_action_manager->getConcreteAction($this);
    }

    public function getMemberCount() {
        $service = new CpUserService();
        return $service->getActionMemberCounts($this->id);
    }

    public function getCp() {
        $cp_action_group = $this->getCpActionGroup();
        return $cp_action_group->getCp();
    }

    public function getStepNo() {
        $service_factory = new aafwServiceFactory();
        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = $service_factory->create('CpFlowService');
        $first_in_group = $cp_flow_service->getMinOrderOfActionInGroup($this->cp_action_group_id);
        $actions_in_group = $cp_flow_service->getCpActionsByCpActionGroupId($this->cp_action_group_id);
        foreach ($actions_in_group as $action) {
            $first_in_group += 1;
            if ($action->id == $this->id) {
                break;
            }
        }
        return $first_in_group;
    }

    /**
     * エンゲージメントログ数
     * @return mixed
     */
    public function getEngagementLogCount() {
        $service = $this->getService('EngagementLogService');
        return $service->getEngagementLogCountByCpActionId($this->id);
    }

    /**
     * @return mixed
     */
    public function getCpUserCount() {
        $service = $this->getService('CpUserService');

        return $service->getCpUserCountByCpId($this->getCp()->id);
    }

    /**
     * First group所属であるか確認する
     *
     * @return boolean
     * @throws aafwException
     */
    public function isFirstGroupAction() {
        $cp_action_group = $this->getCpActionGroup();

        return $cp_action_group->order_no == 1;
    }

    public function isEndTypeNone() {
        return $this->end_type == self::END_TYPE_NONE;
    }

    public function isEndTypeCp() {
        return $this->end_type == self::END_TYPE_CP;
    }

    public function isEndTypeOriginal() {
        return $this->end_type == self::END_TYPE_ORIGINAL;
    }

    public function isAnnounceAction() {
        return $this->type == self::TYPE_ANNOUNCE;
    }

    /**
     * 締め切り期限内であるか
     *
     * @return boolean
     */
    public function isActive($cp = null) {
        if ($cp === null) {
            $cp = $this->getCp();
        }
        if ($cp->isDemo()) {
            // デモモードの場合は、無条件でOKとする
            return true;
        }

        if ($cp->isPermanent()) {
            // 常設キャンペーンの場合は、締切設定無視にする
            return true;
        }

        // 締め切り設定(指定しない)
        if ($this->isEndTypeNone()) {
            return true;
        }

        // 締め切り設定(CPSの情報に合わせる)
        if ($this->isEndTypeCp()) {
            // -----------------------------------------------------------------
            // メッセージの場合
            // -----------------------------------------------------------------
            // 期限がないため、無条件でOKとする
            if ($cp->isCpTypeMessage()) {
                return true;
            }
            // -----------------------------------------------------------------
            // キャンペーンの場合
            // -----------------------------------------------------------------
            // $cp->canEntry()にて判定すると
            // 当選者自身も次のモジュールに進めなくなる場合があるので
            // CAMPAIGN_STATUS_OPENである事のステータス判定に留める
            return $cp->isCampaignStatusOpen();
        }

        // 締め切り日設定(日時を指定する)
        if ($this->isEndTypeOriginal()) {
            $now = new Datetime();
            $end_at = new Datetime($this->end_at);

            return $end_at >= $now;
        }

        // 締め切りタイプが上記以外の場合は
        // 有効期限内と判定する
        return true;
    }

    /**
     *  アクショングループ・キャンペーンタイプによって
     *  締め切り日設定のデフォルト値を取得
     *  * キャンペーンの場合
     *  ** ステップグループ1：応募期間に合わせる
     *  ** ステップグループ2：なし
     *  * メッセージ
     *  ** なし
     *
     *  @return int
     */
    public function getDefaultEndType() {
        $cp = $this->getCpActionGroup()->getCp();

        $service_factory = new aafwServiceFactory();
        $cp_flow_service = $service_factory->create('CpFlowService');
        $cp_action_group = $cp_flow_service->getCpActionGroupByAction($this->id);

        $radio_deadline = CpAction::END_TYPE_NONE;
        if ($cp->isCpTypeCampaign() && $cp_action_group->isFirstGroup()) {
            $radio_deadline = CpAction::END_TYPE_CP;
        }

        return $radio_deadline;
    }

    /**
     * 締め切り日設定を登録済みか確認
     *
     * @return boolena
     */
    public function isEndTypeDraft() {
        return $this->end_type == -1;
    }

    /**
     * 締め切り日を取得
     *
     * @return int
     */
    public function getEndType() {
        if (!$this->isEndTypeDraft()) {
            return $this->end_type;
        }

        return $this->getDefaultEndType();
    }

    /**
     * 最初モジュールであるかどうか
     * @return bool
     */
    public function isOpeningCpAction() {
        if (!$this->isLegalOpeningCpAction()) return false;

        if ($this->order_no != 1) return false;

        $cp_action_group = $this->getCpActionGroup();
        if($cp_action_group->order_no != 1) {
            return false;
        }
        $cp = $cp_action_group->getCp();
        if($cp->type == Cp::TYPE_MESSAGE) {
            return false;
        }
        return true;
    }

    /**
     * 最後のモジュールであるかどうか
     * @return bool
     */
    public function isLastCpActionInGroup() {
        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = $this->getService('CpFlowService');

        $last_action = $cp_flow_service->getLastActionInGroupByGroupId($this->cp_action_group_id);
        return $last_action->id === $this->id;
    }

    /**
     * @return bool
     */
    public function isLegalOpeningCpAction() {
        return in_array($this->type, self::$legal_opening_cp_actions);
    }

    public function isAnnounceDelivery() {
        return $this->type === self::TYPE_ANNOUNCE_DELIVERY;
    }

    public function isShippingAddress() {
        return $this->type === self::TYPE_SHIPPING_ADDRESS;
    }
}
