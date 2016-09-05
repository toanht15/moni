<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.lib.parsers.CSVParser');
AAFW::import('jp.aainc.classes.services.CpQuestionnaireService');

class csv_brand_user_list extends BrandcoGETActionBase {

    protected $ContainerName = 'csv_brand_user_list';

    public $NeedOption = array();
    public $NeedAdminLogin = true;
    protected $searchCondition;
    protected $orderCondition;

//メールの送信は一旦封じる
//    const NOTIFICATION_MAIL = 'sato.tomoaki@aainc.co.jp';

    public function doThisFirst() {
        ini_set('max_execution_time', 3600);

        $this->Data['brand'] = $this->getBrand();
        $this->searchCondition = $this->getBrandSession('searchBrandCondition');
        $this->orderCondition = $this->getBrandSession('orderBrandCondition');
    }

    public function validate() {
        return true;
    }

    function doAction() {
        /** @var BrandPageSettingService $brand_page_settings_service */
        $brand_page_setting_service = $this->createService('BrandPageSettingService');
        $brand_page_setting = $brand_page_setting_service->getPageSettingsByBrandId($this->Data['brand']->id);
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

        try {
            if (!$this->Data['pageStatus']['manager']->id && !$can_download_brand_user_list) {
                return 404;
            }
            $params = array(
                'brand_name' => $this->Data['brand']->name,
                'url' => Util::constructBaseURL($this->Data['brand']->id,$this->Data['brand']->directory_name).'admin-fan/show_brand_user_list',
                'download_time' => date('Y-m-d H:i:s'),
            );
            $data_csv = array();
            if($can_view) {
                array_push($data_csv, 'ユーザ名','メールアドレス');
            }
            array_push($data_csv, '評価', '会員No','住所重複','登録期間','登録日付','登録時刻','Facebook','Facebook友達数','Twitter','Twitterフォロワー数','LINE','Instagram','Instagramフォロワー数','Yahoo!','Google');

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

            array_push($data_csv, '友達数合計','最終ログイン','ログイン回数');

            array_push($data_csv, '性別');

            array_push($data_csv, '都道府県');

            array_push($data_csv, '年齢');


            array_push($data_csv, 'キャンペーン参加回数','キャンペーン当選回数','メッセージ受信数','メッセージ閲覧数','メッセージ閲覧率');

            // プロフィール情報のヘッダー取得
            /** @var CpQuestionnaireService $profile_questionnaire_service */
            $profile_questionnaire_service = $this->createService('CpQuestionnaireService', CpQuestionnaireService::TYPE_PROFILE_QUESTION);
            $profile_question_relations = $profile_questionnaire_service->getPublicProfileQuestionRelationByBrandId($this->Data['brand']->id);
            $use_profile_questions = $profile_questionnaire_service->useProfileQuestion($profile_question_relations);

            $action_questions = array();
            if($use_profile_questions) {
                array_push($data_csv, 'アンケート');
                foreach ($use_profile_questions as $profile_relation) {
                    $profile_question = $profile_questionnaire_service->getQuestionById($profile_relation->question_id);
                    $action_questions[] = array('status' => 'Q'.$profile_relation->number.'.'.$profile_question->question);
                    array_push($data_csv, 'Q'.$profile_relation->number.'.'.$profile_question->question);
                }
            }
            if(count($action_questions) > 0) {
                $params['DOWNLOAD_ITEM'][] = array(
                    'ITEM' => 'プロフィールアンケート',
                    'QUESTION' => $action_questions,
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

            // カスタム属性追加
            /** @var $brand_service BrandService */
            $brand_service = $this->createService('BrandService');
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

            $brands_users_relation_service = $this->createService('BrandsUsersRelationService');
            $cp_user_list_service = $this->createService('CpUserListService');

            $page_info = array(
                'brand_id'  => $this->Data['brand']->id,
                'tab_no' => CpCreateSqlService::TAB_PAGE_PROFILE
            );
            $fan_list_users = $cp_user_list_service->getAllFanList($page_info, $this->searchCondition, $this->orderCondition, '__NOFETCH__', true);

            $download_count = $fan_list_users['resource'] ? $fan_list_users['resource']->num_rows : 0;
            aafwLog4phpLogger::getHipChatLogger()->info('csv_brand_user_list brand_id = '.$this->Data['brand']->id.' Start, Count : '.$download_count);

            $params['download_count'] = $download_count;
//メールの送信は一旦封じる
//            if (!$this->Data['pageStatus']['manager']->id && count($params)) {
//                $mail = new MailManager();
//                $mail->loadMailContent('notification_csv_download');
//                $mail->sendNow(self::NOTIFICATION_MAIL, $params);
//            }

            $db = new aafwDataBuilder();

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
                $data_csv[] = $fan_list_user->shipping_address_duplicate_count ? intval($fan_list_user->shipping_address_duplicate_count) : '未取得'; //住所重複
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

                // カスタム属性の追加
                foreach ($defs as $def) {
                    $data_csv[] = $brand_service->getAssignableCustomAttributeValue($fan_list_user->user_id, $def);
                }

                $array_data = $csv->out(array('data' => $data_csv), 1);
                print mb_convert_encoding($array_data, 'Shift_JIS', "UTF-8");
            }

            aafwLog4phpLogger::getHipChatLogger()->info('csv_brand_user_list brand_id = '.$this->Data['brand']->id.' End');
            exit();
        } catch (Exception $e) {
            aafwLog4phpLogger::getDefaultLogger()->error('csv_brand_user_list get error.'. $e);
        }
    }
}
