<?php
AAFW::import('jp.aainc.lib.base.aafwServiceBase');
AAFW::import('jp.aainc.classes.brandco.cp.CpQuestionnaireActionManager');
AAFW::import('jp.aainc.classes.brandco.cp.CpTwitterFollowActionManager');
AAFW::import('jp.aainc.classes.brandco.cp.CpCouponActionManager');
AAFW::import('jp.aainc.classes.brandco.cp.CpFreeAnswerActionManager');

class FanListDownloadService extends aafwServiceBase {

    const TYPE_PROFILE = 'data_profile';

    const START = 0;
    const FINISH = 1;

    protected $fan_list_dl_history;

    /** @var array $download_file_name */
    public static $download_file_name = array(
        self::TYPE_PROFILE                 => 'profile',
        CpAction::TYPE_QUESTIONNAIRE       => 'questionnaire',
        CpAction::TYPE_PHOTO               => 'photo',
        CpAction::TYPE_FACEBOOK_LIKE       => 'facebookLike',
        CpAction::TYPE_TWITTER_FOLLOW      => 'twitterFollow',
        CpAction::TYPE_INSTANT_WIN         => 'instantLottery',
        CpAction::TYPE_FREE_ANSWER         => 'freeAnswer',
        CpAction::TYPE_SHARE               => 'share',
        CpAction::TYPE_GIFT                => 'gift',
        CpAction::TYPE_COUPON              => 'coupon',
        CpAction::TYPE_CODE_AUTHENTICATION => 'codeAuth',
        CpAction::TYPE_TWEET               => 'tweet',
        CpAction::TYPE_INSTAGRAM_HASHTAG   => 'instagramHash',
        CpAction::TYPE_YOUTUBE_CHANNEL     => 'youtubeChannel',
        CpAction::TYPE_RETWEET             => 'retweet',
        CpAction::TYPE_POPULAR_VOTE        => 'vote',
        CpAction::TYPE_ANNOUNCE            => 'announce'
    );

    public function __construct() {
        $this->fan_list_dl_history = $this->getModel('FanListDlHistories');
    }

    /**
     * データ取得に使用するデータを各ActionManagerから取得
     * @param $type
     * @param $page_info
     * @return array|mixed
     */
    public function getActionData($type, &$page_info) {
        $action_data = array();
        /** @var BrandGlobalSettingService $brand_global_setting_service */
        $brand_global_setting_service = $this->getService('BrandGlobalSettingService');
        $original_sns_account = $brand_global_setting_service->getBrandGlobalSetting(
            $page_info['brand_id'],
            BrandGlobalSettingService::ORIGINAL_SNS_ACCOUNTS
        );
        $can_use_aaid_hash_tag = $brand_global_setting_service->getBrandGlobalSetting(
            $page_info['brand_id'],
            BrandGlobalSettingService::CAN_USE_AAID_HASH_TAG
        );
        // GDOの場合
        if ($original_sns_account) {
            $action_data['original_sns_account'] = $original_sns_account;
        }

        if ($can_use_aaid_hash_tag) {
            $action_data['get_monipla_user_id'] = $can_use_aaid_hash_tag;
        }

        if ($page_info['action_id']) {
            /** @var CpFlowService $cp_flow_service */
            $cp_flow_service = $this->getService('CpFlowService');
            $action_data['target_action'] = $cp_flow_service->getCpActionById($page_info['action_id']);
        }

        /** @var BrandPageSettingService $page_setting_service */
        $page_setting_service = $this->getService('BrandPageSettingService');
        $action_data['page_settings'] = $page_setting_service->getPageSettingsByBrandId($page_info['brand_id']);


        switch ($type) {
            case self::TYPE_PROFILE:
                /** @var BrandSocialAccountService $brand_social_account_service */
                $brand_social_account_service = $this->getService('BrandSocialAccountService');
                $action_data['facebook_accounts'] = $brand_social_account_service->getSocialAccountsByBrandId($page_info['brand_id'], SocialApps::PROVIDER_FACEBOOK);
                $action_data['twitter_accounts'] = $brand_social_account_service->getSocialAccountsByBrandId($page_info['brand_id'], SocialApps::PROVIDER_TWITTER);

                /** @var SocialLikeService $social_like_service */
                $social_like_service = $this->getService('SocialLikeService');
                $isSocialLikesEmpty = $social_like_service->isEmptyTable();
                $action_data['getSocialLikes'] = $action_data['facebook_accounts'] && !$isSocialLikesEmpty;

                /** @var TwitterFollowService $twitter_follow_service */
                $twitter_follow_service = $this->getService('TwitterFollowService');
                $isTwitterFollowsEmpty = $twitter_follow_service->isEmptyTable();
                $action_data['getTwitterFollows'] = $action_data['twitter_accounts'] && !$isTwitterFollowsEmpty;

                /** @var CpQuestionnaireService $profile_questionnaire_service */
                $profile_questionnaire_service = $this->getService('CpQuestionnaireService', CpQuestionnaireService::TYPE_PROFILE_QUESTION);
                $profile_question_relations = $profile_questionnaire_service->getPublicProfileQuestionRelationByBrandId($page_info['brand_id']);
                $action_data['use_profile_questions'] = $profile_questionnaire_service->useProfileQuestion($profile_question_relations);
                $action_data['profile_questions'] = array();
                foreach ($action_data['use_profile_questions'] as $relation) {
                    $action_data['profile_questions'][$relation->id] = $profile_questionnaire_service->getQuestionById($relation->question_id);
                }

                /** @var ConversionService $conversion_service */
                $conversion_service = $this->getService('ConversionService');
                $action_data['conversions'] = $conversion_service->getConversionsByBrandId($page_info['brand_id']);

                /** @var $brand_service BrandService */
                $brand_service = $this->getService('BrandService');
                $action_data['definitions'] = $brand_service->getCustomAttributeDefinitions($page_info['brand_id']);

                $action_data['is_manager'] = $page_info['is_manager'];

                // 参加状況
                /** @var CpFlowService $cp_flow_service */
                $cp_flow_service = $this->getService('CpFlowService');
                $action_data['cp_actions'] = $cp_flow_service->getCpActionsByCpId($page_info['cp_id']);
                $first_cp_action = $cp_flow_service->getFirstActionOfCp($page_info['cp_id']);
                $action_data['second_cp_action'] = $cp_flow_service->getCpActionByGroupIdAndOrderNo($first_cp_action->cp_action_group_id, 2);
                $action_data['last_cp_action'] = $cp_flow_service->getMaxStepNo($first_cp_action->cp_action_group_id);

                /** @var BrandService $brand_service */
                $brand_service = $this->getService('BrandService');
                $brand = $brand_service->getBrandById($page_info['brand_id']);
                $action_data['has_comment_option'] = $brand->hasOption(BrandOptions::OPTION_COMMENT);

                break;
            case CpAction::TYPE_QUESTIONNAIRE:
                $action_manager = new CpQuestionnaireActionManager();
                $questionnaire_data = $action_manager->getActionData($page_info['action_id']);
                $action_data['questions_relations'] = $questionnaire_data['questions_relations'];
                $action_data['questions'] = $questionnaire_data['questions'];
                break;
            case CpAction::TYPE_PHOTO:
                /** @var PhotoUserService $photo_user_service */
                $photo_user_service = $this->getService('PhotoUserService');
                $cp_photo_action = $photo_user_service->getCpPhotoActionByCpActionId($page_info['action_id']);
                $action_data['can_share'] = ($cp_photo_action->fb_share_required || $cp_photo_action->tw_share_required) ? true : false;
                break;
            case CpAction::TYPE_TWITTER_FOLLOW:
                /** @var CpTwitterFollowActionManager $cp_tw_follow_action_manager */
                $cp_tw_follow_action_manager = new CpTwitterFollowActionManager();
                $action_data['cp_concrete_action'] = $cp_tw_follow_action_manager->getCpConcreteActionByCpActionId($page_info['action_id']);
                break;
            case CpAction::TYPE_SHARE:
                /** @var CpShareActionService $cp_share_action_service */
                $cp_share_action_service = $this->getService('CpShareActionService');
                $action_data['cp_share_action_id'] = $cp_share_action_service->getCpShareActionById($page_info['action_id'])->id;
                break;
            case CpAction::TYPE_GIFT:
                /** @var CpGiftActionManager $cp_gift_action_manager */
                $cp_gift_action_manager = new CpGiftActionManager();
                $action_data['cp_concrete_action'] = $cp_gift_action_manager->getCpActions($page_info['action_id'])[1]->id;
                break;
            case CpAction::TYPE_TWEET:
                /** @var CpTweetActionManager $cp_tweet_action_manager */
                $cp_tweet_action_manager = new CpTweetActionManager();
                $action_data['cp_concrete_action_id'] = $cp_tweet_action_manager->getCpActions($page_info['action_id'])[1]->id;
                break;
            case CpAction::TYPE_RETWEET:
                /** @var CpRetweetActionService $cp_retweet_action_service */
                $cp_retweet_action_service = $this->getService('CpRetweetActionService');
                $action_data['cp_retweet_action_id'] = $cp_retweet_action_service->getCpRetweetAction($page_info['action_id'])->id;
                break;
            case CpAction::TYPE_POPULAR_VOTE:
                /** @var CpPopularVoteActionService $cp_vote_action_service */
                $cp_vote_action_service = $this->getService('CpPopularVoteActionService');
                $cp_vote_action = $cp_vote_action_service->getCpPopularVoteActionByCpActionId($page_info['action_id']);
                $action_data['can_share'] = ($cp_vote_action->fb_share_required || $cp_vote_action->tw_share_required) ? true : false;
                break;
        }
        return $action_data;
    }

    /**
     * 各アクションのヘッダーを取得
     * @param $type
     * @param $action_data
     * @return array|mixed
     */
    public function getActionHeader($type, &$action_data) {
        $header = array('会員No', 'レーティング', '性別', '年齢', '都道府県', 'Facebook', 'Facebook友達数',
            'Twitter', 'Twitterフォロワー数', 'LINE', 'Instagram', 'Instagramフォロワー数', 'Yahoo!', 'Google');

        //GDO、LinkedInの場合
        if ($action_data['original_sns_account']) {
            $original_sns_account_array = explode(',',$action_data['original_sns_account']->content);

            if(in_array(SocialAccountService::SOCIAL_MEDIA_GDO, $original_sns_account_array)) {
                array_push($header, 'GDO');
            }

            if(in_array(SocialAccountService::SOCIAL_MEDIA_LINKEDIN, $original_sns_account_array)) {
                array_push($header, 'LinkedIn');
            }
        }

        if ($action_data['has_comment_option']) {
            array_push($header, 'from_id（新規登録）', 'コメント数');
        }

        if($type != self::TYPE_PROFILE) {
            array_push($header, 'ステータス', '完了日', '完了時間');
        }

        switch ($type) {
            case self::TYPE_PROFILE:
                if(!isset($action_data)) return;
                if ($action_data['getSocialLikes']) {
                    foreach ($action_data['facebook_accounts'] as $fb_account) {
                        array_push($header, $fb_account->name . 'にいいね！');
                        array_push($header, $fb_account->name . 'の投稿にいいね！');
                        array_push($header, $fb_account->name . 'の投稿にコメント');
                    }
                }
                if ($action_data['getTwitterFollows']) {
                    foreach ($action_data['twitter_accounts'] as $tw_account) {
                        array_push($header, $tw_account->name . 'をフォロー');
                        array_push($header, $tw_account->name . 'のツイートをリツイート');
                        array_push($header, $tw_account->name . 'のツイートをリプライ');
                    }
                }

                array_push($header, 'キャンペーン参加回数', 'キャンペーン当選回数', 'メッセージ受信数', 'メッセージ閲覧数', 'メッセージ閲覧率',
                    'メール通知', '登録日', '登録時刻', '登録期間', '最終ログイン', 'ログイン回数');

                if ($action_data['use_profile_questions']) {
                    $header[] = 'アンケート';
                    foreach ($action_data['use_profile_questions'] as $profile_relation) {
                        $header[] = 'Q'.$profile_relation->number.'.'.$action_data['profile_questions'][$profile_relation->id]->question;
                    }
                }

                if ($action_data['conversions']) {
                    foreach ($action_data['conversions'] as $conversion) {
                        $header[] = $conversion->name;
                    }
                }

                /** @var BrandGlobalSettingService $brand_global_setting_service */
                $brand_global_setting_service = $this->getService('BrandGlobalSettingService');
                $public_fid_and_referer = $brand_global_setting_service->getBrandGlobalSetting(
                    $action_data['page_settings']->brand_id,
                    BrandGlobalSettingService::PUBLIC_FID_AND_REFERER
                );
                if ($action_data['is_manager'] || $public_fid_and_referer) {
                    $header = array_merge($header, array('fid', '参加時リファラ'));
                }
                $header = array_merge($header, array('参加した日付', '参加した時刻', '最後まで完了した日付', '最後まで完了した時刻'));

                $step_no = 1;
                foreach ($action_data['cp_actions'] as $cp_action) {
                    $header[] = $step_no++ . '.' . $cp_action->getCpActionDetail()['title'];
                }

                if ($action_data['definitions']) {
                    foreach ($action_data['definitions'] as $def) {
                        if($def->attribute_type != BrandUserAttributeDefinitions::ATTRIBUTE_TYPE_SET) {
                            continue;
                        }
                        $header[] = $def->attribute_name;
                    }
                }
                break;
            case CpAction::TYPE_QUESTIONNAIRE:
                if(!isset($action_data)) return;
                foreach ($action_data['questions_relations'] as $relation) {
                    array_push($header, 'Q' . $relation->number . '.' . $action_data['questions'][$relation->id]->question);
                }
                break;
            case CpAction::TYPE_PHOTO:
                if(!isset($action_data)) return;
                array_push($header,'フォトURL', 'タイトル', 'コメント', '検閲');
                if($action_data['can_share']) {
                    array_push($header, 'FBシェア', 'TWシェア', 'シェアテキスト');
                }
                break;
            case CpAction::TYPE_FACEBOOK_LIKE:
                $header[] = 'いいね状況';
                break;
            case CpAction::TYPE_TWITTER_FOLLOW:
                $header[] = 'フォロー状況';
                break;
            case CpAction::TYPE_INSTANT_WIN:
                array_push($header, '当選・落選', 'チャレンジ回数');
                break;
            case CpAction::TYPE_FREE_ANSWER:
                $header[] = '回答内容';
                break;
            case CpAction::TYPE_SHARE:
                array_push($header, 'シェア状況', 'コメント');
                break;
            case CpAction::TYPE_GIFT:
                array_push($header, '受け取り手会員No', 'グリーティングカード画像URL');
                break;
            case CpAction::TYPE_COUPON:
                $header[] = 'コード';
                break;
            case CpAction::TYPE_CODE_AUTHENTICATION:
                array_push($header, 'コード', '入力日時');
                break;
            case CpAction::TYPE_TWEET:
                array_push($header, 'ツイート状況', 'ツイートURL', 'ツイート内容', '公開状況', '出力状況', 'ツイート画像URL');
                break;
            case CpAction::TYPE_INSTAGRAM_HASHTAG:
                array_push($header,'投稿URL', 'コメント', 'ユーザーネーム', 'ユーザーネーム重複','登録投稿順序', '検閲', '登録日時', '投稿日時');
                break;
            case CpAction::TYPE_YOUTUBE_CHANNEL:
                $header[] = '登録状況';
                break;
            case CpAction::TYPE_RETWEET:
                $header[] = 'リツイート状況';
                break;
            case CpAction::TYPE_ANNOUNCE:
                if (!isset($action_data['get_monipla_user_id'])) return;
                if($action_data['get_monipla_user_id']){
                    $header = array('会員No', 'ユーザのハッシュコード');
                }
                break;
            case CpAction::TYPE_POPULAR_VOTE:
                if (!isset($action_data['can_share'])) return;
                if ($action_data['can_share']) {
                    array_push($header, '投票内容', 'Facebookシェア', 'Twitterシェア', 'シェアテキスト');
                } else {
                    $header[] = '投票内容';
                }
                break;
        }
        return $header;
    }

    /**
     * 取得するファイル毎にデータを返す
     * @param $type
     * @param $join_users
     * @param $page_info
     * @param $action_data
     * @return array|mixed
     */
    public function getActionRows($type, &$join_users, &$page_info, &$action_data) {
        if (!self::$download_file_name[$type]) return;
        if (!$join_users) return;
        if (!$page_info) return;

        if ($type == self::TYPE_PROFILE || $type == CpAction::TYPE_ANNOUNCE) {
            $csv_data = $this->getConcreteData($type, $join_users, $page_info, $action_data);
        } else {
            $data['profile'] = $this->getCpActionProfileData($join_users, $page_info, $action_data);
            $data['status'] = $this->getCpActionStatusData($join_users, $page_info, $action_data);
            $data['concrete'] = $this->getConcreteData($type, $join_users, $page_info, $action_data);
            $csv_data = $this->buildActionData($data);
        }
        return $csv_data;
    }

    /**
     * 配列をマージしてキャンペーンアクションデータを作成する
     * @param $data
     * @return mixed
     */
    private function buildActionData(&$data) {
        if (!$data) return;

        $rows = array();
        foreach ($data['profile'] as $k => $v) {
            $rows[$k] = array_merge($data['profile'][$k], $data['status'][$k], $data['concrete'][$k]);
            unset($data['profile'][$k]);
            unset($data['status'][$k]);
            unset($data['concrete'][$k]);
        }
        $csv_data = $rows;

        return $csv_data;
    }

    /**
     * 参加者プロフィールを取得（キャンペーンアクション共通）
     * @param $join_users
     * @param $page_info
     * @param $action_data
     * @return mixed
     */
    private function getCpActionProfileData(&$join_users, &$page_info, &$action_data) {
        if (!$join_users) return;
        if (!$page_info) return;
        if (!$action_data) return;

        /** @var CpUserListService $cp_user_list_service */
        $cp_user_list_service = $this->getService('CpUserListService');
        $profiles = $cp_user_list_service->getFanListProfileForActionDataDownLoad(
            $join_users['user_id'],
            $page_info['brand_id'],
            $action_data
        );

        $rows = array();
        foreach ($join_users['user_id'] as $key => $value) {
            $rows[$key] = $profiles[$value];
        }

        return $rows;
    }

    /**
     * 参加状況を取得（キャンペーンアクション共通）
     * @param $join_users
     * @param $page_info
     * @param $action_data
     * @return array
     */
    private function getCpActionStatusData(&$join_users, &$page_info, &$action_data) {
        if (!$join_users) return;
        if (!$page_info) return;

        /** @var CpUserListService $cp_user_list_service */
        $cp_user_list_service = $this->getService('CpUserListService');
        $rows = $cp_user_list_service->getFanListStatusForActionDataDownLoad($join_users['cp_user_id'], $action_data['target_action']);

        $data = array();
        foreach ($join_users['cp_user_id'] as $key => $value) {
            if ($rows[$value]) {
                $data[$key] = $rows[$value];
            } else {
                $data[$key]['status'] = '';
                $data[$key]['day'] = '';
                $data[$key]['time'] = '';
            }
        }

        return $data;
    }

    /**
     * ファイル固有のデータを取得
     * @param $type
     * @param $join_users
     * @param $page_info
     * @param $action_data
     * @return array
     */
    private function getConcreteData($type, &$join_users, &$page_info, &$action_data) {
        if (!self::$download_file_name[$type]) return;
        if (!$join_users) return;
        if (!$page_info) return;

        /** @var BrandGlobalSettingService $brand_global_setting_service */
        $brand_global_setting_service = $this->getService('BrandGlobalSettingService');
        $public_fid_and_referer = $brand_global_setting_service->getBrandGlobalSetting(
            $action_data['page_settings']->brand_id,
            BrandGlobalSettingService::PUBLIC_FID_AND_REFERER
        );

        $rows = array();
        switch ($type) {
            case self::TYPE_PROFILE:
                /** @var CpUserListService $cp_user_list_service */
                $cp_user_list_service = $this->getService('CpUserListService');
                $rows = $cp_user_list_service->getFanListProfile(
                    $join_users['user_id'],
                    $page_info['brand_id'],
                    $action_data['profile_questions'],
                    $action_data['conversions'],
                    $action_data['original_sns_account'],
                    $action_data['getSocialLikes'],
                    $action_data['getTwitterFollows'],
                    null, $action_data['has_comment_option']
                );

                $pre_data = array();
                foreach ($join_users['user_id'] as $key => $value) {
                    $pre_data[$key] = $rows[$value];
                    unset($rows[$value]);
                }

                $profile_data = array();
                foreach ($pre_data as $key => $value) {
                    $profile_data[$key]['no'] = $value['no'] ?: '-';
                    if($value['rate'] == BrandsUsersRelationService::BLOCK) {
                        $profile_data[$key]['rate'] = 'ブロック';
                    } elseif($value['rate'] == BrandsUsersRelationService::NON_RATE) {
                        $profile_data[$key]['rate'] = '未評価';
                    } else {
                        $profile_data[$key]['rate'] = '+'.$value['rate'];
                    }

                    if($action_data['page_settings']->privacy_required_sex) {
                        if ($value['sex'] == 'm') {
                            $profile_data[$key]['sex'] = '男性';
                        } elseif ($value['sex'] == 'f') {
                            $profile_data[$key]['sex'] = '女性';
                        } else {
                            $profile_data[$key]['sex'] = '';
                        }
                    } else {
                        $profile_data[$key]['sex'] = '';
                    }

                    if($action_data['page_settings']->privacy_required_birthday) {
                        $profile_data[$key]['age'] = $value['age'];
                    } else {
                        $profile_data[$key]['age'] = '';
                    }

                    if($action_data['page_settings']->privacy_required_address) {
                        $profile_data[$key]['pref_name'] = $value['pref_name'];
                    } else {
                        $profile_data[$key]['pref_name'] = '';
                    }
                    $profile_data[$key]['sa1_profile_page_url'] = $value['sa1_id'] ? '◯' : '';
                    $profile_data[$key]['sa1_friend_count'] = $value['sa1_id'] ? $value['sa1_friend_count'] : '';
                    $profile_data[$key]['sa3_profile_page_url'] = $value['sa3_id'] ? '◯' : '';
                    $profile_data[$key]['sa3_friend_count'] = $value['sa3_id'] ? $value['sa3_friend_count'] : '';
                    $profile_data[$key]['sa8_profile_page_url'] = $value['sa8_id'] ? '◯' : '';
                    $profile_data[$key]['sa7_profile_page_url'] = $value['sa7_id'] ? '◯' : '';
                    $profile_data[$key]['sa7_friend_count'] = $value['sa7_id'] ? $value['sa7_friend_count'] : '';
                    $profile_data[$key]['sa5_profile_page_url'] = $value['sa5_id'] ? '◯' : '';
                    $profile_data[$key]['sa4_profile_page_url'] = $value['sa4_id'] ? '◯' : '';

                    if($action_data['original_sns_account']) {
                        $original_sns_account_array = explode(',',$action_data['original_sns_account']->content);

                        if(in_array(SocialAccountService::SOCIAL_MEDIA_GDO, $original_sns_account_array)) {
                            $profile_data[$key]['sa6_profile_page_url'] = $value['sa6_id'] ? '◯' : '';
                        }

                        if(in_array(SocialAccountService::SOCIAL_MEDIA_LINKEDIN, $original_sns_account_array)) {
                            $profile_data[$key]['sa9_profile_page_url'] = $value['sa9_id'] ? '◯' : '';
                        }
                    }

                    if ($action_data['has_comment_option']) {
                        $profile_data[$key]['from_id'] = $value['from_id'] ?: '';
                        $profile_data[$key]['cmt_count'] = $value['cmt_count'] ?: '';
                    }

                    if($action_data['getSocialLikes']) {
                        foreach($action_data['facebook_accounts'] as $fb_account) {
                            $profile_data[$key]['like_id'.$fb_account->social_media_account_id] = $value['like_id'][$fb_account->social_media_account_id] ? '◯' : '';
                            $profile_data[$key]['likes_count'.$fb_account->social_media_account_id] = $value['likes_count'][$fb_account->social_media_account_id] ? $value['likes_count'][$fb_account->social_media_account_id] : '';
                            $profile_data[$key]['comments_count'.$fb_account->social_media_account_id] = $value['comments_count'][$fb_account->social_media_account_id] ? $value['comments_count'][$fb_account->social_media_account_id] : '';
                        }
                    }
                    if($action_data['getTwitterFollows']) {
                        foreach($action_data['twitter_accounts'] as $tw_account) {
                            $profile_data[$key]['tw_uid'.$tw_account->social_media_account_id] = $value['tw_uid'][$tw_account->social_media_account_id] ? '◯' : '';
                            $profile_data[$key]['retweets_count'.$tw_account->social_media_account_id] = $value['retweets_count'][$tw_account->social_media_account_id] ? $value['retweets_count'][$tw_account->social_media_account_id] : '';
                            $profile_data[$key]['replies_count'.$tw_account->social_media_account_id] = $value['replies_count'][$tw_account->social_media_account_id] ? $value['replies_count'][$tw_account->social_media_account_id] : '';                            
                        }
                    }

                    $profile_data[$key]['cp_entry_count'] = $value['cp_entry_count'];
                    $profile_data[$key]['cp_announce_count'] = $value['cp_announce_count'];
                    $profile_data[$key]['message_delivered_count'] = $value['message_delivered_count'];
                    $profile_data[$key]['message_read_count'] = $value['message_read_count'];
                    $profile_data[$key]['message_read_ratio'] = $value['message_read_ratio'];
                    $profile_data[$key]['optin_flg'] = $value['optin_flg'];
                    $profile_data[$key]['history_by_day'] = date('Y/m/d', strtotime($value['history_by_datetime']));
                    $profile_data[$key]['history_by_time'] = date('H:i', strtotime($value['history_by_datetime']));
                    $profile_data[$key]['history'] = $value['history'];
                    $profile_data[$key]['last_login_date'] = $value['last_login_date'];
                    $profile_data[$key]['login_count'] = $value['login_count'];
                    if($action_data['use_profile_questions']) {
                        $profile_data[$key]['profile_questionnaire_status'] = $value['profile_questionnaire_status'];
                    }
                    foreach ($action_data['profile_questions'] as $profile_question) {
                        $profile_data[$key]['question_'.$profile_question->id] = $value['question_'.$profile_question->id];
                    }
                    foreach ($action_data['conversions'] as $conversion) {
                        $profile_data[$key]['conversion'.$conversion->id] = $value['conversion'.$conversion->id];
                    }
                    if ($action_data['is_manager'] || $public_fid_and_referer) {
                        $profile_data[$key]['fid'] = null;
                        $profile_data[$key]['referrer'] = null;
                    }
                    $profile_data[$key]['entry_day'] = null;
                    $profile_data[$key]['entry_time'] = null;
                    $profile_data[$key]['finish_day'] = null;
                    $profile_data[$key]['finish_time'] = null;
                    unset($pre_data[$key]);
                }

                foreach ($action_data['cp_actions'] as $cp_action) {
                    $status_key = 'status' . $cp_action->id;
                    $rows = $cp_user_list_service->getFanListStatus($join_users['cp_user_id'], $cp_action, true);
                    if ($cp_action->id == $action_data['second_cp_action']->id) {
                        foreach ($join_users['cp_user_id'] as $key => $value) {
                            if ($action_data['is_manager'] || $public_fid_and_referer) {
                                $profile_data[$key]['fid'] = $rows[$value]['fid'] ? : '';
                                $profile_data[$key]['referrer'] = $rows[$value]['referrer'] ? : '';
                            }
                            $profile_data[$key]['entry_day'] = $rows[$value]['entry_day'] ? : "-";
                            $profile_data[$key]['entry_time'] = $rows[$value]['entry_time'] ? : "-";
                            $profile_data[$key][$status_key] = $rows[$value]['status'] ? : '';
                        }
                    }
                    if ($cp_action->id == $action_data['last_cp_action']->id) {
                        foreach ($join_users['cp_user_id'] as $key => $value) {
                            $profile_data[$key]['finish_day'] = $rows[$value]['finish_day'] ? : "-";
                            $profile_data[$key]['finish_time'] = $rows[$value]['finish_time'] ? : "-";
                            $profile_data[$key][$status_key] = $rows[$value]['status'] ? : '';
                        }
                    }
                    if($cp_action->id != $action_data['second_cp_action']->id && $cp_action->id != $action_data['last_cp_action']->id){
                        foreach ($join_users['cp_user_id'] as $key => $value) {
                            $profile_data[$key][$status_key] = $rows[$value]['status'] ? : '';
                        }
                    }
                }

                $attributes = $cp_user_list_service->getFanlistAttribute($join_users['user_id'], $action_data['definitions']);
                if($attributes) {
                    foreach ($action_data['definitions'] as $def) {
                        if($def->attribute_type === BrandUserAttributeDefinitions::ATTRIBUTE_TYPE_SET) {
                            foreach ($join_users['user_id'] as $key => $value) {
                                $profile_data[$key]['definition_'.$def->id] = $attributes[$def->id][$value] ?: '';
                            }
                        }
                    }
                }

                $rows = $profile_data;
                break;
            case CpAction::TYPE_QUESTIONNAIRE:
                if(!isset($action_data)) return;
                /** @var CpUserListService $cp_user_list_service */
                $cp_user_list_service = $this->getService('CpUserListService');
                $answers = $cp_user_list_service->getFanListQuestion($join_users['user_id'], $action_data['questions'], $page_info['brand_id']);

                foreach ($join_users['user_id'] as $key => $value) {
                    foreach ($answers[$value] as $row) {
                        $rows[$key][] = $row;
                    }
                }
                break;
            case CpAction::TYPE_ANNOUNCE:
                if(!isset($action_data)) return;
                /** @var CpUserListService $cp_user_list_service */
                $cp_user_list_service = $this->getService('CpUserListService');
                $replace_tag_service = $this->getService('ReplaceTagService');
                $rows = $cp_user_list_service->getFanListProfile(
                    $join_users['user_id'],
                    $page_info['brand_id'],
                    $action_data['profile_questions'],
                    $action_data['conversions'],
                    $action_data['original_sns_account'],
                    $action_data['getSocialLikes'],
                    $action_data['getTwitterFollows'],
                    null, null,
                    $action_data['get_monipla_user_id']
                );
                $pre_data = array();
                foreach ($join_users['user_id'] as $key => $value) {
                    $pre_data[$key] = $rows[$value];
                    unset($rows[$value]);
                }

                $profile_data = array();

                foreach ($pre_data as $key => $value) {
                    $profile_data[$key]['no'] = $value['no'] ?: '-';
                    $profile_data[$key]['ALLIED_ID_hashed'] = $replace_tag_service->getTag(ReplaceTagService::TYPE_ANNOUNCE_TAG, array(ReplaceTagService::TYPE_ANNOUNCE_TAG => $value['monipla_user_id']));
                    unset($pre_data[$key]);
                }

                $rows = $profile_data;
                break;
            case CpAction::TYPE_PHOTO:
                if(!isset($action_data)) return;
                /** @var CpUserListService $cp_user_list_service */
                $cp_user_list_service = $this->getService('CpUserListService');
                $photo_list = $cp_user_list_service->getPhotoFanListUser(
                    $page_info['action_id'],
                    $join_users['cp_user_id'],
                    $action_data['can_share']
                );

                $pre_data = array();
                foreach ($join_users['cp_user_id'] as $key => $value) {
                    $pre_data[$key] = $photo_list[$value];
                    unset($photo_list[$value]);
                }

                foreach ($pre_data as $key => $value) {
                    $rows[$key]['photo_url'] = $value['photo_url'];
                    $rows[$key]['photo_title'] = $value['photo_title'];
                    $rows[$key]['photo_comment'] = $value['photo_comment'];
                    $rows[$key]['approval_status'] = $value['approval_status'];
                    if ($action_data['can_share']) {
                        $rows[$key]['fb_share'] = '';
                        $rows[$key]['tw_share'] = '';
                        foreach ($value['social_media_type'] as $shareSns) {
                            if ($shareSns == SocialAccount::SOCIAL_MEDIA_FACEBOOK) {
                                $rows[$key]['fb_share'] = 'シェア';
                            } elseif ($shareSns == SocialAccount::SOCIAL_MEDIA_TWITTER) {
                                $rows[$key]['tw_share'] = 'シェア';
                            }
                        }
                        $rows[$key]['share_text'] = $value['share_text'];
                    }
                    unset($pre_data[$key]);
                }
                break;
            case CpAction::TYPE_FACEBOOK_LIKE:
                /** @var CpFacebookLikeLogService $cp_fb_like_log_service */
                $cp_fb_like_log_service = $this->getService('CpFacebookLikeLogService');
                $logs = $cp_fb_like_log_service->getCpFbLikeLogStatuses(
                    $join_users['cp_user_id'],
                    $page_info['action_id']
                );

                foreach ($join_users['cp_user_id'] as $key => $value) {
                    $rows[$key]['status_string'] = $logs[$value];
                }
                break;
            case CpAction::TYPE_TWITTER_FOLLOW:
                /** @var CpTwitterFollowLogService $cp_tw_follow_service */
                $cp_tw_follow_service = $this->getService('CpTwitterFollowLogService');

                $rows = $cp_tw_follow_service->getCpTwFollowLogsByCpUserListAndCpActionId(
                    $join_users['cp_user_id'],
                    $action_data['cp_concrete_action']->id
                );

                foreach ($join_users['cp_user_id'] as $key => $value) {
                    $rows[$key]['status_string'] = $rows[$value]->status_string;
                }
                break;
            case CpAction::TYPE_INSTANT_WIN:
                /** @var InstantWinUserService $cp_instant_win_user_service */
                $cp_instant_win_user_service = $this->getService('InstantWinUserService');
                $instant_wins = $cp_instant_win_user_service->getInstantWinUsersByCpActionIdAndCpUserIds(
                    $page_info['action_id'],
                    $join_users['cp_user_id']
                );

                foreach ($join_users['cp_user_id'] as $key => $value) {
                    $rows[$key]['prize_status'] = $instant_wins[$value]['prize_status'];
                    $rows[$key]['join_count'] = $instant_wins[$value]['join_count'];
                }
                break;
            case CpAction::TYPE_FREE_ANSWER:
                /** @var CpFreeAnswerActionManager $cp_free_answer_action_manager */
                $cp_free_answer_action_manager = new CpFreeAnswerActionManager();
                $answers = $cp_free_answer_action_manager->getAnswersByUserAndQuestion(
                    $join_users['cp_user_id'],
                    $page_info['action_id']);

                foreach ($join_users['cp_user_id'] as $key => $value) {
                    $rows[$key]['free_answer'] = $answers[$value]['free_answer'];
                }
                break;
            case CpAction::TYPE_SHARE:
                /** @var CpShareUserLogService $cp_share_user_log_service */
                $cp_share_user_log_service = $this->getService('CpShareUserLogService');
                $list_share_logs = $cp_share_user_log_service->getCpShareUserLogByCpShareActionIdAndFanListUser(
                    $action_data['cp_share_action_id'],
                    $join_users['cp_user_id']
                );
                $shares = (array)$cp_share_user_log_service->getListShareLogOfUser($list_share_logs);

                foreach ($join_users['cp_user_id'] as $key => $value) {
                    $rows[$key]['share_type'] = $shares[$value]->type ?: '';
                    $rows[$key]['share_text'] = $shares[$value]->text ?: '';
                }
                break;
            case CpAction::TYPE_GIFT:
                /** @var CpUserListService $cp_user_list_service */
                $cp_user_list_service = $this->getService('CpUserListService');
                $gifts = $cp_user_list_service->getGiftFanList(
                    $join_users['cp_user_id'],
                    $action_data['cp_concrete_action'],
                    $page_info['brand_id']
                );

                foreach ($join_users['cp_user_id'] as $key => $value) {
                    $rows[$key]['receiver_no'] = $gifts[$value]['receiver_no'];
                    $rows[$key]['image_url'] = $gifts[$value]['image_url'];
                }
                break;
            case CpAction::TYPE_COUPON:
                /** @var CpUserListService $cp_user_list_service */
                $cp_user_list_service = $this->getService('CpUserListService');
                $coupons = $cp_user_list_service->getFanListCoupon(
                    $join_users['user_id'],
                    $page_info['action_id']
                );

                // tmp_id順に並び替え
                foreach ($join_users['user_id'] as $key => $value) {
                    $rows[$key]['code'] = $coupons[$value]['code'];
                }
                break;
            case CpAction::TYPE_CODE_AUTHENTICATION:
                /** @var CpUserListService $cp_user_list_service */
                $cp_user_list_service = $this->getService('CpUserListService');
                $codes = $cp_user_list_service->getFanListCodeAuth(
                    $join_users['user_id'],
                    $page_info['action_id']
                );

                foreach ($join_users['user_id'] as $key => $value) {
                    $rows[$key]['code'] = implode(',', $codes[$value]['code']);
                    $rows[$key]['used_date'] = implode(',', $codes[$value]['used_date']);
                }
                break;
            case CpAction::TYPE_TWEET:
                /** @var CpUserListService $cp_user_list_service */
                $cp_user_list_service = $this->getService('CpUserListService');
                $tweet_lists = $cp_user_list_service->getFanListTweet(
                    $join_users['cp_user_id'],
                    $action_data['cp_concrete_action_id']
                );

                $rows = array();
                foreach ($join_users['cp_user_id'] as $key => $value) {
                    $rows[$key]['status_string'] = $tweet_lists[$value]['status_string'] ? : '';
                    $rows[$key]['tweet_content_url'] = $tweet_lists[$value]['tweet_content_url'];
                    $rows[$key]['tweet_text'] = $tweet_lists[$value]['tweet_text'];
                    $rows[$key]['tweet_status'] = $tweet_lists[$value]['tweet_status'];
                    $rows[$key]['approval_status'] = $tweet_lists[$value]['approval_status'];
                    $rows[$key]['image_url'] = implode(',', $tweet_lists[$value]['image_url']);
                    unset($tweet_lists[$value]);
                }
                break;
            case CpAction::TYPE_INSTAGRAM_HASHTAG:
                /** @var CpUserListService $cp_user_list_service */
                $cp_user_list_service = $this->getService('CpUserListService');
                $hashtags = $cp_user_list_service->getFanListInstagramHashtag(
                    $page_info['action_id'],
                    $join_users['cp_user_id']
                );

                $pre_data = array();
                foreach ($join_users['cp_user_id'] as $key => $value) {
                    $pre_data[$key] = $hashtags[$value];
                    unset($hashtags[$value]);
                }

                $instagram_hashtags = array();
                foreach ($pre_data as $key => $value) {
                    $instagram_hashtags[$key]['link'] = implode(',', $value['link']);
                    $instagram_hashtags[$key]['post_text'] = implode(',', $value['post_text']);
                    $instagram_hashtags[$key]['user_name'] = $value['user_name'];
                    if(!$value['user_name']) {
                        $instagram_hashtags[$key]['duplicate_flg'] = '';
                    } else {
                        $instagram_hashtags[$key]['duplicate_flg'] = $value['duplicate_flg'] ? 'あり' : 'なし';
                    }
                    $instagram_hashtags[$key]['reverse_post_time'] = implode(',', $value['reverse_post_time']);
                    $instagram_hashtags[$key]['approval_status'] = implode(',', $value['approval_status']);
                    $instagram_hashtags[$key]['created_at'] = $value['created_at'];
                    $instagram_hashtags[$key]['post_date_time'] = implode(',', $value['post_date_time']);
                    unset($pre_data[$key]);
                }

                $rows = $instagram_hashtags;
                break;
            case CpAction::TYPE_YOUTUBE_CHANNEL:
                /** @var CpYoutubeChannelUserLogService $cp_ytch_service */
                $cp_ytch_service = $this->getService('CpYoutubeChannelUserLogService');
                $channels = $cp_ytch_service->getLogsByCpUserIds(
                    $page_info['action_id'],
                    $join_users['cp_user_id']
                );

                foreach ($join_users['cp_user_id'] as $key => $value) {
                    $rows[$key]['status_string'] = $channels[$value]->status_string;
                    unset($channels[$value]);
                }
                break;
            case CpAction::TYPE_RETWEET:
                /** @var RetweetMessageService $cp_retweet_message_service */
                $cp_retweet_message_service = $this->getService('RetweetMessageService');
                $retweets = $cp_retweet_message_service->getRetweetActionStatuses(
                    $action_data['cp_retweet_action_id'],
                    $join_users['cp_user_id']
                );

                foreach ($join_users['cp_user_id'] as $key => $value) {
                    if (!$retweets[$value]) {
                        $rows[$key]['retweet'] = '';
                    } else {
                        $rows[$key] = $retweets[$value];
                    }
                    unset($retweets[$value]);
                }
                break;
            case CpAction::TYPE_POPULAR_VOTE:
                /** @var CpUserListService $cp_user_list_service */
                $cp_user_list_service = $this->getService('CpUserListService');
                $votes = $cp_user_list_service->getFanListPopularVote(
                    $page_info['action_id'],
                    $join_users['cp_user_id'],
                    $action_data['can_share']
                );

                foreach ($join_users['cp_user_id'] as $key => $value) {
                    if (!$votes[$value]) {
                        $rows[$key]['title'] = '';
                    } else {
                        $rows[$key] = $votes[$value];
                    }
                    unset($votes[$value]);
                }
                break;
        }
        return $rows;
    }

    public function createFanListDlHistory($user_id, $brand_id, $conditions, $files) {
        if (!$user_id || !$brand_id) return;
        $history = $this->fan_list_dl_history->createEmptyObject();
        $history->user_id = $user_id;
        $history->brand_id = $brand_id;
        $history->search_condition = json_encode($conditions);
        $history->files = json_encode($files);
        $history->downloaded = self::START;
        return $this->fan_list_dl_history->save($history);
    }

    public function completeFanListDlHistory(FanListDlHistory $history) {
        if (!$history) return;
        $history->downloaded = self::FINISH;
        return $this->fan_list_dl_history->save($history);
    }
}
