<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.lib.parsers.CSVParser');

/**
 * 日別キャンペーンレーポトダウンロード機能
 * Class csv_daily_campaign_report
 */
class csv_daily_campaign_report extends BrandcoGETActionBase {
    public $NeedOption = array();
    public $NeedAdminLogin = true;

    protected $ContainerName = 'csv_daily_campaign_report';
    private $cp_id;
    private $db;
    private $logger;

    public function doThisFirst() {
        $this->cp_id = $this->GET['exts'][0];
        $this->db = aafwDataBuilder::newBuilder();
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
    }

    public function validate() {
        $cp_validator = new CpValidator($this->brand->id);
        if (!$cp_validator->isOwner($this->cp_id)) {
            return false;
        }
        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = $this->getService("CpFlowService");
        $cp = $cp_flow_service->getCpById($this->cp_id);

        $yesterday = date("Y-m-d", strtotime("-1 day"));

        // 公開翌日までダウンロードできない
        if (date("Y-m-d", strtotime($cp->start_date)) > $yesterday) {
            return false;
        }

        return true;
    }

    public function doAction() {
        $login_info = $this->getLoginInfo();
        /** @var UserService $user_service */
        $user_service = $this->getService("UserService");
        $user = $user_service->getUserByMoniplaUserId($login_info['userInfo']->id);

        //ログファイルに記録する
        $this->logger->info("csv_daily_campaign_report START! cp_id=".$this->cp_id. ", brand_id=".$this->getBrand()->id.", user_id=".$user->id);

        try {
            /** @var CpFlowService $cp_flow_service */
            $cp_flow_service = $this->getService("CpFlowService");
            $cp = $cp_flow_service->getCpById($this->cp_id);

            $data_csv = array();
            $data_csv['header'] = $this->buildHeader($cp);
            $data_csv['list'] = $this->buildData($cp);

            // Export csv
            $csv = new CSVParser();
            header("Content-type:" . $csv->getContentType());
            $csv->setCSVFileName("csv_daily_campaign_report_" . $this->cp_id . "_" . date("YmdHis"));
            header($csv->getDisposition());

            print mb_convert_encoding($csv->out($data_csv, 1), 'Shift_JIS', "UTF-8");

            $this->logger->info("csv_daily_campaign_report SUCCESS! cp_id=".$this->cp_id. ", brand_id=".$this->getBrand()->id.", user_id=".$user->id);
            exit();
        } catch (Exception $e) {
            $this->logger->error("csv_daily_campaign_report error! cp_id=".$this->cp_id. ", brand_id=".$this->getBrand()->id.", user_id=".$user->id);
            $this->logger->error($e);
            exit();
        }
    }

    /**
     * CSVファイルのベーダを作成する
     * @param Cp $cp
     * @return array
     */
    private function buildHeader(Cp $cp) {
        $csv_header = array(
            "日付",
            "参加者数(全体)",
            "参加者数(PCのみ)",
            "参加者数(モバイルのみ)",
            "参加者数(新規会員のみ)",
            "参加者数(既存会員のみ)",
            "キャンペーントップPV(全体)",
            "キャンペーントップPV(PCのみ)",
            "キャンペーントップPV(モバイルのみ)",
            "キャンペーントップUU(全体)"
        );
        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = $this->getService('CpFlowService');

        $brand = $this->getBrand();
        //キャンペーンの設定でLPが設定されている場合
        if (preg_match('/^\/' . $brand->directory_name . '\/page\//', $cp->reference_url)) {
            $csv_header[] = "キャンペーンLPPV(全体)";
            $csv_header[] = "キャンペーンLPPV(PCのみ)";
            $csv_header[] = "キャンペーンLPPV(モバイルのみ)";
            $csv_header[] = "キャンペーンLPUU(全体)";
        }

        $facebook_like_actions = $cp_flow_service->getCpActionsByCpIdAndActionType($cp->id, CpAction::TYPE_FACEBOOK_LIKE);
        $twitter_follow_actions = $cp_flow_service->getCpActionsByCpIdAndActionType($cp->id, CpAction::TYPE_TWITTER_FOLLOW);
        $instagram_follow_actions = $cp_flow_service->getCpActionsByCpIdAndActionType($cp->id, CpAction::TYPE_INSTAGRAM_FOLLOW);
        $youtube_chanel_actions = $cp_flow_service->getCpActionsByCpIdAndActionType($cp->id, CpAction::TYPE_YOUTUBE_CHANNEL);

        /** @var BrandSocialAccountService $brand_social_account_service */
        $brand_social_account_service = $this->getService("BrandSocialAccountService");

        foreach ($facebook_like_actions as $facebook_like_action) {
            $concrete_action = $facebook_like_action->getCpActionData();
            /** @var CpFacebookLikeService $cp_facebook_like_service */
            $cp_facebook_like_service = $this->getService("CpFacebookLikeService");
            $facebook_account = $cp_facebook_like_service->getLikeTargeAccount($concrete_action->id);

            $csv_header[] = "新規Facebookいいね！数({$facebook_account->name})";
            $csv_header[] = "既存Facebookいいね！数({$facebook_account->name})";
        }

        foreach ($twitter_follow_actions as $twitter_follow_action) {
            $concrete_action = $twitter_follow_action->getCpActionData();
            /** @var CpTwitterFollowAccountService $cp_twitter_follow_account_service */
            $cp_twitter_follow_account_service = $this->getService("CpTwitterFollowAccountService");
            $cp_follow_twitter_account = $cp_twitter_follow_account_service->getFollowTargetSocialAccount($concrete_action->id);
            $twitter_account = $brand_social_account_service->getBrandSocialAccountById($cp_follow_twitter_account->brand_social_account_id);

            $csv_header[] = "新規Twitterフォロー数({$twitter_account->name})";
            $csv_header[] = "既存Twitterフォロー数({$twitter_account->name})";
        }

        foreach ($instagram_follow_actions as $instagram_follow_action) {
            $concrete_action = $instagram_follow_action->getCpActionData();
            /** @var  CpInstagramFollowEntryService $instagram_follow_entry_service */
            $instagram_follow_entry_service = $this->getService("CpInstagramFollowEntryService");
            $instagram_follow_account = $instagram_follow_entry_service->getTargetAccount($concrete_action->id);
            $instagram_account = $brand_social_account_service->getBrandSocialAccountById($instagram_follow_account->brand_social_account_id);

            $csv_header[] = "新規Instagramフォロー数({$instagram_account->screen_name})";
            $csv_header[] = "既存Instagramフォロー数({$instagram_account->screen_name})";
        }

        foreach ($youtube_chanel_actions as $youtube_chanel_action) {
            $concrete_action = $youtube_chanel_action->getCpActionData();
            /** @var CpYoutubeChannelAccountService $cp_yt_channel_account_service */
            $cp_yt_channel_account_service = $this->getService('CpYoutubeChannelAccountService');
            $youtube_chanel_account = $cp_yt_channel_account_service->getAccount($concrete_action->id);
            $youtube_account = $brand_social_account_service->getBrandSocialAccountById($youtube_chanel_account->brand_social_account_id);

            $csv_header[] = "新規チャンネル登録数({$youtube_account->name})";
            $csv_header[] = "既存チャンネル登録数({$youtube_account->name})";
        }

        return $csv_header;
    }

    /**
     * CSVファイルのデータを作成する
     * @param Cp $cp
     * @return array
     */
    private function buildData(Cp $cp) {
        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = $this->getService('CpFlowService');

        $cp_first_action = $cp_flow_service->getFirstActionOfCp($cp->id);
        list ($total_joined_user_count, $pc_joined_user_count, $sp_joined_user_count, $new_joined_user_count, $member_joined_user_count) = $this->getCpJoinedUserCountInfo($cp_first_action->id);

        list ($cp_page_view, $lp_page_view) = $this->getCpPageViewInfo($cp);

        $facebook_like_actions = $cp_flow_service->getCpActionsByCpIdAndActionType($cp->id, CpAction::TYPE_FACEBOOK_LIKE);
        $twitter_follow_actions = $cp_flow_service->getCpActionsByCpIdAndActionType($cp->id, CpAction::TYPE_TWITTER_FOLLOW);
        $instagram_follow_actions = $cp_flow_service->getCpActionsByCpIdAndActionType($cp->id, CpAction::TYPE_INSTAGRAM_FOLLOW);
        $youtube_chanel_actions = $cp_flow_service->getCpActionsByCpIdAndActionType($cp->id, CpAction::TYPE_YOUTUBE_CHANNEL);

        $fb_like_logs = array();
        foreach ($facebook_like_actions as $facebook_like_action) {
            $fb_like_logs[] = $this->getFacebookLikeCountInfo($facebook_like_action->id);
        }

        $twitter_follow_logs = array();
        foreach ($twitter_follow_actions as $twitter_follow_action) {
            $twitter_follow_logs[] = $this->getTwitterFollowCountInfo($twitter_follow_action->id);
        }

        $instagram_follow_logs = array();
        foreach ($instagram_follow_actions as $instagram_follow_action) {
            $instagram_follow_logs[] = $this->getInstagramFollowCountInfo($instagram_follow_action->id);
        }

        $youtube_chanel_follow_logs = array();
        foreach ($youtube_chanel_actions as $youtube_chanel_action) {
            $youtube_chanel_follow_logs[] = $this->getYoutubeFollowCountInfo($youtube_chanel_action->id);
        }

        $data_csv = array();

        $yesterday = date("Y-m-d", strtotime("-1 day"));

        $date = date("Y-m-d", strtotime($cp->start_date));
        $end_date = date("Y-m-d", strtotime($cp->end_date)) > $yesterday ? $yesterday : date("Y-m-d", strtotime($cp->end_date));

        //トータル
        $total_row = array();
        $total_row[] = "トータル";

        while ($date <= $end_date) {
            $data = array();
            $data[] = $date;
            $data[] = intval($total_joined_user_count[$date]['user_count'] ? : 0);
            $data[] = intval($pc_joined_user_count[$date]['user_count'] ?: 0);
            $data[] = intval($sp_joined_user_count[$date]['user_count'] ?: 0);
            $data[] = intval($new_joined_user_count[$date]['user_count'] ?: 0);
            $data[] = intval($member_joined_user_count[$date]['user_count'] ?: 0);
            $data[] = intval($cp_page_view[$date]['total_view_count'] ?: 0);
            $data[] = intval($cp_page_view[$date]['pc_view_count'] ?: 0);
            $data[] = ($cp_page_view[$date]['sp_view_count'] + $cp_page_view[$date]['tablet_view_count']) ?: 0;
            $data[] = intval($cp_page_view[$date]['user_count'] ?: 0);

            //キャンペーンLPページがある場合
            if (isset($lp_page_view)) {
                $data[] = intval($lp_page_view[$date]['total_view_count'] ?: 0);
                $data[] = intval($lp_page_view[$date]['pc_view_count'] ?: 0);
                $data[] = ($lp_page_view[$date]['sp_view_count'] + $lp_page_view[$date]['tablet_view_count']) ?: 0;
                $data[] = intval($lp_page_view[$date]['user_count'] ?: 0);
            }

            //Facebookいいね！モジュールがある場合
            if (count($facebook_like_actions)) {
                foreach ($fb_like_logs as $fb_like_log) {
                    $data[] = intval($fb_like_log[0][$date]['like_count'] ?: 0);
                    $data[] = intval($fb_like_log[1][$date]['like_count'] ?: 0);
                }
            }

            //Twitterフォローモージュルがある場合
            if (count($twitter_follow_actions)) {
                foreach ($twitter_follow_logs as $twitter_follow_log) {
                    $data[] = intval($twitter_follow_log[0][$date]['follow_count'] ?: 0);
                    $data[] = intval($twitter_follow_log[1][$date]['follow_count'] ?: 0);
                }
            }

            //Instagramフォローモージュルがある場合
            if (count($instagram_follow_actions)) {
                foreach ($instagram_follow_logs as $instagram_follow_log) {
                    $data[] = intval($instagram_follow_log[0][$date]['follow_count'] ?: 0);
                    $data[] = intval($instagram_follow_log[1][$date]['follow_count'] ?: 0);
                }
            }

            //Youtubeチャンネル登録モージュルがある場合
            if (count($youtube_chanel_actions)) {
                foreach ($youtube_chanel_follow_logs as $follow_log) {
                    $data[] = intval($follow_log[0][$date]['follow_count'] ?: 0);
                    $data[] = intval($follow_log[1][$date]['follow_count'] ?: 0);
                }
            }

            //トータル
            for($i = 1; $i < count($data); $i++) {
                $total_row[$i] += $data[$i];
            }

            $data_csv[] = $data;
            $date = date("Y-m-d", strtotime("+1 day", strtotime($date)));
        }

        //キャンペーンレーポトのデータがある場合は、
        if(count($total_row) > 1) {
            $data_csv[] = $total_row;
        }

        return $data_csv;
    }

    /**
     * キャンペーンの参加数情報を取得する
     * @param $last_action_id
     * @return array
     */
    private function getCpJoinedUserCountInfo($last_action_id) {
        //参加者数(全体)
        $conditions = array(
            "cp_action_id" => $last_action_id
        );
        $total_joined_user_count = $this->db->getDailyFinishActionCountByActionId($conditions);
        $total_joined_user_count = $this->prepareDailyData($total_joined_user_count);

        //参加者数(PCのみ)
        $conditions = array(
            "cp_action_id" => $last_action_id,
            "USE_DEVICE_TYPE_CONDITION" => "__ON__",
            "device_type" => CpUserActionStatus::DEVICE_TYPE_OTHERS
        );
        $pc_joined_user_count = $this->db->getDailyFinishActionCountByActionId($conditions);
        $pc_joined_user_count = $this->prepareDailyData($pc_joined_user_count);

        //参加者数(モバイルのみ)
        $conditions = array(
            "cp_action_id" => $last_action_id,
            "USE_DEVICE_TYPE_CONDITION" => "__ON__",
            "device_type" => CpUserActionStatus::DEVICE_TYPE_SP
        );
        $sp_joined_user_count = $this->db->getDailyFinishActionCountByActionId($conditions);
        $sp_joined_user_count = $this->prepareDailyData($sp_joined_user_count);


        //参加者数(新規会員のみ)
        $conditions = array(
            'cp_action_id' => $last_action_id,
            'brand_id' => $this->getBrand()->id,
            'NEW_REGISTERED_USER' => "__ON__"
        );
        $new_joined_user_count = $this->db->countDailyCpJoinedUserByActionId($conditions);
        $new_joined_user_count = $this->prepareDailyData($new_joined_user_count);

        //参加者数(既存会員のみ)
        $conditions = array(
            'cp_action_id' => $last_action_id,
            'brand_id' => $this->getBrand()->id,
            'ALREADY_FAN_USER' => "__ON__"
        );
        $member_joined_user_count = $this->db->countDailyCpJoinedUserByActionId($conditions);
        $member_joined_user_count = $this->prepareDailyData($member_joined_user_count);

        return array($total_joined_user_count, $pc_joined_user_count, $sp_joined_user_count, $new_joined_user_count, $member_joined_user_count);
    }

    /**
     * 日別データを変換する
     * @param $data
     * @return array
     */
    private function prepareDailyData($data) {
        $result = array();

        foreach ($data as $value) {
            $date = $value['count_date'];
            unset($value['count_date']);
            $result[$date] = $value;
        }

        return $result;
    }

    /**
     * キャンペーンページビュー情報を取得する
     * @param Cp $cp
     * @return array
     */
    private function getCpPageViewInfo(Cp $cp) {
        //キャンペーントップページのページビューを取得する
        $conditions = array(
            "cp_id" => $cp->id,
            "page_view_type" => CpPageView::TYPE_CP_PAGE
        );
        $cp_page_view = $this->db->getDailyCpPageViewByCpIdAndType($conditions);
        $cp_page_view = $this->prepareDailyData($cp_page_view);

        $brand = $this->getBrand();

        //キャンペーンの設定でLPが設定されている場合
        $lp_page_view = null;
        if (preg_match('/^\/' . $brand->directory_name . '\/page\//', $cp->reference_url)) {
            $conditions = array(
                "cp_id" => $cp->id,
                "page_view_type" => CpPageView::TYPE_LP_PAGE
            );
            $lp_page_view = $this->db->getDailyCpPageViewByCpIdAndType($conditions);
            $lp_page_view = $this->prepareDailyData($lp_page_view);
        }

        return array($cp_page_view, $lp_page_view);
    }

    /**
     * Facebookいいね！数を取得する
     * @param $cp_action_id
     * @return array
     */
    private function getFacebookLikeCountInfo($cp_action_id) {
        //新規Facebookいいね！数
        $conditions = array(
            "cp_action_id" => $cp_action_id,
            "USE_STATUS_CONDITION" => "__ON__",
            "status" => EngagementLog::LIKED_FLG
        );
        $new_fb_like_count = $this->db->getDailyFacebookLikeCountLogByActionId($conditions);
        $new_fb_like_count = $this->prepareDailyData($new_fb_like_count);

        //既存Facebookいいね！数
        $conditions = array(
            "cp_action_id" => $cp_action_id,
            "USE_STATUS_CONDITION" => "__ON__",
            "status" => EngagementLog::PREV_LIKED_FLG
        );
        $fb_liked_count = $this->db->getDailyFacebookLikeCountLogByActionId($conditions);
        $fb_liked_count = $this->prepareDailyData($fb_liked_count);

        return array($new_fb_like_count, $fb_liked_count);
    }

    /**
     * Twitterフォロー数を取得する
     * @param $cp_action_id
     * @return array
     */
    private function getTwitterFollowCountInfo($cp_action_id) {
        //新規Twitterフォロー数
        $conditions = array(
            "cp_action_id" => $cp_action_id,
            "USE_STATUS_CONDITION" => "__ON__",
            "status" => CpTwitterFollowActionManager::FOLLOW_ACTION_EXEC
        );
        $new_tw_follow_count = $this->db->getDailyTwitterFollowLogCountByActionId($conditions);
        $new_tw_follow_count = $this->prepareDailyData($new_tw_follow_count);

        //既存Twitterフォロー数
        $conditions = array(
            "cp_action_id" => $cp_action_id,
            "USE_STATUS_CONDITION" => "__ON__",
            "status" => CpTwitterFollowActionManager::FOLLOW_ACTION_ALREADY
        );
        $tw_following_count = $this->db->getDailyTwitterFollowLogCountByActionId($conditions);
        $tw_following_count = $this->prepareDailyData($tw_following_count);

        return array($new_tw_follow_count, $tw_following_count);
    }

    /**
     * Instagramフォロー数を取得する
     * @param $cp_action_id
     * @return array
     */
    private function getInstagramFollowCountInfo($cp_action_id) {
        //新規Instagramフォロー数
        $conditions = array(
            "cp_action_id" => $cp_action_id,
            "USE_STATUS_CONDITION" => "__ON__",
            "status" => CpInstagramFollowUserLog::FOLLOWED
        );
        $new_ig_follow_count = $this->db->getDailyInstagramFollowLogCountByActionId($conditions);
        $new_ig_follow_count = $this->prepareDailyData($new_ig_follow_count);

        //既存Instagramフォロー数
        $conditions = array(
            "cp_action_id" => $cp_action_id,
            "USE_STATUS_CONDITION" => "__ON__",
            "status" => CpInstagramFollowUserLog::FOLLOWING
        );
        $ig_following_count = $this->db->getDailyInstagramFollowLogCountByActionId($conditions);
        $ig_following_count = $this->prepareDailyData($ig_following_count);

        return array($new_ig_follow_count, $ig_following_count);
    }

    /**
     * Youtubeチャンネル登録数を取得する
     * @param $cp_action_id
     * @return array
     */
    private function getYoutubeFollowCountInfo($cp_action_id) {
        //新規新規チャンネル登録数
        $conditions = array(
            "cp_action_id" => $cp_action_id,
            "USE_STATUS_CONDITION" => "__ON__",
            "status" => CpYoutubeChannelUserLog::STATUS_FOLLOWED
        );
        $new_yt_follow_count = $this->db->getDailyYoutubeChannelUserLogCountByActionId($conditions);
        $new_yt_follow_count = $this->prepareDailyData($new_yt_follow_count);

        //既存新規チャンネル登録数
        $conditions = array(
            "cp_action_id" => $cp_action_id,
            "USE_STATUS_CONDITION" => "__ON__",
            "status" => CpYoutubeChannelUserLog::STATUS_FOLLOWING
        );

        $yt_following_count = $this->db->getDailyYoutubeChannelUserLogCountByActionId($conditions);
        $yt_following_count = $this->prepareDailyData($yt_following_count);

        return array($new_yt_follow_count, $yt_following_count);
    }
}