<?php
AAFW::import('jp.aainc.lib.base.aafwServiceBase');
AAFW::import('jp.aainc.classes.services.ProfileQuestionnaireService');
AAFW::import('jp.aainc.classes.services.SocialAccountService');
AAFW::import('jp.aainc.classes.services.UserAttributeService');
AAFW::import('jp.aainc.classes.services.CpUserActionStatusService');

class   CpCreateSqlService extends aafwServiceBase {

    const DISPLAY_20_ITEMS = 20;
    const DISPLAY_50_ITEMS = 50;
    const DISPLAY_100_ITEMS = 100;
    const DISPLAY_3_ITEMS = 3;


    public static $display_items_range = array(self::DISPLAY_20_ITEMS, self::DISPLAY_50_ITEMS, self::DISPLAY_100_ITEMS);

    const QUERY_TYPE_AND = 1;
    const QUERY_TYPE_OR = 2;

    const DID_NOT_SEND = "not_send";

    // 各カラムの絞り込みに利用
    const SEARCH_PROFILE_MEMBER_NO = 1;         //登録No
    const SEARCH_PROFILE_REGISTER_PERIOD = 2;   //登録期間
    const SEARCH_PROFILE_SOCIAL_ACCOUNT = 3;    //連携済
    const SEARCH_PROFILE_LAST_LOGIN = 4;        //最終ログイン
    const SEARCH_PROFILE_LOGIN_COUNT = 5;       //ログイン回数
    const SEARCH_PROFILE_SEX = 6;               //性別
    const SEARCH_PROFILE_ADDRESS = 7;           //都道府県
    const SEARCH_PROFILE_AGE = 8;               //年齢
    const SEARCH_PROFILE_QUESTIONNAIRE = 9;     //登録時アンケート
    const SEARCH_PROFILE_CONVERSION = 10;       //コンバージョンタグ
    const SEARCH_PROFILE_QUESTIONNAIRE_STATUS = 11; //アンケート取得状況

    const SEARCH_IMPORT_VALUE = 12;                 //外部インポートデータ
    const SEARCH_CP_ENTRY_COUNT = 13;               //キャンペーン参加回数
    const SEARCH_CP_ANNOUNCE_COUNT = 14;            //キャンペーン当選回数
    const SEARCH_MESSAGE_DELIVERED_COUNT = 15;          //メッセージ受信数
    const SEARCH_MESSAGE_READ_COUNT = 16;               //メッセージ開封数
    const SEARCH_MESSAGE_READ_RATIO = 17;               //メッセージ閲覧数
    const SEARCH_SOCIAL_ACCOUNT_SUM = 18;               //SNS情報合計

    const SEARCH_PROFILE_RATE = 19;             //評価
    const SEARCH_SOCIAL_ACCOUNT_INTERACTIVE = 20;         //
    const SEARCH_DUPLICATE_ADDRESS = 21;        //重複住所カウント

    const SEARCH_PARTICIPATE_CONDITION = 28;    //参加状況
    const SEARCH_DELIVERY_TIME = 29;            //送信済みタイミング
    const SEARCH_QUESTIONNAIRE = 30;            //アンケート

    const SEARCH_PHOTO_SHARE_SNS = 41;          //シェアSNS
    const SEARCH_PHOTO_SHARE_TEXT = 42;         //シェアテキスト
    const SEARCH_PHOTO_APPROVAL_STATUS = 43;    //検閲

    const SEARCH_SHARE_TYPE = 51;    //シェア状況
    const SEARCH_SHARE_TEXT = 52;    //シェアコメント

    const SEARCH_INSTAGRAM_HASHTAG_APPROVAL_STATUS = 61; //検閲
    const SEARCH_INSTAGRAM_HASHTAG_DUPLICATION = 62; //重複
    const SEARCH_INSTAGRAM_HASHTAG_REVERSE_POST_TIME = 63; //投稿時間逆

    const SEARCH_CHILD_BIRTH_PERIOD = 77;      //TODO ハードコーディング: カンコーブランドのプロフィールアンケートの回答結果の絞り込み

    const SEARCH_FB_LIKE_TYPE = 70;     // いいね！状態
    const SEARCH_TW_FOLLOW_TYPE = 80;   // フォロー状態
    const SEARCH_TWEET_TYPE = 110;       // ツイート状態

    const SEARCH_YOUTUBE_CHANNEL_SUBSCRIPTION = 91; //YouTubeチャンネル登録

    const SEARCH_GIFT_RECEIVER_FAN = 75;                //ギフトの受け取りの人
    const SEARCH_POPULAR_VOTE_CANDIDATE = 100;               // 投票先
    const SEARCH_POPULAR_VOTE_SHARE_SNS = 101;              // シェア先
    const SEARCH_POPULAR_VOTE_SHARE_TEXT = 102;              // シェアされた投票理由

    const SEARCH_QUERY_USER_TYPE = 201;         //ユーザ対象を選択(送信済ユーザや送信対象ユーザで絞るのか)

    const SEARCH_JOIN_FAN_ONLY = 200;         //ファン全員を出すか

    const NOT_SET_PREFECTURE = 50;

    const SEARCH_FB_POSTS_LIKE_COUNT        = 301;      // 連携済FBページのpostに対するいいね
    const SEARCH_FB_POSTS_COMMENT_COUNT     = 302;      // 連携済FBページのpostに対するコメント

    const SEARCH_TW_TWEET_REPLY_COUNT       = 401;      // 連携済Twitterのツイートに対するリプライ
    const SEARCH_TW_TWEET_RETWEET_COUNT     = 402;      // 連携済Twitterのツイートに対するリツイート

    const SEARCH_SEGMENT_CONDITION          = 501;     //Segment Action 条件

    const ANSWERED_QUESTIONNAIRE = 'Y';         //アンケート回答済
    const NOT_ANSWER_QUESTIONNAIRE = 'N';       //アンケート未回答
    const LINK_SNS = 'Y';                       //SNS連携済み
    const NOT_LINK_SNS = 'N';                   //SNS未連携
    const NOT_SET_VALUE = 'N';                  //外部インポートデータ未設定

    // 参加状況ステータス
    const PARTICIPATE_COMPLETE          = 1;    //参加完了
    const PARTICIPATE_READ              = 2;    //既読
    const PARTICIPATE_NOT_READ          = 3;    //未読
    const PARTICIPATE_NOT_SEND          = 4;    //未送信
    const PARTICIPATE_COUNT_INSTANT_WIN = 5;    //スピードくじ参加回数
    const PARTICIPATE_REJECTED          = 6;    //参加条件外

    //当選者状態
    const PARTICIPATE_TARGET = 7;               //当選者セット
    const PARTICIPATE_NOT_TARGET = 8;           //当選者セットしない

    // タブの種別
    const TAB_PAGE_PROFILE               = '1';  //プロフィールタブ
    const TAB_PAGE_PARTICIPATE_CONDITION = '2';  //参加状況タブ
    const TAB_PAGE_QUESTIONNAIRE_ANSWER  = '3';  //アンケートタブ
    const TAB_PAGE_PHOTO                 = '4';  //写真タブ
    const TAB_PAGE_SHARE                 = '5';  //シェア状況タブ
    const TAB_PAGE_INSTAGRAM_HASHTAG     = '6';  //Instagram Hashtag投稿タブ
    const TAB_PAGE_FACEBOOK_LIKE         = '7';  // Facebookいいね！
    const TAB_PAGE_TWITTER_FOLLOW        = '8';  // Twitterフォロー
    const TAB_PAGE_YOUTUBE_CHANNEL       = '9';  //YouTubeチャンネル登録タブ
    const TAB_PAGE_POPULAR_VOTE          = '10'; //人気投票タブ
    const TAB_PAGE_TWEET                 = '11'; //ツイートタブ

    const QUERY_USER_ALL = 1;                   // 全ユーザ
    const QUERY_USER_TARGET = 2;                // 送信対象ユーザ
    const QUERY_USER_SENT = 3;                  // 送信済ユーザ

    // いいね状況ステータス
    const LIKED = 'Y';                       //SNSいいね済み
    const NOT_LIKE = 'N';                   //SNS未いいね

    // フォロー状態ステータス
    const FOLLOWED = 'Y';                    //SNSフォロー済み
    const NOT_FOLLOW = 'N';                  //SNS未フォロー

    //重複住所
    const SHIPPING_ADDRESS_DUPLICATE = 1;
    const SHIPPING_ADDRESS_USER_DUPLICATE = 2;
    const HAVE_ADDRESS = 1;
    const NOT_HAVE_ADDRESS = 0;

    protected $cp_user;
    protected $search_condition_key;
    protected $brand_id;

    public static $search_range = array(
        self::SEARCH_PROFILE_MEMBER_NO,
        self::SEARCH_PROFILE_REGISTER_PERIOD,
        self::SEARCH_PROFILE_LAST_LOGIN,
        self::SEARCH_PROFILE_LOGIN_COUNT,
        self::SEARCH_PROFILE_AGE,
        self::SEARCH_PROFILE_LOGIN_COUNT,
        self::SEARCH_CP_ENTRY_COUNT,
        self::SEARCH_CP_ANNOUNCE_COUNT,
        self::SEARCH_MESSAGE_DELIVERED_COUNT,
        self::SEARCH_MESSAGE_READ_COUNT,
        self::SEARCH_MESSAGE_READ_RATIO,
        self::SEARCH_SOCIAL_ACCOUNT_SUM,
        self::SEARCH_CHILD_BIRTH_PERIOD     //TODO ハードコーディング: カンコーブランドのプロフィールアンケートの回答結果の絞り込み
    );

    public static $search_range_keys = array(
        self::SEARCH_PROFILE_MEMBER_NO => 'search_profile_member_no',
        self::SEARCH_PROFILE_REGISTER_PERIOD => 'search_profile_register_period',
        self::SEARCH_PROFILE_LAST_LOGIN => 'search_profile_last_login',
        self::SEARCH_PROFILE_LOGIN_COUNT => 'search_profile_login_count',
        self::SEARCH_PROFILE_AGE => 'search_profile_age',
        self::SEARCH_PROFILE_LOGIN_COUNT => 'search_profile_login_count',
        self::SEARCH_CP_ENTRY_COUNT => 'search_cp_entry_count',
        self::SEARCH_CP_ANNOUNCE_COUNT => 'search_cp_announce_count',
        self::SEARCH_MESSAGE_DELIVERED_COUNT => 'search_message_delivered_count',
        self::SEARCH_MESSAGE_READ_COUNT => 'search_message_read_count',
        self::SEARCH_MESSAGE_READ_RATIO => 'search_message_ratio',
        self::SEARCH_SOCIAL_ACCOUNT_SUM => 'search_friend_count_sum',
        self::SEARCH_PROFILE_CONVERSION => 'search_profile_conversion',
        self::SEARCH_CHILD_BIRTH_PERIOD => 'search_child_birth_period'
    );

    public static $search_range_labels = array(
        self::SEARCH_PROFILE_MEMBER_NO => '会員No',
        self::SEARCH_PROFILE_REGISTER_PERIOD => '登録期間',
        self::SEARCH_PROFILE_LAST_LOGIN => '最終ログイン',
        self::SEARCH_PROFILE_LOGIN_COUNT => 'ログイン回数',
        self::SEARCH_PROFILE_AGE => '年齢',
        self::SEARCH_PROFILE_LOGIN_COUNT => 'ログイン数',
        self::SEARCH_CP_ENTRY_COUNT => '参加数',
        self::SEARCH_CP_ANNOUNCE_COUNT => '当選数',
        self::SEARCH_MESSAGE_DELIVERED_COUNT => 'メッセージ送信数',
        self::SEARCH_MESSAGE_READ_COUNT => 'メッセージ閲覧数',
        self::SEARCH_MESSAGE_READ_RATIO => '閲覧率',
        self::SEARCH_SOCIAL_ACCOUNT_SUM => '友達数・フォロワー数'

    );

    public static $search_count_item = array(
        self::SEARCH_PROFILE_LOGIN_COUNT => 'search_profile_login_count',
        self::SEARCH_CP_ENTRY_COUNT => 'search_cp_entry_count',
        self::SEARCH_CP_ANNOUNCE_COUNT => 'search_cp_announce_count',
        self::SEARCH_MESSAGE_DELIVERED_COUNT => 'search_message_delivered_count',
        self::SEARCH_MESSAGE_READ_COUNT => 'search_message_read_count',
    );

     public static $search_sns_action_count = array(
        self::SEARCH_FB_POSTS_LIKE_COUNT => 'search_fb_posts_like_count',
        self::SEARCH_FB_POSTS_COMMENT_COUNT => 'search_fb_posts_comment_count',
        self::SEARCH_TW_TWEET_RETWEET_COUNT => 'search_tw_tweet_retweet_count',
        self::SEARCH_TW_TWEET_REPLY_COUNT => 'search_tw_tweet_reply_count',
    );

    public static $search_count_column = array(
        self::SEARCH_CP_ENTRY_COUNT => 'cp_entry_count',
        self::SEARCH_CP_ANNOUNCE_COUNT => 'cp_announce_count',
        self::SEARCH_MESSAGE_DELIVERED_COUNT => 'message_delivered_count',
        self::SEARCH_MESSAGE_READ_COUNT => 'message_read_count',
    );

    

    public static $search_checkbox_keys = array(
        self::SEARCH_PROFILE_QUESTIONNAIRE_STATUS => 'search_questionnaire_status',
        self::SEARCH_PHOTO_SHARE_TEXT => 'search_photo_share_text',
        self::SEARCH_PHOTO_APPROVAL_STATUS => 'search_photo_approval_status',
        self::SEARCH_INSTAGRAM_HASHTAG_DUPLICATION => 'search_instagram_hashtag_duplicate',
        self::SEARCH_INSTAGRAM_HASHTAG_REVERSE_POST_TIME => 'search_instagram_hashtag_reverse',
        self::SEARCH_INSTAGRAM_HASHTAG_APPROVAL_STATUS => 'search_instagram_hashtag_approval_status',
        self::SEARCH_SHARE_TYPE => 'search_share_type',
        self::SEARCH_SHARE_TEXT => 'search_share_text',
        self::SEARCH_FB_LIKE_TYPE => 'search_fb_like_type',
        self::SEARCH_TW_FOLLOW_TYPE => 'search_tw_follow_type',
        self::SEARCH_YOUTUBE_CHANNEL_SUBSCRIPTION => 'search_ytch_subscription_type',
        self::SEARCH_POPULAR_VOTE_SHARE_TEXT => 'search_popular_vote_share_text'
    );

    public static $search_checkbox_choices = array(
        self::SEARCH_PROFILE_QUESTIONNAIRE_STATUS => array(
            BrandsUsersRelation::SIGNUP_WITHOUT_INFO => '未取得',
            BrandsUsersRelation::SIGNUP_WITH_INFO => '取得済み',
            BrandsUsersRelation::FORCE_WITH_INFO => '要再取得'
        ),
        self::SEARCH_PHOTO_SHARE_TEXT => array(
            PhotoUserShare::SEARCH_EXISTS => 'あり',
            PhotoUserShare::SEARCH_NOT_EXISTS => 'なし'
        ),
        self::SEARCH_PHOTO_APPROVAL_STATUS => array(
            PhotoUser::APPROVAL_STATUS_DEFAULT => '未承認',
            PhotoUser::APPROVAL_STATUS_APPROVE => '承認',
            PhotoUser::APPROVAL_STATUS_REJECT => '非承認'
        ),
        self::SEARCH_INSTAGRAM_HASHTAG_DUPLICATION => array(
            InstagramHashtagUser::SEARCH_EXISTS => 'あり',
            InstagramHashtagUser::SEARCH_NOT_EXISTS => 'なし'
        ),
        self::SEARCH_INSTAGRAM_HASHTAG_REVERSE_POST_TIME => array(
            InstagramHashtagUserPost::REVERSE_POST_TIME_DEFAULT => '登録後投稿',
            InstagramHashtagUserPost::REVERSE_POST_TIME_INVALID => '投稿後登録'
        ),
        self::SEARCH_INSTAGRAM_HASHTAG_APPROVAL_STATUS => array(
            InstagramHashtagUserPost::APPROVAL_STATUS_DEFAULT => '未承認',
            InstagramHashtagUserPost::APPROVAL_STATUS_APPROVE => '承認',
            InstagramHashtagUserPost::APPROVAL_STATUS_REJECT => '非承認'
        ),
        self::SEARCH_SHARE_TYPE => array(
            CpShareUserLog::TYPE_SHARE => CpShareUserLog::STATUS_SHARE,
            CpShareUserLog::TYPE_SKIP => CpShareUserLog::STATUS_SKIP,
            CpShareUserLog::TYPE_UNREAD => CpShareUserLog::STATUS_UNREAD
        ),
        self::SEARCH_SHARE_TEXT => array(
            CpShareUserLog::SEARCH_EXISTS => 'あり',
            CpShareUserLog::SEARCH_NOT_EXISTS => 'なし'
        ),
        self::SEARCH_FB_LIKE_TYPE => array(
            CpFacebookLikeLog::LIKE_ACTION_UNREAD    => CpFacebookLikeLog::STATUS_ACTION_UNREAD,
            CpFacebookLikeLog::LIKE_ACTION_ALREADY   => CpFacebookLikeLog::STATUS_ACTION_ALREADY,
            CpFacebookLikeLog::LIKE_ACTION_EXEC      => CpFacebookLikeLog::STATUS_ACTION_EXEC,
            CpFacebookLikeLog::LIKE_ACTION_SKIP      => CpFacebookLikeLog::STATUS_ACTION_SKIP
        ),
        self::SEARCH_TW_FOLLOW_TYPE => array(
            CpTwitterFollowActionManager::FOLLOW_ACTION_UNREAD    => CpTwitterFollowLog::STATUS_ACTION_UNREAD,
            CpTwitterFollowActionManager::FOLLOW_ACTION_ALREADY   => CpTwitterFollowLog::STATUS_ACTION_ALREADY,
            CpTwitterFollowActionManager::FOLLOW_ACTION_EXEC      => CpTwitterFollowLog::STATUS_ACTION_EXEC,
            CpTwitterFollowActionManager::FOLLOW_ACTION_SKIP      => CpTwitterFollowLog::STATUS_ACTION_SKIP
        ),
        self::SEARCH_YOUTUBE_CHANNEL_SUBSCRIPTION => array(
            CpYoutubeChannelUserLog::STATUS_FOLLOWED  => '新規登録',
            CpYoutubeChannelUserLog::STATUS_FOLLOWING => '既存登録',
            CpYoutubeChannelUserLog::STATUS_SKIP      => 'スキップ'
        ),
        self::SEARCH_POPULAR_VOTE_SHARE_TEXT => array(
            PopularVoteUserShare::SEARCH_EXISTS     => 'あり',
            PopularVoteUserShare::SEARCH_NOT_EXISTS => 'なし'
        )
    );

    public static $search_campaign_conditions = array(
        self::SEARCH_PARTICIPATE_CONDITION,
        self::SEARCH_QUESTIONNAIRE,
        self::SEARCH_PHOTO_SHARE_SNS,
        self::SEARCH_PHOTO_SHARE_TEXT,
        self::SEARCH_PHOTO_APPROVAL_STATUS,
        self::SEARCH_INSTAGRAM_HASHTAG_DUPLICATION,
        self::SEARCH_INSTAGRAM_HASHTAG_REVERSE_POST_TIME,
        self::SEARCH_INSTAGRAM_HASHTAG_APPROVAL_STATUS,
        self::SEARCH_SHARE_TYPE,
        self::SEARCH_SHARE_TEXT,
        self::SEARCH_FB_LIKE_TYPE,
        self::SEARCH_TW_FOLLOW_TYPE,
        self::SEARCH_YOUTUBE_CHANNEL_SUBSCRIPTION,
        self::SEARCH_POPULAR_VOTE_CANDIDATE,
        self::SEARCH_POPULAR_VOTE_SHARE_SNS,
        self::SEARCH_POPULAR_VOTE_SHARE_TEXT
    );

    public static $segment_provision_condition_label = array(
        self::SEARCH_PROFILE_RATE => '評価',
        self::SEARCH_PROFILE_MEMBER_NO => '会員No',
        self::SEARCH_PROFILE_REGISTER_PERIOD => '登録期間',
        self::SEARCH_PROFILE_LAST_LOGIN => '最終ログイン',
        self::SEARCH_PROFILE_LOGIN_COUNT => 'ログイン回数',
        self::SEARCH_PROFILE_SEX => '性別',
        self::SEARCH_PROFILE_AGE => '年齢',
        self::SEARCH_PROFILE_ADDRESS => '都道府県',
        self::SEARCH_PROFILE_QUESTIONNAIRE_STATUS => 'カスタムプロフィール',
        self::SEARCH_PHOTO_SHARE_SNS => '写真投稿 シェアSNS',
        self::SEARCH_PHOTO_SHARE_TEXT => '写真投稿 シェアテキスト',
        self::SEARCH_PHOTO_APPROVAL_STATUS => '写真投稿 検閲',
        self::SEARCH_SHARE_TYPE => 'シェア状況',
        self::SEARCH_SHARE_TEXT => 'シェアコメント',
        self::SEARCH_INSTAGRAM_HASHTAG_DUPLICATION => 'Instagram投稿 ユーザネーム重複',
        self::SEARCH_INSTAGRAM_HASHTAG_REVERSE_POST_TIME => 'Instagram投稿 登録投稿順序',
        self::SEARCH_INSTAGRAM_HASHTAG_APPROVAL_STATUS => 'Instagram投稿 検閲',
        self::SEARCH_FB_LIKE_TYPE => 'Facebookいいね！状況',
        self::SEARCH_TW_FOLLOW_TYPE => 'Twitterフォロー状況',
        self::SEARCH_YOUTUBE_CHANNEL_SUBSCRIPTION => 'YouTubeチャンネル登録 登録状況',
        self::SEARCH_POPULAR_VOTE_CANDIDATE => '人気投票 投票',
        self::SEARCH_POPULAR_VOTE_SHARE_SNS => '人気投票 シェアSNS',
        self::SEARCH_POPULAR_VOTE_SHARE_TEXT => '人気投票 シェアされた投票理由'
    );

    const COLUMN_EMAIL = "email";
    const COLUMN_FBID  = "fb_uid";
    const COLUMN_TWID  = "tw_uid";

    public static function isPhotoQuery($search_key) {
        return ($search_key == self::SEARCH_PHOTO_SHARE_SNS || $search_key == self::SEARCH_PHOTO_SHARE_TEXT || $search_key == self::SEARCH_PHOTO_APPROVAL_STATUS);
    }

    public static function isInstagramHashtagQuery($search_key) {
        return ($search_key == self::SEARCH_INSTAGRAM_HASHTAG_DUPLICATION || $search_key == self::SEARCH_INSTAGRAM_HASHTAG_REVERSE_POST_TIME || $search_key == self::SEARCH_INSTAGRAM_HASHTAG_APPROVAL_STATUS);
    }

    public static function isYoutubeChannelQuery($search_key)
    {
        return ($search_key == self::SEARCH_YOUTUBE_CHANNEL_SUBSCRIPTION);
    }

    public static function isFacebookLikeQuery($search_key) {
        return ($search_key == self::SEARCH_FB_LIKE_TYPE);
    }

    public static function isTwitterFollowQuery($search_key) {
        return ($search_key == self::SEARCH_TW_FOLLOW_TYPE);
    }

    public static function isTweetQuery($search_key) {
        return ($search_key == self::SEARCH_TWEET_TYPE);
    }

    public static function isPopularVoteQuery($search_key) {
        return ($search_key == self::SEARCH_POPULAR_VOTE_SHARE_SNS || $search_key == self::SEARCH_POPULAR_VOTE_SHARE_TEXT || $search_key == self::SEARCH_POPULAR_VOTE_CANDIDATE);
    }

    public function __construct() {
        $this->cp_user = $this->getModel("CpUsers");
    }

    /**
     * ファン一覧の取得で使用するSQLを返す
     * 全体件数とファン一覧取得用でSQLを分ける。
     * @param $page_info
     * @param $search_condition
     * @param $order_condition
     * @param null $count_sql
     * @param null $isDownLoad
     * @param array $extern_columns
     * @param boolean $is_display_fan_list
     * @return string
     */
    public function getUserSql($page_info, $search_condition, $order_condition, $count_sql=null, $isDownLoad=null, $extern_columns = array(), $is_display_fan_list = false) {
        if($count_sql) {
            $sql = $this->getCountSelectSql($page_info['cp_id']);
        } else {
            $sql = $this->getListSelectSql($page_info, $isDownLoad, $extern_columns, $is_display_fan_list);
        }
        $sql .= $this->getFromSql($page_info, $search_condition, $order_condition, $count_sql, $isDownLoad, $extern_columns);
        $sql .= $this->getWhereSql($page_info['brand_id'], $search_condition);

        return $sql;
    }

    /**
     * ファン一覧のリストを取得するSELECT句を返す
     * @param $page_info
     * @param $isDownLoad
     * @param $extern_columns
     * @param $is_display_fan_list
     * @return string
     */
    protected function getListSelectSql($page_info, $isDownLoad, $extern_columns, $is_display_fan_list) {
        // CP参加者リストの時はcp_usersテーブルと結合する
        $select_sql = "SELECT DISTINCT relate.user_id user_id,relate.id brands_users_relations_id ";
        if($page_info['cp_id']) {
            $select_sql .= ",cp_usr.id cp_user_id ";
        }

        if($is_display_fan_list && !$isDownLoad) {
            $select_sql .= ",relate.rate rate ";
        }

        if($isDownLoad) {
            $select_sql .= ",relate.no no,relate.created_at created_at,relate.last_login_date,relate.login_count,relate.personal_info_flg,relate.rate,relate.duplicate_address_count shipping_address_duplicate_count,
                             brand_search_csv.cp_entry_count,brand_search_csv.cp_announce_count,brand_search_csv.message_delivered_count,brand_search_csv.message_read_count";
            if($page_info['cp_id']){
                $select_sql .= ", cp_usr.duplicate_address_count shipping_address_user_duplicate_count ";
            }
        }

        if ($extern_columns && count($extern_columns) > 0) {
            foreach ($extern_columns as $column) {
                if ($column == self::COLUMN_EMAIL) {
                    $select_sql .= ",users.mail_address email";
                } elseif ($column == self::COLUMN_FBID) {
                    $select_sql .= ",social_custom.social_media_account_id fb_uid";
                } elseif ($column == self::COLUMN_TWID) {
                    $select_sql .= ",social_custom.social_media_account_id tw_uid";
                }
            }
        }
        return $select_sql;
    }

    /**
     * ファンの件数を取得するSELECT句を返す
     * @param $cp_id
     * @return string
     */
    protected function getCountSelectSql($cp_id) {
        if($cp_id) {
            $count_sql = 'SELECT COUNT(DISTINCT relate.id) total_count, COUNT(DISTINCT mescnt.id) sent_count ';
        } else {
            $count_sql = 'SELECT COUNT(DISTINCT relate.id) total_count ';
        }
        return $count_sql;
    }

    /**
     * FROM句を返す
     * @param $cp_id
     * @param $action_id
     * @param $search_condition
     * @return $from_sql
     */
    protected function getFromSql($page_info, $search_condition, $order_condition, $count_sql=null, $isDownLoad, $extern_columns) {
        // CP参加者リストの時はcp_usersテーブルと結合する
        $from_sql = " FROM brands_users_relations relate ";
        if($page_info['cp_id']) {
            $from_sql .= " LEFT OUTER JOIN cp_users cp_usr ON relate.user_id = cp_usr.user_id AND cp_usr.cp_id = {$this->escape($page_info['cp_id'])} AND cp_usr.del_flg = 0 ";
            if($count_sql) {
                $from_sql .= " LEFT OUTER JOIN cp_user_action_messages mescnt ON cp_usr.id = mescnt.cp_user_id AND mescnt.cp_action_id = {$this->escape($page_info['action_id'])} AND mescnt.del_flg = 0 ";
            }
        } else {
            $from_sql .= " LEFT OUTER JOIN cp_users cp_usr ON relate.user_id = cp_usr.user_id AND cp_usr.del_flg = 0 ";
        }
        if($isDownLoad) {
            $from_sql .= " LEFT OUTER JOIN brands_users_search_info brand_search_csv ON brand_search_csv.brands_users_relation_id = relate.id AND brand_search_csv.del_flg = 0 ";
        }

        $this->has_social_condition = false;
        $this->brand_id = $page_info['brand_id'];

        if($search_condition || $order_condition) {
            // 絞り込みがあった場合のJOIN句
            $from_sql .= $this->getSearchJoin($search_condition, $order_condition);
        }

        if ($extern_columns && count($extern_columns) > 0) {
            foreach ($extern_columns as $column) {
                if ($column == self::COLUMN_EMAIL) {
                    $from_sql .= " LEFT OUTER JOIN users ON users.id = relate.user_id ";
                } else if ($column == self::COLUMN_FBID) {
                    $from_sql .= " LEFT OUTER JOIN social_accounts social_custom ON social_custom.user_id = relate.user_id AND social_custom.social_media_id = 1";
                } else if ($column == self::COLUMN_TWID) {
                    $from_sql .= " LEFT OUTER JOIN social_accounts social_custom ON social_custom.user_id = relate.user_id AND social_custom.social_media_id = 3";
                }
            }
        }

        return $from_sql;
    }

    public function splitSearchKey($search_condition) {
        // アンケート等のキーは、[サーチタイプ/ID]で構成されているので、サーチタイプだけを取り出す。
        foreach($search_condition as $key=>$value) {
            $split_key = explode('/', $key);
            if ($split_key[0] == self::SEARCH_DELIVERY_TIME || $split_key[0] == self::SEARCH_PROFILE_SOCIAL_ACCOUNT ||
                self::isPhotoQuery(intval($split_key[0])) || self::isInstagramHashtagQuery($split_key[0]) ||
                self::isYoutubeChannelQuery($split_key[0])|| self::isFacebookLikeQuery($split_key[0]) ||
                self::isTwitterFollowQuery($split_key[0]) || self::isPopularVoteQuery($split_key[0]) ||
                self::isTweetQuery($split_key[0])) {
                $this->search_condition_key[$split_key[0]][$split_key[1]] = $value;
            } else {
                $this->search_condition_key[$split_key[0]][] = $value;
            }
        }
        return $this->search_condition_key;
    }

    /**
     * 絞り込みをJOIN句で返す
     * @param $search_condition
     * @return $join_sql
     */
    protected function getSearchJoin($search_condition, $order_condition) {

        $join_sql = "";
        if($search_condition[self::SEARCH_PROFILE_SEX] || $search_condition[self::SEARCH_PROFILE_AGE]) {
            $join_sql .= $this->getSearchAttributeJoin();
        }

        if($search_condition[self::SEARCH_PROFILE_ADDRESS]) {
            $join_sql .= $this->getSearchAddressJoin();
        }

        if(($search_condition[self::SEARCH_CP_ENTRY_COUNT] || $search_condition[self::SEARCH_CP_ANNOUNCE_COUNT] || $search_condition[self::SEARCH_MESSAGE_DELIVERED_COUNT] ||
            $search_condition[self::SEARCH_MESSAGE_READ_COUNT] || $search_condition[self::SEARCH_MESSAGE_READ_RATIO])) {
            $join_sql .= $this->getSearchBrandSearchInfoJoin();
        }

        $this->splitSearchKey($search_condition);

        if($this->search_condition_key[self::SEARCH_PROFILE_SOCIAL_ACCOUNT]) {

            $join_sql .= $this->getSearchSocialAccountJoin($this->search_condition_key[self::SEARCH_PROFILE_SOCIAL_ACCOUNT]);
        }

        if($search_condition[self::SEARCH_SOCIAL_ACCOUNT_SUM]) {
            $join_sql .= $this->getSearchSocialAccountSumJoin();
        }

        if($this->search_condition_key[self::SEARCH_PROFILE_QUESTIONNAIRE]) {
            $join_sql .= $this->getSearchProfileQuestionnaireJoin($this->search_condition_key[self::SEARCH_PROFILE_QUESTIONNAIRE]);
        }

        if($this->search_condition_key[self::SEARCH_PROFILE_CONVERSION]) {
            $join_sql .= $this->getSearchConversionJoin($this->search_condition_key[self::SEARCH_PROFILE_CONVERSION]);
        }

        if($this->search_condition_key[self::SEARCH_IMPORT_VALUE]) {
            $join_sql .= $this->getSearchImportValueJoin($this->search_condition_key[self::SEARCH_IMPORT_VALUE]);
        }

        if($this->search_condition_key[self::SEARCH_PARTICIPATE_CONDITION]) {
            $join_sql .= $this->getSearchParticipateConditionJoin($this->search_condition_key[self::SEARCH_PARTICIPATE_CONDITION]);
        }

        if($this->search_condition_key[self::SEARCH_QUESTIONNAIRE]) {
            $join_sql .= $this->getSearchQuestionnaireJoin($this->search_condition_key[self::SEARCH_QUESTIONNAIRE]);
        }

        if($this->search_condition_key[self::SEARCH_DELIVERY_TIME]) {
            $join_sql .= $this->getSearchDeliveryTimeJoin($this->search_condition_key[self::SEARCH_DELIVERY_TIME]);
        }

        // シェアSNSかシェアテキストか検閲の検索がある場合はjoinする
        if ($this->search_condition_key[self::SEARCH_PHOTO_SHARE_SNS] || $this->search_condition_key[self::SEARCH_PHOTO_SHARE_TEXT] || $this->search_condition_key[self::SEARCH_PHOTO_APPROVAL_STATUS]) {
            $join_sql .= $this->getSearchPhotoUserJoin($this->search_condition_key[self::SEARCH_PHOTO_SHARE_SNS], $this->search_condition_key[self::SEARCH_PHOTO_SHARE_TEXT], $this->search_condition_key[self::SEARCH_PHOTO_APPROVAL_STATUS]);
        }

        if ($this->search_condition_key[self::SEARCH_SHARE_TYPE] || $this->search_condition_key[self::SEARCH_SHARE_TEXT]) {
            $join_sql .= $this->getSearchShareUserLogJoin();
        }

        if($search_condition[self::SEARCH_QUERY_USER_TYPE]) {
            $join_sql .= $this->getSearchQueryUserJoin($search_condition[self::SEARCH_QUERY_USER_TYPE]);
        }

        if ($this->search_condition_key[self::SEARCH_INSTAGRAM_HASHTAG_DUPLICATION] || $this->search_condition_key[self::SEARCH_INSTAGRAM_HASHTAG_REVERSE_POST_TIME] || $this->search_condition_key[self::SEARCH_INSTAGRAM_HASHTAG_APPROVAL_STATUS]) {
            $join_sql .= $this->getSearchInstagramHashtagJoin($this->search_condition_key[self::SEARCH_INSTAGRAM_HASHTAG_DUPLICATION], $this->search_condition_key[self::SEARCH_INSTAGRAM_HASHTAG_REVERSE_POST_TIME], $this->search_condition_key[self::SEARCH_INSTAGRAM_HASHTAG_APPROVAL_STATUS]);
        }

        if ($this->search_condition_key[self::SEARCH_FB_LIKE_TYPE]) {
            $join_sql .= $this->getSearchFbLikeLogJoin();
        }

        if ($this->search_condition_key[self::SEARCH_TW_FOLLOW_TYPE]) {
            $join_sql .= $this->getSearchTwFollowLogJoin();
        }

        if ($this->search_condition_key[self::SEARCH_TWEET_TYPE]) {
            $join_sql .= $this->getSearchTweetMessageJoin();
        }

        if ($this->search_condition_key[self::SEARCH_YOUTUBE_CHANNEL_SUBSCRIPTION]) {
            $join_sql .= $this->getSearchYoutubeChannelJoin($this->search_condition_key[self::SEARCH_YOUTUBE_CHANNEL_SUBSCRIPTION]);
        }
        if ($this->search_condition_key[self::SEARCH_GIFT_RECEIVER_FAN]) {
            $join_sql .= $this->getSearchGiftReceiverFanJoin($this->search_condition_key[self::SEARCH_GIFT_RECEIVER_FAN]);
        }

        if ($this->search_condition_key[self::SEARCH_POPULAR_VOTE_CANDIDATE] || $this->search_condition_key[self::SEARCH_POPULAR_VOTE_SHARE_SNS] || $this->search_condition_key[self::SEARCH_POPULAR_VOTE_SHARE_TEXT]) {
            $join_sql .= $this->getSearchPopularVoteUserJoin($this->search_condition_key[self::SEARCH_POPULAR_VOTE_CANDIDATE], $this->search_condition_key[self::SEARCH_POPULAR_VOTE_SHARE_SNS], $this->search_condition_key[self::SEARCH_POPULAR_VOTE_SHARE_TEXT]);
        }

        if ($this->search_condition_key[self::SEARCH_SOCIAL_ACCOUNT_INTERACTIVE]) {
            $join_sql .= $this->getSearchSocialAccountInteractiveJoin($this->search_condition_key[self::SEARCH_SOCIAL_ACCOUNT_INTERACTIVE]);
        }

        if ($this->search_condition_key[self::SEARCH_SEGMENT_CONDITION]) {
            $join_sql .= $this->getSearchSegmentConditionJoin();
        }

        //TODO ハードコーディング: カンコーブランドのプロフィールアンケートの回答結果の絞り込み
        if($this->search_condition_key[self::SEARCH_CHILD_BIRTH_PERIOD]){
            $join_sql .= $this->getSearchChildBirthPeriodJoin($this->search_condition_key[self::SEARCH_CHILD_BIRTH_PERIOD]);
        }

        if(!$order_condition) {
            return $join_sql;
        }

        // ソートがあった場合はさらにJOINを行う
        $order_split_key = explode('/', key($order_condition));
        $order_condition_key[$order_split_key[0]] = true;

        if($order_condition[self::SEARCH_PROFILE_AGE] && !$search_condition[self::SEARCH_PROFILE_SEX] && !$search_condition[self::SEARCH_PROFILE_AGE]) {
            $join_sql .= $this->getSearchAttributeJoin();
        } elseif((self::$search_count_column[array_keys($order_condition)[0]] || $order_condition[self::SEARCH_MESSAGE_READ_RATIO]) &&
            (!$search_condition[self::SEARCH_CP_ENTRY_COUNT] && !$search_condition[self::SEARCH_CP_ANNOUNCE_COUNT] && !$search_condition[self::SEARCH_MESSAGE_DELIVERED_COUNT] &&
                !$search_condition[self::SEARCH_MESSAGE_READ_COUNT] && !$search_condition[self::SEARCH_MESSAGE_READ_RATIO]
            )) {
            $join_sql .= $this->getSearchBrandSearchInfoJoin();
        } elseif($order_condition_key[self::SEARCH_PROFILE_CONVERSION]) {
            $conversion_id = explode('/', key($order_condition))[1];
            if(!$search_condition[self::SEARCH_PROFILE_CONVERSION . '/' . $conversion_id] || $search_condition[self::SEARCH_PROFILE_CONVERSION . '/' . $conversion_id]['search_profile_conversion_from/' . $conversion_id] == 0) {
                $join_sql .= $this->setConversionJoin($conversion_id, 0, '');
            }
        } elseif($order_condition_key[self::SEARCH_PROFILE_SOCIAL_ACCOUNT]) {
            if(!$search_condition[self::SEARCH_PROFILE_SOCIAL_ACCOUNT . '/' . $order_split_key[1]]) {
                $join_sql .= $this->getSearchSocialAccountDefault0Join(array($order_split_key[1] => $search_condition));
            }
        } elseif($order_condition[self::SEARCH_SOCIAL_ACCOUNT_SUM] && !$search_condition[self::SEARCH_SOCIAL_ACCOUNT_SUM]) {
            $join_sql .= $this->getSearchSocialAccountSumJoin();
        }
        return $join_sql;
    }

    /**
     * WHERE句を返す
     * @param $brand_id
     * @param $search_condition
     * @return string
     */
    protected function getWhereSql($brand_id, $search_condition) {
        $where_sql = " WHERE relate.brand_id = {$this->escape($brand_id)} ";

        if($search_condition[self::SEARCH_PROFILE_RATE]) {
            $where_sql .= $this->getSearchRateWhereClause($search_condition[self::SEARCH_PROFILE_RATE]);
        }

        if($search_condition[self::SEARCH_PROFILE_MEMBER_NO]) {
            $where_sql .= $this->getSearchMemberNoWhereClause($search_condition[self::SEARCH_PROFILE_MEMBER_NO]);
        }

        if($search_condition[self::SEARCH_PROFILE_REGISTER_PERIOD]) {
            $where_sql .= $this->getSearchRegisterPeriodWhereClause($search_condition[self::SEARCH_PROFILE_REGISTER_PERIOD]);
        }

        if($this->search_condition_key[self::SEARCH_PROFILE_SOCIAL_ACCOUNT]) {
            $where_sql .= $this->getSearchSocialAccountWhereClause($this->search_condition_key[self::SEARCH_PROFILE_SOCIAL_ACCOUNT]);
        }

        if($search_condition[self::SEARCH_SOCIAL_ACCOUNT_SUM]) {
            $where_sql .= $this->getSearchSocialAccountSumWhereClause($search_condition[self::SEARCH_SOCIAL_ACCOUNT_SUM]);
        }

        if($search_condition[self::SEARCH_PROFILE_LAST_LOGIN]) {
            $where_sql .= $this->getSearchLastLoginWhereClause($search_condition[self::SEARCH_PROFILE_LAST_LOGIN]);
        }

        if($search_condition[self::SEARCH_PROFILE_LOGIN_COUNT]) {
            $where_sql .= $this->getSearchLoginCountWhereClause($search_condition[self::SEARCH_PROFILE_LOGIN_COUNT]);
        }

        if($search_condition[self::SEARCH_PROFILE_SEX]) {
            $where_sql .= $this->getSearchSexWhereClause($search_condition[self::SEARCH_PROFILE_SEX]);
        }

        if($search_condition[self::SEARCH_PROFILE_AGE]) {
            $where_sql .= $this->getSearchAgeWhereClause($search_condition[self::SEARCH_PROFILE_AGE]);
        }

        if($search_condition[self::SEARCH_PROFILE_ADDRESS]) {
            $where_sql .= $this->getSearchAddressWhereClause($search_condition[self::SEARCH_PROFILE_ADDRESS]);
        }

        if($this->search_condition_key[self::SEARCH_PROFILE_CONVERSION]) {
            $where_sql .= $this->getSearchConversionWhereClause($this->search_condition_key[self::SEARCH_PROFILE_CONVERSION], $brand_id);
        }

        if($search_condition[self::SEARCH_CP_ENTRY_COUNT]) {
            $where_sql .= $this->getSearchCountColumnCountWhereClause($search_condition[self::SEARCH_CP_ENTRY_COUNT], self::SEARCH_CP_ENTRY_COUNT);
        }

        if($search_condition[self::SEARCH_CP_ANNOUNCE_COUNT]) {
            $where_sql .= $this->getSearchCountColumnCountWhereClause($search_condition[self::SEARCH_CP_ANNOUNCE_COUNT], self::SEARCH_CP_ANNOUNCE_COUNT);
        }

        if($search_condition[self::SEARCH_MESSAGE_DELIVERED_COUNT]) {
            $where_sql .= $this->getSearchCountColumnCountWhereClause($search_condition[self::SEARCH_MESSAGE_DELIVERED_COUNT], self::SEARCH_MESSAGE_DELIVERED_COUNT);
        }

        if($search_condition[self::SEARCH_MESSAGE_READ_COUNT]) {
            $where_sql .= $this->getSearchCountColumnCountWhereClause($search_condition[self::SEARCH_MESSAGE_READ_COUNT], self::SEARCH_MESSAGE_READ_COUNT);
        }

        if($search_condition[self::SEARCH_MESSAGE_READ_RATIO]) {
            $where_sql .= $this->getSearchMessageReadRatioWhereClause($search_condition[self::SEARCH_MESSAGE_READ_RATIO]);
        }

        if($this->search_condition_key[self::SEARCH_PROFILE_QUESTIONNAIRE]) {
            $where_sql .= $this->getSearchProfileQuestionnaireWhereClause($this->search_condition_key[self::SEARCH_PROFILE_QUESTIONNAIRE]);
        }

        if($this->search_condition_key[self::SEARCH_IMPORT_VALUE]) {
            $where_sql .= $this->getSearchImportValueWhereClause($this->search_condition_key[self::SEARCH_IMPORT_VALUE]);
        }

        if($search_condition[self::SEARCH_PROFILE_QUESTIONNAIRE_STATUS]) {
            $where_sql .= $this->getSearchQuestionnaireStatusWhereClause($search_condition[self::SEARCH_PROFILE_QUESTIONNAIRE_STATUS]);
        }

        if($this->search_condition_key[self::SEARCH_PARTICIPATE_CONDITION]) {
            $where_sql .= $this->getSearchParticipateConditionWhereClause($this->search_condition_key[self::SEARCH_PARTICIPATE_CONDITION]);
        }

        if($this->search_condition_key[self::SEARCH_QUESTIONNAIRE]) {
            $where_sql .= $this->getSearchQuestionnaireWhereClause($this->search_condition_key[self::SEARCH_QUESTIONNAIRE]);
        }

        if($this->search_condition_key[self::SEARCH_DELIVERY_TIME]) {
            $where_sql .= $this->getSearchDeliveryTimeWhereClause($this->search_condition_key[self::SEARCH_DELIVERY_TIME]);
        }

        if($search_condition[self::SEARCH_QUERY_USER_TYPE]) {
            $where_sql .= $this->getSearchQueryUserWhereClause($search_condition[self::SEARCH_QUERY_USER_TYPE]);
        }

        if($search_condition[self::SEARCH_JOIN_FAN_ONLY]) {
            $where_sql .= $this->getJoinUserOnlyWhereClause($search_condition[self::SEARCH_JOIN_FAN_ONLY]);
        }

        if($this->search_condition_key[self::SEARCH_PHOTO_SHARE_SNS]) {
            $where_sql .= $this->getSearchPhotoShareSnsWhereClause($this->search_condition_key[self::SEARCH_PHOTO_SHARE_SNS]);
        }

        if($this->search_condition_key[self::SEARCH_PHOTO_SHARE_TEXT]) {
            $where_sql .= $this->getSearchPhotoShareTextWhereClause($this->search_condition_key[self::SEARCH_PHOTO_SHARE_TEXT], $this->search_condition_key[self::SEARCH_PHOTO_SHARE_SNS]);
        }

        if($this->search_condition_key[self::SEARCH_PHOTO_APPROVAL_STATUS]) {
            $where_sql .= $this->getSearchPhotoApprovalStatusWhereClause($this->search_condition_key[self::SEARCH_PHOTO_APPROVAL_STATUS]);
        }

        if($search_condition[self::SEARCH_SHARE_TYPE]) {
            $where_sql .= $this->getSearchShareUserLogTypeWhereClause($search_condition[self::SEARCH_SHARE_TYPE]);
        }

        if($search_condition[self::SEARCH_SHARE_TEXT]) {
            $where_sql .= $this->getSearchShareUserLogTextWhereClause($search_condition[self::SEARCH_SHARE_TEXT]);
        }

        if ($this->search_condition_key[self::SEARCH_INSTAGRAM_HASHTAG_DUPLICATION]) {
            $where_sql .= $this->getSearchInstagramHashtagDuplicateWhereClause($this->search_condition_key[self::SEARCH_INSTAGRAM_HASHTAG_DUPLICATION]);
        }

        if ($this->search_condition_key[self::SEARCH_INSTAGRAM_HASHTAG_REVERSE_POST_TIME]) {
            $where_sql .= $this->getSearchInstagramHashtagReverseWhereClause($this->search_condition_key[self::SEARCH_INSTAGRAM_HASHTAG_REVERSE_POST_TIME]);
        }

        if ($this->search_condition_key[self::SEARCH_INSTAGRAM_HASHTAG_APPROVAL_STATUS]) {
            $where_sql .= $this->getSearchInstagramHashtagApprovalStatusWhereClause($this->search_condition_key[self::SEARCH_INSTAGRAM_HASHTAG_APPROVAL_STATUS]);
        }

        if ($this->search_condition_key[self::SEARCH_FB_LIKE_TYPE]) {
            $where_sql .= $this->getSearchFbLikeLogStatusWhereClause($this->search_condition_key[self::SEARCH_FB_LIKE_TYPE]);
        }

        if ($this->search_condition_key[self::SEARCH_TW_FOLLOW_TYPE]) {
            $where_sql .= $this->getSearchTwFollowLogStatusWhereClause($this->search_condition_key[self::SEARCH_TW_FOLLOW_TYPE]);
        }

        if ($this->search_condition_key[self::SEARCH_TWEET_TYPE]) {
            $where_sql .= $this->getSearchTweetMessageStatusWhereClause($this->search_condition_key[self::SEARCH_TWEET_TYPE]);
        }

        if ($this->search_condition_key[self::SEARCH_YOUTUBE_CHANNEL_SUBSCRIPTION]) {
            $where_sql .= $this->getSearchYoutubeChannelApprovalStatusWhereClause($this->search_condition_key[self::SEARCH_YOUTUBE_CHANNEL_SUBSCRIPTION]);
        }

        if ($this->search_condition_key[self::SEARCH_GIFT_RECEIVER_FAN]) {
            $where_sql .= $this->getSearchGiftReceiverFanWhereClause($this->search_condition_key[self::SEARCH_GIFT_RECEIVER_FAN]);
        }

        if($this->search_condition_key[self::SEARCH_POPULAR_VOTE_CANDIDATE]) {
            $where_sql .= $this->getSearchPopularVoteCandidateWhereClause($this->search_condition_key[self::SEARCH_POPULAR_VOTE_CANDIDATE]);
        }

        if($this->search_condition_key[self::SEARCH_POPULAR_VOTE_SHARE_SNS]) {
            $where_sql .= $this->getSearchPopularVoteShareSnsWhereClause($this->search_condition_key[self::SEARCH_POPULAR_VOTE_SHARE_SNS]);
        }

        if($this->search_condition_key[self::SEARCH_POPULAR_VOTE_SHARE_TEXT]) {
            $where_sql .= $this->getSearchPopularVoteShareTextWhereClause($this->search_condition_key[self::SEARCH_POPULAR_VOTE_SHARE_TEXT], $this->search_condition_key[self::SEARCH_POPULAR_VOTE_SHARE_SNS]);
        }

        if($this->search_condition_key[self::SEARCH_SOCIAL_ACCOUNT_INTERACTIVE]) {
            $where_sql .= $this->getSearchSocialAccountInteractiveWhereClause($this->search_condition_key[self::SEARCH_SOCIAL_ACCOUNT_INTERACTIVE]);
        }

        if($this->search_condition_key[self::SEARCH_DUPLICATE_ADDRESS]) {
            $where_sql .= $this->getSearchDuplicateAddressWhereClause($search_condition[self::SEARCH_DUPLICATE_ADDRESS]);
        }

        if($this->search_condition_key[self::SEARCH_SEGMENT_CONDITION]) {
            $where_sql .= $this->getSearchSegmentConditionWhereClause($search_condition[self::SEARCH_SEGMENT_CONDITION]);
        }

        //TODO ハードコーディング: カンコーブランドのプロフィールアンケートの回答結果の絞り込み
        if($this->search_condition_key[self::SEARCH_CHILD_BIRTH_PERIOD]) {
            $where_sql .= $this->getSearchChildBirthPeriodWhereClause($this->search_condition_key[self::SEARCH_CHILD_BIRTH_PERIOD]);
        }

        $where_sql .= " AND relate.del_flg = 0 AND relate.withdraw_flg = 0 ";
        return $where_sql;
    }

    /**
     * 連携済SNSに関する絞り込み
     * @param $conditions
     * @return string
     */
    protected function getSearchSocialAccountJoin($conditions) {
        $join_sql = "";
        foreach($conditions as $media_type => $condition) {
            $join_sql .= " LEFT OUTER JOIN social_accounts sa{$media_type} ON sa{$media_type}.user_id = relate.user_id AND sa{$media_type}.social_media_id = {$media_type} AND sa{$media_type}.del_flg = 0 ";
        }
        return $join_sql;
    }

    /**
     * 連携済SNSに関する絞り込み (デフォルト値を0にする)
     * @param $conditions
     * @return string
     */
    protected function getSearchSocialAccountDefault0Join($conditions) {
        $join_sql = "";
        foreach($conditions as $media_type => $condition) {
            $join_sql .= " LEFT OUTER JOIN (SELECT user_id, IFNULL(friend_count, 0) friend_count FROM social_accounts WHERE social_media_id = {$media_type} AND del_flg = 0) sa{$media_type} ON sa{$media_type}.user_id = relate.user_id ";
        }
        return $join_sql;
    }

    /**
     * 連携済SNSの合計に関する絞り込み
     * @return string
     */
    protected function getSearchSocialAccountSumJoin() {
        $join_sql = " LEFT OUTER JOIN ( SELECT SUM_SA.user_id,sum(ifnull(SUM_SA.friend_count,0)) sum_sa,COUNT(SUM_SA.id) cnt_sa FROM social_accounts SUM_SA
                    WHERE SUM_SA.social_media_id != -1 AND SUM_SA.del_flg = 0 GROUP BY SUM_SA.user_id) sumtmp ON relate.user_id = sumtmp.user_id ";
        return $join_sql;
    }

    /**
     * 性別・生年月日に関する絞り込み
     * @return string
     */
    protected function getSearchAttributeJoin() {
        $join_sql = " LEFT OUTER JOIN user_search_info searchinfo ON searchinfo.user_id = relate.user_id AND searchinfo.del_flg = 0 ";
        return $join_sql;
    }

    /**
     * 都道府県に関する絞り込み
     * @return string
     */
    protected function getSearchAddressJoin() {
        $join_sql = " LEFT OUTER JOIN shipping_addresses addr ON addr.user_id = relate.user_id AND addr.del_flg = 0
                      LEFT OUTER JOIN prefectures pref ON pref.id = addr.pref_id AND pref.del_flg = 0 ";
        return $join_sql;
    }

    /**
     * キャンペーン参加回数・キャンペーン当選回数・メッセージ受信数・メッセージ開封数に関する絞り込み
     * @return string
     */
    protected function getSearchBrandSearchInfoJoin() {
        $join_sql = " LEFT OUTER JOIN brands_users_search_info brand_search ON brand_search.brands_users_relation_id = relate.id AND brand_search.del_flg = 0 ";
        return $join_sql;
    }

    /**
     * 参加時アンケートに関する絞り込み
     * @param $condition
     * @return string
     */
    protected function getSearchProfileQuestionnaireJoin($condition) {
        $join_sql = '';
        foreach ($condition as $questionnaire_condition) {
            $switch_value = '';
            $join = false;
            $free_join = false;
            foreach ($questionnaire_condition as $key => $value) {
                if (preg_match('/^search_profile_questionnaire\//', $key)) {
                    $split_key = explode('/', $key);
                    $relate_id = $split_key[1];
                    $user_answer = $split_key[2];

                    if ($questionnaire_condition['questionnaire_type/'.$relate_id] == QuestionTypeService::FREE_ANSWER_TYPE) {
                        if (!$free_join) {
                            $join_sql .= " LEFT OUTER JOIN profile_question_free_answers free_ans{$relate_id} ON free_ans{$relate_id}.brands_users_relation_id = relate.id AND free_ans{$relate_id}.questionnaires_questions_relation_id = {$relate_id} AND free_ans{$relate_id}.del_flg = 0 ";
                            $free_join = true;
                        }
                    } else {
                        if (!$switch_value) $switch_value = $questionnaire_condition['switch_type/'.self::SEARCH_PROFILE_QUESTIONNAIRE.'/'.$relate_id] ? $questionnaire_condition['switch_type/'.self::SEARCH_PROFILE_QUESTIONNAIRE.'/'.$relate_id] : self::QUERY_TYPE_OR;
                        if ($switch_value == self::QUERY_TYPE_AND) {
                            $join_sql .= " LEFT OUTER JOIN profile_question_choice_answers ans{$relate_id}_{$user_answer} ON ans{$relate_id}_{$user_answer}.brands_users_relation_id = relate.id AND ans{$relate_id}_{$user_answer}.questionnaires_questions_relation_id = {$relate_id} AND ans{$relate_id}_{$user_answer}.del_flg = 0 ";
                        } else {
                            if(!$join) {
                                $join_sql .= " LEFT OUTER JOIN profile_question_choice_answers ans{$relate_id} ON ans{$relate_id}.brands_users_relation_id = relate.id AND ans{$relate_id}.questionnaires_questions_relation_id = {$relate_id} AND ans{$relate_id}.del_flg = 0 ";
                                $join = true;
                            }
                        }
                    }
                }
            }
        }
        return $join_sql;
    }

    /**
     * TODO ハードコーディング: カンコーブランドのプロフィールアンケートの回答結果の絞り込み
     * @param $conditions
     * @return string
     */
    protected function getSearchChildBirthPeriodJoin($conditions) {
        $join_sql = '';
        foreach ($conditions as $child_birth_condition) {
            foreach ($child_birth_condition as $key => $value) {
                if (preg_match('/^search_child_birth_period\//', $key)) {
                    $split_key = explode('/', $key);
                    $relate_id = $split_key[1];
                    $join_sql .= " LEFT OUTER JOIN profile_question_choice_answers ans_choice{$relate_id} ON ans_choice{$relate_id}.brands_users_relation_id = relate.id AND ans_choice{$relate_id}.questionnaires_questions_relation_id = {$relate_id} AND ans_choice{$relate_id}.del_flg = 0 ";
                }
            }
        }
        return $join_sql;
    }

    /**
     * 外部インポートデータに関する絞り込み
     * @param $condition
     * @return string
     */
    protected function getSearchImportValueJoin($condition) {
        $join_sql = '';
        foreach ($condition as $questionnaire_condition) {
            $join = false;
            foreach ($questionnaire_condition as $key => $value) {
                if (preg_match('/^search_import_value\//', $key) && !$join) {
                    $definition_id = explode('/', $key)[1];
                    $join_sql .= " LEFT OUTER JOIN brand_user_attributes bua{$definition_id} ON bua{$definition_id}.user_id = relate.user_id AND bua{$definition_id}.definition_id = {$definition_id} ";
                    $join = true;
                }
            }
        }
        return $join_sql;
    }

    /**
     * コンバージョンに関する絞り込み
     * @param $condition
     * @return string
     */
    protected function getSearchConversionJoin($condition) {
        $join_sql = '';
        foreach ($condition as $conversion_condition) {
            $conversion_id = '';
            foreach ($conversion_condition as $key => $value) {
                if (preg_match('/^search_profile_conversion_/', $key)) {
                    if($conversion_id != explode('/', $key)[1]) {
                        $conversion_id = $this->escape(explode('/', $key)[1]);
                        $search_conversion_from = $conversion_condition['search_profile_conversion_from/' . $conversion_id];
                        $search_conversion_to = $conversion_condition['search_profile_conversion_to/' . $conversion_id];

                        if ($search_conversion_from == 0 AND $search_conversion_to === '') { // 検索条件が「0〜」の場合は絞り込みをする必要なし(全員対象になる)
                            break;
                        } elseif ($search_conversion_from == 0 AND $search_conversion_to == 0) { // 検索条件が「0〜0」の場合はコンバージョンテーブルに存在しない人だけを対象にすれば良い
                            $join_sql .= " LEFT OUTER JOIN brands_users_conversions cv{$conversion_id} ON cv{$conversion_id}.user_id = relate.user_id AND cv{$conversion_id}.id = {$conversion_id} AND cv{$conversion_id}.del_flg = 0 ";
                        } else { // 検索条件が「1〜100」等の場合
                            $join_sql .= $this->setConversionJoin($conversion_id, $search_conversion_from, $search_conversion_to);
                        }
                    }
                }
            }
        }
        return $join_sql;
    }

    /**
     * @param $conditions
     * @return string
     */
    protected function getSearchDeliveryTimeJoin($conditions) {
        $join_sql = "";
        foreach ($conditions as $action_id => $value) {
            $join_sql .= ' LEFT OUTER JOIN cp_message_delivery_targets as target'.$action_id.' ON target'.$action_id .'.user_id = relate.user_id AND target'.$action_id .'.cp_action_id = '.$action_id .' AND target'.$action_id .'.status = 1 AND target'.$action_id .'.del_flg = 0 ';
        }
        return $join_sql;
    }

    /**
     * @param $share_sns_condition
     * @param $share_text_condition
     * @return string
     */
    protected function getSearchPhotoUserJoin($share_sns_condition, $share_text_condition ,$approval_status) {

        // 今回の絞り込みを行っているフォトのaction_idを取得する
        $action_id_array = array();
        foreach($share_sns_condition as $key => $value) {
            if(!in_array($key, $action_id_array)) {
                $action_id_array[] = $key;
            }
        }
        foreach($share_text_condition as $key => $value) {
            if(!in_array($key, $action_id_array)) {
                $action_id_array[] = $key;
            }
        }
        foreach($approval_status as $key => $value) {
            if(!in_array($key, $action_id_array)) {
                $action_id_array[] = $key;
            }
        }

        $join_sql = '';
        foreach($action_id_array as $action_id) {
            $escape_action_id = $this->escape($action_id);
            $join_sql .= " LEFT OUTER JOIN photo_users as photo_user{$escape_action_id} ON photo_user{$escape_action_id}.cp_user_id = cp_usr.id AND photo_user{$escape_action_id}.cp_action_id = {$escape_action_id} AND photo_user{$escape_action_id}.del_flg = 0 ";
            if($share_sns = $share_sns_condition[$action_id]) {
                $switch_type = $share_sns['switch_type/' . self::SEARCH_PHOTO_SHARE_SNS. '/' .$action_id];
                if($switch_type == self::QUERY_TYPE_AND) {
                    if($share_sns['search_photo_share_sns/' . $action_id . '/' . SocialAccount::SOCIAL_MEDIA_FACEBOOK]) {
                        $fb_alias = $this->escape($action_id) . '_' . SocialAccount::SOCIAL_MEDIA_FACEBOOK;
                        $join_sql .= " LEFT OUTER JOIN photo_user_shares as user_share{$fb_alias}
                                       ON photo_user{$escape_action_id}.id = user_share{$fb_alias}.photo_user_id AND user_share{$fb_alias}.del_flg = 0 ";
                    }

                    if($share_sns['search_photo_share_sns/' . $action_id . '/' . SocialAccount::SOCIAL_MEDIA_TWITTER]) {
                        $tw_alias = $this->escape($action_id) . '_' . SocialAccount::SOCIAL_MEDIA_TWITTER;
                        $join_sql .= " LEFT OUTER JOIN photo_user_shares as user_share{$tw_alias}
                                       ON photo_user{$escape_action_id}.id = user_share{$tw_alias}.photo_user_id AND user_share{$tw_alias}.del_flg = 0 ";
                    }

                    if($share_sns['search_photo_share_sns/' . $action_id . '/-1']) {
                        $not_share_alias = $this->escape($action_id) .'_' . '99';//-1はテーブル別名に指定できない
                        $join_sql .= " LEFT OUTER JOIN photo_user_shares as user_share{$not_share_alias}
                                       ON photo_user{$escape_action_id}.id = user_share{$not_share_alias}.photo_user_id AND user_share{$not_share_alias}.del_flg = 0 ";
                    }
                } else {
                    $join_sql .= " LEFT OUTER JOIN photo_user_shares as user_share{$escape_action_id} ON photo_user{$escape_action_id}.id = user_share{$escape_action_id}.photo_user_id AND user_share{$escape_action_id}.del_flg = 0 ";
                }
            } elseif($share_text_condition[$action_id]) {
                $join_sql .= " LEFT OUTER JOIN photo_user_shares as user_share{$escape_action_id} ON photo_user{$escape_action_id}.id = user_share{$escape_action_id}.photo_user_id AND user_share{$escape_action_id}.del_flg = 0";
            }
        }
        return $join_sql;
    }

    protected function getSearchInstagramHashtagJoin($duplicate_condition, $reverse_condition, $approval_status_condition) {

        $action_id_array = array();
        foreach($duplicate_condition as $key => $value) {
            if(!in_array($key, $action_id_array)) {
                $action_id_array[] = $key;
            }
        }
        foreach($reverse_condition as $key => $value) {
            if(!in_array($key, $action_id_array)) {
                $action_id_array[] = $key;
            }
        }
        foreach($approval_status_condition as $key => $value) {
            if(!in_array($key, $action_id_array)) {
                $action_id_array[] = $key;
            }
        }

        $join_sql = '';
        foreach ($action_id_array as $action_id) {
            $escape_action_id = $this->escape($action_id);
            $join_sql .= " LEFT OUTER JOIN instagram_hashtag_users as hashtag_users{$escape_action_id} ON hashtag_users{$escape_action_id}.cp_user_id = cp_usr.id AND hashtag_users{$escape_action_id}.cp_action_id = {$escape_action_id} AND hashtag_users{$escape_action_id}.del_flg = 0 ";
            $join_sql .= " LEFT OUTER JOIN instagram_hashtag_user_posts as hashtag_user_posts{$escape_action_id} ON hashtag_user_posts{$escape_action_id}.instagram_hashtag_user_id = hashtag_users{$escape_action_id}.id AND hashtag_users{$escape_action_id}.del_flg = 0 ";
        }

        return $join_sql;
    }

    protected function getSearchYoutubeChannelJoin($subscription_condition) {

        $action_id_array = array();
        foreach($subscription_condition as $key => $value) {
            if(!in_array($key, $action_id_array)) {
                $action_id_array[] = $key;
            }
        }

        $join_sql = '';
        foreach ($action_id_array as $action_id) {
            $escape_action_id = $this->escape($action_id);
            $join_sql .= " LEFT OUTER JOIN cp_youtube_channel_user_logs as ytch_user_logs{$escape_action_id} ON ytch_user_logs{$escape_action_id}.cp_user_id = cp_usr.id AND ytch_user_logs{$escape_action_id}.cp_action_id = {$escape_action_id} AND ytch_user_logs{$escape_action_id}.del_flg = 0 ";
        }

        return $join_sql;
    }

    protected function getSearchGiftReceiverFanJoin($search_conditions) {
        $action_id_array = array();
        foreach ($search_conditions as  $key => $action_id) {
            if (!in_array($action_id, $action_id_array)) {
                $action_id_array[] = $action_id;
            }
        }
        $cp_gift_action_service = $this->getService('CpGiftActionService');
        $join_sql = '';
        foreach ($action_id_array as $action_id) {
            $escape_action_id = $this->escape($action_id);
            $concrete_action = $cp_gift_action_service->getCpGiftAction($action_id);
            $join_sql .= " LEFT OUTER JOIN gift_messages as gift_message{$escape_action_id} ON gift_message{$escape_action_id}.receiver_user_id = relate.user_id AND gift_message{$escape_action_id}.cp_gift_action_id = {$concrete_action->id} AND gift_message{$escape_action_id}.del_flg = 0";
        }

        return $join_sql;
    }
    /**
     * @param $share_sns_condition
     * @param $share_text_condition
     * @return string
     */
    protected function getSearchShareUserLogJoin() {
        $join_sql = " LEFT OUTER JOIN cp_share_user_logs as share_user_logs ON share_user_logs.cp_user_id = cp_usr.id AND share_user_logs.del_flg = 0";

        return $join_sql;
    }

    protected function getSearchFbLikeLogJoin() {
        $action_id_array = array();

        foreach ($this->search_condition_key[self::SEARCH_FB_LIKE_TYPE] as $action_id => $value) {
            if (!in_array($action_id, $action_id_array)) {
                $action_id_array[] = $action_id;
            }
        }

        $join_sql = '';
        foreach ($action_id_array as $action_id) {
            $escape_action_id = $this->escape($action_id);

            $join_sql .= " LEFT OUTER JOIN engagement_logs as fb_like_logs{$escape_action_id} ON fb_like_logs{$escape_action_id}.cp_user_id = cp_usr.id AND fb_like_logs{$escape_action_id}.cp_action_id = {$escape_action_id} AND fb_like_logs{$escape_action_id}.del_flg = 0";
        }

        return $join_sql;
    }

    protected function getSearchTwFollowLogJoin() {
        $action_id_array = array();

        foreach ($this->search_condition_key[self::SEARCH_TW_FOLLOW_TYPE] as $action_id => $value) {
            if (!in_array($action_id, $action_id_array)) {
                $action_id_array[] = $action_id;
            }
        }

        $cp_twitter_follow_action_service = $this->getService('CpTwitterFollowActionService');
        $join_sql = '';
        foreach ($action_id_array as $action_id) {
            $escape_action_id = $this->escape($action_id);
            $concrete_action = $cp_twitter_follow_action_service->getCpTwitterFollowAction($action_id);
            $join_sql .= " LEFT OUTER JOIN cp_twitter_follow_logs as tw_follow_logs{$escape_action_id} ON tw_follow_logs{$escape_action_id}.cp_user_id = cp_usr.id AND tw_follow_logs{$escape_action_id}.action_id = {$concrete_action->id} AND tw_follow_logs{$escape_action_id}.del_flg = 0";
        }

        return $join_sql;
    }

    protected function getSearchTweetMessageJoin() {
        $action_id_array = array();

        foreach ($this->search_condition_key[self::SEARCH_TWEET_TYPE] as $action_id => $value) {
            if (!in_array($action_id, $action_id_array)) {
                $action_id_array[] = $action_id;
            }
        }

        $cp_tweet_action_service = $this->getService('CpTweetActionService');
        $join_sql = '';

        foreach ($action_id_array as $action_id) {
            $escape_action_id = $this->escape($action_id);
            $concrete_action = $cp_tweet_action_service->getCpTweetAction($action_id);
            $join_sql .= " LEFT OUTER JOIN tweet_messages as tweet_messages{$escape_action_id} ON tweet_messages{$escape_action_id}.cp_user_id = cp_usr.id AND tweet_messages{$escape_action_id}.cp_tweet_action_id = {$concrete_action->id} AND tweet_messages{$escape_action_id}.del_flg = 0";
        }
        return $join_sql;
    }

    /**
     * @param $candidate_condition
     * @param $share_sns_condition
     * @param $share_text_condition
     * @return string
     */
    protected function getSearchPopularVoteUserJoin($candidate_condition, $share_sns_condition, $share_text_condition) {

        $action_id_array = array();
        foreach($candidate_condition as $key => $value) {
            if(!in_array($key, $action_id_array)) {
                $action_id_array[] = $key;
            }
        }
        foreach($share_sns_condition as $key => $value) {
            if(!in_array($key, $action_id_array)) {
                $action_id_array[] = $key;
            }
        }
        foreach($share_text_condition as $key => $value) {
            if(!in_array($key, $action_id_array)) {
                $action_id_array[] = $key;
            }
        }

        $join_sql = '';
        foreach($action_id_array as $action_id) {
            $escape_action_id = $this->escape($action_id);
            $join_sql .= " LEFT OUTER JOIN popular_vote_users as popular_vote_user{$escape_action_id} ON popular_vote_user{$escape_action_id}.cp_user_id = cp_usr.id AND popular_vote_user{$escape_action_id}.cp_action_id = {$escape_action_id} AND popular_vote_user{$escape_action_id}.del_flg = 0 ";
            if($share_sns = $share_sns_condition[$action_id]) {
                $switch_type = $share_sns['switch_type/' . self::SEARCH_POPULAR_VOTE_SHARE_SNS. '/' .$action_id];
                if($switch_type == self::QUERY_TYPE_AND) {
                    if($share_sns['search_popular_vote_share_sns/' . $action_id . '/' . SocialAccount::SOCIAL_MEDIA_FACEBOOK]) {
                        $fb_alias = $this->escape($action_id) . '_' . SocialAccount::SOCIAL_MEDIA_FACEBOOK;
                        $join_sql .= " LEFT OUTER JOIN popular_vote_user_shares as popular_vote_user_share{$fb_alias}
                                       ON popular_vote_user{$escape_action_id}.id = popular_vote_user_share{$fb_alias}.popular_vote_user_id AND popular_vote_user_share{$fb_alias}.del_flg = 0 ";
                    }

                    if($share_sns['search_popular_vote_share_sns/' . $action_id . '/' . SocialAccount::SOCIAL_MEDIA_TWITTER]) {
                        $tw_alias = $this->escape($action_id) . '_' . SocialAccount::SOCIAL_MEDIA_TWITTER;
                        $join_sql .= " LEFT OUTER JOIN popular_vote_user_shares as popular_vote_user_share{$tw_alias}
                                       ON popular_vote_user{$escape_action_id}.id = popular_vote_user_share{$tw_alias}.popular_vote_user_id AND popular_vote_user_share{$tw_alias}.del_flg = 0 ";
                    }

                    if($share_sns['search_popular_vote_share_sns/' . $action_id . '/-1']) {
                        $not_share_alias = $this->escape($action_id) .'_' . '99';//-1はテーブル別名に指定できない
                        $join_sql .= " LEFT OUTER JOIN popular_vote_user_shares as popular_vote_user_share{$not_share_alias}
                                       ON popular_vote_user{$escape_action_id}.id = popular_vote_user_share{$not_share_alias}.popular_vote_user_id AND popular_vote_user_share{$not_share_alias}.del_flg = 0 ";
                    }
                } else {
                    $join_sql .= " LEFT OUTER JOIN popular_vote_user_shares as popular_vote_user_share{$escape_action_id} ON popular_vote_user{$escape_action_id}.id = popular_vote_user_share{$escape_action_id}.popular_vote_user_id AND popular_vote_user_share{$escape_action_id}.del_flg = 0 ";
                }
            } elseif($share_text_condition[$action_id]) {
                $join_sql .= " LEFT OUTER JOIN popular_vote_user_shares as popular_vote_user_share{$escape_action_id} ON popular_vote_user{$escape_action_id}.id = popular_vote_user_share{$escape_action_id}.popular_vote_user_id AND popular_vote_user_share{$escape_action_id}.del_flg = 0";
            }
        }
        return $join_sql;
    }

    /**
     * 参加状況に関する絞り込み
     * @param $condition
     * @return $join_sql
     */
    protected function getSearchParticipateConditionJoin($condition) {
        $join_sql = '';
        $delivery_target_action_id_array = array();
        foreach($condition as $participate_condition) {
            $use_status_table = false;
            $use_message_table = false;
            $use_target_table = false;
            $first_flg = true;
            foreach ($participate_condition as $key => $value) {
                if (preg_match('/^search_participate_condition\//', $key)) {
                    $action_id = explode('/', $key)[1];
                    $participate_status = explode('/', $key)[2];
                    if ($first_flg) {
                        // スピードくじのときだけ、ANDとORの選択がある
                        $switch_value = $participate_condition['switch_type/' . self::SEARCH_PARTICIPATE_CONDITION . '/' . $action_id] ? $participate_condition['switch_type/' . self::SEARCH_PARTICIPATE_CONDITION . '/' . $action_id] : '';
                        if ($switch_value) {
                            $join_sql = $this->createParticipateSwitchPatternJoin($join_sql, $participate_condition, $switch_value);
                            break;
                        }
                    }
                    if ($participate_status == self::PARTICIPATE_COMPLETE || $participate_status == self::PARTICIPATE_REJECTED) {
                        $use_status_table = true;
                    }
                    if ($participate_status == self::PARTICIPATE_READ) {
                        $use_status_table = true;
                        $use_message_table = true;
                    }
                    if ($participate_status == self::PARTICIPATE_NOT_READ || $participate_status == self::PARTICIPATE_NOT_SEND) {
                        $use_message_table = true;
                    }
                    if ($participate_status == self::PARTICIPATE_TARGET || $participate_status == self::PARTICIPATE_NOT_TARGET) {
                        $use_target_table = true;
                    }
                    $first_flg = false;
                }
            }
            if($use_status_table) {
                $join_sql .= " LEFT OUTER JOIN cp_user_action_statuses state{$this->escape($action_id)} ON state{$this->escape($action_id)}.cp_user_id = cp_usr.id AND state{$this->escape($action_id)}.cp_action_id = {$this->escape($action_id)} AND state{$this->escape($action_id)}.del_flg = 0 ";
            }
            if($use_message_table) {
                $join_sql .= " LEFT OUTER JOIN cp_user_action_messages mes{$this->escape($action_id)} ON mes{$this->escape($action_id)}.cp_user_id = cp_usr.id AND mes{$this->escape($action_id)}.cp_action_id = {$this->escape($action_id)} AND mes{$this->escape($action_id)}.del_flg = 0 ";
            }
            if ($use_target_table) {
                $delivery_target_action_id = $this->getDeliveryTargetActionId($action_id);
                if(!in_array($delivery_target_action_id,$delivery_target_action_id_array)){
                    $delivery_target_action_id_array[] = $delivery_target_action_id;
                    $join_sql .= " LEFT OUTER JOIN cp_message_delivery_targets target{$this->escape($delivery_target_action_id)} ON target{$this->escape($delivery_target_action_id)}.user_id = relate.user_id AND target{$this->escape($delivery_target_action_id)}.cp_action_id = {$this->escape($delivery_target_action_id)} AND
                                target{$this->escape($delivery_target_action_id)}.del_flg = 0 AND target{$this->escape($delivery_target_action_id)}.fix_target_flg = " . CpMessageDeliveryTarget::FIX_TARGET_ON . " ";
                }
            }
        }
        return $join_sql;
    }


    /**
     * 送信済み・送信対象に関する絞り込み
     * @param $condition
     * @return $join_sql
     */
    protected function getSearchQueryUserJoin($condition) {
        $query_user_condition = explode('/', $condition);
        if($query_user_condition[0] == self::QUERY_USER_TARGET) {
            $join_sql = " LEFT OUTER JOIN cp_message_delivery_targets tar ON tar.user_id = relate.user_id AND tar.status = 0
                          AND tar.cp_message_delivery_reservation_id = {$this->escape($query_user_condition[1])} AND tar.cp_action_id = {$this->escape($query_user_condition[2])} AND tar.del_flg = 0 ";
        } elseif($query_user_condition[0] == self::QUERY_USER_SENT) {
            $join_sql = " LEFT OUTER JOIN cp_user_action_messages mes ON mes.cp_user_id = cp_usr.id AND mes.cp_action_id = {$this->escape($query_user_condition[1])} AND mes.del_flg = 0 ";
        }
        return $join_sql;
    }

    /**
     * アンケートに関する絞り込み
     * @param $condition
     * @return $join_sql
     */
    protected function getSearchQuestionnaireJoin($condition) {
        $join_sql = '';
        foreach ($condition as $questionnaire_condition) {
            $switch_value = '';
            $join = false;
            $free_join = false;
            foreach ($questionnaire_condition as $key => $value) {
                if (preg_match('/^search_questionnaire\//', $key)) {
                    $split_key = explode('/', $key);
                    $relate_id = $split_key[1];
                    $user_answer = $split_key[2];
                    if ($questionnaire_condition['questionnaire_type/'.$relate_id] == QuestionTypeService::FREE_ANSWER_TYPE) {
                        if (!$free_join) {
                            $join_sql .= " LEFT OUTER JOIN question_free_answers q_free_ans{$relate_id} ON q_free_ans{$relate_id}.brands_users_relation_id = relate.id AND q_free_ans{$relate_id}.questionnaires_questions_relation_id = {$relate_id} AND q_free_ans{$relate_id}.del_flg = 0 ";
                            $free_join = true;
                        }
                    } else {
                        if (!$switch_value) $switch_value = $questionnaire_condition['switch_type/' . self::SEARCH_QUESTIONNAIRE . '/' . $relate_id] ? $questionnaire_condition['switch_type/' . self::SEARCH_QUESTIONNAIRE . '/' . $relate_id] : self::QUERY_TYPE_OR;
                        if ($switch_value == self::QUERY_TYPE_AND) {
                            $join_sql .= " LEFT OUTER JOIN question_choice_answers ans{$relate_id}_{$user_answer} ON ans{$relate_id}_{$user_answer}.brands_users_relation_id = relate.id AND ans{$relate_id}_{$user_answer}.questionnaires_questions_relation_id = {$relate_id} AND ans{$relate_id}_{$user_answer}.del_flg = 0 ";
                        } else {
                            if (!$join) {
                                $join_sql .= " LEFT OUTER JOIN question_choice_answers ans{$relate_id} ON ans{$relate_id}.brands_users_relation_id = relate.id AND ans{$relate_id}.questionnaires_questions_relation_id = {$relate_id} AND ans{$relate_id}.del_flg = 0 ";
                                $join = true;
                            }
                        }
                    }
                }
            }
        }
        return $join_sql;
    }

    /**
     * snsインタラクティブに関する絞り込み
     * @param $condition
     * @return $join_sql
     */
    protected function getSearchSocialAccountInteractiveJoin($condition) {
        $join_sql = ' LEFT OUTER JOIN users US ON US.id = relate.user_id AND US.del_flg = 0';
        $statuses = array();
        $tw_post_reply_count = array();
        $tw_post_retweet_count = array();
        $replied_count_statuses = array();
        $retweeted_count_statuses = array();
        $fb_post_like_count = array();
        $fb_post_comment_count = array();
        $liked_count_statuses = array();
        $commented_count_statuses = array();
        foreach ($condition as $social_interactive_condition) {
            foreach ($social_interactive_condition as $key => $value) {
                if (preg_match('/^search_social_account_interactive\//', $key)) {
                    $split_key = explode('/', $key);
                    $social_app_id = $split_key[1];
                    $social_media_id = $split_key[2];
                    $status = $split_key[3];
                    if (isset($statuses[$social_app_id][$social_media_id])) {
                        unset($statuses[$social_app_id][$social_media_id]);
                    } else {
                        $statuses[$social_app_id][$social_media_id] = $status;
                    }
                }
                
                if (preg_match('/^search_social_account_is_retweeted_count\//', $key)) {
                    $split_key = explode('/', $key);
                    $social_app_id = $split_key[1];
                    $social_media_id = $split_key[2];
                    $status = $split_key[3];
                    if(isset($retweeted_count_statuses[$social_app_id][$social_media_id])) {
                        unset($retweeted_count_statuses[$social_app_id][$social_media_id]);
                    } else {
                        $retweeted_count_statuses[$social_app_id][$social_media_id] = $status;
                    }
                }

                if (preg_match('/^search_social_account_is_liked_count\//', $key)) {
                    $split_key = explode('/', $key);
                    $social_app_id = $split_key[1];
                    $social_media_id = $split_key[2];
                    $status = $split_key[3];
                    if(isset($liked_count_statuses[$social_app_id][$social_media_id])) {
                        unset($liked_count_statuses[$social_app_id][$social_media_id]);
                    } else {
                        $liked_count_statuses[$social_app_id][$social_media_id] = $status;
                    }
                }
                 
                    

                if (preg_match('/^search_social_account_is_replied_count\//', $key)) {
                    $split_key = explode('/', $key);
                    $social_app_id = $split_key[1];
                    $social_media_id = $split_key[2];
                    $status = $split_key[3];
                    if(isset($replied_count_statuses[$social_app_id][$social_media_id])) {
                        unset($replied_count_statuses[$social_app_id][$social_media_id]);
                    } else {
                        $replied_count_statuses[$social_app_id][$social_media_id] = $status;
                    }
                }
                    

                if (preg_match('/^search_social_account_is_commented_count\//', $key)) {
                    $split_key = explode('/', $key);
                    $social_app_id = $split_key[1];
                    $social_media_id = $split_key[2];
                    $status = $split_key[3];
                    if(isset($commented_count_statuses[$social_app_id][$social_media_id])) {
                        unset($commented_count_statuses[$social_app_id][$social_media_id]);
                    } else {
                        $commented_count_statuses[$social_app_id][$social_media_id] = $status;
                    }
                }
                    

                if (preg_match('/^search_tw_tweet_retweet_count\//', $key) && $value != '') {
                    $split_key = explode('/', $key);
                    $social_app_id = $split_key[1];
                    $social_media_id = $split_key[2];
                    $range = $split_key[3];
                    $tw_post_retweet_count[$social_app_id][$social_media_id][$range] = $value;
                }

                if (preg_match('/^search_fb_posts_like_count\//', $key) && $value != '') {
                    $split_key = explode('/', $key);
                    $social_app_id = $split_key[1];
                    $social_media_id = $split_key[2];
                    $range = $split_key[3];
                    $fb_post_like_count[$social_app_id][$social_media_id][$range] = $value;
                }

                if (preg_match('/^search_tw_tweet_reply_count\//', $key) && $value != '') {
                    $split_key = explode('/', $key);
                    $social_app_id = $split_key[1];
                    $social_media_id = $split_key[2];
                    $range = $split_key[3];
                    $tw_post_reply_count[$social_app_id][$social_media_id][$range] = $value;
                }

                if (preg_match('/^search_fb_posts_comment_count\//', $key) && $value != '') {
                    $split_key = explode('/', $key);
                    $social_app_id = $split_key[1];
                    $social_media_id = $split_key[2];
                    $range = $split_key[3];
                    $fb_post_comment_count[$social_app_id][$social_media_id][$range] = $value;
                }

            }
        }
        foreach($statuses as $key => $value) {
            $social_app_id = $key;
            $social_media = $this->convertSocialAppIdToSocaialAccountAppId($social_app_id);
            $join_sql .= " LEFT OUTER JOIN social_accounts SA_{$social_app_id} ON SA_{$social_app_id}.user_id = relate.user_id AND SA_{$social_app_id}.social_media_id = {$social_media} AND SA_{$social_app_id}.del_flg = 0 ";
            if ($social_app_id == SocialApps::PROVIDER_FACEBOOK) {
                foreach($value as $social_media_id => $status) {
                    $alias = $social_app_id.'_'.$social_media_id;
                    $join_sql .= " LEFT OUTER JOIN brand_social_accounts BSA_{$alias} ON BSA_{$alias}.brand_id = relate.brand_id AND BSA_{$alias}.del_flg = 0
                               AND BSA_{$alias}.social_app_id = {$social_app_id} AND BSA_{$alias}.social_media_account_id = '{$social_media_id}'
                               LEFT OUTER JOIN social_likes sns_like_{$alias} ON sns_like_{$alias}.user_id = US.monipla_user_id AND sns_like_{$alias}.like_id = BSA_{$alias}.social_media_account_id";
                }
            }
            if ($social_app_id == SocialApps::PROVIDER_TWITTER) {
                foreach($value as $social_media_id => $status) {
                    $alias = $social_app_id.'_'.$social_media_id;
                    $join_sql .= " LEFT OUTER JOIN brand_social_accounts BSA_{$alias} ON BSA_{$alias}.brand_id = relate.brand_id AND BSA_{$alias}.del_flg = 0
                               AND BSA_{$alias}.social_app_id = {$social_app_id} AND BSA_{$alias}.social_media_account_id = '{$social_media_id}'
                               LEFT OUTER JOIN twitter_streams TS_{$alias} ON TS_{$alias}.brand_social_account_id = BSA_{$alias}.id
                               LEFT OUTER JOIN twitter_follows TL_{$alias} ON TL_{$alias}.stream_id = TS_{$alias}.id AND SA_{$social_app_id}.social_media_account_id = TL_{$alias}.follower_id";
                }
            }
        }
        //////////
        foreach($retweeted_count_statuses as $key => $value) {
            $social_app_id = $key;
            $social_media = $this->convertSocialAppIdToSocaialAccountAppId($social_app_id);
            if ($social_app_id == SocialApps::PROVIDER_TWITTER) {
                foreach($value as $social_media_id => $range) {
                    $alias = $social_app_id.'_'.$social_media_id;
                    $join_sql .= " LEFT OUTER JOIN brand_social_accounts BSA_TWEURT_{$alias} ON BSA_TWEURT_{$alias}.brand_id = relate.brand_id
                                AND BSA_TWEURT_{$alias}.del_flg = 0
                                AND BSA_TWEURT_{$alias}.social_app_id = {$social_app_id}
                                AND BSA_TWEURT_{$alias}.social_media_account_id = '{$social_media_id}'
                                AND BSA_TWEURT_{$alias}.hidden_flg = 0
                                LEFT OUTER JOIN sns_action_count_logs SACL_RETWEET_{$alias} ON SACL_RETWEET_{$alias}.social_media_account_id = BSA_TWEURT_{$alias}.social_media_account_id
                                AND SACL_RETWEET_{$alias}.social_app_id = ".DetailCrawlerUrl::CRAWLER_TYPE_TWITTER."
                                AND SACL_RETWEET_{$alias}.log_type = ".DetailCrawlerUrl::DATA_TYPE_RETWEET."
                                AND relate.user_id = SACL_RETWEET_{$alias}.user_id";

                }

            }

        }
        foreach($liked_count_statuses as $key => $value) {
            $social_app_id = $key;
            if ($social_app_id == SocialApps::PROVIDER_FACEBOOK) {
                foreach($value as $social_media_id => $range) {
                    $alias = $social_app_id.'_'.$social_media_id;
                    $join_sql .= " LEFT OUTER JOIN brand_social_accounts BSA_FBEUL_{$alias} ON BSA_FBEUL_{$alias}.brand_id = relate.brand_id
                                AND BSA_FBEUL_{$alias}.del_flg = 0
                                AND BSA_FBEUL_{$alias}.social_app_id = {$social_app_id}
                                AND BSA_FBEUL_{$alias}.social_media_account_id = '{$social_media_id}'
                                AND BSA_FBEUL_{$alias}.hidden_flg = 0
                                LEFT OUTER JOIN sns_action_count_logs SACL_LIKE_{$alias} ON SACL_LIKE_{$alias}.social_media_account_id = BSA_FBEUL_{$alias}.social_media_account_id
                                AND SACL_LIKE_{$alias}.social_app_id = ".DetailCrawlerUrl::CRAWLER_TYPE_FACEBOOK."
                                AND SACL_LIKE_{$alias}.log_type = ".DetailCrawlerUrl::DATA_TYPE_LIKE."
                                AND relate.user_id = SACL_LIKE_{$alias}.user_id";
                }

            }

        }
        if(!$retweeted_count_statuses[$social_app_id][$social_media_id]){
            foreach($tw_post_retweet_count as $key => $value) {
                $social_app_id = $key;
                $social_media = $this->convertSocialAppIdToSocaialAccountAppId($social_app_id);
                if ($social_app_id == SocialApps::PROVIDER_TWITTER) {
                    foreach($value as $social_media_id => $range) {
                        $alias = $social_app_id.'_'.$social_media_id;
                        $join_sql .= " LEFT OUTER JOIN brand_social_accounts BSA_TWEURT_{$alias} ON BSA_TWEURT_{$alias}.brand_id = relate.brand_id
                                AND BSA_TWEURT_{$alias}.del_flg = 0
                                AND BSA_TWEURT_{$alias}.social_app_id = {$social_app_id}
                                AND BSA_TWEURT_{$alias}.social_media_account_id = '{$social_media_id}'
                                AND BSA_TWEURT_{$alias}.hidden_flg = 0
                                LEFT OUTER JOIN sns_action_count_logs SACL_RETWEET_{$alias} ON SACL_RETWEET_{$alias}.social_media_account_id = BSA_TWEURT_{$alias}.social_media_account_id
                                AND SACL_RETWEET_{$alias}.social_app_id = ".DetailCrawlerUrl::CRAWLER_TYPE_TWITTER."
                                AND SACL_RETWEET_{$alias}.log_type = ".DetailCrawlerUrl::DATA_TYPE_RETWEET."
                                AND relate.user_id = SACL_RETWEET_{$alias}.user_id";
                    }

                }

            }
        }
        if(!$liked_count_statuses[$social_app_id][$social_media_id]){
            foreach($fb_post_like_count as $key => $value) {
                $social_app_id = $key;
                if ($social_app_id == SocialApps::PROVIDER_FACEBOOK) {
                    foreach($value as $social_media_id => $range) {
                        $alias = $social_app_id.'_'.$social_media_id;
                        $join_sql .= " LEFT OUTER JOIN brand_social_accounts BSA_FBEUL_{$alias} ON BSA_FBEUL_{$alias}.brand_id = relate.brand_id
                                    AND BSA_FBEUL_{$alias}.del_flg = 0
                                    AND BSA_FBEUL_{$alias}.social_app_id = {$social_app_id}
                                    AND BSA_FBEUL_{$alias}.social_media_account_id = '{$social_media_id}'
                                    AND BSA_FBEUL_{$alias}.hidden_flg = 0
                                    LEFT OUTER JOIN sns_action_count_logs SACL_LIKE_{$alias} ON SACL_LIKE_{$alias}.social_media_account_id = BSA_FBEUL_{$alias}.social_media_account_id
                                    AND SACL_LIKE_{$alias}.social_app_id = ".DetailCrawlerUrl::CRAWLER_TYPE_FACEBOOK."
                                    AND SACL_LIKE_{$alias}.log_type = ".DetailCrawlerUrl::DATA_TYPE_LIKE."
                                    AND relate.user_id = SACL_LIKE_{$alias}.user_id";
                    }

                }

            }
        }

        foreach($replied_count_statuses as $key => $value) {
                $social_app_id = $key;
                $social_media = $this->convertSocialAppIdToSocaialAccountAppId($social_app_id);
                if ($social_app_id == SocialApps::PROVIDER_TWITTER) {
                    foreach($value as $social_media_id => $range) {
                        $alias = $social_app_id.'_'.$social_media_id;
                        $join_sql .= " LEFT OUTER JOIN brand_social_accounts BSA_TWEURP_{$alias} ON BSA_TWEURP_{$alias}.brand_id = relate.brand_id
                                    AND BSA_TWEURP_{$alias}.del_flg = 0
                                    AND BSA_TWEURP_{$alias}.social_app_id = {$social_app_id}
                                    AND BSA_TWEURP_{$alias}.social_media_account_id = '{$social_media_id}'
                                    AND BSA_TWEURP_{$alias}.hidden_flg = 0
                                    LEFT OUTER JOIN sns_action_count_logs SACL_REPLY_{$alias} ON SACL_REPLY_{$alias}.social_media_account_id = BSA_TWEURP_{$alias}.social_media_account_id
                                    AND SACL_REPLY_{$alias}.social_app_id = ".DetailCrawlerUrl::CRAWLER_TYPE_TWITTER."
                                    AND SACL_REPLY_{$alias}.log_type = ".DetailCrawlerUrl::DATA_TYPE_REPLY."
                                    AND relate.user_id = SACL_REPLY_{$alias}.user_id";
                        
                    }
                }

        }

        foreach($commented_count_statuses as $key => $value) {
            $social_app_id = $key;
            if ($social_app_id == SocialApps::PROVIDER_FACEBOOK) {
                foreach($value as $social_media_id => $range) {
                    $alias = $social_app_id.'_'.$social_media_id;
                    $join_sql .= " LEFT OUTER JOIN brand_social_accounts BSA_FBEUC_{$alias} ON BSA_FBEUC_{$alias}.brand_id = relate.brand_id
                                AND BSA_FBEUC_{$alias}.del_flg = 0
                                AND BSA_FBEUC_{$alias}.social_app_id = {$social_app_id}
                                AND BSA_FBEUC_{$alias}.social_media_account_id = '{$social_media_id}'
                                AND BSA_FBEUC_{$alias}.hidden_flg = 0
                                LEFT OUTER JOIN sns_action_count_logs SACL_COMMENT_{$alias} ON SACL_COMMENT_{$alias}.social_media_account_id = BSA_FBEUC_{$alias}.social_media_account_id
                                AND SACL_COMMENT_{$alias}.social_app_id = ".DetailCrawlerUrl::CRAWLER_TYPE_FACEBOOK."
                                AND SACL_COMMENT_{$alias}.log_type = ".DetailCrawlerUrl::DATA_TYPE_COMMENT."
                                AND relate.user_id = SACL_COMMENT_{$alias}.user_id";

                }

            }
        }

        if(!$replied_count_statuses[$social_app_id][$social_media_id]){
            foreach($tw_post_reply_count as $key => $value) {
                $social_app_id = $key;
                $social_media = $this->convertSocialAppIdToSocaialAccountAppId($social_app_id);
                if ($social_app_id == SocialApps::PROVIDER_TWITTER) {
                    foreach($value as $social_media_id => $range) {
                        $alias = $social_app_id.'_'.$social_media_id;
                        $join_sql .= " LEFT OUTER JOIN brand_social_accounts BSA_TWEURP_{$alias} ON BSA_TWEURP_{$alias}.brand_id = relate.brand_id
                                    AND BSA_TWEURP_{$alias}.del_flg = 0
                                    AND BSA_TWEURP_{$alias}.social_app_id = {$social_app_id}
                                    AND BSA_TWEURP_{$alias}.social_media_account_id = '{$social_media_id}'
                                    AND BSA_TWEURP_{$alias}.hidden_flg = 0
                                    LEFT OUTER JOIN sns_action_count_logs SACL_REPLY_{$alias} ON SACL_REPLY_{$alias}.social_media_account_id = BSA_TWEURP_{$alias}.social_media_account_id
                                    AND SACL_REPLY_{$alias}.social_app_id = ".DetailCrawlerUrl::CRAWLER_TYPE_TWITTER."
                                    AND SACL_REPLY_{$alias}.log_type = ".DetailCrawlerUrl::DATA_TYPE_REPLY."
                                    AND relate.user_id = SACL_REPLY_{$alias}.user_id";
                    }

                }

            }
        }

        if(!$commented_count_statuses[$social_app_id][$social_media_id]){
            foreach($fb_post_comment_count as $key => $value) {
                $social_app_id = $key;
                $social_media = $this->convertSocialAppIdToSocaialAccountAppId($social_app_id);
                if ($social_app_id == SocialApps::PROVIDER_FACEBOOK) {
                    foreach($value as $social_media_id => $range) {
                        $alias = $social_app_id.'_'.$social_media_id;
                        $join_sql .= " LEFT OUTER JOIN brand_social_accounts BSA_FBEUC_{$alias} ON BSA_FBEUC_{$alias}.brand_id = relate.brand_id
                                    AND BSA_FBEUC_{$alias}.del_flg = 0
                                    AND BSA_FBEUC_{$alias}.social_app_id = {$social_app_id}
                                    AND BSA_FBEUC_{$alias}.social_media_account_id = '{$social_media_id}'
                                    AND BSA_FBEUC_{$alias}.hidden_flg = 0
                                    LEFT OUTER JOIN sns_action_count_logs SACL_COMMENT_{$alias} ON SACL_COMMENT_{$alias}.social_media_account_id = BSA_FBEUC_{$alias}.social_media_account_id
                                    AND SACL_COMMENT_{$alias}.social_app_id = ".DetailCrawlerUrl::CRAWLER_TYPE_FACEBOOK."
                                    AND SACL_COMMENT_{$alias}.log_type = ".DetailCrawlerUrl::DATA_TYPE_COMMENT."
                                    AND relate.user_id = SACL_COMMENT_{$alias}.user_id";
                    }

                }

            }
        }
        return $join_sql;
    }

    /**
     * join table: segment_provisions_users_relations
     */
    protected function  getSearchSegmentConditionJoin() {
        return  " LEFT OUTER JOIN segment_provisions_users_relations sp_user_relate ON sp_user_relate.brands_users_relation_id = relate.id AND sp_user_relate.del_flg = 0 ";
    }

    /**
     * 会員番号に関する絞り込み
     * @param $condition
     * @return $where_sql
     */
    protected function getSearchMemberNoWhereClause($condition) {
        $member_no_from = $this->escape($condition['search_profile_member_no_from']);
        // カンマが含まれているか判定(数値かどうかの判定はvalidateで行うので不要)
        if(preg_match("/,/",$member_no_from)) {
            $where_sql = " AND (relate.no IN ({$member_no_from})) ";
        } else {
            $where_sql = " AND (relate.no = {$member_no_from}) ";
        }
        return $where_sql;
    }

    /**
     * 登録期間に関する絞り込み
     * @param $condition
     * @return $where_sql
     */
    protected function getSearchRegisterPeriodWhereClause($condition) {
        if(!$condition['search_profile_register_period_to']) {
            $where_sql = " AND (relate.created_at >= '{$this->getFromDateFormat($condition['search_profile_register_period_from'])}') ";
        } elseif(!$condition['search_profile_register_period_from']) {
            $where_sql = " AND (relate.created_at <= '{$this->getToDateFormat($condition['search_profile_register_period_to'])}') ";
        } else {
            $where_sql = " AND (relate.created_at BETWEEN '{$this->getFromDateFormat($condition['search_profile_register_period_from'])}' AND '{$this->getToDateFormat($condition['search_profile_register_period_to'])}') ";
        }
        return $where_sql;
    }

    /**
     * 連携済SNSに関する絞り込み
     * @param $condition
     * @return $where_sql
     */
    protected function getSearchSocialAccountWhereClause($condition) {
        $where_sql = "";
        foreach ($condition as $media_type => $value) {
            // 連携・未連携の絞り込み
            $where_sql .= " AND ( ";
            if($value['search_social_account/'.$media_type.'/'.self::LINK_SNS]) {
                $where_sql .= " sa{$media_type}.id IS NOT NULL ";
            }
            if($value['search_social_account/'.$media_type.'/'.self::NOT_LINK_SNS]) {
                if($value['search_social_account/'.$media_type.'/'.self::LINK_SNS]) {
                    $where_sql .= " OR sa{$media_type}.id IS NULL ";
                } else {
                    $where_sql .= " sa{$media_type}.id IS NULL ";
                }
            }
            $where_sql .= " ) ";

            // 友達数の絞り込み
            $from_count = isset($value['search_friend_count_from/'.$media_type]) ? $value['search_friend_count_from/'.$media_type] : "";
            $to_count = isset($value['search_friend_count_to/'.$media_type]) ? $value['search_friend_count_to/'.$media_type] : "";
            if($from_count === '' && $to_count === '') {
                continue;
            } elseif($from_count === '' && $to_count !== '') {
                $where_sql .= " AND ( sa{$media_type}.friend_count <= {$this->escape($to_count)} OR sa{$media_type}.friend_count IS NULL ) ";
            } elseif($from_count !== '' && $to_count === '') {
                $where_sql .= " AND ( sa{$media_type}.friend_count >= {$this->escape($from_count)} ";
                if($from_count == 0) {
                    $where_sql .= " OR sa{$media_type}.friend_count IS NULL ";
                }
                $where_sql .= " ) ";
            } elseif($from_count !== '' && $to_count !== '') {
                $where_sql .= " AND ( sa{$media_type}.friend_count BETWEEN {$this->escape($from_count)} AND {$this->escape($to_count)} ";
                if($from_count == 0) {
                    $where_sql .= " OR sa{$media_type}.friend_count IS NULL ";
                }
                $where_sql .= " ) ";
            }
        }
        return $where_sql;
    }

    /**
     * 連携済SNSの合計に関する絞り込み
     * @param $condition
     * @return $where_sql
     */
    protected function getSearchSocialAccountSumWhereClause($condition) {
        $where_sql = "";
        $friend_sum_from = isset($condition['search_friend_count_sum_from']) ? $condition['search_friend_count_sum_from'] : "";
        $friend_sum_to = isset($condition['search_friend_count_sum_to']) ? $condition['search_friend_count_sum_to'] : "";
        $link_sum_from = isset($condition['search_link_sns_count_from']) ? $condition['search_link_sns_count_from'] : "";
        $link_sum_to = isset($condition['search_link_sns_count_to']) ? $condition['search_link_sns_count_to'] : "";

        if($friend_sum_from !== '' && $friend_sum_to !== '') {
            $where_sql .= " AND (ifnull(sumtmp.sum_sa,0) BETWEEN {$friend_sum_from} AND {$friend_sum_to})";
        } elseif($friend_sum_from !== '' && $friend_sum_to === '') {
            $where_sql .= " AND (ifnull(sumtmp.sum_sa,0) >= {$friend_sum_from})";
        } elseif($friend_sum_from === '' && $friend_sum_to !== '') {
            $where_sql .= " AND (ifnull(sumtmp.sum_sa,0) <= {$friend_sum_to})";
        }

        if($link_sum_from !== '' && $link_sum_to !== '') {
            $where_sql .= " AND (ifnull(sumtmp.cnt_sa,0) BETWEEN {$link_sum_from} AND {$link_sum_to})";
        } elseif($link_sum_from !== '' && $link_sum_to === '') {
            $where_sql .= " AND (ifnull(sumtmp.cnt_sa,0) >= {$link_sum_from})";
        } elseif($link_sum_from === '' && $link_sum_to !== '') {
            $where_sql .= " AND (ifnull(sumtmp.cnt_sa,0) <= {$link_sum_to})";
        }
        return $where_sql;
    }

    /**
     * 最終ログインに関する絞り込み
     * @param $condition
     * @return $where_sql
     */
    protected function getSearchLastLoginWhereClause($condition) {
        if(!$condition['search_profile_last_login_to']) {
            $where_sql = " AND (relate.last_login_date >= '{$this->getFromDateTimeFormat($condition['search_profile_last_login_from'])}') ";
        } elseif(!$condition['search_profile_last_login_from']) {
            $where_sql = " AND (relate.last_login_date <= '{$this->getToDateTimeFormat($condition['search_profile_last_login_to'])}') ";
        } else {
            $where_sql = " AND (relate.last_login_date BETWEEN '{$this->getFromDateTimeFormat($condition['search_profile_last_login_from'])}' AND '{$this->getToDateTimeFormat($condition['search_profile_last_login_to'])}') ";
        }
        return $where_sql;
    }

    /**
     * ログイン回数に関する絞り込み
     * @param $condition
     * @return $where_sql
     */
    protected function getSearchLoginCountWhereClause($condition) {
        // 範囲選択のチェックがついている場合
        if(is_null($condition['search_profile_login_count_to']) || $condition['search_profile_login_count_to'] === '') { //toはpostされないこともあるのでnullのチェックも必要
            $where_sql = " AND (relate.login_count >= {$this->escape($condition['search_profile_login_count_from'])}) ";
        } elseif($condition['search_profile_login_count_from'] === '') { //fromはpostされないことはない
            $where_sql = " AND (relate.login_count <= {$this->escape($condition['search_profile_login_count_to'])}) ";
        } else {
            $where_sql = " AND (relate.login_count BETWEEN {$this->escape($condition['search_profile_login_count_from'])} AND {$this->escape($condition['search_profile_login_count_to'])}) ";
        }
        return $where_sql;
    }

    /**
     * @param $conditions
     * @return string
     */
    protected function getSearchDeliveryTimeWhereClause($conditions) {
        $where_sql = '';

        foreach ($conditions as $action_id => $value) {

            if (count($value) == 1 && array_values($value)[0] == self::DID_NOT_SEND) {
                $where_sql .= ' AND target'.$action_id.'.user_id IS NULL ';
                continue;
            }
            $where_sql .= ' AND ( ';

            $id_arr = '';
            $not_send = false;
            foreach ($value as $input_name => $input_value) {
                if ($input_value == self::DID_NOT_SEND) {
                    $not_send = true;
                    continue;
                }
                $id_arr .= '"' .$input_value . '",';
            }
            $id_arr = trim ($id_arr, ',');

            $where_sql .= 'target'.$action_id.'.cp_message_delivery_reservation_id IN ('.$id_arr.') ';
            if ($not_send) {
                $where_sql .= ' OR target'.$action_id.'.user_id IS NULL ';
            }
            $where_sql .= ' ) ';
        }

        return $where_sql;
    }

    /**
     * 評価に関する絞り込み
     * @param $condition
     * @return string
     */
    protected function getSearchRateWhereClause($condition) {
        $first_flg = true;
        $where_sql = " AND ( ";
        foreach($condition as $key=>$value) {
            if(preg_match('/^search_rate\//', $key)) {
                $status = explode('/', $key)[1];
                if($first_flg) {
                    $where_sql .= " relate.rate = {$status} ";
                    $first_flg = false;
                } else {
                    $where_sql .= " OR relate.rate = {$status} ";
                }
            }
        }
        $where_sql .= " ) ";
        return $where_sql;
    }

    /**
     * アンケート参加状況に関する絞り込み
     * @param $condition
     * @return $where_sql
     */
    protected function getSearchQuestionnaireStatusWhereClause($condition) {
        $first_flg = true;
        $where_sql = " AND ( ";
        foreach($condition as $key=>$value) {
            if(preg_match('/^search_questionnaire_status\//', $key)) {
                $status = explode('/', $key)[1];
                if($first_flg) {
                    $where_sql .= " relate.personal_info_flg = {$status} ";
                    $first_flg = false;
                } else {
                    $where_sql .= " OR relate.personal_info_flg = {$status} ";
                }
            }
        }
        $where_sql .= " ) ";
        return $where_sql;
    }

    /**
     * 性別に関する絞り込み
     * @param $condition
     * @return $where_sql
     */
    protected function getSearchSexWhereClause($condition) {
        $first_flg = true;
        $where_sql = " AND ( ";
        foreach ($condition as $key => $value) {
            if (preg_match('/^search_profile_sex\//', $key)) {
                $sex = explode('/', $key)[1];
                if($first_flg) {
                    if($sex == UserAttributeService::ATTRIBUTE_SEX_UNKWOWN) {
                        $where_sql .= " ( searchinfo.sex IS NULL OR searchinfo.sex = '' ) ";
                    } else {
                        $where_sql .= " searchinfo.sex = '{$this->escape($sex)}' ";
                    }
                } else {
                    if($sex == UserAttributeService::ATTRIBUTE_SEX_UNKWOWN) {
                        $where_sql .= " OR ( searchinfo.sex IS NULL OR searchinfo.sex = '' ) ";
                    } else {
                        $where_sql .= " OR searchinfo.sex = '{$this->escape($sex)}' ";
                    }
                }
            }
            $first_flg = false;
        }
        $where_sql .= " ) ";
        return $where_sql;
    }

    /**
     * 住所に関する絞り込み
     * @param $condition
     * @return $where_sql
     */
    protected function getSearchAddressWhereClause($condition) {
        $first_flg = true;
        $where_sql = " AND ( ";
        foreach($condition as $key => $value) {
            if (preg_match('/^search_profile_address\//', $key)) {
                $prefecture_id = explode('/', $key)[1];
                if ($first_flg) {
                    if ($prefecture_id == self::NOT_SET_PREFECTURE) {
                        $where_sql .= " ( addr.pref_id IS NULL OR addr.pref_id = '' OR addr.pref_id = 0 ) ";
                    } else {
                        $where_sql .= " addr.pref_id = {$this->escape($prefecture_id)} ";
                    }
                } else {
                    if ($prefecture_id == self::NOT_SET_PREFECTURE) {
                        $where_sql .= " OR ( addr.pref_id IS NULL OR addr.pref_id = '' OR addr.pref_id = 0 ) ";
                    } else {
                        $where_sql .= " OR addr.pref_id = {$this->escape($prefecture_id)} ";
                    }
                }
            }
            $first_flg = false;
        }
        $where_sql .= " ) ";
        return $where_sql;
    }

    /**
     * 年齢に関する絞り込み
     * @param $condition
     * @return $where_sql
     */
    protected function getSearchAgeWhereClause($condition) {
        $where_sql = " AND ( ";
        $exist_flg = false;
        if($condition['search_profile_age_to'] === '' && $condition['search_profile_age_from'] !== '') {
            $birthday_from = $this->getBirthdayByAge($condition['search_profile_age_from']);
            $where_sql .= " searchinfo.birthday BETWEEN '1900-00-00' AND '{$birthday_from}' ";
            $exist_flg = true;
        } elseif($condition['search_profile_age_from'] === '' && $condition['search_profile_age_to'] !== '') {
            $birthday_to = $this->getBirthdayByAge($condition['search_profile_age_to'] + 1);
            $where_sql .= " searchinfo.birthday >= '{$birthday_to}' ";
            $exist_flg = true;
        } elseif($condition['search_profile_age_to'] !== '' && $condition['search_profile_age_from'] !== '') {
            $birthday_to = $this->getBirthdayByAge($condition['search_profile_age_to'] + 1);
            $birthday_from = $this->getBirthdayByAge($condition['search_profile_age_from']);
            $where_sql .= " searchinfo.birthday BETWEEN '{$birthday_to}' AND '{$birthday_from}' ";
            $exist_flg = true;
        }
        if($condition['search_profile_age_not_set']) {
            if($exist_flg) {
                $where_sql .= " OR ( searchinfo.birthday IS NULL OR searchinfo.birthday = '0000-00-00' ) ";
            } else {
                $where_sql .= " searchinfo.birthday IS NULL OR searchinfo.birthday = '0000-00-00' ";
            }
        }
        $where_sql .= " ) ";
        return $where_sql;
    }

    /**
     * コンバージョンに関する絞り込み
     * @param $condition
     * @return $where_sql
     */
    protected function getSearchConversionWhereClause($condition) {
        $where_sql = '';
        foreach ($condition as $conversion_condition) {
            $conversion_id = '';
            foreach ($conversion_condition as $key => $value) {
                if (preg_match('/^search_profile_conversion_/', $key)) {
                    if($conversion_id != explode('/', $key)[1]) {
                        $conversion_id = $this->escape(explode('/', $key)[1]);
                        $search_conversion_from = $conversion_condition['search_profile_conversion_from/' . $conversion_id];
                        $search_conversion_to = $conversion_condition['search_profile_conversion_to/' . $conversion_id];

                        if ($search_conversion_from == 0 AND $search_conversion_to === '') { // 検索条件が「0〜」の場合は絞り込みをする必要なし(全員対象になる)
                            break;
                        } elseif ($search_conversion_from == 0 AND $search_conversion_to == 0) { // 検索条件が「0〜0」の場合はコンバージョンテーブルに存在しない人だけを対象にすれば良い
                            $where_sql .= " AND cv{$conversion_id}.user_id IS NULL ";
                        } else { // 検索条件が「1〜100」等の場合
                            if ($search_conversion_from == 0) {
                                break;
                            } else {
                                $where_sql .= " AND cvtmp{$conversion_id}.user_id IS NOT NULL ";
                            }
                        }
                    }
                }
            }
        }
        return $where_sql;
    }

    /**
     * キャンペーン参加回数・キャンペーン当選数・メッセージ受信数・メッセージ開封数に関する絞り込み
     * @param $condition
     * @return $where_sql
     */
    protected function getSearchCountColumnCountWhereClause($condition, $search_type) {
        $count_item = self::$search_count_item[$search_type];
        $count_column = self::$search_count_column[$search_type];

        if($condition[$count_item.'_to'] === '' && $condition[$count_item.'_from'] !== '') {
            $where_sql = " AND (brand_search.{$count_column} >= {$this->escape($condition[$count_item.'_from'])} ";
            if($condition[$count_item.'_from'] == 0) {
                $where_sql .= " OR brand_search.{$count_column} IS NULL ";
            }
            $where_sql .= ") ";
        } elseif($condition[$count_item.'_to'] !== '' && $condition[$count_item.'_from'] === '') {
            $where_sql = " AND (brand_search.{$count_column} <= {$this->escape($condition[$count_item.'_to'])} ";
            if($condition[$count_item.'_to'] == 0) {
                $where_sql .= " OR brand_search.{$count_column} IS NULL ";
            }
            $where_sql .= ") ";
        } elseif($condition[$count_item.'_to'] !== '' && $condition[$count_item.'_from'] !== '') {
            $where_sql = " AND (brand_search.{$count_column} BETWEEN {$this->escape($condition[$count_item.'_from'])} AND {$this->escape($condition[$count_item.'_to'])} ";
            if($condition[$count_item.'_from'] == 0) {
                $where_sql .= " OR brand_search.{$count_column} IS NULL ";
            }
            $where_sql .= ") ";
        } else{
            $where_sql = "";
        }
        return $where_sql;
    }

    /**
     * メッセージ開封率に関する絞り込み
     * @param $condition
     * @return $where_sql
     */
    protected function getSearchMessageReadRatioWhereClause($condition) {
        if($condition['search_message_ratio_to'] === '' && $condition['search_message_ratio_from'] !== '') {
            if($condition['search_message_ratio_from'] == 0) {
                $where_sql = "";
            } else {
                $where_sql = " AND (ifnull(brand_search.message_read_count/brand_search.message_delivered_count,0)*100 >= {$this->escape($condition['search_message_ratio_from'])})";
            }
        } elseif($condition['search_message_ratio_to'] !== '' && $condition['search_message_ratio_from'] === '') {
            if($condition['search_message_ratio_to'] == 0) {
                $where_sql = "";
            } else {
                $where_sql = " AND (ifnull(brand_search.message_read_count/brand_search.message_delivered_count,0)*100 <= {$this->escape($condition['search_message_ratio_to'])} OR brand_search.message_read_count IS NULL)";
            }
        } else {
            $where_sql = " AND (ifnull(brand_search.message_read_count/brand_search.message_delivered_count,0)*100
                    BETWEEN {$this->escape($condition['search_message_ratio_from'])} AND {$this->escape($condition['search_message_ratio_to'])} ";
            if ($condition['search_message_ratio_from'] == 0) {
                $where_sql .= " OR brand_search.message_read_count IS NULL ";
            }
            $where_sql .= ")";
        }
        return $where_sql;
    }

    /**
     * 参加時アンケートに関する絞り込み
     * @param $condition
     * @return $where_sql
     */
    protected function getSearchProfileQuestionnaireWhereClause($condition) {
        $where_sql = "";
        foreach ($condition as $questionnaire_condition) {
            $first_flg = true;
            $where_sql .= " AND ( ";
            $switch_value = '';
            $have_free_ans_condition_flg = false;
            foreach ($questionnaire_condition as $key => $value) {
                if (preg_match('/^search_profile_questionnaire\//', $key)) {
                    $split_key = explode('/', $key);
                    $relate_id = $split_key[1];
                    $user_answer = $split_key[2];
                    if ($questionnaire_condition['questionnaire_type/'.$relate_id] == QuestionTypeService::FREE_ANSWER_TYPE) {
                        $search_free_ans_sql = '';
                        if (preg_match('/^search_profile_questionnaire\/'.$relate_id.'\/'.CpCreateSqlService::NOT_ANSWER_QUESTIONNAIRE.'/', $key)) {
                            $search_free_ans_sql .= ' free_ans'.$relate_id.'.answer_text IS NULL OR free_ans'.$relate_id.'.answer_text = "" ';

                        } else {
                            $search_free_ans_sql .= ' free_ans'.$relate_id.'.answer_text IS NOT NULL AND free_ans'.$relate_id.'.answer_text != "" ';
                        }
                        if($first_flg) {
                            $where_sql .= $search_free_ans_sql;
                            $first_flg = false;
                        } else {
                            if ($have_free_ans_condition_flg) {
                                $where_sql .= " OR ( ".$search_free_ans_sql.") ";
                            } else {
                                $where_sql .= " AND ( ".$search_free_ans_sql;
                            }
                        }
                        $have_free_ans_condition_flg = true;
                        continue;
                    }
                    if (!$switch_value) $switch_value = $questionnaire_condition['switch_type/'.self::SEARCH_PROFILE_QUESTIONNAIRE.'/'.$relate_id] ? $questionnaire_condition['switch_type/'.self::SEARCH_PROFILE_QUESTIONNAIRE.'/'.$relate_id] : self::QUERY_TYPE_OR;
                    if ($switch_value == self::QUERY_TYPE_AND) {
                        if($user_answer == self::NOT_ANSWER_QUESTIONNAIRE) {
                            if($first_flg) {
                                $where_sql .= " ( ans{$relate_id}_{$user_answer}.id = '' OR ans{$relate_id}_{$user_answer}.id IS NULL ) ";
                            } else {
                                $where_sql .= " AND ( ans{$relate_id}_{$user_answer}.id = '' OR ans{$relate_id}_{$user_answer}.id IS NULL ) ";
                            }
                        } else {
                            if($first_flg) {
                                $where_sql .= " ans{$relate_id}_{$user_answer}.choice_id = {$user_answer} ";
                            } else {
                                $where_sql .= " AND ans{$relate_id}_{$user_answer}.choice_id = {$user_answer} ";
                            }
                        }
                    } else {
                        if($user_answer == self::NOT_ANSWER_QUESTIONNAIRE) {
                            if ($first_flg) {
                                $where_sql .= " ( ans{$relate_id}.id = '' OR ans{$relate_id}.id IS NULL ) ";
                            } else {
                                $where_sql .= " OR ( ans{$relate_id}.id = '' OR ans{$relate_id}.id IS NULL ) ";
                            }
                        } else {
                            if ($first_flg) {
                                $where_sql .= " ans{$relate_id}.choice_id = {$user_answer} ";
                            } else {
                                $where_sql .= " OR ans{$relate_id}.choice_id = {$user_answer} ";
                            }
                        }
                    }
                    $first_flg = false;
                }
            }
            $where_sql .= " ) ";
        }
        return $where_sql;
    }

    /**
     * TODO ハードコーディング: カンコーブランドのプロフィールアンケートの回答結果の絞り込み
     * @param $conditions
     * @return string
     */
    protected function getSearchChildBirthPeriodWhereClause ($conditions) {
        $where_sql = "";

        foreach ($conditions as $child_birth_conditions) {
            $where_sql .= " AND ( ";
            $first_flg = true;

            foreach ($child_birth_conditions as $key => $child_birth_condition){
                if (preg_match('/^search_child_birth_period\//', $key)) {
                    $split_key = explode('/', $key);
                    $relate_id = $split_key[1];

                    foreach ($child_birth_condition as $value) {
                        if($value['created_at_from'] && $value['created_at_to']) {
                            if($first_flg) {
                                $where_sql .= " ans_choice{$relate_id}.choice_id IN (". implode(',', $value['choice_ids']).") AND ans_choice{$relate_id}.created_at BETWEEN '{$value['created_at_from']}' AND '{$value['created_at_to']}'";
                            } else {
                                $where_sql .= " OR ans_choice{$relate_id}.choice_id IN (". implode(',', $value['choice_ids']).") AND ans_choice{$relate_id}.created_at BETWEEN '{$value['created_at_from']}' AND '{$value['created_at_to']}'";
                            }
                        } elseif($value['created_at_from']) {
                            if($first_flg) {
                                $where_sql .= " ans_choice{$relate_id}.choice_id IN (". implode(',', $value['choice_ids']).") AND ans_choice{$relate_id}.created_at > '{$value['created_at_from']}'";
                            } else {
                                $where_sql .= " OR ans_choice{$relate_id}.choice_id IN (". implode(',', $value['choice_ids']).") AND ans_choice{$relate_id}.created_at > '{$value['created_at_from']}'";
                            }
                        } elseif($value['created_at_to']){
                            if($first_flg) {
                                $where_sql .= " ans_choice{$relate_id}.choice_id IN (". implode(',', $value['choice_ids']).") AND ans_choice{$relate_id}.created_at < '{$value['created_at_to']}'";
                            } else {
                                $where_sql .= " OR ans_choice{$relate_id}.choice_id IN (". implode(',', $value['choice_ids']).") AND ans_choice{$relate_id}.created_at < '{$value['created_at_to']}'";
                            }
                        } else {
                            if($first_flg) {
                                $where_sql .= " ans_choice{$relate_id}.choice_id IN (". implode(',', $value['choice_ids']).")";
                            } else {
                                $where_sql .= " OR ans_choice{$relate_id}.choice_id IN (". implode(',', $value['choice_ids']).")";
                            }
                        }
                        $first_flg = false;
                    }
                }
            }
            $where_sql .= " ) ";
        }

        return $where_sql;
    }

    /**
     * 外部インポートデータに関する絞り込み
     * @param $condition
     * @return $where_sql
     */
    protected function getSearchImportValueWhereClause($condition) {
        $where_sql = "";
        foreach ($condition as $questionnaire_condition) {
            $first_flg = true;
            $where_sql .= " AND ( ";
            foreach ($questionnaire_condition as $key => $value) {
                if (preg_match('/^search_import_value\//', $key)) {
                    $split_key = explode('/', $key);
                    $definition_id = $split_key[1];
                    $value = $split_key[2];
                    if($value == self::NOT_SET_VALUE) {
                        if ($first_flg) {
                            $where_sql .= " ( bua{$definition_id}.id = '' OR bua{$definition_id}.id IS NULL ) ";
                        } else {
                            $where_sql .= " OR ( bua{$definition_id}.id = '' OR bua{$definition_id}.id IS NULL ) ";
                        }
                    } else {
                        if ($first_flg) {
                            $where_sql .= " bua{$definition_id}.value = {$value} ";
                        } else {
                            $where_sql .= " OR bua{$definition_id}.value = {$value} ";
                        }
                    }
                }
                $first_flg = false;
            }
            $where_sql .= " ) ";
        }
        return $where_sql;
    }

    /**
     * 参加状況に関する絞り込み
     * @param $condition
     * @return $where_sql
     */
    protected function getSearchParticipateConditionWhereClause($condition) {
        $where_sql = "";
        foreach($condition as $participate_condition) {
            $where_sql .= " AND ( ";
            $first_flg = true;
            $conjunction = " OR ";
            foreach ($participate_condition as $key => $value) {
                $action_id = explode('/', $key)[1];
                $participate_status = explode('/', $key)[2];
                if($first_flg) {
                    // スピードくじのときだけ、ANDとORの選択がある
                    $switch_value = $participate_condition['switch_type/'.self::SEARCH_PARTICIPATE_CONDITION.'/'.$action_id] ? $participate_condition['switch_type/'.self::SEARCH_PARTICIPATE_CONDITION.'/'.$action_id] : '';
                    if($switch_value) {
                        $conjunction = $this->getConjunction($switch_value);
                    }
                }
                if($switch_value == self::QUERY_TYPE_AND) {
                    $alias = $this->escape($action_id).'_'.$this->escape($participate_status);
                } else {
                    $alias = $this->escape($action_id);
                }
                if ($participate_status == self::PARTICIPATE_COMPLETE) {
                    $status = CpUserActionStatus::JOIN;

                    $where_sql .= $first_flg ? "" : " {$conjunction}";
                    // switch_valueがあるときスピードくじと判断
                    if ($switch_value) {
                        $where_sql .= " instant{$alias}.prize_status = " . InstantWinUsers::PRIZE_STATUS_WIN . " ";
                    } else {
                        $where_sql .= " state{$alias}.status = {$status} ";
                    }
                }
                if ($participate_status == self::PARTICIPATE_REJECTED) {
                    $status = CpUserActionStatus::CAN_NOT_JOIN;
                    if($first_flg) {
                        $where_sql .= " state{$alias}.status = {$status} ";
                    } else {
                        $where_sql .= " {$conjunction} state{$alias}.status = {$status} ";
                    }
                }
                if($participate_status == self::PARTICIPATE_READ) {
                    $status = CpUserActionStatus::NOT_JOIN;
                    $read_flg = CpUserActionMessage::STATUS_READ;
                    if($first_flg) {
                        $where_sql .= " ( state{$alias}.status = {$status} AND mes{$alias}.read_flg = {$read_flg} ) ";
                    } else {
                        $where_sql .= " {$conjunction} ( state{$alias}.status = {$status} AND mes{$alias}.read_flg = {$read_flg} ) ";
                    }
                }
                if($participate_status == self::PARTICIPATE_NOT_READ) {
                    $read_flg = CpUserActionMessage::STATUS_UNREAD;
                    if($first_flg) {
                        $where_sql .= " mes{$alias}.read_flg = {$read_flg} ";
                    } else {
                        $where_sql .= " {$conjunction} mes{$alias}.read_flg = {$read_flg} ";
                    }
                }
                if($participate_status == self::PARTICIPATE_NOT_SEND) {
                    if($first_flg) {
                        $where_sql .= " mes{$alias}.id IS NULL ";
                    } else {
                        $where_sql .= " {$conjunction} mes{$alias}.id IS NULL ";
                    }
                }
                if($participate_status == self::PARTICIPATE_COUNT_INSTANT_WIN) {
                    if(!$first_flg) {
                        $where_sql .= " {$conjunction} ";
                    }
                    if($participate_condition['search_count_instant_win_from/'.$action_id] === '' && $participate_condition['search_count_instant_win_to/'.$action_id] !== '') {
                        $escape_count_to = $this->escape($participate_condition['search_count_instant_win_to/'.$action_id]);
                        $where_sql .= " ( instant{$alias}.join_count <= {$escape_count_to} OR instant{$alias}.join_count IS NULL ) ";
                    } elseif($participate_condition['search_count_instant_win_from/'.$action_id] !== '' && $participate_condition['search_count_instant_win_to/'.$action_id] === '') {
                        $escape_count_from = $this->escape($participate_condition['search_count_instant_win_from/'.$action_id]);
                        $where_sql .= " instant{$alias}.join_count >= {$escape_count_from} ";
                    } elseif($participate_condition['search_count_instant_win_from/'.$action_id] !== '' && $participate_condition['search_count_instant_win_to/'.$action_id] !== '') {
                        $escape_count_from = $this->escape($participate_condition['search_count_instant_win_from/'.$action_id]);
                        $escape_count_to = $this->escape($participate_condition['search_count_instant_win_to/'.$action_id]);
                        $where_sql .= " instant{$alias}.join_count BETWEEN {$escape_count_from} AND {$escape_count_to} ";
                    }
                }

                if ($participate_status == self::PARTICIPATE_TARGET || $participate_status == self::PARTICIPATE_NOT_TARGET) {
                    $target_action_id = $this->getDeliveryTargetActionId($action_id);
                    if ($switch_value == self::QUERY_TYPE_AND) {
                        $target_alias = $this->escape($target_action_id) . '_' . $this->escape($participate_status);
                    } else {
                        $target_alias = $this->escape($target_action_id);
                    }
                }

                if ($participate_status == self::PARTICIPATE_TARGET) {
                    if ($first_flg) {
                        $where_sql .= " target{$target_alias}.id IS NOT NULL ";
                    } else {
                        $where_sql .= " {$conjunction} target{$target_alias}.id IS NOT NULL ";
                    }
                }

                if ($participate_status == self::PARTICIPATE_NOT_TARGET) {
                    if ($first_flg) {
                        $where_sql .= " target{$target_alias}.id IS NULL ";
                    } else {
                        $where_sql .= " {$conjunction} target{$target_alias}.id IS NULL ";
                    }
                }

                $first_flg = false;
            }
            $where_sql .= " ) ";
        }
        return $where_sql;
    }

    /**
     * 送信済み・送信対象に関する絞り込み
     * @param $condition
     * @return $where_sql
     */
    protected function getSearchQueryUserWhereClause($condition) {
        $query_user_condition = explode('/', $condition);
        if($query_user_condition[0] == self::QUERY_USER_TARGET) {
            $where_sql = " AND tar.id IS NOT NULL ";
        } elseif($query_user_condition[0] == self::QUERY_USER_SENT) {
            $where_sql = " AND mes.id IS NOT NULL ";
        }
        return $where_sql;
    }

    /**
     * アンケートに関する絞り込み
     * @param $condition
     * @return $where_sql
     */
    protected function getSearchQuestionnaireWhereClause($condition) {
        $where_sql = "";
        foreach ($condition as $questionnaire_condition) {
            $first_flg = true;
            $where_sql .= " AND ( ";
            $switch_value = '';
            $have_free_ans_condition_flg = false;
            foreach ($questionnaire_condition as $key => $value) {
                if (preg_match('/^search_questionnaire\//', $key)) {
                    $split_key = explode('/', $key);
                    $relate_id = $split_key[1];
                    $user_answer = $split_key[2];

                    if ($questionnaire_condition['questionnaire_type/'.$relate_id] == QuestionTypeService::FREE_ANSWER_TYPE) {
                        $search_free_ans_sql = '';
                        if (preg_match('/^search_questionnaire\/'.$relate_id.'\/'.CpCreateSqlService::NOT_ANSWER_QUESTIONNAIRE.'/', $key)) {
                            $search_free_ans_sql .= " q_free_ans{$relate_id}.answer_text IS NULL ";
                        } else {
                            $search_free_ans_sql .= " q_free_ans{$relate_id}.answer_text IS NOT NULL ";
                        }
                        if($first_flg) {
                            $where_sql .= $search_free_ans_sql;
                            $first_flg = false;
                        } else {
                            if ($have_free_ans_condition_flg) {
                                $where_sql .= " OR ( ".$search_free_ans_sql.") ";
                            } else {
                                $where_sql .= " AND ( ".$search_free_ans_sql;
                            }
                        }
                        $have_free_ans_condition_flg = true;
                        continue;
                    }

                    if (!$switch_value) $switch_value = $questionnaire_condition['switch_type/' . self::SEARCH_QUESTIONNAIRE . '/' . $relate_id] ? $questionnaire_condition['switch_type/' . self::SEARCH_QUESTIONNAIRE . '/' . $relate_id] : self::QUERY_TYPE_OR;
                    if ($switch_value == self::QUERY_TYPE_AND) {
                        if($user_answer == self::NOT_ANSWER_QUESTIONNAIRE) {
                            if($first_flg) {
                                $where_sql .= " ( ans{$relate_id}_{$user_answer}.id = '' OR ans{$relate_id}_{$user_answer}.id IS NULL ) ";
                            } else {
                                $where_sql .= " AND ( ans{$relate_id}_{$user_answer}.id = '' OR ans{$relate_id}_{$user_answer}.id IS NULL ) ";
                            }
                        } else {
                            if($first_flg) {
                                $where_sql .= " ans{$relate_id}_{$user_answer}.choice_id = {$user_answer} ";
                            } else {
                                $where_sql .= " AND ans{$relate_id}_{$user_answer}.choice_id = {$user_answer} ";
                            }
                        }
                    } else {
                        if($user_answer == self::NOT_ANSWER_QUESTIONNAIRE) {
                            if ($first_flg) {
                                $where_sql .= " ( ans{$relate_id}.id = '' OR ans{$relate_id}.id IS NULL ) ";
                            } else {
                                $where_sql .= " OR ( ans{$relate_id}.id = '' OR ans{$relate_id}.id IS NULL ) ";
                            }
                        } else {
                            if ($first_flg) {
                                $where_sql .= " ans{$relate_id}.choice_id = {$user_answer} ";
                            } else {
                                $where_sql .= " OR ans{$relate_id}.choice_id = {$user_answer} ";
                            }
                        }
                    }
                    $first_flg = false;
                }
            }
            $where_sql .= " ) ";
        }
        return $where_sql;
    }

    /**
     * ファン全員を返すか
     * @param $condition
     * @return $where_sql
     */
    protected function getJoinUserOnlyWhereClause($condition) {
        if($condition) {
            $where_sql = " AND cp_usr.id IS NOT NULL ";
        }
        return $where_sql;
    }

    /**
     * 写真投稿シェアSNS
     * @param $share_sns_condition
     * @return string
     */
    protected function getSearchPhotoShareSnsWhereClause($share_sns_condition) {
        $where_sql = '';

        foreach ($share_sns_condition as $action_id => $condition) {
            $where_sql .= " AND (";
            $escape_action_id = $this->escape($action_id);
            $name = 'search_photo_share_sns/' . $action_id . '/';
            $exist_same_action = FALSE;
            $switch_type = $condition['switch_type/' . self::SEARCH_PHOTO_SHARE_SNS. '/' .$action_id];
            if($switch_type == self::QUERY_TYPE_AND) {
                if($condition[$name . SocialAccount::SOCIAL_MEDIA_FACEBOOK]) {
                    $fb_alias = $this->escape($action_id) . '_' . SocialAccount::SOCIAL_MEDIA_FACEBOOK;
                    $where_sql .= " (user_share{$fb_alias}.execute_status = 1 AND user_share{$fb_alias}.social_media_type = ".SocialAccount::SOCIAL_MEDIA_FACEBOOK.") ";
                    $exist_same_action = TRUE;
                }
                if($condition[$name . SocialAccount::SOCIAL_MEDIA_TWITTER]) {
                    $tw_alias = $this->escape($action_id) . '_' . SocialAccount::SOCIAL_MEDIA_TWITTER;
                    if($exist_same_action) {
                        $where_sql .= " AND ";
                    }
                    $where_sql .= " (user_share{$tw_alias}.execute_status = 1 AND user_share{$tw_alias}.social_media_type = ".SocialAccount::SOCIAL_MEDIA_TWITTER.") ";
                    $exist_same_action = TRUE;
                }
                if($condition[$name. '-1']) {
                    $not_share_alias = $this->escape($action_id) .'_' . '99';//-1はテーブル別名に指定できない
                    if($exist_same_action) {
                        $where_sql .= " AND ";
                    }
                    $where_sql .= " (user_share{$not_share_alias}.execute_status = 0 OR user_share{$not_share_alias}.id IS NULL) ";
                }
            } else {
                if($condition[$name . SocialAccount::SOCIAL_MEDIA_FACEBOOK]) {
                    $where_sql .= " (user_share{$escape_action_id}.execute_status = 1 AND user_share{$escape_action_id}.social_media_type = ".SocialAccount::SOCIAL_MEDIA_FACEBOOK.") ";
                    $exist_same_action = TRUE;
                }
                if($condition[$name . SocialAccount::SOCIAL_MEDIA_TWITTER]) {
                    if($exist_same_action) {
                        $where_sql .= " OR ";
                    }
                    $where_sql .= " (user_share{$escape_action_id}.execute_status = 1 AND user_share{$escape_action_id}.social_media_type = ".SocialAccount::SOCIAL_MEDIA_TWITTER.") ";
                    $exist_same_action = TRUE;
                }
                if($condition[$name. '-1']) {
                    if($exist_same_action) {
                        $where_sql .= " OR ";
                    }
                    $where_sql .= " (user_share{$escape_action_id}.execute_status = 0 OR user_share{$escape_action_id}.id IS NULL) ";
                }
            }
            $where_sql .= " ) ";
        }

        return $where_sql;
    }

    /**
     * 写真投稿シェアテキスト
     * シェアSNSのAND/ORによってSQLを分ける
     * @param $share_text_condition
     * @return string
     */
    protected function getSearchPhotoShareTextWhereClause($share_text_condition, $share_sns_condition) {
        $where_sql = '';

        foreach ($share_text_condition as $action_id => $condition) {
            $escape_action_id = $this->escape($action_id);
            $where_sql .= " AND (";
            $sns_name = 'search_photo_share_sns/' . $action_id . '/';
            $name = 'search_photo_share_text/' . $action_id . '/';

            if($share_sns = $share_sns_condition[$action_id]) {
                $switch_type = $share_sns['switch_type/' . self::SEARCH_PHOTO_SHARE_SNS. '/' .$action_id];
                if($switch_type == self::QUERY_TYPE_AND) {
                    // テキストの有無は下記のいずれか1テーブルを参照できればよい
                    if($share_sns[$sns_name . SocialAccount::SOCIAL_MEDIA_FACEBOOK]) {
                        $fb_alias = $this->escape($action_id) . '_' . SocialAccount::SOCIAL_MEDIA_FACEBOOK;
                        $share_table = " user_share{$fb_alias} ";
                    } elseif($share_sns[$sns_name . SocialAccount::SOCIAL_MEDIA_TWITTER]) {
                        $tw_alias = $this->escape($action_id) . '_' . SocialAccount::SOCIAL_MEDIA_TWITTER;
                        $share_table = " user_share{$tw_alias} ";
                    } elseif($share_sns[$sns_name. '-1']) {
                        $not_share_alias = $this->escape($action_id) .'_' . '99'; // -1はテーブル別名に指定できない
                        $share_table = " user_share{$not_share_alias} ";
                    }
                } else {
                    $share_table = "user_share{$escape_action_id}";
                }
            } elseif($share_text_condition[$action_id]) {
                $share_table = "user_share{$escape_action_id}";
            }

            if(isset($condition[$name . PhotoUserShare::SEARCH_EXISTS]) && $condition[$name . PhotoUserShare::SEARCH_EXISTS] == PhotoUserShare::SEARCH_EXISTS) {
                $where_sql .= " ({$share_table}.execute_status = 1 AND ({$share_table}.share_text IS NOT NULL AND {$share_table}.share_text != '')) ";
                $search_exist = TRUE;
            }
            if(isset($condition[$name . PhotoUserShare::SEARCH_NOT_EXISTS]) && $condition[$name . PhotoUserShare::SEARCH_NOT_EXISTS] == PhotoUserShare::SEARCH_NOT_EXISTS) {
                if($search_exist) {
                    $where_sql .= " OR ";
                }
                $where_sql .= " ({$share_table}.share_text IS NULL OR {$share_table}.share_text = '') ";
            }
            $where_sql .= " ) ";
            $search_exist = FALSE;
        }

        return $where_sql;
    }

    /**
     * 写真投稿の承認
     * @param $condition
     * @return string
     */
    protected function getSearchPhotoApprovalStatusWhereClause($approval_condition) {
        $sns = array();
        $where_sql = '';

        foreach ($approval_condition as $action_id => $condition) {
            $escape_action_id = $this->escape($action_id);
            $name = 'search_photo_approval_status/' . $action_id . '/';

            if (isset($condition[$name . PhotoUser::APPROVAL_STATUS_DEFAULT]) &&
                $condition[$name . PhotoUser::APPROVAL_STATUS_DEFAULT] == PhotoUser::APPROVAL_STATUS_DEFAULT) {
                $sns[] = PhotoUser::APPROVAL_STATUS_DEFAULT;
            }
            if(isset($condition[$name . PhotoUser::APPROVAL_STATUS_APPROVE]) &&
                $condition[$name . PhotoUser::APPROVAL_STATUS_APPROVE] == PhotoUser::APPROVAL_STATUS_APPROVE) {
                $sns[] = PhotoUser::APPROVAL_STATUS_APPROVE;
            }
            if(isset($condition[$name . PhotoUser::APPROVAL_STATUS_REJECT]) &&
                $condition[$name . PhotoUser::APPROVAL_STATUS_REJECT] == PhotoUser::APPROVAL_STATUS_REJECT) {
                $sns[] = PhotoUser::APPROVAL_STATUS_REJECT;
            }
            $where_sql .= " AND photo_user{$escape_action_id}.approval_status IN(" . implode(',',$sns) . ")";
            $sns = array();
        }
        return $where_sql;
    }

    /**
     * シェア状況
     * @param $condition
     * @return string
     */
    protected function getSearchShareUserLogTypeWhereClause($condition) {
        $type = array();

        foreach ($condition as $key => $value) {
            if (preg_match('#^search_share_type/#', $key)) {
                $split_key = explode('/', $key);
                $type[] = $split_key[1];
            }
        }

        return " AND share_user_logs.type IN(" . implode(',', $type) . ")";
    }

    protected function getSearchFbLikeLogStatusWhereClause($search_conditions) {
        $where_sql = '';

        foreach ($search_conditions as $action_id => $action_search_conditions) {
            $status = array();
            $escape_action_id = $this->escape($action_id);
            $prefix = 'search_fb_like_type/' . $action_id . '/';

            foreach ($action_search_conditions as $condition => $value) {
                if (preg_match('#^' . $prefix . '#', $condition)) {
                    $split_key = explode('/', $condition);
                    $status[] = $split_key[2];
                }
            }

            $where_sql .= " AND fb_like_logs{$escape_action_id}.status IN (" . implode(',', $status) . ")";
        }

        return $where_sql;
    }

    protected function getSearchTwFollowLogStatusWhereClause($search_conditions) {
        $where_sql = '';

        foreach ($search_conditions as $action_id => $action_search_conditions) {
            $status = array();
            $escape_action_id = $this->escape($action_id);
            $prefix = 'search_tw_follow_type/' . $action_id . '/';

            foreach ($action_search_conditions as $condition => $value) {
                if (preg_match('#^' . $prefix . '#', $condition)) {
                    $split_key = explode('/', $condition);
                    $status[] = $split_key[2];
                }
            }

            $where_sql .= " AND tw_follow_logs{$escape_action_id}.status IN (" . implode(',', $status) . ")";
        }

        return $where_sql;
    }

    protected function getSearchTweetMessageStatusWhereClause($search_conditions) {
        $where_sql = array();
        foreach ($search_conditions as $action_id => $action_search_conditions) {
            $status = array();
            $escape_action_id = $this->escape($action_id);
            $prefix = 'search_tweet_type/' . $action_id . '/';
            foreach ($action_search_conditions as $condition => $value) {
                if (preg_match('#^' . $prefix . '#', $condition)) {
                    $split_key = explode('/', $condition);
                    $status[] = $split_key[2];
                }
            }
            if (in_array(TweetMessage::TWEET_ACTION_EXEC, $status)) {
                $where_sql[] = "(tweet_messages{$escape_action_id}.skipped = 0 AND tweet_messages{$escape_action_id}.tweet_content_url != '')";
            }
            if (in_array(TweetMessage::TWEET_ACTION_SKIP, $status)) {
                $where_sql[] = "(tweet_messages{$escape_action_id}.skipped = 1)";
            }
        }

        $where_sql = " AND (" . implode(' OR ', $where_sql) . ")";
        return $where_sql;
    }
    /**
     * シェアコメント
     * @param $condition
     * @return string
     */
    protected function getSearchShareUserLogTextWhereClause($condition) {
        $where_sql = array();

        foreach ($condition as $key => $value) {
            if (preg_match('#^search_share_text/#', $key)) {
                $split_key = explode('/', $key);
                if ($split_key[1] == CpShareUserLog::SEARCH_EXISTS) {
                    $where_sql[] = "CHAR_LENGTH(share_user_logs.text) > 0";
                } elseif ($split_key[1] == CpShareUserLog::SEARCH_NOT_EXISTS) {
                    $where_sql[] = "(CHAR_LENGTH(share_user_logs.text) = 0 || share_user_logs.text IS NULL)";
                }
            }
        }

        $where_sql = " AND (" . implode(' OR ', $where_sql) . ")";
        return $where_sql;
    }

    protected function getSearchInstagramHashtagDuplicateWhereClause($duplicate_condition) {
        $status = array();
        $where_sql = '';

        foreach ($duplicate_condition as $action_id => $condition) {
            $escape_action_id = $this->escape($action_id);
            $name = 'search_instagram_hashtag_duplicate/' . $action_id . '/';

            if (isset($condition[$name . InstagramHashtagUser::SEARCH_EXISTS]) &&
                $condition[$name . InstagramHashtagUser::SEARCH_EXISTS] == InstagramHashtagUser::SEARCH_EXISTS) {
                $status[] = InstagramHashtagUser::SEARCH_EXISTS;
            }
            if(isset($condition[$name . InstagramHashtagUser::SEARCH_NOT_EXISTS]) &&
                $condition[$name . InstagramHashtagUser::SEARCH_NOT_EXISTS] == InstagramHashtagUser::SEARCH_NOT_EXISTS) {
                $status[] = InstagramHashtagUser::SEARCH_NOT_EXISTS;
            }
            $where_sql .= " AND hashtag_users{$escape_action_id}.duplicate_flg IN(" . implode(',', $status) . ")";
            $status = array();
        }
        return $where_sql;
    }

    protected function getSearchInstagramHashtagReverseWhereClause($reverse_condition) {
        $status = array();
        $where_sql = '';

        foreach ($reverse_condition as $action_id => $condition) {
            $escape_action_id = $this->escape($action_id);
            $name = 'search_instagram_hashtag_reverse/' . $action_id . '/';

            if (isset($condition[$name . InstagramHashtagUserPost::REVERSE_POST_TIME_DEFAULT]) &&
                $condition[$name . InstagramHashtagUserPost::REVERSE_POST_TIME_DEFAULT] == InstagramHashtagUserPost::REVERSE_POST_TIME_DEFAULT) {
                $status[] = InstagramHashtagUserPost::REVERSE_POST_TIME_DEFAULT;
            }
            if(isset($condition[$name . InstagramHashtagUserPost::REVERSE_POST_TIME_INVALID]) &&
                $condition[$name . InstagramHashtagUserPost::REVERSE_POST_TIME_INVALID] == InstagramHashtagUserPost::REVERSE_POST_TIME_INVALID) {
                $status[] = InstagramHashtagUserPost::REVERSE_POST_TIME_INVALID;
            }
            $where_sql .= " AND hashtag_user_posts{$escape_action_id}.reverse_post_time_flg IN(" . implode(',', $status) . ")";
            $status = array();
        }
        return $where_sql;
    }

    protected function getSearchInstagramHashtagApprovalStatusWhereClause($approval_condition) {
        $status = array();
        $where_sql = '';

        foreach ($approval_condition as $action_id => $condition) {
            $escape_action_id = $this->escape($action_id);
            $name = 'search_instagram_hashtag_approval_status/' . $action_id . '/';

            if (isset($condition[$name . InstagramHashtagUserPost::APPROVAL_STATUS_DEFAULT]) &&
                $condition[$name . InstagramHashtagUserPost::APPROVAL_STATUS_DEFAULT] == InstagramHashtagUserPost::APPROVAL_STATUS_DEFAULT) {
                $status[] = InstagramHashtagUserPost::APPROVAL_STATUS_DEFAULT;
            }
            if(isset($condition[$name . InstagramHashtagUserPost::APPROVAL_STATUS_APPROVE]) &&
                $condition[$name . InstagramHashtagUserPost::APPROVAL_STATUS_APPROVE] == InstagramHashtagUserPost::APPROVAL_STATUS_APPROVE) {
                $status[] = InstagramHashtagUserPost::APPROVAL_STATUS_APPROVE;
            }
            if(isset($condition[$name . InstagramHashtagUserPost::APPROVAL_STATUS_REJECT]) &&
                $condition[$name . InstagramHashtagUserPost::APPROVAL_STATUS_REJECT] == InstagramHashtagUserPost::APPROVAL_STATUS_REJECT) {
                $status[] = InstagramHashtagUserPost::APPROVAL_STATUS_REJECT;
            }
            $where_sql .= " AND hashtag_user_posts{$escape_action_id}.approval_status IN(" . implode(',',$status) . ")";
            $status = array();
        }
        return $where_sql;
    }

    /**
     * チャンネル登録状況
     * @param $condition
     * @return string
     */
    protected function getSearchYoutubeChannelApprovalStatusWhereClause($condition) {
        $status = array();
        $where_sql = '';

        foreach ($condition as $condition_key => $condition_value) {
            $escape_action_id = $this->escape($condition_key);
            foreach ($condition_value as $key => $value) {
                if (preg_match('#^search_ytch_subscription_type/#', $key)) {
                    $split_key = explode('/', $key);
                    $status[] = $split_key[2];
                }
            }
            $where_sql .= " AND ytch_user_logs{$escape_action_id}.status IN(" . implode(',', $status) . ")";
        }

        return $where_sql;
    }
    /**
     * 人気投票
     * @param $candidate_condition
     * @return string
     */
    protected function getSearchPopularVoteCandidateWhereClause($candidate_condition) {
        $where_sql = '';

        foreach ($candidate_condition as $action_id => $condition) {
            $escape_action_id = $this->escape($action_id);
            $where_sql .= " AND (";
            $name = 'search_popular_vote_candidate/' . $action_id . '/';

            if ($candidate_condition[$action_id]) {
                $user_table = "popular_vote_user{$escape_action_id}";
            }

            $search_exist = false;
            foreach ($condition as $key => $value) {
                if ($search_exist) {
                    $where_sql .= " OR ";
                }

                if ($key === $name . CpPopularVoteCandidate::SEARCH_NOT_VOTED) {
                    $where_sql .= " ({$user_table}.cp_popular_vote_candidate_id IS NULL OR {$user_table}.cp_popular_vote_candidate_id = '' OR {$user_table}.cp_popular_vote_candidate_id = 0) ";
                    $search_exist = true;
                } else {
                    $where_sql .= " ({$user_table}.cp_popular_vote_candidate_id = {$value}) ";
                    $search_exist = true;
                }
            }

            $where_sql .= " ) ";
        }
        return $where_sql;
    }

    /**
     * 人気投票シェアSNS
     * @param $share_sns_condition
     * @return string
     */
    protected function getSearchPopularVoteShareSnsWhereClause($share_sns_condition) {
        $where_sql = '';

        foreach ($share_sns_condition as $action_id => $condition) {
            $where_sql .= " AND (";
            $escape_action_id = $this->escape($action_id);
            $name = 'search_popular_vote_share_sns/' . $action_id . '/';
            $exist_same_action = false;
            $switch_type = $condition['switch_type/' . self::SEARCH_POPULAR_VOTE_SHARE_SNS. '/' .$action_id];
            if ($switch_type == self::QUERY_TYPE_AND) {
                if ($condition[$name . SocialAccount::SOCIAL_MEDIA_FACEBOOK]) {
                    $fb_alias = $this->escape($action_id) . '_' . SocialAccount::SOCIAL_MEDIA_FACEBOOK;
                    $where_sql .= " (popular_vote_user_share{$fb_alias}.execute_status = 1 AND popular_vote_user_share{$fb_alias}.social_media_type = ".SocialAccount::SOCIAL_MEDIA_FACEBOOK.") ";
                    $exist_same_action = true;
                }
                if ($condition[$name . SocialAccount::SOCIAL_MEDIA_TWITTER]) {
                    $tw_alias = $this->escape($action_id) . '_' . SocialAccount::SOCIAL_MEDIA_TWITTER;
                    if ($exist_same_action) {
                        $where_sql .= " AND ";
                    }
                    $where_sql .= " (popular_vote_user_share{$tw_alias}.execute_status = 1 AND popular_vote_user_share{$tw_alias}.social_media_type = ".SocialAccount::SOCIAL_MEDIA_TWITTER.") ";
                    $exist_same_action = true;
                }
                if ($condition[$name. '-1']) {
                    $not_share_alias = $this->escape($action_id) .'_' . '99';//-1はテーブル別名に指定できない
                    if ($exist_same_action) {
                        $where_sql .= " AND ";
                    }
                    $where_sql .= " (popular_vote_user_share{$not_share_alias}.execute_status = 0 OR popular_vote_user_share{$not_share_alias}.id IS NULL) ";
                }
            } else {
                if ($condition[$name . SocialAccount::SOCIAL_MEDIA_FACEBOOK]) {
                    $where_sql .= " (popular_vote_user_share{$escape_action_id}.execute_status = 1 AND popular_vote_user_share{$escape_action_id}.social_media_type = ".SocialAccount::SOCIAL_MEDIA_FACEBOOK.") ";
                    $exist_same_action = true;
                }
                if ($condition[$name . SocialAccount::SOCIAL_MEDIA_TWITTER]) {
                    if ($exist_same_action) {
                        $where_sql .= " OR ";
                    }
                    $where_sql .= " (popular_vote_user_share{$escape_action_id}.execute_status = 1 AND popular_vote_user_share{$escape_action_id}.social_media_type = ".SocialAccount::SOCIAL_MEDIA_TWITTER.") ";
                    $exist_same_action = true;
                }
                if ($condition[$name. '-1']) {
                    if ($exist_same_action) {
                        $where_sql .= " OR ";
                    }
                    $where_sql .= " (popular_vote_user_share{$escape_action_id}.execute_status = 0 OR popular_vote_user_share{$escape_action_id}.id IS NULL) ";
                }
            }
            $where_sql .= " ) ";
        }
        return $where_sql;
    }

    /**
     * 人気投票シェアテキスト
     * @param $share_text_condition
     * @return string
     */
    protected function getSearchPopularVoteShareTextWhereClause($share_text_condition, $share_sns_condition) {
        $where_sql = '';

        foreach ($share_text_condition as $action_id => $condition) {
            $escape_action_id = $this->escape($action_id);
            $where_sql .= " AND (";
            $sns_name = 'search_popular_vote_share_sns/' . $action_id . '/';
            $name = 'search_popular_vote_share_text/' . $action_id . '/';

            if ($share_sns = $share_sns_condition[$action_id]) {
                $switch_type = $share_sns['switch_type/' . self::SEARCH_POPULAR_VOTE_SHARE_SNS. '/' .$action_id];
                if ($switch_type == self::QUERY_TYPE_AND) {
                    // テキストの有無は下記のいずれか1テーブルを参照できればよい
                    if ($share_sns[$sns_name . SocialAccount::SOCIAL_MEDIA_FACEBOOK]) {
                        $fb_alias = $this->escape($action_id) . '_' . SocialAccount::SOCIAL_MEDIA_FACEBOOK;
                        $share_table = " popular_vote_user_share{$fb_alias} ";
                    } elseif ($share_sns[$sns_name . SocialAccount::SOCIAL_MEDIA_TWITTER]) {
                        $tw_alias = $this->escape($action_id) . '_' . SocialAccount::SOCIAL_MEDIA_TWITTER;
                        $share_table = " popular_vote_user_share{$tw_alias} ";
                    } elseif ($share_sns[$sns_name. '-1']) {
                        $not_share_alias = $this->escape($action_id) .'_' . '99'; // -1はテーブル別名に指定できない
                        $share_table = " popular_vote_user_share{$not_share_alias} ";
                    }
                } else {
                    $share_table = "popular_vote_user_share{$escape_action_id}";
                }
            } elseif ($share_text_condition[$action_id]) {
                $share_table = "popular_vote_user_share{$escape_action_id}";
            }

            if (isset($condition[$name . PhotoUserShare::SEARCH_EXISTS]) && $condition[$name . PhotoUserShare::SEARCH_EXISTS] == PhotoUserShare::SEARCH_EXISTS) {
                $where_sql .= " ({$share_table}.execute_status = 1 AND ({$share_table}.share_text IS NOT NULL AND {$share_table}.share_text != '')) ";
                $search_exist = true;
            }
            if (isset($condition[$name . PhotoUserShare::SEARCH_NOT_EXISTS]) && $condition[$name . PhotoUserShare::SEARCH_NOT_EXISTS] == PhotoUserShare::SEARCH_NOT_EXISTS) {
                if ($search_exist) {
                    $where_sql .= " OR ";
                }
                $where_sql .= " ({$share_table}.share_text IS NULL OR {$share_table}.share_text = '') ";
            }
            $where_sql .= " ) ";
            $search_exist = false;
        }

        return $where_sql;
    }

    protected function getSearchGiftReceiverFanWhereClause($search_conditions) {
        $where_sql = '';

        foreach ($search_conditions as $key => $action_id) {
            $escape_action_id = $this->escape($action_id);
            $where_sql .= " AND gift_message{$escape_action_id}.receiver_user_id > 0";
        }

        return $where_sql;
    }

    /**
     * snsインタラクティブ
     * @param $interactive_conditions
     * @return string
     */
    protected function getSearchSocialAccountInteractiveWhereClause($interactive_conditions) {
        $statuses = array();
        $where_sql = '';
        $tw_post_retweet_count = array();
        $tw_post_reply_count = array();
        $retweeted_count_statuses = array();
        $replied_count_statuses = array();
        $fb_post_like_count = array();
        $fb_post_comment_count = array();
        $liked_count_statuses = array();
        $commented_count_statuses = array();
        foreach ($interactive_conditions as $interactive_condition) {
            foreach ($interactive_condition as $key => $value) {
                if (preg_match('/^search_social_account_interactive\//', $key)) {
                    $split_key = explode('/', $key);
                    $social_app_id = $split_key[1];
                    $social_media_id = $split_key[2];
                    $status = $split_key[3];
                    if(isset($statuses[$social_app_id][$social_media_id])) {
                        unset($statuses[$social_app_id][$social_media_id]);
                    } else {
                        $statuses[$social_app_id][$social_media_id] = $status;
                    }
                }
                if (preg_match('/^search_social_account_is_retweeted_count\//', $key)) {
                    $split_key = explode('/', $key);
                    $social_app_id = $split_key[1];
                    $social_media_id = $split_key[2];
                    $status = $split_key[3];
                    if(isset($retweeted_count_statuses[$social_app_id][$social_media_id])) {
                        unset($retweeted_count_statuses[$social_app_id][$social_media_id]);
                    } else {
                        $retweeted_count_statuses[$social_app_id][$social_media_id] = $status;
                    }
                }

                if (preg_match('/^search_social_account_is_liked_count\//', $key)) {
                    $split_key = explode('/', $key);
                    $social_app_id = $split_key[1];
                    $social_media_id = $split_key[2];
                    $status = $split_key[3];
                    if(isset($liked_count_statuses[$social_app_id][$social_media_id])) {
                        unset($liked_count_statuses[$social_app_id][$social_media_id]);
                    } else {
                        $liked_count_statuses[$social_app_id][$social_media_id] = $status;
                    }
                }
                    

                if (preg_match('/^search_social_account_is_replied_count\//', $key)) {
                    $split_key = explode('/', $key);
                    $social_app_id = $split_key[1];
                    $social_media_id = $split_key[2];
                    $status = $split_key[3];
                    if(isset($replied_count_statuses[$social_app_id][$social_media_id])) {
                        unset($replied_count_statuses[$social_app_id][$social_media_id]);
                    } else {
                        $replied_count_statuses[$social_app_id][$social_media_id] = $status;
                    }
                }
                    

                if (preg_match('/^search_social_account_is_commented_count\//', $key)) {
                    $split_key = explode('/', $key);
                    $social_app_id = $split_key[1];
                    $social_media_id = $split_key[2];
                    $status = $split_key[3];
                    if(isset($commented_count_statuses[$social_app_id][$social_media_id])) {
                        unset($commented_count_statuses[$social_app_id][$social_media_id]);
                    } else {
                        $commented_count_statuses[$social_app_id][$social_media_id] = $status;
                    }
                }
                    
                
                if (preg_match('/^search_tw_tweet_retweet_count\//', $key) && $value != '') {
                    $split_key = explode('/', $key);
                    $social_app_id = $split_key[1];
                    $social_media_id = $split_key[2];
                    $range = $split_key[3];
                    $tw_post_retweet_count[$social_app_id][$social_media_id][$range] = $value;
                }
                
                if (preg_match('/^search_fb_posts_like_count\//', $key) && $value != '') {
                    $split_key = explode('/', $key);
                    $social_app_id = $split_key[1];
                    $social_media_id = $split_key[2];
                    $range = $split_key[3];
                    $fb_post_like_count[$social_app_id][$social_media_id][$range] = $value;
                }
                    

                if (preg_match('/^search_tw_tweet_reply_count\//', $key) && $value != '') {
                    $split_key = explode('/', $key);
                    $social_app_id = $split_key[1];
                    $social_media_id = $split_key[2];
                    $range = $split_key[3];
                    $tw_post_reply_count[$social_app_id][$social_media_id][$range] = $value;
                }

                if (preg_match('/^search_fb_posts_comment_count\//', $key) && $value != '') {
                    $split_key = explode('/', $key);
                    $social_app_id = $split_key[1];
                    $social_media_id = $split_key[2];
                    $range = $split_key[3];
                    $fb_post_comment_count[$social_app_id][$social_media_id][$range] = $value;
                }
            }
        }
        foreach($statuses as $key => $value) {
            $social_app_id = $key;
            if ($social_app_id == SocialApps::PROVIDER_FACEBOOK) {
                foreach($value as $social_media_id => $status) {
                    $alias = $social_app_id.'_'.$social_media_id;
                    $where_sql .= "  ";
                    if($status == self::LIKED) {
                        $where_sql .= "  AND SA_{$social_app_id}.user_id IS NOT NULL  AND sns_like_{$alias}.like_id IS NOT NULL " ;
                    } else {
                        $where_sql .= " AND (SA_{$social_app_id}.user_id IS NULL OR sns_like_{$alias}.like_id IS NULL ) " ;
                    }
                }
            }
            if ($social_app_id == SocialApps::PROVIDER_TWITTER) {
                foreach($value as $social_media_id => $status) {
                    $alias = $social_app_id.'_'.$social_media_id;
                    $where_sql .= "  ";
                    if($status == self::FOLLOWED) {
                        $where_sql .= "  AND SA_{$social_app_id}.user_id IS NOT NULL  AND TL_{$alias}.follower_id IS NOT NULL " ;
                    } else {
                        $where_sql .= " AND (SA_{$social_app_id}.user_id IS NULL OR TL_{$alias}.follower_id IS NULL ) " ;
                    }
                }
            }
        }

        ///
        foreach($retweeted_count_statuses as $key => $value) {
            $social_app_id = $key;
            if ($social_app_id == SocialApps::PROVIDER_TWITTER) {
                foreach($value as $social_media_id => $status) {
                    $alias = $social_app_id.'_'.$social_media_id;
                    $where_sql .= "  ";
                    if($status == self::LIKED) {
                        $where_sql .= " AND SACL_RETWEET_{$alias}.action_count >= 1 ";
                    } 
                    if ($status == self::NOT_LIKE) {
                        $where_sql .= " AND (SACL_RETWEET_{$alias}.action_count = 0 OR SACL_RETWEET_{$alias}.action_count IS NULL) ";
                    }
                }
            }
            
        }
        foreach($liked_count_statuses as $key => $value) {
            $social_app_id = $key;
            if ($social_app_id == SocialApps::PROVIDER_FACEBOOK) {
                foreach($value as $social_media_id => $status) {
                    $alias = $social_app_id.'_'.$social_media_id;
                    $where_sql .= "  ";
                    if($status == self::LIKED) {
                        $where_sql .= " AND SACL_LIKE_{$alias}.action_count >= 1 ";
                    } 
                    if ($status == self::NOT_LIKE) {
                        $where_sql .= "AND (SACL_LIKE_{$alias}.action_count = 0 OR SACL_LIKE_{$alias}.action_count IS NULL) ";
                    }
                }
            }

        }
                    
        foreach($replied_count_statuses as $key => $value) {
            $social_app_id = $key;
            if ($social_app_id == SocialApps::PROVIDER_TWITTER) {
                foreach($value as $social_media_id => $status) {
                    $alias = $social_app_id.'_'.$social_media_id;
                    $where_sql .= "  ";
                    if($status == self::LIKED) {
                        $where_sql .= " AND SACL_REPLY_{$alias}.action_count >= 1 ";
                    } 
                    if ($status == self::NOT_LIKE) {
                        $where_sql .= " AND (SACL_REPLY_{$alias}.action_count = 0 OR SACL_REPLY_{$alias}.action_count IS NULL) ";
                    }
                }
            }
            
        }
        
        foreach($commented_count_statuses as $key => $value) {
            $social_app_id = $key;
            if ($social_app_id == SocialApps::PROVIDER_FACEBOOK) {
                foreach($value as $social_media_id => $status) {
                    $alias = $social_app_id.'_'.$social_media_id;
                    $where_sql .= "  ";
                    if($status == self::LIKED) {
                        $where_sql .= " AND SACL_COMMENT_{$alias}.action_count >= 1 ";
                    } 
                    if ($status == self::NOT_LIKE) {
                        $where_sql .= " AND (SACL_COMMENT_{$alias}.action_count = 0 OR SACL_COMMENT_{$alias}.action_count IS NULL) ";
                    }
                }
            }
        }
                        
        foreach ($tw_post_retweet_count as $key => $value) {
            $social_app_id = $key;
            if ($social_app_id == SocialApps::PROVIDER_TWITTER) {
                foreach($value as $social_media_id => $range) {
                    $alias = $social_app_id.'_'.$social_media_id;
                    if($range['from'] && $range['to']) {
                        $where_sql .= " AND SACL_RETWEET_{$alias}.action_count >= {$range['from']} AND SACL_RETWEET_{$alias}.action_count <= {$range['to']} ";
                    } else if ($range['from']) {
                        $where_sql .= " AND SACL_RETWEET_{$alias}.action_count >= {$range['from']} ";
                    } else {
                        $where_sql .= " AND SACL_RETWEET_{$alias}.action_count <= {$range['to']} ";
                    }
                }
            }
            
        }

        foreach ($tw_post_reply_count as $key => $value) {
            $social_app_id = $key;
            if ($social_app_id == SocialApps::PROVIDER_TWITTER) {
                foreach($value as $social_media_id => $range) {
                    $alias = $social_app_id.'_'.$social_media_id;
                    if($range['from'] && $range['to']) {
                        $where_sql .= " AND SACL_REPLY_{$alias}.action_count >= {$range['from']} AND SACL_REPLY_{$alias}.action_count <= {$range['to']} ";
                    } else if ($range['from']) {
                        $where_sql .= " AND SACL_REPLY_{$alias}.action_count >= {$range['from']} ";
                    } else {
                        $where_sql .= " AND SACL_REPLY_{$alias}.action_count <= {$range['to']} ";
                    }
                }
            }
            
        }


        foreach ($fb_post_like_count as $key => $value) {
            $social_app_id = $key;
            if ($social_app_id == SocialApps::PROVIDER_FACEBOOK) {
                foreach($value as $social_media_id => $range) {
                    $alias = $social_app_id.'_'.$social_media_id;
                    if($range['from'] && $range['to']) {
                        $where_sql .= " AND SACL_LIKE_{$alias}.action_count >= {$range['from']} AND SACL_LIKE_{$alias}.action_count <= {$range['to']} ";
                    } else if ($range['from']) {
                        $where_sql .= " AND SACL_LIKE_{$alias}.action_count >= {$range['from']} ";
                    } else {
                        $where_sql .= " AND SACL_LIKE_{$alias}.action_count <= {$range['to']} ";
                    }
                }
            }
        }

        foreach ($fb_post_comment_count as $key => $value) {
            $social_app_id = $key;
            if ($social_app_id == SocialApps::PROVIDER_FACEBOOK) {
                foreach($value as $social_media_id => $range) {
                    $alias = $social_app_id.'_'.$social_media_id;
                    if($range['from'] && $range['to']) {
                        $where_sql .= " AND SACL_COMMENT_{$alias}.action_count >= {$range['from']} AND SACL_COMMENT_{$alias}.action_count <= {$range['to']} ";
                    } else if ($range['from']) {
                        $where_sql .= " AND SACL_COMMENT_{$alias}.action_count >= {$range['from']} ";
                    } else {
                        $where_sql .= " AND SACL_COMMENT_{$alias}.action_count <= {$range['to']} ";
                    }
                }
            }
        }
        return $where_sql;
    }

    protected function getSearchDuplicateAddressWhereClause($condition) {
        if($condition['search_duplicate_address/'.self::SHIPPING_ADDRESS_DUPLICATE.'/'.self::NOT_HAVE_ADDRESS] || $condition['search_duplicate_address/'.self::SHIPPING_ADDRESS_DUPLICATE.'/'.self::HAVE_ADDRESS]){
            return $this->getSearchDuplicateAddressBrandUserWhereClause($condition);
        }
        return $this->getSearchDuplicateAddressCpUserWhereClause($condition);
    }

    /**
     * @param $condition
     *
     * target table: segment_provisions_users_relations
     */
    protected function  getSearchSegmentConditionWhereClause($condition) {
        return  " AND sp_user_relate.segment_provision_id IN(" . implode(',', $condition['provision_ids']) . ") AND sp_user_relate.created_date IN(" . implode(',', $condition['create_dates']) . ") ";
    }
    
    /**
     * ブランドユーザ重複住所絞り込み
     * @param $condition
     * @return string
     *
     */
    protected function getSearchDuplicateAddressBrandUserWhereClause($condition) {

        $where_sql = ' AND (';

        if($condition['search_duplicate_address/'.self::SHIPPING_ADDRESS_DUPLICATE.'/'.self::NOT_HAVE_ADDRESS]) {
            $where_sql .= ' relate.duplicate_address_count = '.BrandsUsersRelationService::NOT_HAVE_ADDRESS . ' OR relate.duplicate_address_count IS NULL  ';
        }

        if($condition['search_duplicate_address/'.self::SHIPPING_ADDRESS_DUPLICATE.'/'.self::NOT_HAVE_ADDRESS] && $condition['search_duplicate_address/'.self::SHIPPING_ADDRESS_DUPLICATE.'/'.self::HAVE_ADDRESS]) {
            $where_sql .= ' OR ';
        }

        if($condition['search_duplicate_address/'.self::SHIPPING_ADDRESS_DUPLICATE.'/'.self::HAVE_ADDRESS]) {

            if($condition['search_duplicate_address_from'] === '' && $condition['search_duplicate_address_to'] === '') {
                $where_sql .= ' relate.duplicate_address_count > 0 ';
            } elseif($condition['search_duplicate_address_from'] !== '' && $condition['search_duplicate_address_to'] === '') {
                $where_sql .= ' relate.duplicate_address_count >= '.$condition['search_duplicate_address_from'];
            } elseif($condition['search_duplicate_address_from'] === '' && $condition['search_duplicate_address_to'] !== '') {
                $where_sql .= ' (relate.duplicate_address_count > 0 AND relate.duplicate_address_count <='.$condition['search_duplicate_address_to'].') ';
            } else {
                $where_sql .= ' (relate.duplicate_address_count >='.$condition['search_duplicate_address_from'] .' AND relate.duplicate_address_count <='.$condition['search_duplicate_address_to'].') ';
            }

        }

        $where_sql .= ' ) ';

        return $where_sql;
    }


    /**
     * cpユーザ重複住所絞り込み
     * @param $condition
     * @return string
     *
     */
    protected function getSearchDuplicateAddressCpUserWhereClause($condition) {

        $where_sql = ' AND (';

        if($condition['search_duplicate_address/'.self::SHIPPING_ADDRESS_USER_DUPLICATE.'/'.self::NOT_HAVE_ADDRESS]) {
            $where_sql .= ' cp_usr.duplicate_address_count = '.CpUser::NOT_HAVE_ADDRESS . ' OR cp_usr.duplicate_address_count IS NULL  ';
        }

        if($condition['search_duplicate_address/'.self::SHIPPING_ADDRESS_USER_DUPLICATE.'/'.self::NOT_HAVE_ADDRESS] && $condition['search_duplicate_address/'.self::SHIPPING_ADDRESS_USER_DUPLICATE.'/'.self::HAVE_ADDRESS]) {
            $where_sql .= ' OR ';
        }

        if($condition['search_duplicate_address/'.self::SHIPPING_ADDRESS_USER_DUPLICATE.'/'.self::HAVE_ADDRESS]) {

            if($condition['search_duplicate_address_from'] === '' && $condition['search_duplicate_address_to'] === '') {
                $where_sql .= ' cp_usr.duplicate_address_count > 0 ';
            } elseif($condition['search_duplicate_address_from'] !== '' && $condition['search_duplicate_address_to'] === '') {
                $where_sql .= ' cp_usr.duplicate_address_count >= '.$condition['search_duplicate_address_from'];
            } elseif($condition['search_duplicate_address_from'] === '' && $condition['search_duplicate_address_to'] !== '') {
                $where_sql .= ' (cp_usr.duplicate_address_count > 0 AND cp_usr.duplicate_address_count <='.$condition['search_duplicate_address_to'].') ';
            } else {
                $where_sql .= ' (cp_usr.duplicate_address_count >='.$condition['search_duplicate_address_from'] .' AND cp_usr.duplicate_address_count <='.$condition['search_duplicate_address_to'].') ';
            }

        }

        $where_sql .= ' ) ';

        return $where_sql;
    }

    /**
     * スピードくじアクションがある場合の、参加状況に関する絞り込み
     * @param $participate_condition
     * @return $switch_value
     */
    protected function createParticipateSwitchPatternJoin($join_sql, $participate_condition, $switch_value) {
        if($switch_value == self::QUERY_TYPE_AND) {
            foreach($participate_condition as $key => $value) {
                if(preg_match('/^search_participate_condition\//', $key)) {
                    $action_id = explode('/', $key)[1];
                    $participate_status = explode('/', $key)[2];
                    $alias = $this->escape($action_id).'_'.$this->escape($participate_status);
                    if($participate_status == self::PARTICIPATE_COMPLETE) {
                        $join_sql .= " LEFT OUTER JOIN instant_win_users instant{$alias} ON instant{$alias}.cp_user_id = cp_usr.id AND instant{$alias}.cp_action_id = {$this->escape($action_id)} AND instant{$alias}.del_flg = 0 ";
                    }
                    if ($participate_status == self::PARTICIPATE_REJECTED) {
                        $join_sql .= " LEFT OUTER JOIN cp_user_action_statuses state{$alias} ON state{$alias}.cp_user_id = cp_usr.id AND state{$alias}.cp_action_id = {$this->escape($action_id)} AND state{$alias}.del_flg = 0 ";
                    }
                    if($participate_status == self::PARTICIPATE_READ) {
                        $join_sql .= " LEFT OUTER JOIN cp_user_action_statuses state{$alias} ON state{$alias}.cp_user_id = cp_usr.id AND state{$alias}.cp_action_id = {$this->escape($action_id)} AND state{$alias}.del_flg = 0 ";
                        $join_sql .= " LEFT OUTER JOIN cp_user_action_messages mes{$alias} ON mes{$alias}.cp_user_id = cp_usr.id AND mes{$alias}.cp_action_id = {$this->escape($action_id)} AND mes{$alias}.del_flg = 0 ";
                    }
                    if($participate_status == self::PARTICIPATE_NOT_READ || $participate_status == self::PARTICIPATE_NOT_SEND) {
                        $join_sql .= " LEFT OUTER JOIN cp_user_action_messages mes{$alias} ON mes{$alias}.cp_user_id = cp_usr.id AND mes{$alias}.cp_action_id = {$this->escape($action_id)} AND mes{$alias}.del_flg = 0 ";
                    }
                    if($participate_status == self::PARTICIPATE_COUNT_INSTANT_WIN) {
                        $join_sql .= " LEFT OUTER JOIN instant_win_users instant{$alias} ON instant{$alias}.cp_user_id = cp_usr.id AND instant{$alias}.cp_action_id = {$this->escape($action_id)} AND instant{$alias}.del_flg = 0 ";
                    }
                }
            }
        } else {
            $use_instant_win_table = false;
            $use_status_table = false;
            $use_message_table = false;
            foreach($participate_condition as $key => $value) {
                if(preg_match('/^search_participate_condition\//', $key)) {
                    $action_id = explode('/', $key)[1];
                    $participate_status = explode('/', $key)[2];
                    if($participate_status == self::PARTICIPATE_COMPLETE) {
                        $use_instant_win_table = true;
                    }
                    if ($participate_status == self::PARTICIPATE_REJECTED) {
                        $use_status_table = true;
                    }
                    if($participate_status == self::PARTICIPATE_READ) {
                        $use_status_table = true;
                        $use_message_table = true;
                    }
                    if($participate_status == self::PARTICIPATE_NOT_READ || $participate_status == self::PARTICIPATE_NOT_SEND) {
                        $use_message_table = true;
                    }
                    if($participate_status == self::PARTICIPATE_COUNT_INSTANT_WIN) {
                        $use_instant_win_table = true;
                    }
                }
            }
            if($use_instant_win_table) {
                $join_sql .= " LEFT OUTER JOIN instant_win_users instant{$this->escape($action_id)} ON instant{$this->escape($action_id)}.cp_user_id = cp_usr.id AND instant{$this->escape($action_id)}.cp_action_id = {$this->escape($action_id)} AND instant{$this->escape($action_id)}.del_flg = 0 ";
            }
            if($use_status_table) {
                $join_sql .= " LEFT OUTER JOIN cp_user_action_statuses state{$this->escape($action_id)} ON state{$this->escape($action_id)}.cp_user_id = cp_usr.id AND state{$this->escape($action_id)}.cp_action_id = {$this->escape($action_id)} AND state{$this->escape($action_id)}.del_flg = 0 ";
            }
            if($use_message_table) {
                $join_sql .= " LEFT OUTER JOIN cp_user_action_messages mes{$this->escape($action_id)} ON mes{$this->escape($action_id)}.cp_user_id = cp_usr.id AND mes{$this->escape($action_id)}.cp_action_id = {$this->escape($action_id)} AND mes{$this->escape($action_id)}.del_flg = 0 ";
            }
        }
        return $join_sql;
    }

    /**
     * コンバージョンタグ絞り込み時のJOIN
     * @param $conversion_id
     * @param $search_conversion_from
     * @param $search_conversion_to
     * @return $join_sql
     */
    protected function setConversionJoin($conversion_id, $search_conversion_from, $search_conversion_to){
        $join_sql = " LEFT OUTER JOIN ( SELECT cv{$conversion_id}.user_id,cv{$conversion_id}.date_conversion,COUNT(*) cnt FROM brands_users_conversions cv{$conversion_id}
                    INNER JOIN brands_users_relations cv_relate{$conversion_id} ON cv_relate{$conversion_id}.brand_id = cv{$conversion_id}.brand_id AND cv_relate{$conversion_id}.user_id = cv{$conversion_id}.user_id
                    WHERE cv{$conversion_id}.conversion_id = {$conversion_id} AND cv{$conversion_id}.del_flg = 0 ";

        //TODO ハードコーディング: 特定ブランド以外では、コンバージョン後にブランド会員登録したログはカウントしない
        if ($this->brand_id != Brand::ANGERS AND $this->brand_id != Brand::CHOJYU) {
            $join_sql .= " AND cv{$conversion_id}.date_conversion > cv_relate{$conversion_id}.created_at ";
        }
        $join_sql .= " GROUP BY cv{$conversion_id}.user_id ";

        if($search_conversion_from !== '' AND $search_conversion_to === ''){
            $join_sql .= " HAVING COUNT(*) >= {$search_conversion_from}";
        } elseif($search_conversion_from === '' AND $search_conversion_to !== ''){
            $join_sql .= " HAVING COUNT(*) <= {$search_conversion_to}";
        } else {
            $join_sql .= " HAVING COUNT(*) >= {$search_conversion_from} AND COUNT(*) <= {$search_conversion_to} ";
        }
        $join_sql .= " ) cvtmp{$conversion_id} ON cvtmp{$conversion_id}.user_id = relate.user_id ";

        return $join_sql;
    }

    protected function escape($value){
        return $this->cp_user->escapeForSQL($value);
    }

    /**
     * @param $switch_value
     * @return $string
     */
    protected function getConjunction($switch_value) {
        if($switch_value == self::QUERY_TYPE_AND) {
            return " AND ";
        } else {
            return " OR ";
        }
    }

    /**
     * スラッシュ付の日付を日付型に変換する(fromの時間は00:00:00とする)
     * @param $date
     * @return $date_format
     */
    protected function getFromDateFormat($date) {
        $date_format = date('Y-m-d H:i:s', strtotime($date.' 00:00:00'));
        return $this->escape($date_format);
    }

    /**
     * スラッシュ付の日付を日付型に変換する(toの時間は23:59:59とする)
     * @param $date
     * @return $date_format
     */
    protected function getToDateFormat($date) {
        $date_format = date('Y-m-d H:i:s', strtotime($date.' 23:59:59'));
        return $this->escape($date_format);
    }

    /**
     * 日時型に変換する
     * @param $date
     * @return $date_format
     */
    protected function getFromDateTimeFormat($date) {
        $date_format = date('Y-m-d H:i:s', strtotime($date));
        return $this->escape($date_format);
    }

    /**
     * 日時型に変換する
     * @param $date
     * @return $date_format
     */
    protected function getToDateTimeFormat($date) {
        $date_format = date('Y-m-d H:i:s', strtotime($this->escape($date)));
        return $this->escape($date_format);
    }

    /**
     * 年齢から誕生日を返す
     * @param $age
     */
    protected function getBirthdayByAge($age) {
        $birthday = date("Y-m-d", strtotime("-{$this->escape($age)} year"));

        return $birthday;
    }

    public function convertSocialAppIdToSocaialAccountAppId($socialAppId) {
        $socialAccountAppId = 0;
        switch($socialAppId) {
            case SocialApps::PROVIDER_FACEBOOK:
                $socialAccountAppId = SocialAccount::SOCIAL_MEDIA_FACEBOOK;
                break;
            case SocialApps::PROVIDER_GOOGLE:
                $socialAccountAppId = SocialAccount::SOCIAL_MEDIA_GOOGLE;
                break;
            case SocialApps::PROVIDER_TWITTER:
                $socialAccountAppId = SocialAccount::SOCIAL_MEDIA_TWITTER;
                break;
            case SocialApps::PROVIDER_INSTAGRAM:
                $socialAccountAppId = SocialAccount::SOCIAL_MEDIA_INSTAGRAM;
                break;
        }
        return $socialAccountAppId;
    }

    public function resetCurrentParameter() {
        $this->search_condition_key = array();
    }

    /**
     * Check logical negation of an expression
     * @param $search_conditions
     * @return bool
     */
    protected function isNegativeExpression($search_conditions) {
        if ($search_conditions['not_flg'] === "on") {
            return true;
        }

        return false;
    }

    /**
     * @param $search_key
     * @return array
     */
    public function parseSearchKey($search_key) {
        $split_key = explode('/', $search_key);

        return array($split_key[0], $split_key[1]);
    }

    /**
     * @param $search_condition
     * @param $key
     * @param $text
     * @param string $extend_key
     * @return string
     */
    protected function getSearchRangeConditionTextByKey($search_condition, $key, $text, $extend_key = "") {
        if (isset($search_condition[$key . '_from' . $extend_key])) {
            $text .= $search_condition[$key . '_from' . $extend_key];
        }
        $text .= '〜';
        if (isset($search_condition[$key . '_to' . $extend_key])) {
            $text .= $search_condition[$key . '_to' . $extend_key];
        }

        return $text;
    }

    /**
     * @param $search_conditions
     * @param int $max_width
     * @return string
     */
    public function getConditionsBriefText($search_conditions, $max_width = 500) {
        $conditions_text = $this->getConditionsText($search_conditions);

        return Util::cutTextByWidth($conditions_text, $max_width);
    }

    /**
     * @param $search_conditions
     * @return string
     */
    public function getConditionsText($search_conditions) {
        $conditions_array = array();
        $conditions_text_array = $this->getConditionsTextArray($search_conditions);

        foreach ($conditions_text_array as $key => $value) {
            if (preg_match('/^segmenting_condition_/', $key)) {
                $value = implode(' OR ', $value);
            }

            if (!Util::isNullOrEmpty($value)) {
                $conditions_array[] = $value;
            }
        }

        return implode('/', $conditions_array);
    }

    /**
     * @param $search_conditions
     * @return array
     */
    public function getConditionsTextArray($search_conditions) {
        $conditions_text = array();

        foreach ($search_conditions as $key => $value) {
            if (preg_match('/^segmenting_condition_/', $key)) {
                $temp_condition[$key] = $this->getConditionsTextArray($value);

                if (!is_array($temp_condition[$key])) continue;
            } else {
                $temp_condition = $this->getConditionText($key, $value);
            }

            if (!is_array($temp_condition)) continue;

            $conditions_text = array_merge($conditions_text, $temp_condition);
        }

        return $conditions_text;
    }

    /**
     * @param $search_condition
     * @param $search_type
     * @return array
     */
    public function getRangeSearchConditionText($search_condition, $search_type) {
        $result = array();
        $key = CpCreateSqlService::$search_range_keys[$search_type];
        $text = CpCreateSqlService::$search_range_labels[$search_type] . '：';

        $text = $this->getSearchRangeConditionTextByKey($search_condition, $key, $text);
        $result[$key] = $text;

        return $result;
    }

    /**
     * @param $search_key
     * @param $search_condition
     * @return array|null
     */
    public function getConditionText($search_key, $search_condition) {
        $condition_text = null;
        list($search_type, $search_sub_key) = $this->parseSearchKey($search_key);

        switch ($search_type) {
            case self::SEARCH_PROFILE_RATE:
                $condition_text = $this->getProfileRateSearchConditionText($search_condition);
                break;
            case self::SEARCH_PROFILE_MEMBER_NO:
                $condition_text = $this->getProfileMemberNoSearchConditionText($search_condition);
                break;
            case self::SEARCH_PROFILE_REGISTER_PERIOD:
            case self::SEARCH_PROFILE_LAST_LOGIN:
            case self::SEARCH_PROFILE_LOGIN_COUNT:
            case self::SEARCH_CP_ENTRY_COUNT:
            case self::SEARCH_CP_ANNOUNCE_COUNT:
            case self::SEARCH_MESSAGE_DELIVERED_COUNT:
            case self::SEARCH_MESSAGE_READ_COUNT:
            case self::SEARCH_MESSAGE_READ_RATIO:
            case self::SEARCH_SOCIAL_ACCOUNT_SUM:
                $condition_text = $this->getRangeSearchConditionText($search_condition, $search_type);
                break;
            case self::SEARCH_PROFILE_SOCIAL_ACCOUNT:
                $condition_text = $this->getProfileSocialAccountSearchConditionText($search_condition, $search_sub_key);
                break;
            case self::SEARCH_PROFILE_SEX:
                $condition_text = $this->getProfileSexSearchConditionText($search_condition);
                break;
            case self::SEARCH_PROFILE_AGE:
                $condition_text = $this->getProfileAgeSearchConditionText($search_condition);
                break;
            case self::SEARCH_PROFILE_ADDRESS:
                $condition_text = $this->getProfileAddressSearchConditionText($search_condition);
                break;
            case self::SEARCH_PROFILE_QUESTIONNAIRE_STATUS:
                $condition_text = $this->getProfileQuestionnaireStatusSearchConditionText($search_condition);
                break;
            case self::SEARCH_PROFILE_QUESTIONNAIRE:
            case self::SEARCH_QUESTIONNAIRE:
                $condition_text = $this->getQuestionnaireSearchConditionText($search_condition, $search_type);
                break;
            case self::SEARCH_PROFILE_CONVERSION:
                $condition_text = $this->getProfileConversionSearchConditionText($search_condition);
                break;
            case self::SEARCH_PARTICIPATE_CONDITION:
                $condition_text = $this->getParticipateConditionSearchConditionText($search_condition);
                break;
            case self::SEARCH_PHOTO_SHARE_SNS:
                $condition_text = $this->getPhotoShareSnsSearchConditionText($search_condition, $search_sub_key);
                break;
            case self::SEARCH_PHOTO_SHARE_TEXT:
                $condition_text = $this->getPhotoShareTextSearchConditionText($search_condition, $search_sub_key);
                break;
            case self::SEARCH_PHOTO_APPROVAL_STATUS:
                $condition_text = $this->getPhotoApprovalStatusSearchConditionText($search_condition, $search_sub_key);
                break;
            case self::SEARCH_SHARE_TYPE:
                $condition_text = $this->getShareTypeSearchConditionText($search_condition);
                break;
            case self::SEARCH_SHARE_TEXT:
                $condition_text = $this->getShareTextSearchConditionText($search_condition);
                break;
            case self::SEARCH_INSTAGRAM_HASHTAG_DUPLICATION:
                $condition_text = $this->getInstagramHashtagDuplicationSearchConditionText($search_condition, $search_sub_key);
                break;
            case self::SEARCH_INSTAGRAM_HASHTAG_REVERSE_POST_TIME:
                $condition_text = $this->getInstagramHashtagReversePostTimeSearchConditionText($search_condition, $search_sub_key);
                break;
            case self::SEARCH_INSTAGRAM_HASHTAG_APPROVAL_STATUS:
                $condition_text = $this->getInstagramHashtagApprovalStatusSearchConditionKey($search_condition, $search_sub_key);
                break;
            case self::SEARCH_FB_LIKE_TYPE:
                $condition_text = $this->getFbLikeTypeSearchConditionText($search_condition, $search_sub_key);
                break;
            case self::SEARCH_TW_FOLLOW_TYPE:
                $condition_text = $this->getTwFollowTypeSearchConditionText($search_condition, $search_sub_key);
                break;
            case self::SEARCH_YOUTUBE_CHANNEL_SUBSCRIPTION:
                $condition_text = $this->getYoutubeChannelSubscriptionSearchConditionText($search_condition, $search_sub_key);
                break;
            case self::SEARCH_POPULAR_VOTE_CANDIDATE:
                $condition_text = $this->getPopularVoteCandidateSearchConditionText($search_condition, $search_sub_key);
                break;
            case self::SEARCH_POPULAR_VOTE_SHARE_SNS:
                $condition_text = $this->getPopularVoteShareSnsSearchConditionText($search_condition, $search_sub_key);
                break;
            case self::SEARCH_POPULAR_VOTE_SHARE_TEXT:
                $condition_text = $this->getPopularVoteShareTextSearchConditionText($search_condition, $search_sub_key);
                break;
            case self::SEARCH_SOCIAL_ACCOUNT_INTERACTIVE:
                $condition_text = $this->getSocialAccountInteractiveSearchConditionText($search_condition);
                break;
            case self::SEARCH_IMPORT_VALUE:
                $condition_text = $this->getImportValueSearchConditionText($search_condition, $search_sub_key);
                break;
            default:
                // TODO 対応しない
                break;
        }

        return $condition_text;
    }

    /**
     * @param $search_condition
     * @return array
     */
    public function getProfileRateSearchConditionText($search_condition) {
        $result = array();

        foreach ($search_condition as $key => $value) {
            $text = '評価：';
            if (preg_match('/^search_rate\//', $key)) {
                $status = explode('/', $key)[1];
                if($status == BrandsUsersRelationService::BLOCK) {
                    $status = 'ブロックユーザー';
                } elseif ($status == BrandsUsersRelationService::NON_RATE) {
                    $status = '未評価';
                }
                $text .= $status;

                $result[$key] = $text;
            }
        }

        return $result;
    }

    /**
     * @param $search_condition
     * @return string
     */
    public function getProfileMemberNoSearchConditionText($search_condition) {
        $result = array();

        if (isset($search_condition['search_profile_member_no_from'])) {
            $result[] = '会員No：' . $search_condition['search_profile_member_no_from'];
        }

        return $result;
    }

    /**
     * @param $search_condition
     * @param $media_type
     * @return array
     */
    public function getProfileSocialAccountSearchConditionText($search_condition, $media_type) {
        $result = array();

        if (isset($search_condition['search_social_account/' . $media_type . '/' . CpCreateSqlService::LINK_SNS])) {
            $result['search_social_account/' . $media_type . '/' . CpCreateSqlService::LINK_SNS] = '連携';
        }

        if (isset($search_condition['search_social_account/' . $media_type . '/' . CpCreateSqlService::NOT_LINK_SNS])) {
            $result['search_social_account/' . $media_type . '/' . CpCreateSqlService::NOT_LINK_SNS] = '未連携';
        }

        if ((isset($search_condition['search_friend_count_from/' . $media_type]) && $search_condition['search_friend_count_from/' . $media_type] !== '')
            || (isset($search_condition['search_friend_count_to/' . $media_type]) && $search_condition['search_friend_count_to/' . $media_type] !== '')) {

            $text = $this->getSearchRangeConditionTextByKey($search_condition, 'search_friend_count', '友達数：', '/'.$media_type);

            $result['search_friend_count/' . $media_type] = $text;
        }

        return $result;
    }

    /**
     * @param $search_condition
     * @return array
     */
    public function getSocialAccountSumSearchConditionText($search_condition) {
        $result = array();

        if (isset($search_condition['search_friend_count_sum_from']) || isset($search_condition['search_friend_count_sum_to'])) {
            $text = $this->getSearchRangeConditionTextByKey($search_condition, 'search_friend_count_sum', '友達数・フォロワー数：');
            $result['search_friend_count_sum'] = $text;
        }

        return $result;
    }

    /**
     * @param $search_condition
     * @return array
     */
    public function getProfileSexSearchConditionText($search_condition) {
        $result = array();

        foreach ($search_condition as $key => $value) {
            $text = '性別：';
            if (preg_match('/^search_profile_sex\//', $key)) {
                $sex = explode('/', $key)[1];
                if ($sex == UserAttributeService::ATTRIBUTE_SEX_MAN) {
                    $text .= '男';
                    $result['search_profile_sex/' . UserAttributeService::ATTRIBUTE_SEX_MAN] = $text;
                } else if ($sex == UserAttributeService::ATTRIBUTE_SEX_WOMAN) {
                    $text .= '女';
                    $result['search_profile_sex/' . UserAttributeService::ATTRIBUTE_SEX_WOMAN] = $text;
                } else {
                    $text .= '未設定';
                    $result['search_profile_sex/' . UserAttributeService::ATTRIBUTE_SEX_UNKWOWN] = $text;
                }
            }
        }

        return $result;
    }

    /**
     * @param $search_condition
     * @return array
     */
    public function getProfileAgeSearchConditionText($search_condition) {
        $result = array();
        $key = CpCreateSqlService::$search_range_keys[CpCreateSqlService::SEARCH_PROFILE_AGE];
        $text = CpCreateSqlService::$search_range_labels[CpCreateSqlService::SEARCH_PROFILE_AGE] . '：';

        $text = $this->getSearchRangeConditionTextByKey($search_condition, $key, $text);
        $result[$key] = $text;

        if (isset($search_condition['search_profile_age_not_set'])) {
            $result['search_profile_age_not_set'] = '年齢：未設定';
        }

        return $result;
    }

    /**
     * @param $search_condition
     * @return array
     */
    public function getProfileAddressSearchConditionText($search_condition) {
        $result = array();

        /** @var PrefectureService $prefecture_service */
        $prefecture_service = $this->getService('PrefectureService');
        foreach ($search_condition as $key => $address_condition) {
            $prefecture_id = explode('/', $key)[1];
            if (preg_match('/^search_profile_address\//', $key)) {
                if ($prefecture_id == CpCreateSqlService::NOT_SET_PREFECTURE) {
                    $result[$key] = '都道府県：未設定';
                } else {
                    $prefecture = $prefecture_service->getPrefectureByPrefId($prefecture_id);
                    $result[$key] = '都道府県：' . $prefecture;
                }
            }
        }

        return $result;
    }

    /**
     * @param $search_condition
     * @return array
     */
    public function getProfileQuestionnaireStatusSearchConditionText($search_condition) {
        $result = array();

        foreach ($search_condition as $key => $value) {
            if (preg_match('/^search_questionnaire_status\//', $key)) {
                $status = explode('/', $key)[1];
                $text = 'カスタムプロフィール：';
                if ($status == BrandsUsersRelation::SIGNUP_WITHOUT_INFO) {
                    $result[$key] = $text . '未取得';
                } else if ($status == BrandsUsersRelation::SIGNUP_WITH_INFO) {
                    $result[$key] = $text . '取得済み';
                } else if ($status == BrandsUsersRelation::FORCE_WITH_INFO) {
                    $result[$key] = $text . '要再取得';
                }
            }
        }

        return $result;
    }

    /**
     * @param $search_condition
     * @param $search_type
     * @return array
     */
    public function getQuestionnaireSearchConditionText($search_condition, $search_type) {
        if ($search_type == CpCreateSqlService::SEARCH_QUESTIONNAIRE) {
            /** @var CpQuestionnaireService $questionnaire_service */
            $questionnaire_service = $this->getService("CpQuestionnaireService", CpQuestionnaireService::TYPE_CP_QUESTION);
            $text = 'アンケート：';
            $input_key = "search_questionnaire";
        } else {
            /** @var CpQuestionnaireService $questionnaire_service */
            $questionnaire_service = $this->getService("CpQuestionnaireService", CpQuestionnaireService::TYPE_PROFILE_QUESTION);
            $text = 'カスタムプロフィール：';
            $input_key = "search_profile_questionnaire";
        }
        $result = array();

        foreach ($search_condition as $key => $value) {
            if (preg_match('/^'.$input_key.'\//', $key)) {
                $split_key = explode('/', $key);
                $relate_id = $split_key[1];
                $user_answer = $split_key[2];
                $profile_question_relate = $questionnaire_service->getProfileQuestionRelationsById($relate_id);

                if ($search_condition['questionnaire_type/'. $relate_id] == QuestionTypeService::FREE_ANSWER_TYPE) {
                    if ($user_answer == CpCreateSqlService::ANSWERED_QUESTIONNAIRE) {
                        $result[$key] = $text . "Q" . $profile_question_relate->number . '　回答済 ';
                    } else if ($user_answer == CpCreateSqlService::NOT_ANSWER_QUESTIONNAIRE) {
                        $result[$key] = $text . "Q" . $profile_question_relate->number . '　未回答 ';
                    }
                } else {
                    if ($user_answer == CpCreateSqlService::NOT_ANSWER_QUESTIONNAIRE) {
                        $result[$key] = $text . "Q" . $profile_question_relate->number . '　未回答 ';
                    } else {
                        $question_choice_answer = $questionnaire_service->getChoiceById($user_answer);
                        $result[$key] = $text . "Q" . $profile_question_relate->number . '/A' . $question_choice_answer->choice_num;
                    }
                }
            }
        }

        unset ($this->_Services["CpQuestionnaireService"]);
        return $result;
    }

    /**
     * @param $search_condition
     * @return array
     */
    public function getProfileConversionSearchConditionText($search_condition) {
        $result = array();

        /** @var ConversionService $conversion_service */
        $conversion_service = $this->getService("ConversionService");
        foreach ($search_condition as $key => $value) {
            $text = "コンバージョン：";
            if (preg_match('/^search_profile_conversion_/', $key)) {
                if ($conversion_id = explode('/', $key)[1]) {
                    $conversion = $conversion_service->getConversionById($conversion_id);
                    $text .= $this->getSearchRangeConditionTextByKey($search_condition, 'search_profile_conversion', $conversion->name.'　', '/' . $conversion_id);
                    $result[$key] = $text;
                    break;
                }
            }
        }

        return $result;
    }

    public function getParticipateConditionSearchConditionText($search_condition) {
        $result = array();

        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = $this->getService("CpFlowService");
        foreach ($search_condition as $key => $value) {
            if (preg_match('/^search_participate_condition\//', $key)) {
                $action_id = explode('/', $key)[1];
                $participate_status = explode('/', $key)[2];
                $cp_action = $cp_flow_service->getCpActionById($action_id);
                $action_detail = $cp_action->getCpActionDetail();
                switch ($participate_status) {
                    case CpCreateSqlService::PARTICIPATE_COMPLETE:
                        $status = "完了";
                        break;
                    case CpCreateSqlService::PARTICIPATE_REJECTED:
                        $status = "参加条件外";
                        break;
                    case CpCreateSqlService::PARTICIPATE_READ:
                        $status = "既読";
                        break;
                    case CpCreateSqlService::PARTICIPATE_NOT_READ:
                        $status = "未読";
                        break;
                    case CpCreateSqlService::PARTICIPATE_NOT_SEND:
                        $status = "未送信";
                        break;
                    case CpCreateSqlService::PARTICIPATE_COUNT_INSTANT_WIN:
                        $status = "抽選回数指定 ";
                        if ($search_condition['search_count_instant_win_from/'.$action_id]) {
                            $status .= $search_condition['search_count_instant_win_from/'.$action_id];
                        }
                        $status .= '〜';
                        if ($search_condition['search_count_instant_win_to/'.$action_id]) {
                            $status .= $search_condition['search_count_instant_win_to/'.$action_id];
                        }
                        break;
                }
                $result[$key] = $action_detail["title"] . '：' . $status;
            }
        }

        return $result;
    }

    /**
     * @param $search_condition
     * @param $action_id
     * @return array
     */
    public function getPhotoShareSnsSearchConditionText($search_condition, $action_id) {
        $result = array();
        $name = 'search_photo_share_sns/' . $action_id . '/';

        if($search_condition[$name . SocialAccount::SOCIAL_MEDIA_FACEBOOK]) {
            $result[$name . SocialAccount::SOCIAL_MEDIA_FACEBOOK] = '写真投稿 シェアSNS Facebook';
        }
        if($search_condition[$name . SocialAccount::SOCIAL_MEDIA_TWITTER]) {
            $result[$name . SocialAccount::SOCIAL_MEDIA_TWITTER] = '写真投稿 シェアSNS Twitter';
        }
        if($search_condition[$name. '-1']) {
            $result[$name . '-1'] = '写真投稿 シェアSNS 未シェア';
        }

        return $result;
    }

    /**
     * @param $search_condition
     * @param $action_id
     * @return array
     */
    public function getPhotoShareTextSearchConditionText($search_condition, $action_id) {
        $result = array();

        $name = 'search_photo_share_text/' . $action_id . '/';
        if (isset($search_condition[$name.PhotoUserShare::SEARCH_EXISTS])) {
            $result[$name . PhotoUserShare::SEARCH_EXISTS] = '写真投稿 シェアテキスト：あり';
        }
        if (isset($search_condition[$name.PhotoUserShare::SEARCH_NOT_EXISTS])) {
            $result[$name . PhotoUserShare::SEARCH_NOT_EXISTS] = '写真投稿 シェアテキスト：なし';
        }

        return $result;
    }

    /**
     * @param $search_condition
     * @param $action_id
     * @return array
     */
    public function getPhotoApprovalStatusSearchConditionText($search_condition, $action_id) {
        $result = array();
        $name = 'search_photo_approval_status/' . $action_id . '/';

        if (isset($search_condition[$name . PhotoUser::APPROVAL_STATUS_DEFAULT]) &&
            $search_condition[$name . PhotoUser::APPROVAL_STATUS_DEFAULT] == PhotoUser::APPROVAL_STATUS_DEFAULT) {
            $result[$name . PhotoUser::APPROVAL_STATUS_DEFAULT] = '写真投稿 検閲 未承認';
        }
        if(isset($search_condition[$name . PhotoUser::APPROVAL_STATUS_APPROVE]) &&
            $search_condition[$name . PhotoUser::APPROVAL_STATUS_APPROVE] == PhotoUser::APPROVAL_STATUS_APPROVE) {
            $result[$name . PhotoUser::APPROVAL_STATUS_APPROVE] = '写真投稿 検閲 承認';
        }
        if(isset($search_condition[$name . PhotoUser::APPROVAL_STATUS_REJECT]) &&
            $search_condition[$name . PhotoUser::APPROVAL_STATUS_REJECT] == PhotoUser::APPROVAL_STATUS_REJECT) {
            $result[$name . PhotoUser::APPROVAL_STATUS_REJECT] = '写真投稿 検閲 非承認';
        }

        return $result;
    }

    /**
     * @param $search_condition
     * @return array
     */
    public function getShareTypeSearchConditionText($search_condition) {
        $result = array();
        $name = 'search_share_type/';

        foreach ($search_condition as $key => $value) {
            if (isset($search_condition[$name.CpShareUserLog::TYPE_SHARE])) {
                $result[$name.CpShareUserLog::TYPE_SHARE] = "シェア状況 ".CpShareUserLog::STATUS_SHARE;
            }
            if (isset($search_condition[$name.CpShareUserLog::TYPE_SKIP])) {
                $result[$name.CpShareUserLog::TYPE_SKIP] = "シェア状況 ".CpShareUserLog::STATUS_SKIP;
            }
            if (isset($search_condition[$name.CpShareUserLog::TYPE_UNREAD])) {
                $result[$name.CpShareUserLog::TYPE_UNREAD] = "シェア状況 ".CpShareUserLog::STATUS_UNREAD;
            }
        }

        return $result;
    }

    /**
     * @param $search_condition
     * @return array
     */
    public function getShareTextSearchConditionText($search_condition) {
        $result = array();
        $name = 'search_share_text/';

        foreach ($search_condition as $key => $value) {
            if (isset($search_condition[$name.CpShareUserLog::SEARCH_EXISTS])) {
                $result[$name.CpShareUserLog::SEARCH_EXISTS] = 'シェアコメント あり';
            }
            if (isset($search_condition[$name.CpShareUserLog::SEARCH_NOT_EXISTS])) {
                $result[$name.CpShareUserLog::SEARCH_NOT_EXISTS] = 'シェアコメント なし';
            }
        }

        return $result;
    }

    /**
     * @param $search_condition
     * @param $action_id
     * @return array
     */
    public function getInstagramHashtagDuplicationSearchConditionText($search_condition, $action_id) {
        $result = array();
        $name = 'search_instagram_hashtag_duplicate/' . $action_id . '/';

        if (isset($search_condition[$name . InstagramHashtagUser::SEARCH_EXISTS]) &&
            $search_condition[$name . InstagramHashtagUser::SEARCH_EXISTS] == InstagramHashtagUser::SEARCH_EXISTS) {
            $result[$name . InstagramHashtagUser::SEARCH_EXISTS] = 'Instagram投稿 ユーザネーム重複 あり';
        }
        if(isset($search_condition[$name . InstagramHashtagUser::SEARCH_NOT_EXISTS]) &&
            $search_condition[$name . InstagramHashtagUser::SEARCH_NOT_EXISTS] == InstagramHashtagUser::SEARCH_NOT_EXISTS) {
            $result[$name . InstagramHashtagUser::SEARCH_NOT_EXISTS] = 'Instagram投稿 ユーザネーム重複 なし';
        }

        return $result;
    }

    /**
     * @param $search_condition
     * @param $action_id
     * @return array
     */
    public function getInstagramHashtagReversePostTimeSearchConditionText($search_condition, $action_id) {
        $result = array();
        $name = 'search_instagram_hashtag_reverse/' . $action_id . '/';

        if (isset($search_condition[$name . InstagramHashtagUserPost::REVERSE_POST_TIME_DEFAULT]) &&
            $search_condition[$name . InstagramHashtagUserPost::REVERSE_POST_TIME_DEFAULT] == InstagramHashtagUserPost::REVERSE_POST_TIME_DEFAULT) {
            $result[$name . InstagramHashtagUserPost::REVERSE_POST_TIME_DEFAULT] = 'Instagram投稿 登録投稿順序 登録後投稿';
        }
        if(isset($search_condition[$name . InstagramHashtagUserPost::REVERSE_POST_TIME_INVALID]) &&
            $search_condition[$name . InstagramHashtagUserPost::REVERSE_POST_TIME_INVALID] == InstagramHashtagUserPost::REVERSE_POST_TIME_INVALID) {
            $result[$name . InstagramHashtagUserPost::REVERSE_POST_TIME_INVALID] = 'Instagram投稿 登録投稿順序 投稿後登録';
        }

        return $result;
    }

    /**
     * @param $search_condition
     * @param $action_id
     * @return array
     */
    public function getInstagramHashtagApprovalStatusSearchConditionKey($search_condition, $action_id) {
        $result = array();
        $name = 'search_instagram_hashtag_approval_status/' . $action_id . '/';

        if (isset($search_condition[$name . InstagramHashtagUserPost::APPROVAL_STATUS_DEFAULT]) &&
            $search_condition[$name . InstagramHashtagUserPost::APPROVAL_STATUS_DEFAULT] == InstagramHashtagUserPost::APPROVAL_STATUS_DEFAULT) {
            $result[$name . InstagramHashtagUserPost::APPROVAL_STATUS_DEFAULT] = 'Instagram投稿 検閲 未承認';
        }
        if(isset($search_condition[$name . InstagramHashtagUserPost::APPROVAL_STATUS_APPROVE]) &&
            $search_condition[$name . InstagramHashtagUserPost::APPROVAL_STATUS_APPROVE] == InstagramHashtagUserPost::APPROVAL_STATUS_APPROVE) {
            $result[$name . InstagramHashtagUserPost::APPROVAL_STATUS_APPROVE] = 'Instagram投稿 検閲 承認';
        }
        if(isset($search_condition[$name . InstagramHashtagUserPost::APPROVAL_STATUS_REJECT]) &&
            $search_condition[$name . InstagramHashtagUserPost::APPROVAL_STATUS_REJECT] == InstagramHashtagUserPost::APPROVAL_STATUS_REJECT) {
            $result[$name . InstagramHashtagUserPost::APPROVAL_STATUS_REJECT] = 'Instagram投稿 検閲 非承認';
        }

        return $result;
    }

    /**
     * @param $search_condition
     * @param $action_id
     * @return array
     */
    public function getFbLikeTypeSearchConditionText($search_condition, $action_id) {
        $result = array();
        $name = 'search_fb_like_type/' . $action_id . '/';

        foreach (CpFacebookLikeLog::$fb_like_statuses as $like_action => $status_action) {
            if (isset($search_condition[$name . $like_action])) {
                $result[$name . $like_action] = 'Facebookいいね！状況 '.$status_action;
            }
        }

        return $result;
    }

    /**
     * @param $search_condition
     * @param $action_id
     * @return array
     */
    public function getTwFollowTypeSearchConditionText($search_condition, $action_id) {
        $result = array();
        $name = 'search_tw_follow_type/' . $action_id . '/';

        foreach (CpTwitterFollowLog::$tw_follow_statuses as $key => $label) {
            if (isset($search_condition[$name.$key])) {
                $result[$name.$key] = 'Twitterフォロー状況 '.$label;
            }
        }

        return $result;
    }

    /**
     * @param $search_condition
     * @param $action_id
     * @return array
     */
    public function getYoutubeChannelSubscriptionSearchConditionText($search_condition, $action_id) {
        $result = array();
        $name = 'search_ytch_subscription_type/' . $action_id . '/';

        foreach (CpYoutubeChannelUserLog::$youtube_status_string as $key => $label) {
            if (isset($search_condition[$name . $key])) {
                $search_condition[$name . $key] = 'YouTubeチャンネル登録 登録状況 ' . $label;
            }
        }

        return $result;
    }

    /**
     * @param $search_condition
     * @param $action_id
     * @return array
     */
    public function getPopularVoteCandidateSearchConditionText($search_condition, $action_id) {
        /** @var CpPopularVoteActionService $cp_popular_vote_action_service */
        $cp_popular_vote_action_service = $this->getService('CpPopularVoteActionService');

        $cp_popular_vote_action = $cp_popular_vote_action_service->getCpPopularVoteActionByCpActionId($action_id);
        $cp_popular_vote_candidates = $cp_popular_vote_action->getCpPopularVoteCandidates(array('del_flg' => 0));

        $result = array();
        $name = 'search_popular_vote_candidate/' . $action_id . '/';

        foreach ($cp_popular_vote_candidates as $cp_popular_vote_candidate) {
            if (isset($search_condition[$name.$cp_popular_vote_candidate->id])) {
                $result[$name . $cp_popular_vote_candidate->id] = '人気投票 投票 ' . $cp_popular_vote_candidate->title;
            }
        }

        if (isset($search_condition[$name.CpPopularVoteCandidate::SEARCH_NOT_VOTED])) {
            $result[$name . CpPopularVoteCandidate::SEARCH_NOT_VOTED] = '人気投票 投票 未投票';
        }

        return $result;
    }

    /**
     * @param $search_condition
     * @param $action_id
     * @return array
     */
    public function getPopularVoteShareSnsSearchConditionText($search_condition, $action_id) {
        $result = array();
        $name = 'search_popular_vote_share_sns/'.$action_id.'/';

        if (isset($search_condition[$name . SocialAccount::SOCIAL_MEDIA_FACEBOOK])) {
            $result[$name . SocialAccount::SOCIAL_MEDIA_FACEBOOK] = '人気投票 シェアSNS Facebook';
        }
        if (isset($search_condition[$name . SocialAccount::SOCIAL_MEDIA_TWITTER])) {
            $result[$name . SocialAccount::SOCIAL_MEDIA_TWITTER] = '人気投票 シェアSNS TWitter';
        }
        if (isset($search_condition[$name.'-1'])) {
            $result[$name.'-1'] = '人気投票 シェアSNS 未シェア';
        }

        return $result;
    }

    /**
     * @param $search_condition
     * @param $action_id
     * @return array
     */
    public function getPopularVoteShareTextSearchConditionText($search_condition, $action_id) {
        $result = array();
        $name = 'search_popular_vote_share_text/'.$action_id.'/';

        if (isset($search_condition[$name . PopularVoteUserShare::SEARCH_EXISTS])) {
            $result[$name . PopularVoteUserShare::SEARCH_EXISTS] = '人気投票 シェアされた投票理由 あり';
        }
        if (isset($search_condition[$name . PopularVoteUserShare::SEARCH_NOT_EXISTS])) {
            $result[$name . PopularVoteUserShare::SEARCH_NOT_EXISTS] = '人気投票 シェアされた投票理由 なし';
        }

        return $result;
    }

    /**
     * @param $search_condition
     * @return array
     */
    public function getSocialAccountInteractiveSearchConditionText($search_condition) {
        /** @var  BrandSocialAccountService $brandSocialAccountService */
        $brandSocialAccountService = $this->getService('BrandSocialAccountService');
        $result = array();

        foreach($search_condition as $key => $value) {
            $split_key = explode('/', $key);
            $social_app_id = $split_key[1];
            $page_id = $split_key[2];
            $condition = $split_key[3];
            $page = $brandSocialAccountService->getBrandSocialAccountByAccountId($page_id, $social_app_id);
            if($condition == 'Y') {
                $result[$key] = Util::cutTextByWidth($page->name,150) . '：いいね！済';
            }
            if($condition == 'N') {
                $result[$key] = Util::cutTextByWidth($page->name,150) . '：未いいね！';
            }
        }

        return $result;
    }

    /**
     * @param $search_condition
     * @param $definition_id
     * @return array
     */
    public function getImportValueSearchConditionText($search_condition, $definition_id) {
        $result = array();
        $brand_service = $this->getService('BrandService');
        $bua_definition = $brand_service->getBrandUserAttributeDefinitionById($definition_id);

        $definition_value_set = json_decode($bua_definition->value_set, true);

        foreach ($search_condition as $key => $value) {
            if (strpos($key, 'search_import_value') !== false) {
                $definition_value = explode('/', $key)[2];
                $result[$key] = $bua_definition->attribute_name . ':' . $definition_value_set[$definition_value];
            }
        }

        return $result;

    }

    public function getDeliveryTargetActionId($action_id){
        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = $this->getService('CpFlowService');
        $cp_action = $cp_flow_service->getCpActionById($action_id);
        $cp_action_group = $cp_flow_service->getCpActionGroupByAction($action_id);

        if ($cp_action->order_no != 1 && $cp_action_group->order_no != 1) {
            $first_action = $cp_flow_service->getFirstActionInGroupByGroupId($cp_action_group->id);
            $delivery_target_action_id = $first_action->id;
        } else {
            $delivery_target_action_id = $action_id;
        }

        return $delivery_target_action_id;
    }
}
