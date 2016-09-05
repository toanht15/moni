<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.lib.parsers.CSVParser');
AAFW::import('jp.aainc.classes.entities.CpUserActionStatus');

class csv_join_user_list extends BrandcoGETActionBase {

    protected $ContainerName = 'csv_join_user_list';

    public $NeedOption = array();
    public $NeedAdminLogin = true;
    protected $searchCondition;
    protected $orderCondition;

//メールの送信は一旦封じる
//    const NOTIFICATION_MAIL = 'sato.tomoaki@aainc.co.jp';

    public function doThisFirst() {
        ini_set('max_execution_time', 3600);

        $this->Data['cp_id'] = $this->GET['exts'][0];
        $this->Data['action_id'] = $this->GET['exts'][1];
        $this->Data['brand'] = $this->getBrand();
        $this->searchCondition = $this->getSearchConditionSession($this->Data['cp_id']);
        $this->orderCondition = $this->getBrandSession('orderCondition');
    }

    public function validate() {
        $cp_validator = new CpValidator($this->Data['brand']->id);
        if (!$cp_validator->isOwner($this->Data['cp_id'])) {
            return false;
        }
        if (!$cp_validator->isOwnerOfAction($this->Data['action_id'])) {
            return false;
        }
        return true;
    }

    function doAction() {
        /** @var BrandPageSettingService $brand_page_settings_service */
        $brand_page_setting_service = $this->createService('BrandPageSettingService');
        $brand_page_setting = $brand_page_setting_service->getPageSettingsByBrandId($this->Data['brand']->id);
        /** @var CpUserService $cp_user_service */
        $cp_user_service = $this->createService("CpUserService");
        /** @var BrandGlobalSettingService $brand_global_setting_service */
        $brand_global_setting_service = $this->createService('BrandGlobalSettingService');
        $can_download_brand_user_list = $brand_global_setting_service->getBrandGlobalSetting($this->Data['brand']->id, BrandGlobalSettingService::CAN_DOWNLOAD_BRAND_USER_LIST);
        $original_sns_account = $brand_global_setting_service->getBrandGlobalSetting($this->Data['brand']->id, BrandGlobalSettingService::ORIGINAL_SNS_ACCOUNTS);

        /** @var BrandSocialAccountService $brand_social_account_service */
        $brand_social_account_service = $this->createService('BrandSocialAccountService');
        $facebook_accounts = $brand_social_account_service->getSocialAccountsByBrandId($this->Data['brand']->id,SocialApps::PROVIDER_FACEBOOK);

        /** @var SocialLikeService $social_like_service */
        $social_like_service = $this->createService('SocialLikeService');

        /** @var ConversionService $conversion_service */
        $conversion_service = $this->createService('ConversionService');
        $conversions = $conversion_service->getConversionsByBrandId($this->Data['brand']->id);

        $can_view = $this->Data['pageStatus']['manager']->canView();

        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = $this->createService('CpFlowService');

        try {
            if (!$this->Data['pageStatus']['manager']->id && !$can_download_brand_user_list) {
                return 404;
            }
            $params = array(
                'brand_name' => $this->Data['brand']->name,
                'url' => Util::constructBaseURL($this->Data['brand']->id,$this->Data['brand']->directory_name).'admin-cp/show_user_list/'.$this->Data['cp_id'].'/'.$this->Data['action_id'],
                'download_time' => date('Y-m-d H:i:s'),
            );
            $data_csv = array();
            if($can_view) {
                array_push($data_csv, 'ユーザ名','メールアドレス');
            }
            array_push($data_csv, '評価', '会員No');

            //Anncounce Action
            $cp = $cp_flow_service->getCpById($this->Data['cp_id']);
            $isShowDuplicateAddress = false;
            $cpAction = $cp_flow_service->getCpActionById($this->Data['action_id']);
            if(!$cp->isLimitCp() && $cp_flow_service->isExistShippingAddressActionInFirstGroup($this->Data['cp_id']) && $cp_flow_service->isExistAnnounceDeliveryActionFromSecondGroup($this->Data['cp_id']) && $cp_flow_service->isExistAnnounceActionInGroup($cpAction->cp_action_group_id)){
                $isShowDuplicateAddress = true;
                array_push($data_csv, '住所重複');
            }

            array_push($data_csv, '登録期間','登録日付','登録時刻','Facebook','Facebook友達数','Twitter','Twitterフォロワー数','LINE','Instagram','Instagramフォロワー数','Yahoo!','Google');

            // GDOの場合
            if($original_sns_account) {
                if($original_sns_account->content == SocialAccountService::SOCIAL_MEDIA_GDO) {
                    array_push($data_csv, 'GDO');
                }
            }

            //FBいいねステータス
            if(!$social_like_service->isEmptyTable()) {
                foreach($facebook_accounts as $facebook_account) {
                    array_push($data_csv, $this->cutLongText($facebook_account->name, 20).'いいね!');
                }
            }

            array_push($data_csv, '友達数合計','最終ログイン','ログイン回数','参加した日付','参加した時刻','参加完了した日付','参加完了した時刻');

            array_push($data_csv, '性別');

            array_push($data_csv, '都道府県');

            array_push($data_csv, '年齢');

            array_push($data_csv, 'キャンペーン参加回数','キャンペーン当選回数','メッセージ受信数','メッセージ閲覧数','メッセージ閲覧率');
            array_push($data_csv, 'fid');
            array_push($data_csv, 'ref');

            /** @var CpQuestionnaireService $profile_questionnaire_service */
            $profile_questionnaire_service = $this->createService('CpQuestionnaireService', CpQuestionnaireService::TYPE_PROFILE_QUESTION);
            $profile_question_relations = $profile_questionnaire_service->getPublicProfileQuestionRelationByBrandId($this->Data['brand']->id);
            $use_profile_questions = $profile_questionnaire_service->useProfileQuestion($profile_question_relations);

            $profile_questions = array();
            if($use_profile_questions) {
                array_push($data_csv, 'アンケート');

                foreach ($use_profile_questions as $profile_relation) {
                    $profile_question = $profile_questionnaire_service->getQuestionById($profile_relation->question_id);
                    $profile_questions[] = array('status' => 'Q'.$profile_relation->number.'.'.$profile_question->question);
                    array_push($data_csv, 'Q'.$profile_relation->number.'.'.$profile_question->question);
                }
            }

            if(count($profile_questions) > 0) {
                $params['DOWNLOAD_ITEM'][] = array(
                    'ITEM' => 'プロフィールアンケート',
                    'PROFILE_QUESTION' => $profile_questions,
                );
            }

            $conversion_names = array();
            //コンバージョン名追加
            if ($conversions) {
                foreach ($conversions as $conversion) {
                    $conversion_names[] = array('status' => $conversion->name);
                    array_push($data_csv, $conversion->name);
                }
            }

            if(count($conversion_names) > 0) {
                $params['DOWNLOAD_ITEM'][] = array(
                    'ITEM' => 'コンバージョン',
                    'CONVERSION' => $conversion_names,
                );
            }

            // 参加状況のヘッダー取得

            $cp_actions = $cp_flow_service->getCpActionsByCpId($this->Data['cp_id']);

            $item_no = 1;
            $items = array();
            foreach($cp_actions as $action){
                $cp_action_data = $action->getCpActionData();
                $items[] = array('status' => $item_no.'.'.$cp_action_data->title.'('.$action->getCpActionDetail()['title'].')');
                array_push($data_csv, $item_no.'.'.$cp_action_data->title);
                $item_no++;
            }

            if(count($items) > 0) {
                $params['DOWNLOAD_ITEM'][] = array(
                    'ITEM' => '参加状況',
                    'JOIN_STATUS' => $items,
                );
            }

            $action_no = 1;
            $action_questions = array();
            $action_photos = array();
            $action_shares = array();
            foreach($cp_actions as $action){
                // アンケート回答状況のヘッダー取得
                if($action->type == CpAction::TYPE_QUESTIONNAIRE) {
                    /** @var CpQuestionnaireService $cp_questionnaire_service */
                    $cp_questionnaire_service = $this->getService('CpQuestionnaireService', CpQuestionnaireService::TYPE_CP_QUESTION);
                    // アンケートの設問を並び順通りに取得
                    $questionnaire_action = $cp_questionnaire_service->getCpQuestionnaireAction($action->id);
                    if ($questionnaire_action->id) {
                        $relations = $cp_questionnaire_service->getRelationsByQuestionnaireActionId($questionnaire_action->id);
                        foreach ($relations as $relation) {
                            $question = $cp_questionnaire_service->getQuestionById($relation->question_id);
                            $action_questions[] = array('status' => $action_no . '-Q' . $relation->number . '.' . $question->question);
                            array_push($data_csv, $action_no . '-Q' . $relation->number . '.' . $question->question);
                        }
                    }
                }

                if($action->type == CpAction::TYPE_SHARE) {
                    $action_shares[] = array('status' => 'シェア状況');
                    $action_shares[] = array('status' => 'シェアコメント');
                    array_push($data_csv, 'シェア状況');
                    array_push($data_csv, 'シェアコメント');
                }

                // 写真投稿回答状況のヘッダー取得
                if ($action->type == CpAction::TYPE_PHOTO) {
                    /** @var CpPhotoActionService $cp_photo_action_service */
                    $cp_photo_action_service = $this->getService('CpPhotoActionService');
                    /** @var PhotoUserService $photo_user_service */
                    $photo_user_service = $this->getService('PhotoUserService');

                    $cp_photo_action = $cp_photo_action_service->getCpPhotoAction($action->id);
                    if ($cp_photo_action->title_required) {
                        $action_photos[] = array('status' => $action_no . '-タイトル');
                        array_push($data_csv, $action_no . '-写真投稿タイトル');
                    }

                    if ($cp_photo_action->comment_required) {
                        $action_photos[] = array('status' => $action_no . '-コメント');
                        array_push($data_csv, $action_no . '-写真投稿コメント');
                    }

                    if ($cp_photo_action->fb_share_required) {
                        $action_photos[] = array('status' => $action_no . '-Facebook');
                        array_push($data_csv, $action_no . '-写真投稿 Facebook');
                    }

                    if ($cp_photo_action->tw_share_required) {
                        $action_photos[] = array('status' => $action_no . '-Twitter');
                        array_push($data_csv, $action_no . '-写真投稿 Twitter');
                    }

                    if ($cp_photo_action->panel_hidden_flg) {
                        $action_photos[] = array('status' => $action_no . '-検閲');
                        array_push($data_csv, $action_no . '-写真投稿 検閲');
                    }
                }
                $action_no += 1;

                if ($action->type == CpAction::TYPE_INSTAGRAM_HASHTAG) {
                    /** @var CpInstagramHashtagActionService $cp_instagram_hashtag_action_service */
                    $cp_instagram_hashtag_action_service = $this->getService('CpInstagramHashtagActionService');

                    /** @var InstagramHashtagUserService $instagram_hashtag_user_service */
                    $instagram_hashtag_user_service = $this->getService('InstagramHashtagUserService');

                    if (!$cp_instagram_hashtag_action) {
                        $cp_instagram_hashtag_action = $cp_instagram_hashtag_action_service->getCpInstagramHashtagActionByCpActionId($action->id);
                    }

                    array_push($data_csv, $action_no . '-Instagram投稿 写真');
                    array_push($data_csv, $action_no . '-Instagram投稿 コメント');
                    array_push($data_csv, $action_no . '-Instagram投稿 ユーザネーム');
                    array_push($data_csv, $action_no . '-Instagram投稿 ユーザネーム重複');
                    array_push($data_csv, $action_no . '-Instagram投稿 登録投稿順序');
                    if ($cp_instagram_hashtag_action->approval_flg) {
                        array_push($data_csv, $action_no . '-Instagram投稿 検閲');
                    }
                    array_push($data_csv, $action_no . '-Instagram投稿 登録日時');
                    array_push($data_csv, $action_no . '-Instagram投稿 投稿日時');
                }
            }

            if(count($action_questions) > 0) {
                $params['DOWNLOAD_ITEM'][] = array(
                    'ITEM' => 'アンケート',
                    'QUESTION' => $action_questions,
                );
            }

            if(count($action_shares) > 0) {
                $params['DOWNLOAD_ITEM'][] = array(
                    'ITEM' => 'シェア',
                    'SHARE' => $action_shares,
                );
            }

            if(count($action_photos) > 0) {
                $params['DOWNLOAD_ITEM'][] = array(
                    'ITEM' => '写真投稿',
                    'PHOTO' => $action_photos,
                );
            }

            /** @var $brand_service BrandService */
            $brand_service = $this->createService("BrandService");
            $defs = $brand_service->getCustomAttributeDefinitions($this->Data['brand']->id);
            foreach ($defs as $def) {
                $params['DOWNLOAD_ITEM'][] = array(
                    'ITEM' => $def->attribute_name
                );
                array_push($data_csv, $def->attribute_name);
            }

            // Export csv
            $csv = new CSVParser();

            header("Content-type:" . $csv->getContentType());
            header($csv->getDisposition());
            $array_data = $csv->out(array('data' => $data_csv), null, true, true);
            print mb_convert_encoding($array_data, 'Shift_JIS', "UTF-8");

            /** @var BrandsUsersRelationService $brands_users_relation_service */
            $brands_users_relation_service = $this->createService('BrandsUsersRelationService');
            /** @var CpUserListService $cp_user_list_service */
            $cp_user_list_service = $this->createService('CpUserListService');

            $page_info = array(
                'cp_id'     => $this->Data['cp_id'],
                'action_id' => $this->Data['action_id'],
                'brand_id'  => $this->Data['brand']->id,
                'tab_no' => CpCreateSqlService::TAB_PAGE_PROFILE
            );
            $fan_list_users = $cp_user_list_service->getAllFanList($page_info, $this->searchCondition, $this->orderCondition, '__NOFETCH__', true);

            /** @var CpUserActionStatusService $cp_user_status_service */
            $cp_user_status_service = $this->createService('CpUserActionStatusService');

            $download_count = $fan_list_users['resource'] ? $fan_list_users['resource']->num_rows : 0;
            aafwLog4phpLogger::getHipChatLogger()->info('csv_join_user_list brand_id = '.$this->Data['brand']->id.' cp_id = '.$this->Data['cp_id'].' action_id = '.$this->Data['action_id'].' Start, Count : '.$download_count);

            $params['download_count'] = $download_count;
//メールの送信は一旦封じる
//            if (!$this->Data['pageStatus']['manager']->id && count($params)) {
//                $mail = new MailManager();
//                $mail->loadMailContent('notification_csv_download');
//                $mail->sendNow(self::NOTIFICATION_MAIL, $params);
//            }

            $db = new aafwDataBuilder();

            /** @var CpShareActionService $cp_share_action_service */
            $cp_share_action_service = $this->getService('CpShareActionService');
            /** @var CpShareUserLogService $cp_share_user_log_service */
            $cp_share_user_log_service = $this->getService('CpShareUserLogService');

            while($fan_list_user = $db->fetch($fan_list_users)) {
                $user_info = $fan_list_user->getBrandcoUser();
                $data_csv = array();

                if($can_view) {
                    $data_csv[] = $user_info->name; //ユーザ名
                    $data_csv[] = $user_info->mail_address; //メールアドレス(ファン一覧にはないが、csvでは対応)
                }
                if($fan_list_user->rate == BrandsUsersRelationService::BLOCK) {
                    $data_csv[] = 'ブロック';
                } elseif($fan_list_user->rate == BrandsUsersRelationService::NON_RATE) {
                    $data_csv[] = '未評価';
                } else {
                    $data_csv[] = '+'.$fan_list_user->rate;
                }
                $data_csv[] = $fan_list_user->no > 0 ? intval($fan_list_user->no) : '-'; //会員No

                if($isShowDuplicateAddress){
                    $data_csv[] = $fan_list_user->shipping_address_user_duplicate_count ? intval($fan_list_user->shipping_address_user_duplicate_count) : '未取得';
                }

                $data_csv[] = $brands_users_relation_service->getHistorySummary($fan_list_user->created_at);//登録期間
                $data_csv[] = date("Y/m/d", strtotime($fan_list_user->created_at));//登録日付
                $data_csv[] = date("H:i", strtotime($fan_list_user->created_at));//登録時間

                $fb_social_account = $fan_list_user->getFacebookSocialAccounts();
                $data_csv[] = $fb_social_account ? '◯' : ''; //Facebook
                $data_csv[] = $fb_social_account && $fb_social_account->friend_count >= 0 ? intval($fb_social_account->friend_count) : '';

                $tw_social_account = $fan_list_user->getTwitterSocialAccounts();
                $data_csv[] = $tw_social_account ? '◯' : '';  //Twitter
                $data_csv[] = $tw_social_account && $tw_social_account->friend_count >= 0  ? intval($tw_social_account->friend_count) : '';

                $data_csv[] = $fan_list_user->getLineSocialAccounts() ? '◯' : '';  // LINE

                $ig_social_account = $fan_list_user->getInstagramSocialAccounts();
                $data_csv[] = $ig_social_account ? '◯' : '';  //Instagram
                $data_csv[] = $ig_social_account && $ig_social_account->friend_count >= 0 ? intval($ig_social_account->friend_count) : '';

                $data_csv[] = $fan_list_user->getYahooSocialAccounts() ? '◯' : '';    //Yahoo
                $data_csv[] = $fan_list_user->getGoogleSocialAccounts() ? '◯' : '';   //Google
                // GDOの場合
                if($original_sns_account) {
                    if($original_sns_account->content == SocialAccountService::SOCIAL_MEDIA_GDO) {
                        $data_csv[] = $fan_list_user->getGdoSocialAccounts() ? '◯' : '';   //GDO
                    }
                }
                //FBいいねステータス
                if(!$social_like_service->isEmptyTable()) {
                    foreach($facebook_accounts as $facebook_account) {
                        if($social_like_service->isLikedPage($user_info->monipla_user_id,SocialAccount::SOCIAL_MEDIA_FACEBOOK,$facebook_account->social_media_account_id) && $fb_social_account) {
                            $data_csv[] = '◯';
                        } else {
                            $data_csv[] = '';
                        }
                    }
                }
                $sns_friend_sum = ($fb_social_account && $fb_social_account->friend_count >= 0 ? $fb_social_account->friend_count : 0) +
                    ($tw_social_account && $tw_social_account->friend_count >= 0 ? $tw_social_account->friend_count : 0) +
                    ($ig_social_account && $ig_social_account->friend_count >= 0 ? $ig_social_account->friend_count : 0);
                $data_csv[] = $sns_friend_sum;

                $data_csv[] = $brands_users_relation_service->getLastLoginSummary($fan_list_user->last_login_date); //最終ログイン
                $data_csv[] = intval($fan_list_user->login_count); //ログイン回数

                $first_cp_action = $cp_flow_service->getFirstActionOfCp($this->Data['cp_id']);
                $cp_user_info = $cp_user_service->getCpUserByCpIdAndUserId($this->Data['cp_id'],$user_info->id);
                $last_cp_action = $cp_flow_service->getMaxStepNo($first_cp_action->cp_action_group_id);

                if($cp_user_info == null) {
                    $data_csv[] = "-";
                    $data_csv[] = "-";
                    $data_csv[] = "-";
                    $data_csv[] = "-";
                } else {
                    $first_cp_user_status = $cp_user_status_service->getCpUserActionStatus($cp_user_info->id, $first_cp_action->id);
                    $last_cp_user_status = $cp_user_status_service->getCpUserActionStatus($cp_user_info->id, $last_cp_action->id);
                    if ($first_cp_user_status->status == CpUserActionStatus::JOIN) {
                        $data_csv[] = date('Y/m/d', strtotime($first_cp_user_status->updated_at));
                        $data_csv[] = date('H:i', strtotime($first_cp_user_status->updated_at));
                    }else{
                        $data_csv[] = "-";
                        $data_csv[] = "-";
                    }
                    if ($last_cp_user_status->status == CpUserActionStatus::JOIN) {
                        $data_csv[] = date('Y/m/d', strtotime($last_cp_user_status->updated_at));
                        $data_csv[] = date('H:i', strtotime($last_cp_user_status->updated_at));
                    }else{
                        $data_csv[] = "-";
                        $data_csv[] = "-";
                    }
                }

                if($brand_page_setting->privacy_required_sex || $brand_page_setting->privacy_required_birthday) {
                    $user_attribute = $fan_list_user->getUserAttributeInfo();
                }

                if($brand_page_setting->privacy_required_sex) {
                    if ($user_attribute[0] == 'm') { //性別
                        $data_csv[] = '男性';
                    } elseif ($user_attribute[0] == 'f') {
                        $data_csv[] = '女性';
                    } else {
                        $data_csv[] = '';
                    }
                } else {
                    $data_csv[] = '';
                }

                if($brand_page_setting->privacy_required_address) {
                    $data_csv[] = $fan_list_user->getPrefecture() ? $fan_list_user->getPrefecture() : ''; //都道府県
                } else {
                    $data_csv[] = '';
                }

                if($brand_page_setting->privacy_required_birthday) {
                    $data_csv[] = $user_attribute[1] ? intval($user_attribute[1]) : ''; //年齢
                } else {
                    $data_csv[] = '';
                }

                $data_csv[] = $fan_list_user->cp_entry_count ? intval($fan_list_user->cp_entry_count) : 0;
                $data_csv[] = $fan_list_user->cp_announce_count ? intval($fan_list_user->cp_announce_count) : 0;
                $data_csv[] = $fan_list_user->message_delivered_count ? intval($fan_list_user->message_delivered_count) : 0;
                $data_csv[] = $fan_list_user->message_read_count ? intval($fan_list_user->message_read_count) : 0;
                if(!$fan_list_user->message_delivered_count || $fan_list_user->message_delivered_count == 0 ||
                    !$fan_list_user->message_read_count || $fan_list_user->message_read_count == 0) {
                    $data_csv[] = '0%';
                } else if($fan_list_user->message_delivered_count == $fan_list_user->message_read_count) {
                    $data_csv[] = '100%';
                } else {
                    $data_csv[] = number_format(($fan_list_user->message_read_count / $fan_list_user->message_delivered_count) * 100,1).'%';
                }

                $cp_user = $this->getModel("CpUsers")->findOne(array('id' => $fan_list_user->cp_user_id));
                $data_csv[] = $cp_user->from_id; //fid
                $data_csv[] = $cp_user->referrer; //リファラー

                // 参加時アンケート
                if($use_profile_questions) {
                    $data_csv[] = $brands_users_relation_service->getProfileQuestionnaireStatus($fan_list_user->personal_info_flg);

                    foreach ($use_profile_questions as $profile_relation) {
                        $question = $profile_questionnaire_service->getQuestionById($profile_relation->question_id);

                        if($question->type_id == QuestionTypeService::FREE_ANSWER_TYPE) {
                            $profile_answer = $profile_questionnaire_service->getFreeAnswer($fan_list_user->brands_users_relations_id, $profile_relation->id)->answer_text;
                        } else {
                            $profile_answer = $profile_questionnaire_service->getChoiceAnswer($fan_list_user->brands_users_relations_id, $profile_relation->id);
                        }
                        $data_csv[] = $profile_answer !== '' ? $profile_answer : '';
                    }
                }

                // コンバージョン情報取得
                if ($conversions) {
                    foreach ($conversions as $conversion) {
                        $data_csv[] = (int) $conversion_service->countUserConversionByUserIdAndConversionId($fan_list_user->user_id, $conversion->id, $this->Data['brand']->id);
                    }
                }

                // キャンペーン参加状況
                foreach($cp_actions as $action){
                    if($fan_list_user->cp_user_id) {
                        switch ($action->type) {
                            case CpAction::TYPE_COUPON:
                                $data_csv[] = $cp_user_status_service->getCouponActionStatusByCpUserIdAndCpActionId($fan_list_user->cp_user_id, $action->id);
                                break;
                            case CpAction::TYPE_INSTANT_WIN:
                                $data_csv[] = $cp_user_status_service->getInstantWinActionStatusByCpUserIdAndCpActionId($fan_list_user->cp_user_id, $action->id);
                                break;
                            case CpAction::TYPE_FREE_ANSWER:
                                $data_csv[] = $cp_user_status_service->getFreeAnswerStatusByCpUserIdAndCpActionId($fan_list_user->cp_user_id, $action->id);
                                break;
                            case CpAction::TYPE_ANNOUNCE_DELIVERY:
                                $data_csv[] = $cp_user_status_service->getAnnounceDeliveredStatusByCpUserIdAndCpActionId($fan_list_user->cp_user_id, $action->id);
                                break;
                            default:
                                $data_csv[] = $cp_user_status_service->getStatusByCpUserIdAndCpActionId($fan_list_user->cp_user_id, $action->id);
                                break;
                        }
                    } else {
                        $data_csv[] = CpUserActionStatus::STATUS_UNSENT;
                    }
                }

                foreach($cp_actions as $action){

                    // アンケート回答状況
                    if($action->type == CpAction::TYPE_QUESTIONNAIRE) {
                        // アンケートの設問を並び順通りに取得
                        $questionnaire_action = $cp_questionnaire_service->getCpQuestionnaireAction($action->id);
                        if ($questionnaire_action->id) {
                            $relations = $cp_questionnaire_service->getRelationsByQuestionnaireActionId($questionnaire_action->id);
                            foreach ($relations as $relation) {
                                $question = $cp_questionnaire_service->getQuestionById($relation->question_id);
                                if (QuestionTypeService::isChoiceQuestion($question->type_id)) {

                                    $answer = $cp_questionnaire_service->getChoiceAnswer($fan_list_user->brands_users_relations_id, $relation->id);
                                } else {
                                    $answer = $cp_questionnaire_service->getFreeAnswer($fan_list_user->brands_users_relations_id, $relation->id)->answer_text;
                                }
                                $data_csv[] = $answer !== '' ? $answer : '';
                            }
                        }
                    }

                    // シェア状況
                    if($action->type == CpAction::TYPE_SHARE) {
                        $cp_share_action = $cp_share_action_service->getCpShareActionById($action->id);

                        if($cp_share_action->id) {
                            $cp_share_user_log = $cp_share_user_log_service->getCpShareUserLogWithStatusStringByIds($fan_list_user->cp_user_id, $cp_share_action->id);

                            $data_csv[] = $cp_share_user_log->type !== '' ? $cp_share_user_log->type : '';
                            $data_csv[] = $cp_share_user_log->text !== '' ? $cp_share_user_log->text : '';
                        }
                    }

                    // 写真投稿回答状況
                    if ($action->type == CpAction::TYPE_PHOTO) {
                        $photo_user = $photo_user_service->getPhotoUserByIds($action->id, $fan_list_user->cp_user_id);

                        $cp_photo_action = $cp_photo_action_service->getCpPhotoAction($action->id);
                        if ($cp_photo_action->title_required) {
                            $data_csv[] = $photo_user ? $photo_user->photo_title : '';
                        }

                        if ($cp_photo_action->comment_required) {
                            $data_csv[] = $photo_user ? $photo_user->photo_comment : '';
                        }

                        if ($cp_photo_action->fb_share_required) {
                            if ($photo_user && $photo_user->getPhotoUserShare(array('social_media_type' => SocialAccount::SOCIAL_MEDIA_FACEBOOK))) {
                                $data_csv[] = '◯';
                            }else{
                                $data_csv[] = '';
                            }
                        }

                        if ($cp_photo_action->tw_share_required) {
                            if ($photo_user && $photo_user->getPhotoUserShare(array('social_media_type' => SocialAccount::SOCIAL_MEDIA_TWITTER))) {
                                $data_csv[] = '◯';
                            }else{
                                $data_csv[] = '';
                            }
                        }

                        if ($cp_photo_action->panel_hidden_flg) {
                            $data_csv[] = $photo_user ? $photo_user->getApprovalStatus() : '';
                        }
                    }

                    if ($action->type == CpAction::TYPE_INSTAGRAM_HASHTAG) {
                        $instagram_hashtag_user = $instagram_hashtag_user_service->getInstagramHashtagUserByCpActionIdAndCpUserId($action->id, $fan_list_user->cp_user_id);

                        if (!$cp_instagram_hashtag_action) {
                            $cp_instagram_hashtag_action = $cp_instagram_hashtag_action_service->getCpInstagramHashtagActionByCpActionId($action->id);
                        }

                        if ($instagram_hashtag_user) {
                            $instagram_hashtag_list = $instagram_hashtag_user_service->getInstagramHashtagUserPostList($instagram_hashtag_user, true, $this->brand->id);

                            $data_csv[] = $instagram_hashtag_list['photo_url'] ? $instagram_hashtag_list['photo_url'] : '';
                            $data_csv[] = $instagram_hashtag_list['post_text'] ? $instagram_hashtag_list['post_text'] : '';
                            $data_csv[] = $instagram_hashtag_list['user_name'] ? $instagram_hashtag_list['user_name'] : '';
                            $data_csv[] = $instagram_hashtag_user->duplicate_flg ? 'あり' : 'なし';
                            $data_csv[] = $instagram_hashtag_list['reverse_post_time'] ? $instagram_hashtag_list['reverse_post_time'] : '';
                            if ($cp_instagram_hashtag_action->approval_flg) {
                                $data_csv[] = $instagram_hashtag_list['approval_status'] ? $instagram_hashtag_list['approval_status'] : '';
                            }
                            $data_csv[] = $instagram_hashtag_user->created_at ? date('Y/m/d H:i', strtotime($instagram_hashtag_user->created_at)) : '';
                            $data_csv[] = $instagram_hashtag_list['post_date_time'] ? $instagram_hashtag_list['post_date_time'] : '';
                        }else {
                            $data_csv[] = '';
                            $data_csv[] = '';
                            $data_csv[] = '';
                            $data_csv[] = '';
                            $data_csv[] = '';
                            if ($cp_instagram_hashtag_action->approval_flg) {
                                $data_csv[] = '';
                            }
                            $data_csv[] = '';
                            $data_csv[] = '';
                        }
                    }
                }

                foreach ($defs as $def) {
                    $data_csv[] = $brand_service->getAssignableCustomAttributeValue($fan_list_user->user_id, $def);
                }

                $array_data = $csv->out(array('data' => $data_csv), 1);
                print mb_convert_encoding($array_data, 'Shift_JIS', "UTF-8");

            }

            aafwLog4phpLogger::getHipChatLogger()->info('csv_join_user_list brand_id = '.$this->Data['brand']->id.' cp_id = '.$this->Data['cp_id'].' action_id = '.$this->Data['action_id'].' End');
            exit();

        } catch (Exception $e) {
            aafwLog4phpLogger::getDefaultLogger()->error('csv_join_user_list get error.'. $e);
        }
    }
}
