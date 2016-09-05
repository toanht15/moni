<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.lib.parsers.CSVParser');

class csv_gift_campaign_fan extends BrandcoGETActionBase {

    protected $ContainerName = 'csv_gift_campaign_fan';
    public $NeedOption = array();
    public $NeedAdminLogin = true;
    protected $searchCondition;
    protected $orderCondition;
    protected $cp_action_id;
    protected $has_address;
    protected $concrete_action;

    public function doThisFirst() {
        ini_set('max_execution_time', 3600);
        ini_set('memory_limit', '256M');

        $this->searchCondition = $this->getBrandSession('searchBrandCondition');
        $this->orderCondition = $this->getBrandSession('orderBrandCondition');

        $this->cp_action_id = $this->GET['exts'][0];
        $this->has_address  = $this->GET['exts'][1];
    }

    public function validate() {

        $validator = new GiftCpValidator($this->brand->id, $this->cp_action_id);
        if (!$validator->validate()) {
            return false;
        }
        $cp_gift_action_service = $this->getService('CpGiftActionService');
        $this->concrete_action  = $cp_gift_action_service->getCpGiftAction($this->cp_action_id);
        if ($this->has_address) {
            if(!Util::isAcceptRemote()) {
                return false;
            }
            return $this->concrete_action->incentive_type == CpGiftAction::INCENTIVE_TYPE_PRODUCT;
        }

        return true;
    }

    function doAction() {
        /** @var BrandPageSettingService $brand_page_settings_service */
        $brand_page_setting_service = $this->createService('BrandPageSettingService');
        $brand_page_setting = $brand_page_setting_service->getPageSettingsByBrandId($this->brand->id);

        /** @var BrandGlobalSettingService $brand_global_setting_service */
        $brand_global_setting_service = $this->createService('BrandGlobalSettingService');
        $original_sns_account = $brand_global_setting_service->getBrandGlobalSetting($this->brand->id, BrandGlobalSettingService::ORIGINAL_SNS_ACCOUNTS);

        /** @var ConversionService $conversion_service */
        $conversion_service = $this->createService('ConversionService');
        $conversions = $conversion_service->getConversionsByBrandId($this->brand->id);

        try {
            $data_csv = array();

            array_push($data_csv, '評価','会員No');

            if (!$this->has_address) {
                array_push($data_csv, '登録期間','Facebook','Facebook友達数','Twitter','Twitterフォロワー数','LINE','Instagram','Instagramフォロワー数','Yahoo!','Google');
                // GDO、LinkedInの場合
                if($original_sns_account) {

                    $original_sns_account_array = explode(',',$original_sns_account->content);

                    if(in_array(SocialAccountService::SOCIAL_MEDIA_GDO, $original_sns_account_array)) {
                        array_push($data_csv, 'GDO');
                    }

                    if(in_array(SocialAccountService::SOCIAL_MEDIA_LINKEDIN, $original_sns_account_array)) {
                        array_push($data_csv, 'LinkedIn');
                    }
                }

                array_push($data_csv, '友達数合計','最終ログイン','ログイン回数');

                array_push($data_csv, '性別');

                array_push($data_csv, '都道府県');

                array_push($data_csv, '年齢');

                array_push($data_csv, 'キャンペーン参加回数','キャンペーン当選回数','メッセージ受信数','メッセージ閲覧数','メッセージ閲覧率');

                $conversion_names = array();

                //コンバージョン名追加
                if ($conversions) {
                    foreach ($conversions as $conversion) {
                        $conversion_names[] = array('status' => $conversion->name);
                        array_push($data_csv, $conversion->name);
                    }
                }

                // カスタム属性追加
                /** @var $brand_service BrandService */
                $brand_service = $this->createService('BrandService');
                $definitions = $brand_service->getCustomAttributeDefinitions($this->brand->id);
                foreach ($definitions as $def) {
                    array_push($data_csv, $def->attribute_name);
                }
            } else {
                array_push($data_csv, '姓');
                array_push($data_csv, '名');
                array_push($data_csv, 'せい');
                array_push($data_csv, 'めい');
                array_push($data_csv, '郵便番号');
                array_push($data_csv, '都道府県');
                array_push($data_csv, '市区町村');
                array_push($data_csv, '番地');
                array_push($data_csv, '建物');
                array_push($data_csv, '電話番号');
            }
            array_push($data_csv, '送り手の会員No');
            array_push($data_csv, '受取日時');

            // Export csv
            $csv = new CSVParser();

            header("Content-type:" . $csv->getContentType());
            header($csv->getDisposition());
            $array_data = $csv->out(array('data' => $data_csv), null, true, true);
            print mb_convert_encoding($array_data, 'Shift_JIS', "UTF-8");

            $brands_users_relation_service = $this->createService('BrandsUsersRelationService');
            $cp_user_list_service = $this->createService('CpUserListService');

            $page_info = array(
                'brand_id'  => $this->brand->id,
                'tab_no' => CpCreateSqlService::TAB_PAGE_PROFILE
            );

            $this->searchCondition[CpCreateSqlService::SEARCH_GIFT_RECEIVER_FAN] = array('cp_action_id' => $this->cp_action_id);
            $fan_list_users = $cp_user_list_service->getAllFanList($page_info, $this->searchCondition, $this->orderCondition, '__NOFETCH__', true);

            $gift_sender_fan_list = $cp_user_list_service->getGiftSenderFanList($this->brand->id, $this->concrete_action->id, $this->has_address);

            $download_count = $fan_list_users['resource'] ? $fan_list_users['resource']->num_rows : 0;
            aafwLog4phpLogger::getHipChatLogger()->info('csv_gift_campaign_fan brand_id = '.$this->brand->id.' cp_action_id = ' . $this->cp_action_id . ' Start, Count : '.$download_count);

            $db = new aafwDataBuilder();

            while($fan_list_user = $db->fetch($fan_list_users)) {
                foreach($gift_sender_fan_list[$fan_list_user->user_id] as $gift_message_sender) {
                    $data_csv = array();

                    if($fan_list_user->rate == BrandsUsersRelationService::BLOCK) {
                        $data_csv[] = 'ブロック';
                    } elseif($fan_list_user->rate == BrandsUsersRelationService::NON_RATE) {
                        $data_csv[] = '未評価';
                    } else {
                        $data_csv[] = '+'.$fan_list_user->rate;
                    }
                    $data_csv[] = $fan_list_user->no > 0 ? intval($fan_list_user->no) : '-'; //会員No
                    if (!$this->has_address) {
                        $data_csv[] = $brands_users_relation_service->getHistorySummary($fan_list_user->created_at);//登録期間

                        $fb_social_account = $fan_list_user->getFacebookSocialAccounts();
                        $data_csv[] = $fb_social_account ? '◯' : ''; //Facebook
                        $data_csv[] = $fb_social_account && $fb_social_account->friend_count >= 0 ? intval($fb_social_account->friend_count) : '';

                        $tw_social_account = $fan_list_user->getTwitterSocialAccounts();
                        $data_csv[] = $tw_social_account ? '◯' : ''; //Twitter
                        $data_csv[] = $tw_social_account && $tw_social_account->friend_count >= 0  ? intval($tw_social_account->friend_count) : '';

                        $data_csv[] = $fan_list_user->getLineSocialAccounts() ? '◯' : '';  // LINE

                        $ig_social_account = $fan_list_user->getInstagramSocialAccounts();
                        $data_csv[] = $ig_social_account  ? '◯' : ''; //Instagram
                        $data_csv[] = $ig_social_account && $ig_social_account->friend_count >= 0 ? intval($ig_social_account->friend_count) : '';

                        $data_csv[] = $fan_list_user->getYahooSocialAccounts()  ? '◯' : ''; //Yahoo
                        $data_csv[] = $fan_list_user->getGoogleSocialAccounts() ? '◯' : ''; //Google

                        // GDO、LinkedInの場合
                        if($original_sns_account) {

                            $original_sns_account_array = explode(',',$original_sns_account->content);

                            if(in_array(SocialAccountService::SOCIAL_MEDIA_GDO, $original_sns_account_array)) {
                                $data_csv[] = $fan_list_user->getGdoSocialAccounts() ? '◯' : '';   //GDO
                            }

                            if(in_array(SocialAccountService::SOCIAL_MEDIA_LINKEDIN, $original_sns_account_array)) {
                                $data_csv[] = $fan_list_user->getLinkedInSocialAccounts() ? '◯' : ''; //LinkedIn
                            }
                        }

                        $sns_friend_sum =
                            ($fb_social_account && $fb_social_account->friend_count >= 0 ? $fb_social_account->friend_count : 0) +
                            ($tw_social_account && $tw_social_account->friend_count >= 0 ? $tw_social_account->friend_count : 0) +
                            ($ig_social_account && $ig_social_account->friend_count >= 0 ? $ig_social_account->friend_count : 0);
                        $data_csv[] = $sns_friend_sum; //友達数合計

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

                        $data_csv[] = $fan_list_user->cp_entry_count ? intval($fan_list_user->cp_entry_count) : 0;                      //キャンペーン参加回数
                        $data_csv[] = $fan_list_user->cp_announce_count ? intval($fan_list_user->cp_announce_count) : 0;                //キャンペーン当選回数
                        $data_csv[] = $fan_list_user->message_delivered_count ? intval($fan_list_user->message_delivered_count) : 0;    //メッセージ受信数
                        $data_csv[] = $fan_list_user->message_read_count ? intval($fan_list_user->message_read_count) : 0;              //メッセージ閲覧数
                        if(!$fan_list_user->message_delivered_count || $fan_list_user->message_delivered_count == 0 ||
                            !$fan_list_user->message_read_count || $fan_list_user->message_read_count == 0) {
                            $data_csv[] = '0%';
                        } else if($fan_list_user->message_delivered_count == $fan_list_user->message_read_count) {
                            $data_csv[] = '100%';
                        } else {
                            $data_csv[] = number_format(($fan_list_user->message_read_count / $fan_list_user->message_delivered_count) * 100,1).'%';    //メッセージ閲覧率
                        }

                        // コンバージョン情報取得
                        if ($conversions) {
                            foreach ($conversions as $conversion) {
                                $data_csv[] = $conversion_service->countUserConversionByUserIdAndConversionId($fan_list_user->user_id, $conversion->id, $this->brand->id);
                            }
                        }

                        // カスタム属性の追加
                        foreach ($definitions as $def) {
                            $data_csv[] = $brand_service->getAssignableCustomAttributeValue($fan_list_user->user_id, $def);
                        }
                    } else {
                        $data_csv[] = $gift_message_sender['first_name'];
                        $data_csv[] = $gift_message_sender['last_name'];
                        $data_csv[] = $gift_message_sender['first_name_kana'];
                        $data_csv[] = $gift_message_sender['last_name_kana'];
                        $data_csv[] = $gift_message_sender['zip_code'];
                        $data_csv[] = $gift_message_sender['address0'];
                        $data_csv[] = $gift_message_sender['address1'];
                        $data_csv[] = $gift_message_sender['address2'];
                        $data_csv[] = $gift_message_sender['address3'];
                        $data_csv[] = $gift_message_sender['tel_no'];
                    }
                    $data_csv[] = $gift_message_sender['no'];
                    $data_csv[] = $gift_message_sender['updated_at'];

                    $array_data = $csv->out(array('data' => $data_csv), 1);
                    print mb_convert_encoding($array_data, 'Shift_JIS', "UTF-8");
                }

            }

            aafwLog4phpLogger::getHipChatLogger()->info('csv_gift_campaign_fan brand_id = '.$this->brand->id.' cp_action_id = ' . $this->cp_action_id . ' End');
            exit();

        } catch (Exception $e) {
            aafwLog4phpLogger::getDefaultLogger()->error('csv_gift_campaign_fan get error.'. $e);
        }

    }
}
