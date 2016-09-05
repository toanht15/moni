<?php
AAFW::import('jp.aainc.classes.services.monipla.MoniplaCpService');
AAFW::import('jp.aainc.aafw.parsers.PHPParser');
AAFW::import('jp.aainc.classes.services.UserMailTrackingService');

class UserMailService extends aafwServiceBase {
    const TEMPLATE_ID_WELCOME = 'welcome_mail';
    const TEMPLATE_ID_ENTRY = 'entry_mail';
    const TEMPLATE_ID_CP_LOST = "cp_lost_notification_mail";

    //CP落選ページURLのパラメーター
    const CP_LOST_URL_PROVIDER_ID = 3;
    const CP_LOST_URL_TYPE = 'lose_notice';

    //A/Bテストに利用
    // CURRENT_TYPEには2,3,4のいずれかを設定
    const CURRENT_TYPE = 3;
    
    const TYPE_POPULAR_CPS       = 1;
    const TYPE_SPEED_LOTTERY     = 2;
    const TYPE_LARGE_INCENTIVE   = 3;
    const TYPE_DEADLINE          = 4;

    // tagのタイプ
    public static $tags = array(
        self::TYPE_POPULAR_CPS       => 'hot',
        self::TYPE_SPEED_LOTTERY     => 'speed_lottery',
        self::TYPE_LARGE_INCENTIVE   => 'large_incentive',
        self::TYPE_DEADLINE          => 'deadline'
    );

    // メディアのキャンペーンのタイプ名
    public static $media_cp_type_names = array(
        self::TYPE_POPULAR_CPS       => '人気のキャンペーン',
        self::TYPE_SPEED_LOTTERY     => 'スピードくじキャンペーン',
        self::TYPE_LARGE_INCENTIVE   => '大量当選キャンペーン',
        self::TYPE_DEADLINE          => '締切間近キャンペーン'
    );

    /** @var BrandcoAuthService $brandco_auth_service */
    private $brandco_auth_service;
    /** @var CpFlowService $cp_flow_service */
    private $cp_flow_service;
    /** @var BrandService $brand_service */
    private $brand_service;
    /** @var PHPParser $php_parser */
    private $php_parser;

    public function __construct() {
        // サービスの呼び出し
        $this->brandco_auth_service = $this->getService('BrandcoAuthService');
        $this->cp_flow_service = $this->getService('CpFlowService');
        $this->brand_service = $this->getService('BrandService');

        $this->php_parser = new PHPParser();
    }

    /**
     * @param $user
     * @param null $brand
     * @param null $entry_cp_id
     */
    public function sendWelcomeMail($user, $brand_id, $entry_cp_id = null) {
        // ユーザ情報の取得
        $user_info = $this->brandco_auth_service->getUserInfoByQuery($user->monipla_user_id);

        // メール配信可能か
        if (!$user_info->mailAddress && !$user->mail_address) {
            return;
        }

        // 変数の設定
        $user_account_info = $this->getUserAccountInfo($user_info, $user);
        $entry_cp_info = array();
        if ($entry_cp_id) {
            $entry_cp      = $this->cp_flow_service->getCpById($entry_cp_id);
            $brand         = $this->brand_service->getBrandById($entry_cp->brand_id);
            $entry_cp_info = array(
                'brand'        => $brand,
                'cp_title'     => $entry_cp ? $entry_cp->getTitle() : '',
                'cp_image_url' => $entry_cp ? $entry_cp->image_url : '',
            );
        }
        
        $userMailTransaction = aafwEntityStoreFactory::create('UserMails');
        try {
            $userMailTransaction->begin();

            //UserMailsにレコードを追加
            //saveの返り値としてuser_mail_idを取得
            $user_mail_tracking_service = new UserMailTrackingService();
            $user_mail_id = $user_mail_tracking_service->createUserMail($user->id)->id;
            $user_mail_tracking_service->createWelcomeMail($user_mail_id, $brand_id, $entry_cp_id);

            $params_val = array(
                'brand_id'     => $brand_id,
                'cp_id'        => $entry_cp_id,
                'user_mail_id' => $user_mail_id,
                'mail_type'    => self::TEMPLATE_ID_WELCOME,
            );

            $encoded_params = base64_encode(json_encode($params_val));

            // メールテンプレートの置換変数の設定
            $replace_params = array(
                'USER_NAME' => $user->name,
                // HTML
                'HEADER_HTML' => $this->getHeaderHtml(self::TEMPLATE_ID_WELCOME, $entry_cp_info, array(
                    'user_account_info' => $user_account_info,
                    'monipla_media_url' => $user_account_info,
                )),
                'OPEN_USER_MAIL_TRACKER_HTML' => '<img src="' . config('Protocol.Secure') . '://' . config('Domain.brandco_tracker') . '/open_user_mail_tracker.php?params=' . $encoded_params . '"' . '>',
                'MONIPLA_INFO_HTML' => $this->getMoniplaInfoHtml(),
                'HOW_TO_ENJOY_HTML' => $this->getHowToEnjoyHtml(),
                'FOOTER_HTML' => $this->getFooterHtml('wel_htm'),

                // TEXT
                'MONIPLA_INFO_TEXT' => $this->getMoniplaInfoText('wel_htm'),
                'AAID_INQUIRY_URL' => Util::createApplicationUrl(config('Domain.aaid'), array('inquiry', 'inquiry'), array('r' => 'wel_htm')),
                'USER_ACCOUNT_INFO_TEXT' => $this->getUserAccountInfoText($user_account_info),
                'ENTRY_CP_INFO_TEXT' => $this->getEntryCpInfoText($entry_cp_info),
            );

            // 人気のキャンペーン一覧の設定
            list($replace_params['POPULAR_CPS_INFO_HTML'], $replace_params['POPULAR_CPS_INFO_TEXT']) = $this->buildMediaCpsMailBody(self::TYPE_POPULAR_CPS);

            // メディアから取得したキャンペーンの設定
            list($replace_params['MEDIA_CPS_HTML'], $replace_params['MEDIA_CPS_TEXT']) = $this->buildMediaCpsMailBody(self::CURRENT_TYPE);

            $from_address = $this->getFromAddressByBrandId($brand_id);

            $this->send($user_info->mailAddress ?: $user->mail_address, self::TEMPLATE_ID_WELCOME, $replace_params, $from_address);
            $userMailTransaction->commit();
        } catch (Exception $e) {
            $userMailTransaction->rollback();
            error_log("\n------------------------\n" . "[" . date("Y/m/d H:i:s") . " UserMailService @sendWelcomeMail]: " . $e->getMessage() . "\n", 3, $this->log_file);
        }
    }

    /**
     * @param $type
     * @return array
     */
    public function buildMediaCpsMailBody($type) {
        $monipla_cp_service = $this->getService('MoniplaCpService');
        $media_cps = $monipla_cp_service->getCp(array('tag' => self::$tags[$type]));

        if (count($media_cps) > 0) {
            // メディアから取得したキャンペーンの内、上位3件をランダムで選ぶ
            $media_cps = Util::chooseAtRandom($media_cps, 3);

            list($html, $text) = $this->getMediaCpsTemplate($media_cps, self::$media_cp_type_names[$type]);

            return array($html, $text);
        } else {
            $html = '<table width="600" cellpadding="0" cellspacing="0" border="0" style="background: #FFF;"><tbody><tr><td colspan="3" height="45"></td></tr></tbody></table>';
            return array($html,null);
        }
    }

    /**
     * @param $user_id
     * @param $entry_cp_id
     */
    public function sendEntryMail($user_id, $entry_cp_id) {
        // ユーザ情報の取得
        /** @var UserService $user_service */
        $user_service = $this->getService('UserService');
        $user         = $user_service->getUserByBrandcoUserId($user_id);
        $user_info    = $this->brandco_auth_service->getUserInfoByQuery($user->monipla_user_id);

        // メール配信可能か
        if (!$user_info->mailAddress && !$user->mail_address) {
            return;
        }

        // 変数の設定
        $user_account_info = $this->getUserAccountInfo($user_info, $user);
        $entry_cp          = $this->cp_flow_service->getCpById($entry_cp_id);
        $brand             = $this->brand_service->getBrandById($entry_cp->brand_id);

        // TODO 特別対応 CP参加メールを送らない
        if ($brand->id == Brand::KENKO_KENTEI_ID) {
            return;
        }

        $entry_cp_info = array(
            'brand'    => $brand,
            'cp_title' => $entry_cp ? $entry_cp->getTitle() : '',
            'cp'       => $entry_cp ?: null,
            'fid'      => 'mpentml',
        );
        
        $UserMailsTransaction = aafwEntityStoreFactory::create('UserMails');
        try {
            $UserMailsTransaction->begin();
            
            //UserMailsにレコードを追加
            //saveの返り値としてuser_mail_idを取得
            $user_mail_tracking_service = new UserMailTrackingService();
            $user_mail_id = $user_mail_tracking_service->createUserMail($user_id)->id;
            $user_mail_tracking_service->createEntryMail($user_mail_id, $entry_cp_id);
            
            $params_val = array(
                'cp_id'        => $entry_cp_id,
                'user_mail_id' => $user_mail_id,
                'mail_type'    => self::TEMPLATE_ID_ENTRY,
            );

            $encoded_params = base64_encode(json_encode($params_val));

            $replace_params = array(
                'CP_TITLE' => $entry_cp_info['cp_title'],
                'ENTERPRISE_NAME' => $brand->enterprise_name,

                // HTMLのみ
                'HEADER_HTML' => $this->getHeaderHtml(self::TEMPLATE_ID_ENTRY, $entry_cp_info),
                'OPEN_USER_MAIL_TRACKER_HTML' => '<img src="' . config('Protocol.Secure') . '://' . config('Domain.brandco_tracker') . '/open_user_mail_tracker.php?params=' . $encoded_params . '"' . '>',
                // TODO 特別対応 BrandId 479 第1引数
                'MONIPLA_INFO_HTML' => $this->getMoniplaInfoHtml($brand->id == 479),
                // TODO 特別対応 BrandId 479 第２引数
                'FOOTER_HTML' => $this->getFooterHtml('ent_htm', $brand->id == 479),

                // TEXTのみ
                // TODO 特別対応 BrandId 479
                'MONIPLA_INFO_TEXT' => $brand->id == 479 ? '' : $this->getMoniplaInfoText('ent_htm'),
                'AAID_INQUIRY_URL' => Util::createApplicationUrl(config('Domain.aaid'), array('inquiry', 'inquiry'), array('r' => 'ent_htm')),
            );

            list($replace_params['BRAND_AND_USER_INFO_HTML'], $replace_params['BRAND_AND_USER_INFO_TEXT']) = $this->getBrandAndUserInfoTemplate($brand, $user_account_info);
            list($replace_params['ENTRY_CP_INFO_HTML'], $replace_params['ENTRY_CP_INFO_TEXT']) = $this->getEntryCpInfoTemplate($entry_cp_info);

            $from_address = $this->getFromAddressByBrandId($brand->id);
            $this->send($user_info->mailAddress ?: $user->mail_address, self::TEMPLATE_ID_ENTRY, $replace_params, $from_address);

            $UserMailsTransaction->commit();
        } catch (Exception $e) {
            $UserMailsTransaction->rollback();
        }
    }
    
    public function sendSignUpCustomMail($user_id,$brandId) {
        // ユーザ情報の取得
        /** @var UserService $user_service */
        $user_service = $this->getService('UserService');
        $user = $user_service->getUserByBrandcoUserId($user_id);
        $user_info = $this->brandco_auth_service->getUserInfoByQuery($user->monipla_user_id);

        // メール配信可能か
        if (!$user_info->mailAddress && !$user->mail_address) {
            return;
        }

        $mail_params = $this->getSignUpCustomMailTemplate($brandId);
        if(!$mail_params){
            return;
        }

        $this->insertSignUpCustomMailQueue($user_info->mailAddress ?: $user->mail_address, $mail_params);
    }

    private function getSignUpCustomMailTemplate($brandId){

        $brand_custom_mail_template = $this->getService('BrandCustomMailTemplateService');
        $custom_mail_template = $brand_custom_mail_template->getBrandCustomMailByBrandId($brandId);

        if(!$custom_mail_template){
            return null;
        }

        $settings = aafwApplicationConfig::getInstance();

        $from_address = $this->getFromAddressByBrandId($brandId);

        if(Util::isNullOrEmpty($from_address)) {
            $from_address = $custom_mail_template->sender_name . '<' .$settings->Mail['Default']['Envelope'] . '>';
        }

        $mail_params = array(
            'FromAddress' => $from_address,
            'Subject' => $custom_mail_template->subject,
            'BodyPlain' => $custom_mail_template->body_plain. $this->getSignUpMailFooter()
        );

        return $mail_params;
    }

    /**
     * メールを作成、mail_queuesに入れる
     * @param $user_info
     * @param $cp_id
     * @return bool
     */
    public function sendCpLostNotificationMail($user_info, $cp_id) {
        $cp = $this->cp_flow_service->getCpById($cp_id);
        $brand = $this->brand_service->getBrandById($cp->brand_id);

        $userMailsTransaction = aafwEntityStoreFactory::create('UserMails');

        try {
            $userMailsTransaction->begin();

            //UserMailsにレコードを追加
            //saveの返り値としてuser_mail_idを取得
            $user_mail_tracking_service = new UserMailTrackingService();
            $user_mail_id = $user_mail_tracking_service->createUserMail($user_info['id'])->id;
            $user_mail_tracking_service->createCpLostMail($user_mail_id, $cp_id);

            //メールトラッカーのパラメーターエンコード
            $mail_tracker_params = array(
                'cp_id'         => $cp_id,
                'user_mail_id'  => $user_mail_id,
                'mail_type'     => self::TEMPLATE_ID_CP_LOST,
            );
            $mail_tracker_encoded_params = base64_encode(json_encode($mail_tracker_params));

            //CP結果のページURLをhash化する
            $url_hash_params = array(
                'provider_id' => self::CP_LOST_URL_PROVIDER_ID,
                'campaign_id' => $cp_id,
                'type'        => self::CP_LOST_URL_TYPE
            );
            $url_hash_encoded_params = base64_encode(json_encode($url_hash_params));

            $cp_lost_url = Util::createApplicationUrl(config('Domain.monipla_media'), array(),array('_ml' => $url_hash_encoded_params));

            //メール作成のパラメーター
            $replace_params = array(
                'USER_NAME'                   => $user_info['name'],
                'CAMPAIGN_TITLE'              => $cp ? $cp->getTitle() : '',
                'BRAND_NAME'                  => $brand->name,

                // HTMLのみ
                'CP_LOST_URL_HTML'            => '<a href="'.$cp_lost_url.'" target="_blank">'.$cp_lost_url.'</a>',
                'OPEN_USER_MAIL_TRACKER_HTML' => '<img src="' . config('Protocol.Secure') . '://' . config('Domain.brandco_tracker') . '/open_user_mail_tracker.php?params=' . $mail_tracker_encoded_params . '"' . '>',

                //TEXTのみ
                'CP_LOST_URL'                 => $cp_lost_url
            );

            // メールキューに追加
            $mail_manager = new MailManager();
            $mail_manager->loadMailContent(self::TEMPLATE_ID_CP_LOST);
            $mail_manager->sendLater($user_info['mail_address'], $replace_params);

            $userMailsTransaction->commit();
        } catch (Exception $e) {
            $userMailsTransaction->rollback();
            aafwLog4phpLogger::getDefaultLogger()->error("UserMailService#sendCpLostNotificationMail error can't send mail");
            aafwLog4phpLogger::getDefaultLogger()->error($e);
            return false;
        }

        return true;
    }

    private function getSignUpMailFooter(){

        $replace_params = array(
            'BRAND_INQUIRY_URL' => Util::rewriteUrl( 'inquiry', 'index', array(), array() ),
            'MONIPLA_MEDIA_URL' => Util::createApplicationUrl(config('Domain.monipla_media'), array(), array('r' => 'ent_htm'))
        );

        $mail_manager = new MailManager();
        $mail_manager->loadBodyPlain('signup_mail_share');
        $content = $mail_manager->BodyPlain;

        $tmpl = new aafwTemplateTag($content, $replace_params);

        return $tmpl->evalTag();
    }

    /**
     * @param $to_address
     * @param $template_id
     * @param array $replace_params
     * @param null $from_address
     * @param bool|true $sent_soon
     */
    public function send($to_address, $template_id, $replace_params = array(), $from_address = null, $sent_soon = true) {
        try {
            $mail_manager = new MailManager();

            if($from_address != null) {
                $mail_manager->FromAddress = $from_address;
            }

            $mail_manager->loadMailContent($template_id);
            $mail_manager->BccAddress = null;
            if ($sent_soon) {
                $mail_manager->sendNow($to_address, $replace_params);
            } else {
                $mail_manager->sendLater($to_address, $replace_params);
            }
        } catch (aafwException $e) {
            aafwLog4phpLogger::getHipChatLogger()->error("UserMailService#send error can't send mail");
            aafwLog4phpLogger::getDefaultLogger()->error("UserMailService#send error can't send mail");
            aafwLog4phpLogger::getDefaultLogger()->error($e);
        }
    }

    public function insertSignUpCustomMailQueue($to_address, $mail_params) {
        try {

            $mail_manager = new MailManager();
            $mail_manager->FromAddress = $mail_params['FromAddress'];
            $mail_manager->Subject = $mail_params['Subject'];
            $mail_manager->BodyPlain = $mail_params['BodyPlain'];

            $mail_manager->sendLater($to_address);
        } catch (aafwException $e) {
            aafwLog4phpLogger::getHipChatLogger()->error("UserMailService#insertSignUpCustomMailQueue error can't send mail");
            aafwLog4phpLogger::getDefaultLogger()->error("UserMailService#insertinsertSignUpCustomMailQueueMailQueue error can't send mail");
            aafwLog4phpLogger::getDefaultLogger()->error($e);
        }
    }

    private function getFromAddressByBrandId($brandId) {
        /** @var BrandGlobalSettingService $brand_global_setting_service */
        $brand_global_setting_service = $this->getService('BrandGlobalSettingService');
        $can_set_mail_from_address = $brand_global_setting_service->getBrandGlobalSetting($brandId, BrandGlobalSettingService::CAN_SET_MAIL_FROM_ADDRESS);

        if(Util::isNullOrEmpty($can_set_mail_from_address) || $can_set_mail_from_address->content == '') {
            return null;
        }

        return $can_set_mail_from_address->content;
    }

    /**
     * @param $user_info
     * @param $user
     * @return array
     */
    public function getUserAccountInfo($user_info, $user) {
        /** @var SocialAccountService $social_account_service */
        $social_account_service = $this->getService('SocialAccountService');
        $social_accounts = $social_account_service->getSocialAccountsByUserIdOrderBySocialMediaAccountId($user->id);
        $mail_address = $user_info->mailAddress ?: $user->mail_address;
        $has_mail_address = $user_info->mailAddress && $this->isMailAddress($user_info->mailAddress);

        return array(
            'user_name' => $user_info->name,
            'mail_address' => $mail_address,
            'has_mail_address' => $has_mail_address,
            'profile_image' => $user_info->socialAccounts[0]->profileImageUrl ?: config('Protocol.Secure') . ':' . $this->php_parser->setVersion('/img/mail/welcome/imgUser1.png'),
            'social_accounts' => $social_accounts
        );
    }

    /***************************************************************************************************
     * HTML&TEXT
     **************************************************************************************************/
    /**
     * @param $brand
     * @param $user_account_info
     * @return array
     */
    public function getBrandAndUserInfoTemplate($brand, $user_account_info) {
        $fan_site_url = $brand->getUrl() . '?fid=mpentml';

        /** @var BrandContractService $brand_contract_service */
        $brand_contract_service = $this->getService('BrandContractService');
        $brand_contract = $brand_contract_service->getBrandContractByBrandId($brand->id);

        $has_fan_site = ( $brand_contract->plan == BrandContract::PLAN_MANAGER_STANDARD && $brand->hasOption(BrandOptions::OPTION_TOP) );

        // htmlメール用
        $html = Util::sanitizeOutput($this->php_parser->parseTemplate('user_mail/BrandAndUserInfo.php', array(
            'brand' => $brand,
            'fan_site_url' => $fan_site_url,
            'is_manager' => $has_fan_site,
            'user_account_info' => $user_account_info
        )));

        // textメール用
        $text = '';
        if ($has_fan_site) {
            $brand_names = ($brand->enterprise_name == $brand->name) ? $brand->name : $brand->name . PHP_EOL . $brand->enterprise_name;
            $text = <<<EOS
++++++++++++++++++++++++++
★あなたが登録したサイトはこちら★
++++++++++++++++++++++++++
{$brand_names}

サイトへ -> {$fan_site_url}


EOS;
        }

        $text .= <<<EOS
あなたのご登録情報は以下となります。
連携アカウント・メールアドレスを用いてログインをお願いします。

EOS;
        $text .= $this->getUserAccountInfoText($user_account_info);

        return array($html, $text);
    }
    
    /**
     * @param array $media_cps
     * @param $media_cp_type_name
     * @return array
     */
    public function getMediaCpsTemplate($media_cps = array(), $media_cp_type_name) {
        // htmlメール用
        $html = Util::sanitizeOutput($this->php_parser->parseTemplate('user_mail/MediaCps.php', array(
            'media_cps' => $media_cps,
            'media_cp_type_name' => $media_cp_type_name,
            'monipla_media_url' => Util::createApplicationUrl(config('Domain.monipla_media'), array(), array('r' => 'wel_htm'))
        )));

        // textメール用
        $text = <<<EOS
++++++++++++++++++++++++++
★ 開催中のキャンペーン情報 ★
++++++++++++++++++++++++++
現在開催中の{$media_cp_type_name}はコチラ！

EOS;
        foreach ($media_cps as $media_cp) {
            $text .= <<<EOS
◇ {$media_cp['name']}
[当選数] {$media_cp['winningLabel']}
-> {$media_cp['url']}?fid=mpwelml

EOS;
        }

        return array($html, $text);
    }

    /**
     * @param $entry_cp_info
     * @return array
     */
    public function getEntryCpInfoTemplate($entry_cp_info) {
        // htmlメール用
        $html = Util::sanitizeOutput($this->php_parser->parseTemplate('user_mail/EntryCpInfo.php', $entry_cp_info));

        // textメール用
        $start_date = Util::getFormatDateString($entry_cp_info['cp']->start_date);
        $end_date = $entry_cp_info['cp']->isNonIncentiveCp() ? '' : Util::getFormatDateString($entry_cp_info['cp']->end_date);
        if ($entry_cp_info['cp']->isNonIncentiveCp()) {
            $announce_date = '';
        } else if ($entry_cp_info['cp']->announce_display_label_use_flg == 1) {
            $announce_date = PHP_EOL . '発表日：' . $entry_cp_info['cp']->announce_display_label;
        } else if ($entry_cp_info["cp"]->shipping_method == Cp::SHIPPING_METHOD_PRESENT) {
            $announce_date = PHP_EOL . '発表日：賞品の発送をもって発表';
        } else {
            $announce_date = PHP_EOL . '発表日：' . Util::getFormatDateString($entry_cp_info['cp']->announce_date);
        }

        $text = <<<EOS
----------
{$entry_cp_info['cp_title']}
開催：{$entry_cp_info['brand']->enterprise_name}
期間：{$start_date}〜{$end_date}{$announce_date}
参加した企画ページへ　-> {$entry_cp_info['cp']->getThreadUrl()}?fid=mpentml
----------
EOS;

        return array($html, $text);
    }

    /***************************************************************************************************
     * HTML
     **************************************************************************************************/
    /**
     * @param $template_id
     * @param $entry_cp_info
     * @param array $params
     * @return mixed
     */
    public function getHeaderHtml($template_id, $entry_cp_info, $params = array()) {
        return Util::sanitizeOutput($this->php_parser->parseTemplate('user_mail/Header.php', array_merge($params, array(
            'template_id' => $template_id,
            'monipla_media_url' => Util::createApplicationUrl(config('Domain.monipla_media'), array(), array('r' => 'wel_htm')),
            'entry_cp_info' => $entry_cp_info,
            'title' => ($template_id === self::TEMPLATE_ID_WELCOME) ? 'ご登録' : 'ご参加',
            'sub_title' => ($template_id === self::TEMPLATE_ID_WELCOME) ? 'このメールは、下記キャンペーン経由でモニプラに登録された方にお送りしています。' : 'このメールは、下記の企画に参加登録された方にお送りしています。',
        ))));
    }

    /**
     * @param $is_whitelist
     * @return mixed
     */
    public function getMoniplaInfoHtml($is_whitelist = false) {
        return $is_whitelist ? '' : Util::sanitizeOutput($this->php_parser->parseTemplate('user_mail/MoniplaInfo.php'));
    }

    /**
     * @return mixed
     */
    public function getHowToEnjoyHtml() {
        return Util::sanitizeOutput($this->php_parser->parseTemplate('user_mail/HowToEnjoy.php', array(
            'monipla_media_url' => Util::createApplicationUrl(config('Domain.monipla_media'), array(), array('r' => 'wel_htm')),
            'spacer_url' => config('Protocol.Secure') . ':' . $this->php_parser->setVersion('/img/mail/welcome/spacer.gif'),
        )));
    }

    /**
     * @param $r
     * @param $is_whitelist
     * @return mixed
     */
    public function getFooterHtml($r, $is_whitelist = false) {
        return Util::sanitizeOutput($this->php_parser->parseTemplate('user_mail/Footer.php', array(
            'monipla_logo_url' => config('Protocol.Secure') . ':' .  $this->php_parser->setVersion('/img/mail/welcome/logoMonipla_02.jpg'),
            'monipla_media_url' => Util::createApplicationUrl(config('Domain.monipla_media'), array(), array('r' => $r)),
            'spacer_url' => config('Protocol.Secure') . ':' . $this->php_parser->setVersion('/img/mail/welcome/spacer.gif'),
            'aaid_faq_url' => Util::createApplicationUrl(config('Domain.monipla_media'), array('help', 'faq'), array('r' => $r)),
            'aaid_inquiry_url' => Util::createApplicationUrl(config('Domain.aaid'), array('inquiry', 'inquiry'), array('r' => $r)),
            'is_whitelist' => $is_whitelist
        )));
    }

    /***************************************************************************************************
     * TEXT
     **************************************************************************************************/
    /**
     * @param $user_account_info
     * @return array
     */
    public function getUserAccountInfoText($user_account_info) {
        $text_blocks = array();

        // ソーシャル情報の取得
        $buf = '';
        foreach ($user_account_info['social_accounts'] as $social_account) {
            $buf .= SocialAccount::$socialMediaTypeName[$social_account->social_media_id] . ':' . $social_account->name . PHP_EOL;
        }

        if (strlen($buf) > 0) {
            $text_blocks[] = '* 以下のアカウントと連携しています。' . PHP_EOL . $buf;

        }

        // メールアドレスの取得
        if ($user_account_info['has_mail_address']) {
            $text_blocks[] = <<<EOS
* ログイン用メールアドレス
{$user_account_info['mail_address']}
EOS;
        }

        $text = '----------' . PHP_EOL;
        $text .= implode(PHP_EOL, $text_blocks) . PHP_EOL;
        $text .= '----------' . PHP_EOL;

        return $text;
    }

    /**
     * @param $entry_cp_info
     * @return string
     */
    public function getEntryCpInfoText($entry_cp_info) {
        $text = '';
        if ($entry_cp_info) {
            $text = <<<EOS
※このメールは、下記キャンペーン経由でモニプラに登録された方にお送りしています。

----------
{$entry_cp_info['cp_title']}
開催：{$entry_cp_info['brand']->enterprise_name}
----------

EOS;
        }

        return $text;
    }

    /**
     * @param $r
     * @return string
     */
    public function getMoniplaInfoText($r) {
        $monipla_media_url =  Util::createApplicationUrl(config('Domain.monipla_media'), array(), array('r' => $r));

        $text = <<<EOS
////////////////////////////////
★ モニプラ の ご紹介 ★
////////////////////////////////

キャンペーン情報満載！
モニプラでちょっと楽しく“おトク”体験をしよう。

常時100件以上のおトクなキャンペーン情報を掲載！
飲料、食品、家電、雑貨、旅行、コスメ、ギフトカードなど
有名企業の人気商品やアイテムが当たるチャンス。
「モニプラ」であなたも豪華プレゼントをゲットしよう！

▼「モニプラ」を見る
{$monipla_media_url}


EOS;

        return $text;
    }
}
