<?php
AAFW::import('jp.aainc.lib.base.aafwServiceBase');
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');
AAFW::import('jp.aainc.classes.entities.BrandsUsersRelation');
AAFW::import('jp.aainc.classes.services.CpQuestionnaireService');

class DashboardService extends aafwServiceBase {

    const DATE_BRAND_FAN_COUNT            = 1;
    const SNS_FAN_COUNT                   = 2;
    const SEX_FAN_COUNT                   = 3;
    const AREA_FAN_COUNT                  = 4;
    const AGE_FAN_COUNT                   = 5;
    const PROFILE_QUESTIONNAIRE_FAN_COUNT = 6;
    const BRAND_PV_COUNT                  = 7;

    const DATE_SUMMARY = 1;
    const DATE_TERM    = 2;

    const AGE_FAN_FROM_0_TO_19   = 1;
    const AGE_FAN_FROM_20_TO_29  = 2;
    const AGE_FAN_FROM_30_TO_39  = 3;
    const AGE_FAN_FROM_40_TO_49  = 4;
    const AGE_FAN_FROM_50_TO_100 = 5;
    const AGE_FAN_NOT_REGISTER   = -1;

    const TERM_TODAY            = 1;
    const TERM_YESTERDAY        = 2;
    const TERM_LAST_WEEK        = 3;
    const TERM_LAST_MONTH       = 4;
    const TERM_LAST_SEVEN_DAYS  = 5;
    const TERM_LAST_THIRTY_DAYS = 6;
    const TERM_CUSTOM           = 7;

    const SUMMARY_TODAY     = 1;
    const SUMMARY_YESTERDAY = 2;
    const SUMMARY_CUSTOM    = 3;

    private $logger;
    private $db;
    private $brand;
    private $service_factory;

    public function __construct($brand) {
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
        $this->db = new aafwDataBuilder();
        $this->brand = $brand;
        $this->service_factory = new aafwServiceFactory();
    }

    /**
     * @param $summary_date_type
     * @param $summary_date
     * @return array($from_date,$to_date)
     */
    public function getSummaryDate($summary_date_type, $summary_date) {
        $from_date = $this->brand->created_at;
        if($summary_date_type == self::SUMMARY_TODAY) {
            $to_date = date('Y-m-d H:i:s', strtotime('today 23:59:59'));
        }
        if($summary_date_type == self::SUMMARY_YESTERDAY) {
            $to_date = date('Y-m-d H:i:s', strtotime('yesterday 23:59:59'));
        }
        if($summary_date_type == self::SUMMARY_CUSTOM) {
            $to_date = date('Y-m-d H:i:s', strtotime($summary_date.' 23:59:59'));
        }
        return array($from_date, $to_date);
    }

    /**
     * @param $term_date_type
     * @param $term_from_date
     * @param $term_to_date
     * @return array($from_date,$to_date)
     */
    public function getTermDate($term_date_type, $term_from_date, $term_to_date) {
        if($term_date_type == self::TERM_TODAY) {
            $from_date = date('Y-m-d H:i:s', strtotime('today 00:00:00'));
            $to_date = date('Y-m-d H:i:s', strtotime('today 23:59:59'));
        }
        if($term_date_type == self::TERM_YESTERDAY) {
            $from_date = date('Y-m-d H:i:s', strtotime('yesterday 00:00:00'));
            $to_date = date('Y-m-d H:i:s', strtotime('yesterday 23:59:59'));
        }
        if($term_date_type == self::TERM_LAST_WEEK) {
            if(date('l') == 'Sunday') {
                $from_date = date('Y-m-d H:i:s', strtotime('Sunday previous week 00:00:00'));
            } else {
                $from_date = date('Y-m-d H:i:s', strtotime('2 weeks ago Sunday 00:00:00'));
            }
            $to_date = date('Y-m-d H:i:s', strtotime('Saturday previous week 23:59:59'));
        }
        if($term_date_type == self::TERM_LAST_MONTH) {
            $from_date = date('Y-m-d H:i:s', strtotime('first day of -1 month 00:00:00'));
            $to_date = date('Y-m-d H:i:s', strtotime('last day of -1 month 23:59:59'));
        }
        if($term_date_type == self::TERM_LAST_SEVEN_DAYS) {
            $from_date = date('Y-m-d H:i:s', strtotime('-7 day 00:00:00'));
            $to_date = date('Y-m-d H:i:s', strtotime('-1 day 23:59:59'));
        }
        if($term_date_type == self::TERM_LAST_THIRTY_DAYS) {
            $from_date = date('Y-m-d H:i:s', strtotime('-30 day 00:00:00'));
            $to_date = date('Y-m-d H:i:s', strtotime('-1 day 23:59:59'));
        }
        if($term_date_type == self::TERM_CUSTOM) {
            $from_date = date('Y-m-d H:i:s', strtotime($term_from_date.' 00:00:00'));
            $to_date = date('Y-m-d H:i:s', strtotime($term_to_date.' 23:59:59'));
        }
        return array($from_date, $to_date);
    }

    /**
     * @param $summary_date
     * @return $error
     */
    public function getSummaryDateError($summary_date) {
        if (!$summary_date) {
            $error = 'カスタムを選択時は日付を入力してください。';
        } elseif (!$this->isDate($summary_date)) {
            $error = '日付形式(年/月/日)で入力してください。';
        } elseif ($summary_date > date('Y/m/d', strtotime('today')) || $summary_date < date('Y/m/d', strtotime($this->brand->created_at))) {
            $error = 'ページの開設日〜本日の範囲で入力してください。';
        }
        return $error;
    }

    /**
     * @param $term_from_date
     * @param $term_to_date
     * @return $error
     */
    public function getTermDateError($term_from_date, $term_to_date) {
        if(!$term_from_date || !$term_to_date) {
            $error = 'カスタムを選択時は日付を入力してください。';
        } elseif(!$this->isDate($term_from_date) || !$this->isDate($term_to_date)) {
            $error = '日付形式(年/月/日)で入力してください。';
        } elseif($term_from_date > date('Y/m/d', strtotime('today')) || $term_from_date < date('Y/m/d', strtotime($this->brand->created_at)) ||
            $term_to_date > date('Y/m/d', strtotime('today')) || $term_to_date < date('Y/m/d', strtotime($this->brand->created_at))) {
            $error = 'ページの作成日〜本日の範囲で入力してください。';
        } elseif($term_from_date > $term_to_date) {
            $error = '日付の指定順序が正しくありません。';
        }
        return $error;
    }

    /**
     * @param $summary_date_type
     * @param $summary_date
     * @return array($title_date, $title_date_text)
     */
    public function getSummaryTitleDate($summary_date_type, $summary_date) {
        if($summary_date_type == self::SUMMARY_TODAY) {
            $title_date = '今日('.date('Y-m-d', strtotime('today')).')';
        }
        if($summary_date_type == self::SUMMARY_YESTERDAY) {
            $title_date = '昨日('.date('Y-m-d', strtotime('yesterday')).')';
        }
        if($summary_date_type == self::SUMMARY_CUSTOM) {
            $title_date = date('Y-m-d', strtotime($summary_date));
        }
        $title_date_text = '開設 から '.$title_date.' までの累計';
        return array($title_date, $title_date_text);
    }

    /**
     * @param $term_date_type
     * @param $from_date
     * @param $to_date
     * @return array($title_date, $title_date_text)
     */
    public function getTermTitleDate($term_date_type, $from_date, $to_date) {
        if($term_date_type == self::TERM_TODAY) {
            $title_date = '今日('.date('Y-m-d', strtotime('today')).')';
        }
        if($term_date_type == self::TERM_YESTERDAY) {
            $title_date = '昨日('.date('Y-m-d', strtotime('yesterday')).')';
        }
        if($term_date_type != self::TERM_TODAY && $term_date_type != self::TERM_YESTERDAY) {
            $title_date = date('Y-m-d', strtotime($from_date)).' 〜 '.date('Y-m-d', strtotime($to_date));
        }
        $title_date_text = $title_date.' の新規登録';
        return array($title_date, $title_date_text);
    }

    /**
     * @param $summary_date_type
     * @return array($summary_date_li_style, $term_date_li_style, $summary_date_span_style)
     */
    public function getSummaryElementStyle($summary_date_type) {
        $summary_date_li_style = "";
        $term_date_li_style = "display:none";
        $summary_date_span_style = $summary_date_type == self::SUMMARY_CUSTOM ? '' : "display:none";
        return array($summary_date_li_style, $term_date_li_style, $summary_date_span_style);
    }

    /**
     * @param $term_date_type
     * @return array($summary_date_li_style, $term_date_li_style, $term_date_span_style)
     */
    public function getTermElementStyle($term_date_type) {
        $summary_date_li_style = "display:none";
        $term_date_li_style = "";
        $term_date_span_style = $term_date_type == self::TERM_CUSTOM ? '' : "display:none";
        return array($summary_date_li_style, $term_date_li_style, $term_date_span_style);
    }

    /**
     * @return $summary_options
     */
    public function getSummaryDatePicker() {
        $summary_options[self::SUMMARY_TODAY] = '今日まで';
        if ($this->brand->created_at <= date('Y-m-d H:i:s', strtotime('yesterday 23:59:59'))) {
            $summary_options[self::SUMMARY_YESTERDAY] = '昨日まで';
        }
        $summary_options[self::SUMMARY_CUSTOM] = 'カスタム';
        return $summary_options;
    }

    /**
     * @return $term_options
     */
    public function getTermDatePicker() {
        $term_options[self::TERM_TODAY] = '今日';
        if($this->brand->created_at <= date('Y-m-d H:i:s', strtotime('yesterday 23:59:59'))) {
            $term_options[self::TERM_YESTERDAY] = '昨日';
        }
        if(date('l') == 'Sunday') {
            $term_from_date = 'Sunday previous week';
        } else {
            $term_from_date = '2 weeks ago Sunday';
        }
        if($this->brand->created_at <= date('Y-m-d H:i:s', strtotime($term_from_date.' 23:59:59'))) {
            $term_options[self::TERM_LAST_WEEK] = '前週';
        }
        if($this->brand->created_at <= date('Y-m-d H:i:s', strtotime('last day of -1 month 23:59:59'))) {
            $term_options[self::TERM_LAST_MONTH] = '先月';
        }
        if($this->brand->created_at <= date('Y-m-d H:i:s', strtotime('-7 day 23:59:59'))) {
            $term_options[self::TERM_LAST_SEVEN_DAYS] = '過去7日間';
        }
        if($this->brand->created_at <= date('Y-m-d H:i:s', strtotime('-30 day 23:59:59'))) {
            $term_options[self::TERM_LAST_THIRTY_DAYS] = '過去30日間';
        }
        $term_options[self::TERM_CUSTOM] = 'カスタム';
        return $term_options;
    }

    /**
     * @param $brand_relation_from
     * @param $brand_relation_to
     * @return $all_fan_count
     */
    public function getAllFanCount($brand_relation_from, $brand_relation_to) {
        $condition = array(
            'brand_id' => $this->brand->id,
            'from_date' => $brand_relation_from,
            'to_date' => $brand_relation_to
        );
        $all_fan_count = $this->db->getBrandFanCount($condition)[0]['cnt'];
        return $all_fan_count;
    }

    /**
     * @param $question_id
     * @return $question_height
     */
    public function getQuestionHeight($question_requirement) {
        /** @var CpQuestionnaireService $cp_questionnaire_service */
        $cp_questionnaire_service = $this->service_factory->create('CpQuestionnaireService', CpQuestionnaireService::TYPE_PROFILE_QUESTION);

        $question_choices = $cp_questionnaire_service->getChoicesByQuestionId($question_requirement->question_id);
        if(!$question_choices) {
            return;
        }
        $choice_count = $question_choices->total();
        if($question_requirement->multi_answer_flg) {
            if($choice_count <= 3) {
                $question_height = 140;
            } else {
                $question_height = 140 + 31 * ($choice_count - 3);
            }
        } else {
            if($choice_count <= 5) {
                $question_height = 150;
            } else {
                $question_height = 150 + 25 * ($choice_count - 5);
            }
        }
        return $question_height;
    }

    /**
     * @param $date_type
     * @param $dashboard_type
     * @param $from_date
     * @param $to_date
     * @param $all_fan_count
     * @return $dashboard_info
     */
    public function getDashboardInfo($date_type, $dashboard_type, $from_date, $to_date, $all_fan_count) {
        $dashboard_code = explode('/', $dashboard_type)[0];
        if($dashboard_code == self::DATE_BRAND_FAN_COUNT) {
            $dashboard_info = $this->getDateBrandFanInfo($date_type, $from_date, $to_date);
        }
        if($dashboard_code == self::SNS_FAN_COUNT) {
            $dashboard_info = $this->getSnsFanInfo($from_date, $to_date, $all_fan_count);
        }
        if($dashboard_code == self::SEX_FAN_COUNT) {
            $dashboard_info = $this->getSexFanInfo($from_date, $to_date, $all_fan_count);
        }
        if($dashboard_code == self::AGE_FAN_COUNT) {
            $dashboard_info = $this->getAgeFanInfo($from_date, $to_date, $all_fan_count);
        }
        if($dashboard_code == self::AREA_FAN_COUNT) {
            $dashboard_info = $this->getAreaFanInfo($from_date, $to_date, $all_fan_count);
        }
        if($dashboard_code == self::PROFILE_QUESTIONNAIRE_FAN_COUNT) {
            $dashboard_info = $this->getProfileQuestionnaireInfo($from_date, $to_date, $all_fan_count, $dashboard_type);
        }
        if($dashboard_code == self::BRAND_PV_COUNT) {
            $dashboard_info = $this->getBrandPvCount($from_date, $to_date);
        }
        return $dashboard_info;
    }

    /**
     * ブランドの日々のファン人数取得
     * @param $dashboard_type
     * @return $dashboard_info
     */
    private function getDateBrandFanInfo($date_type, $from_date, $to_date) {
        $brand_fan_info = array();

        // 指定した期間中の日別の増加分
        $date_fan_condition = array(
            'brand_id' => $this->brand->id,
            'from_date' => $from_date,
            'to_date' => $to_date,
        );
        $date_fan_order = array(array('name' => 'register_date','direction' => 'ASC'));
        $date_fan_args = array('', $date_fan_order, '', '', '');
        $date_fan_count = $this->db->getBrandDateRegisterCount($date_fan_condition, $date_fan_args);

        // 指定した期間中の日別の減少（退会）分
        $date_withdraw_condition = array(
            'brand_id' => $this->brand->id,
            'from_date' => $from_date,
            'to_date' => $to_date,
        );
        $withdraw_fan_order = array(array('name' => 'withdraw_date','direction' => 'ASC'));
        $withdraw_fan_args = array('', $withdraw_fan_order, '', '', '');
        $date_withdraw_fan_count = $this->db->getBrandDateWithdrawCount($date_withdraw_condition, $withdraw_fan_args);

        if($date_type == self::DATE_TERM) {
            // 期間指定の場合は、指定した期間以前の累計も取得する
            $last_date_to = date('Y-m-d 23:59:59', strtotime("{$from_date} -1 day"));
            $prev_fan_condition = array(
                'brand_id' => $this->brand->id,
                'from_date' => $this->brand->created_at,
                'to_date' => $last_date_to,
            );
            $last_sum_count = intval($this->db->getBrandFanCount($prev_fan_condition, array())[0]['cnt']);
        } else {
            $last_sum_count = 0;
        }

        // 日付と取得数をキー・バリューの形に変換
        foreach($date_fan_count as $fan_count) {
            $tmp_fan_count[$fan_count['register_date']] = $fan_count['cnt'];
        }
        foreach($date_withdraw_fan_count as $withdraw_fan_count) {
            $tmp_withdraw_fan_count[$withdraw_fan_count['withdraw_date']] = $withdraw_fan_count['cnt'];
        }
        $periods = new DatePeriod(
            new DateTime($from_date),
            new DateInterval('P1D'),
            new DateTime($to_date)
        );
        $sum_count = $last_sum_count;
        foreach($periods as $period) {
            $period_format = $period->format('Y/m/d');
            if($tmp_fan_count[$period_format]) {
                $sum_count += intval($tmp_fan_count[$period_format]);
            }
            if($tmp_withdraw_fan_count[$period_format]) {
                $sum_count -= intval($tmp_withdraw_fan_count[$period_format]);
            }
            $brand_fan_info[$period_format][0] = $sum_count;

            $difference = $sum_count - $last_sum_count >= 0 ? '+'.number_format($sum_count - $last_sum_count) : number_format($sum_count - $last_sum_count);
            $brand_fan_info[$period_format][1] = $difference;
            $last_sum_count = $sum_count;
        }
        return $brand_fan_info;
    }

    private function getSnsFanInfo($from_date, $to_date, $all_fan_count) {
        $sns_info = array();

        /** @var BrandGlobalSettingService $brand_global_setting_service */
        $brand_global_setting_service = $this->service_factory->create('BrandGlobalSettingService');
        $original_sns_account = $brand_global_setting_service->getBrandGlobalSetting($this->brand->id, BrandGlobalSettingService::ORIGINAL_SNS_ACCOUNTS);

        $social_media_gdo = NULL;
        $social_media_linkedin = NULL;

        $original_sns_account_array = array();

        if($original_sns_account) {
            $original_sns_account_array = explode(',',$original_sns_account->content);

            if(in_array(SocialAccountService::SOCIAL_MEDIA_GDO, $original_sns_account_array)) {
                $social_media_gdo .= "__ON__";
            }

            if(in_array(SocialAccountService::SOCIAL_MEDIA_LINKEDIN, $original_sns_account_array)) {
                $social_media_linkedin .= "__ON__";
            }
        }

        $sns_fan_condition = array(
            'brand_id' => $this->brand->id,
            'from_date' => $from_date,
            'to_date' => $to_date,
            'social_media_gdo' => $social_media_gdo,
            'social_media_linkedin' => $social_media_linkedin
        );
        $sns_count_order = array(array('name' => 'tmp.social_media_id','direction' => 'ASC'));
        $sns_count_args = array('', $sns_count_order, '', '', '');
        $sns_fan_count = $this->db->getBrandSnsFanCount($sns_fan_condition, $sns_count_args);

        foreach($sns_fan_count as $sns_fan) {
            $social_media_num = SocialAccountService::$socialMediaOrder[$sns_fan['social_media_id']];
            $sns_info[$social_media_num]['cnt'] = intval($sns_fan['cnt']);
            if($sns_fan['cnt'] == $all_fan_count) {
                $sns_info[$social_media_num]['ratio'] = 100;
            } else {
                $sns_info[$social_media_num]['ratio'] = number_format(($sns_fan['cnt'] / $all_fan_count) * 100,1);
            }
        }

        foreach(SocialAccountService::$socialMediaOrder as $order_num) {

           if($order_num == SocialAccountService::$socialMediaOrder[SocialAccountService::SOCIAL_MEDIA_GDO]) {

               if(in_array(SocialAccountService::SOCIAL_MEDIA_GDO,$original_sns_account_array) && !array_key_exists($order_num, $sns_info)) {
                   $sns_info[$order_num] = array('cnt' => 0,'ratio' => 0);
               }

           } elseif($order_num == SocialAccountService::$socialMediaOrder[SocialAccountService::SOCIAL_MEDIA_LINKEDIN]) {

               if(in_array(SocialAccountService::SOCIAL_MEDIA_LINKEDIN,$original_sns_account_array) && !array_key_exists($order_num, $sns_info)) {
                   $sns_info[$order_num] = array('cnt' => 0,'ratio' => 0);
               }

           } else {

               if(!array_key_exists($order_num, $sns_info)) {
                   $sns_info[$order_num] = array('cnt' => 0,'ratio' => 0);
               }
           }

        }

        return $sns_info;
    }

    private function getSexFanInfo($from_date, $to_date, $all_fan_count) {
        $sex_info = array();

        $sex_count_condition = array(
            'brand_id' => $this->brand->id,
            'from_date' => $from_date,
            'to_date' => $to_date,
        );

        $sex_count_order = array(array('name' => 'I.sex','direction' => 'ASC'));
        $sex_count_args = array('', $sex_count_order, '', '', '');
        $sex_fan_count = $this->db->getBrandSexFanCount($sex_count_condition, $sex_count_args);

        foreach($sex_fan_count as $sex_fan) {
            if($sex_fan['sex'] == '') {
                continue;
            } else {
                $sex_type = $sex_fan['sex'];
            }
            $sex_info[$sex_type]['cnt'] = intval($sex_fan['cnt']);
            if($sex_fan['cnt'] == $all_fan_count) {
                $sex_info[$sex_type]['ratio'] = 100;
            } else {
                $sex_info[$sex_type]['ratio'] = number_format(($sex_fan['cnt']/$all_fan_count)*100,1);
            }
        }
        if(!array_key_exists(UserAttributeService::ATTRIBUTE_SEX_WOMAN, $sex_info)) {
            $sex_info[UserAttributeService::ATTRIBUTE_SEX_WOMAN]['cnt'] = 0;
            $sex_info[UserAttributeService::ATTRIBUTE_SEX_WOMAN]['ratio'] = 0;
        }

        if(!array_key_exists(UserAttributeService::ATTRIBUTE_SEX_MAN, $sex_info)) {
            $sex_info[UserAttributeService::ATTRIBUTE_SEX_MAN]['cnt'] = 0;
            $sex_info[UserAttributeService::ATTRIBUTE_SEX_MAN]['ratio'] = 0;
        }

        $sex_info[UserAttributeService::ATTRIBUTE_SEX_UNKWOWN]['cnt'] =
            $all_fan_count - $sex_info[UserAttributeService::ATTRIBUTE_SEX_WOMAN]['cnt'] - $sex_info[UserAttributeService::ATTRIBUTE_SEX_MAN]['cnt'];

        $sex_info[UserAttributeService::ATTRIBUTE_SEX_UNKWOWN]['ratio'] =
            round(($sex_info[UserAttributeService::ATTRIBUTE_SEX_UNKWOWN]['cnt'] / $all_fan_count ) * 100, 1);
        return $sex_info;
    }

    private function getAgeFanInfo($from_date, $to_date, $all_fan_count) {
        $age_info = array();
        $age_count_condition = array(
            'brand_id' => $this->brand->id,
            'from_date' => $from_date,
            'to_date' => $to_date,
        );

        $age_count_order = array(array('name' => 'age','direction' => 'ASC'));
        $age_count_args = array('', $age_count_order, '', '', '');
        $age_fan_count = $this->db->getBrandAgeFanCount($age_count_condition, $age_count_args);

        foreach($age_fan_count as $age_fan) {
            if($age_fan['age']) {
                $age_range = $age_fan['age'];
                $age_info[$age_range]['cnt'] = intval($age_fan['cnt']);
                if($age_fan['cnt'] == $all_fan_count) {
                    $age_info[$age_range]['ratio'] = 100;
                } else {
                    $age_info[$age_range]['ratio'] = number_format(($age_fan['cnt']/$all_fan_count)*100,1);
                }
            }
        }

        if(!array_key_exists(self::AGE_FAN_FROM_0_TO_19, $age_info)) {
            $age_info[self::AGE_FAN_FROM_0_TO_19] = array('cnt' => 0,'ratio' => 0);
        }
        if(!array_key_exists(self::AGE_FAN_FROM_20_TO_29, $age_info)) {
            $age_info[self::AGE_FAN_FROM_20_TO_29] = array('cnt' => 0,'ratio' => 0);
        }
        if(!array_key_exists(self::AGE_FAN_FROM_30_TO_39, $age_info)) {
            $age_info[self::AGE_FAN_FROM_30_TO_39] = array('cnt' => 0,'ratio' => 0);
        }
        if(!array_key_exists(self::AGE_FAN_FROM_40_TO_49, $age_info)) {
            $age_info[self::AGE_FAN_FROM_40_TO_49] = array('cnt' => 0,'ratio' => 0);
        }
        if(!array_key_exists(self::AGE_FAN_FROM_50_TO_100, $age_info)) {
            $age_info[self::AGE_FAN_FROM_50_TO_100] = array('cnt' => 0,'ratio' => 0);
        }
        $age_info[self::AGE_FAN_NOT_REGISTER]['cnt'] = $all_fan_count -
            ($age_info[self::AGE_FAN_FROM_0_TO_19]['cnt'] +
                $age_info[self::AGE_FAN_FROM_20_TO_29]['cnt'] +
                $age_info[self::AGE_FAN_FROM_30_TO_39]['cnt'] +
                $age_info[self::AGE_FAN_FROM_40_TO_49]['cnt'] +
                $age_info[self::AGE_FAN_FROM_50_TO_100]['cnt']);
        if($age_info[self::AGE_FAN_NOT_REGISTER]['cnt'] == $all_fan_count && $age_info[self::AGE_FAN_NOT_REGISTER]['cnt'] != 0) {
            $age_info[self::AGE_FAN_NOT_REGISTER]['ratio'] = 100;
        } else {
            $age_info[self::AGE_FAN_NOT_REGISTER]['ratio'] = $age_info[self::AGE_FAN_NOT_REGISTER]['cnt'] == 0 ? 0 : number_format(($age_info[self::AGE_FAN_NOT_REGISTER]['cnt']/$all_fan_count ) * 100, 1);
        }
        $age_info[self::AGE_FAN_FROM_0_TO_19]['name'] = $age_info[self::AGE_FAN_FROM_0_TO_19]['summary_name'] = '20才未満';
        $age_info[self::AGE_FAN_FROM_20_TO_29]['name'] = $age_info[self::AGE_FAN_FROM_20_TO_29]['summary_name'] = '20-29才';
        $age_info[self::AGE_FAN_FROM_30_TO_39]['name'] = $age_info[self::AGE_FAN_FROM_30_TO_39]['summary_name'] = '30-39才';
        $age_info[self::AGE_FAN_FROM_40_TO_49]['name'] = $age_info[self::AGE_FAN_FROM_40_TO_49]['summary_name'] = '40-49才';
        $age_info[self::AGE_FAN_FROM_50_TO_100]['name'] = $age_info[self::AGE_FAN_FROM_50_TO_100]['summary_name'] = '50才以上';
        $age_info[self::AGE_FAN_NOT_REGISTER]['name'] = $age_info[self::AGE_FAN_NOT_REGISTER]['summary_name'] = '未登録';

        return $age_info;
    }

    private function getAreaFanInfo($from_date, $to_date, $all_fan_count) {
        $area_info = array();
        $area_count_condition = array(
            'brand_id' => $this->brand->id,
            'from_date' => $from_date,
            'to_date' => $to_date,
        );

        $area_count_order = array(array('name' => 'cnt','direction' => 'DESC'),array('name' => 'S.pref_id','direction' => 'ASC'));
        $area_count_args = array('', $area_count_order, '', '', '');
        $area_fan_count = $this->db->getBrandAreaFanCount($area_count_condition, $area_count_args);

        $register_fan_count = 0;
        foreach($area_fan_count as $area_fan) {
            $pref_id = $area_fan['pref_id'];
            if($pref_id != 0) {
                $area_info[$pref_id]['cnt'] = intval($area_fan['cnt']);
                if($area_fan['cnt'] == $all_fan_count) {
                    $area_info[$pref_id]['ratio'] = 100;
                } else {
                    $area_info[$pref_id]['ratio'] = number_format(($area_fan['cnt'] / $all_fan_count) * 100,1);
                }
                $register_fan_count += $area_fan['cnt'];
            }
        }

        /** @var PrefectureService $prefecture_service */
        $prefecture_service = $this->service_factory->create('PrefectureService');

        $all_prefectures = $prefecture_service->getAllPrefectures();
        foreach($all_prefectures as $prefecture) {
            if(!array_key_exists($prefecture->id, $area_info)) {
                $area_info[$prefecture->id] = array('cnt' => 0,'ratio' => 0);
            }
            $area_info[$prefecture->id]['name'] = $prefecture->name;
        }
        $not_register_fan_count = $all_fan_count - $register_fan_count;
        // cntの降順に並び替え
        foreach($area_info as $key=>$value) {
            $cnt[$key] = $value['cnt'];
            $pref[$key] = $key;
        }
        array_multisort($cnt, SORT_DESC, $pref, SORT_ASC, $area_info);

        $area_info[47] = array('cnt' => $not_register_fan_count,
            'ratio' => $not_register_fan_count == 0 ? 0 : number_format((($all_fan_count - $register_fan_count)/$all_fan_count)*100,1),
            'name' => '未登録');
        return $area_info;
    }

    private function getProfileQuestionnaireInfo($from_date, $to_date, $all_fan_count, $dashboard_type) {
        /** @var CpQuestionnaireService $cp_questionnaire_service */
        $cp_questionnaire_service = $this->service_factory->create('CpQuestionnaireService', CpQuestionnaireService::TYPE_PROFILE_QUESTION);
        $relation_id = explode('/', $dashboard_type)[1];
        $questionnaires_questions_relation = $cp_questionnaire_service->getProfileQuestionRelationsById($relation_id);
        $question_id = $questionnaires_questions_relation->question_id;

        $questionnaire_info = array();

        $answer_count_condition = array(
            'question_id' => $question_id,
            'from_date' => $from_date,
            'to_date' => $to_date,
        );

        $answer_count_order = array(array('name' => 'C.choice_num','direction' => 'ASC'));
        $answer_count_args = array('', $answer_count_order, '', '', '');
        $questionnaire_answer_count = $this->db->getProfileQuestionnaireAnswerCount($answer_count_condition, $answer_count_args);

        $user_count_condition = array(
            'relation_id' => $relation_id,
            'from_date' => $from_date,
            'to_date' => $to_date,
        );

        $questionnaire_user_count = $this->db->getProfileQuestionnaireUserCount($user_count_condition, array())[0]['cnt'];

        // 設問IDと取得数をキー・バリューの形に変換
        foreach($questionnaire_answer_count as $answer_count) {
            $choice_num = $cp_questionnaire_service->getChoiceById($answer_count['choice_id'])->choice_num;
            $tmp_answer_count[$choice_num] = $answer_count['cnt'];
        }

        $question_choices = $cp_questionnaire_service->getChoicesByQuestionId($question_id);

        foreach($question_choices as $choice) {
            if(!$tmp_answer_count[$choice->choice_num]) {
                $questionnaire_info[$choice->choice_num] = array('cnt' => 0,'ratio' => 0);
            } else {
                $questionnaire_info[$choice->choice_num]['cnt'] = intval($tmp_answer_count[$choice->choice_num]);
                if($questionnaire_info[$choice->choice_num]['cnt'] == $all_fan_count) {
                    $questionnaire_info[$choice->choice_num]['ratio'] = 100;
                } else {
                    $questionnaire_info[$choice->choice_num]['ratio'] = number_format(($questionnaire_info[$choice->choice_num]['cnt']/$all_fan_count) * 100,1);
                }
            }
            $question_choice = $cp_questionnaire_service->getChoiceById($choice->id)->choice;
            $questionnaire_info[$choice->choice_num]['summary_name'] = Util::cutTextByWidth($question_choice, 180);
            $questionnaire_info[$choice->choice_num]['name'] = $question_choice;
        }

        // 未回答のユーザ
        if($all_fan_count == $questionnaire_user_count) {
            $not_answer_ratio = 0;
        } elseif($questionnaire_user_count == 0) {
            $not_answer_ratio = 100;
        } else {
            $not_answer_ratio = number_format((($all_fan_count - $questionnaire_user_count) / $all_fan_count) * 100, 1);
        }

        $questionnaire_info[-1]['cnt'] = $all_fan_count - $questionnaire_user_count;
        $questionnaire_info[-1]['ratio'] = $not_answer_ratio;
        $questionnaire_info[-1]['summary_name'] = $questionnaire_info[-1]['name'] = '未回答';

        return $questionnaire_info;
    }

    private function getBrandPvCount($from_date, $to_date) {
        /** @var CpQuestionnaireService $cp_questionnaire_service */
        $manager_brand_kpi_service = $this->service_factory->create('ManagerBrandKpiService');
        $managerBrandKpiColumn = $manager_brand_kpi_service->getManagerBrandKpiColumnByImport('jp.aainc.classes.manager_brand_kpi.BrandsPV');

        $from_summed_date = date('Y-m-d', strtotime($from_date . '-1 day'));
        $to_summed_date = date('Y-m-d', strtotime($to_date));

        $pv_count_order = array('name' => 'summed_date','direction' => 'ASC');
        $condition = array(
            'brand_id' => $this->brand->id,
            'column_id' => $managerBrandKpiColumn->id,
            'from_date' => $from_summed_date,
            'to_date' => $to_summed_date
        );
        $daily_pv_count = $this->db->getBrandDatePVCount($condition, $pv_count_order, '', '', '');

        // 日付と取得数をキー・バリューの形に変換
        foreach($daily_pv_count as $pv_count) {
            $brand_pv_data[$pv_count['summed_date']] = $pv_count['value'];
        }

        // 当日がto_dateだった場合、apiより取得して最後のキーとして加える
        if($to_summed_date == date('Y-m-d', strtotime('today'))) {
            AAFW::import('jp.aainc.classes.manager_brand_kpi.BrandsPV');
            /** @var BrandsPV $brand_pv_service */
            $brand_pv_service = new BrandsPV();
            $brand_pv_data[$to_summed_date] = $brand_pv_service->doExecute(date('Y-m-d', strtotime('today')), $this->brand->id);
        }

        $last_count = 0;
        foreach($brand_pv_data as $key => $value) {
            if($key > $from_summed_date) {
                $period_format = date('Y/m/d', strtotime($key));
                $brand_pv_info[$period_format][0] = intval($value);
                if(intval($value) - $last_count >= 0) {
                    $difference = '+'.number_format($value - $last_count);
                } else {
                    $difference = number_format($value - $last_count);
                }
                $brand_pv_info[$period_format][1] = $difference;
            }
            $last_count = intval($value);
        }
        return $brand_pv_info;
    }
}
