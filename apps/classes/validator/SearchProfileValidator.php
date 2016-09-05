<?php
AAFW::import('jp.aainc.classes.validator.BaseValidator');
AAFW::import('jp.aainc.classes.validator.SearchProfileValidator');

class SearchProfileValidator extends BaseValidator {

    private $search_condition;

    public function __construct($search_condition, $search_type, $nullable = false) {
        parent::__construct();
        $this->search_condition = $search_condition;
        $this->search_type = $search_type;
        $this->nullable = $nullable;
    }

    public function validate() {

        if ($this->nullable && count($this->search_condition) == 0) {
            return true;
        }

        //登録Noのバリデーション
        if($this->search_type == CpCreateSqlService::SEARCH_PROFILE_MEMBER_NO) {
            if($this->search_condition['search_profile_member_no_from'] === '') {
                $this->errors['searchError/'.CpCreateSqlService::SEARCH_PROFILE_MEMBER_NO][] = "絞り込み条件を入力してください。";
                return;
            }

            if($this->search_condition['search_profile_member_no_from'] !== '') { // fromの方は空でもPOSTはされるので、nullになることはない
                if(preg_match("/,/",$this->search_condition['search_profile_member_no_from'])) {
                    $search_member_numbers = explode(',', $this->search_condition['search_profile_member_no_from']);
                } else {
                    $search_member_numbers[] = $this->search_condition['search_profile_member_no_from'];
                }
                foreach($search_member_numbers as $search_member_number) {
                    if(!preg_match("/^[0-9]+$/",$search_member_number)) {
                        $this->errors['searchError/'.CpCreateSqlService::SEARCH_PROFILE_MEMBER_NO][] = "半角数字または半角カンマで入力してください。";
                        return;
                    } elseif($search_member_number == 0) {
                        $this->errors['searchError/'.CpCreateSqlService::SEARCH_PROFILE_MEMBER_NO][] = "1以上の数字で入力してください。";
                        return;
                    }
                }
            }
        }

        //登録期間のバリデーション
        if($this->search_type == CpCreateSqlService::SEARCH_PROFILE_REGISTER_PERIOD) {
            if(!$this->search_condition['search_profile_register_period_from'] && !$this->search_condition['search_profile_register_period_to']) {
                $this->errors['searchError/'.CpCreateSqlService::SEARCH_PROFILE_REGISTER_PERIOD][] = "絞り込み条件を入力してください。";
                return;
            }
            if($this->search_condition['search_profile_register_period_from']) {
                if(!strptime($this->search_condition['search_profile_register_period_from'], '%Y/%m/%d') || !$this->isValidDate($this->search_condition['search_profile_register_period_from'])) {
                    $this->errors['searchError/'.CpCreateSqlService::SEARCH_PROFILE_REGISTER_PERIOD][] = "日付形式で入力してください。";
                    return;
                }
            }
            if($this->search_condition['search_profile_register_period_to']) {
                if(!strptime($this->search_condition['search_profile_register_period_to'], '%Y/%m/%d') || !$this->isValidDate($this->search_condition['search_profile_register_period_to'])) {
                    $this->errors['searchError/'.CpCreateSqlService::SEARCH_PROFILE_REGISTER_PERIOD][] = "日付形式で入力してください。";
                    return;
                }
            }
            if(($this->search_condition['search_profile_register_period_from'] && $this->search_condition['search_profile_register_period_to']) &&
               ($this->search_condition['search_profile_register_period_from'] > $this->search_condition['search_profile_register_period_to'])) {
                $this->errors['searchError/'.CpCreateSqlService::SEARCH_PROFILE_REGISTER_PERIOD][] = "範囲の指定順序が正しくありません。";
                return;
            }
        }

        //連携済SNSのバリデーション
        if (preg_match('/^'.CpCreateSqlService::SEARCH_PROFILE_SOCIAL_ACCOUNT.'\//', $this->search_type)) {
            if(!$this->search_condition) {
                $this->errors['searchError/'.$this->search_type][] = "1つ以上選択してください。";
                return;
            }
            $social_media_id = explode('/',$this->search_type)[1];
            if($this->search_condition['search_friend_count_from/'.$social_media_id] !== '' && !is_null($this->search_condition['search_friend_count_from/'.$social_media_id])) {
                if (!preg_match("/^[0-9]+$/", $this->search_condition['search_friend_count_from/' . $social_media_id])) {
                    $this->errors['searchError/' . $this->search_type][] = "半角数字で入力してください。";
                    return;
                }
            }
            if($this->search_condition['search_friend_count_to/'.$social_media_id] !== '' && !is_null($this->search_condition['search_friend_count_to/'.$social_media_id])) {
                if (!preg_match("/^[0-9]+$/", $this->search_condition['search_friend_count_to/' . $social_media_id])) {
                    $this->errors['searchError/' . $this->search_type][] = "半角数字で入力してください。";
                    return;
                }
            }
            if($this->search_condition['search_friend_count_from/'.$social_media_id] !== '' && $this->search_condition['search_friend_count_to/'.$social_media_id] !== '') {
                if($this->search_condition['search_friend_count_from/'.$social_media_id] > $this->search_condition['search_friend_count_to/'.$social_media_id]) {
                    $this->errors['searchError/' . $this->search_type][] = "範囲の指定順序が正しくありません。";
                    return;
                }
            }
        }

        //SNS合計カラムのバリデーション
        if($this->search_type == CpCreateSqlService::SEARCH_SOCIAL_ACCOUNT_SUM) {
            $from_value = $this->search_condition['search_friend_count_sum_from'];
            $to_value = $this->search_condition['search_friend_count_sum_to'];
            
            if($from_value === '' && $to_value === '' &&
                $this->search_condition['search_link_sns_count_from'] === '' && $this->search_condition['search_link_sns_count_to'] === '') {
                $this->errors['searchError/'.CpCreateSqlService::SEARCH_SOCIAL_ACCOUNT_SUM][] = "絞り込み条件を入力してください。";
                return;
            }
            if($from_value !== '') {
                if(!preg_match("/^[0-9]+$/",$from_value)) {
                    $this->errors['searchError/'.CpCreateSqlService::SEARCH_SOCIAL_ACCOUNT_SUM][] = "半角数字で入力してください。";
                    return;
                }
            }
            if($to_value !== '') {
                if(!preg_match("/^[0-9]+$/",$to_value)) {
                    $this->errors['searchError/'.CpCreateSqlService::SEARCH_SOCIAL_ACCOUNT_SUM][] = "半角数字で入力してください。";
                    return;
                }
            }
            //FB ads絞り込みでsearch_link_sns_count絞り込まない
            if(!$this->nullable && $this->search_condition['search_link_sns_count_from'] !== '') {
                if(!preg_match("/^[0-9]+$/",$this->search_condition['search_link_sns_count_from'])) {
                    $this->errors['searchError/'.CpCreateSqlService::SEARCH_SOCIAL_ACCOUNT_SUM][] = "半角数字で入力してください。";
                    return;
                }
            }
            if(!$this->nullable && $this->search_condition['search_link_sns_count_to'] !== '') {
                if(!preg_match("/^[0-9]+$/",$this->search_condition['search_link_sns_count_to'])) {
                    $this->errors['searchError/'.CpCreateSqlService::SEARCH_SOCIAL_ACCOUNT_SUM][] = "半角数字で入力してください。";
                    return;
                }
            }
            if(($from_value !== '' && $to_value !== '') &&
                ($from_value > $to_value)) {
                $this->errors['searchError/'.CpCreateSqlService::SEARCH_SOCIAL_ACCOUNT_SUM][] = "範囲の指定順序が正しくありません。";
                return;
            }
            if(($this->search_condition['search_link_sns_count_from'] !== '' && $this->search_condition['search_link_sns_count_to'] !== '') &&
                ($this->search_condition['search_link_sns_count_from'] > $this->search_condition['search_link_sns_count_to'])) {
                $this->errors['searchError/'.CpCreateSqlService::SEARCH_SOCIAL_ACCOUNT_SUM][] = "範囲の指定順序が正しくありません。";
                return;
            }
        }

        //最終ログインのバリデーション
        if($this->search_type == CpCreateSqlService::SEARCH_PROFILE_LAST_LOGIN) {
            if(!$this->search_condition['search_profile_last_login_from'] && !$this->search_condition['search_profile_last_login_to']) {
                $this->errors['searchError/'.CpCreateSqlService::SEARCH_PROFILE_LAST_LOGIN][] = "絞り込み条件を入力してください。";
                return;
            }

            if($this->search_condition['search_profile_last_login_from']) {
                if(!strptime($this->search_condition['search_profile_last_login_from'], '%Y/%m/%d %H:%M:%S') || !$this->isValidDate($this->search_condition['search_profile_last_login_from'])) {
                    $this->errors['searchError/'.CpCreateSqlService::SEARCH_PROFILE_LAST_LOGIN][] = "日付形式で入力してください。";
                    return;
                }
            }
            if($this->search_condition['search_profile_last_login_to']) {
                if(!strptime($this->search_condition['search_profile_last_login_to'], '%Y/%m/%d %H:%M:%S') || !$this->isValidDate($this->search_condition['search_profile_last_login_to'])) {
                    $this->errors['searchError/'.CpCreateSqlService::SEARCH_PROFILE_LAST_LOGIN][] = "日付形式で入力してください。";
                    return;
                }
            }
            if(($this->search_condition['search_profile_last_login_from'] && $this->search_condition['search_profile_last_login_to']) && 
               ($this->search_condition['search_profile_last_login_from'] > $this->search_condition['search_profile_last_login_to'])) {
                $this->errors['searchError/'.CpCreateSqlService::SEARCH_PROFILE_LAST_LOGIN][] = "範囲の指定順序が正しくありません。";
                return;
            }
        }

        //回数に関する項目のバリデーション
        if($count_item = CpCreateSqlService::$search_count_item[$this->search_type]) {
            if(!$this->search_condition) {
                $this->errors['searchError/'.$this->search_type][] = "1つ以上選択してください。";
                return;
            }
            if($this->search_condition[$count_item.'_from'] !== '') {
                if(!preg_match("/^[0-9]+$/",$this->search_condition[$count_item.'_from'])) {
                    $this->errors['searchError/'.$this->search_type][] = "半角数字で入力してください。";
                    return;
                }
            }
            if($this->search_condition[$count_item.'_to'] !== '') {
                if(!preg_match("/^[0-9]+$/",$this->search_condition[$count_item.'_to'])) {
                    $this->errors['searchError/'.$this->search_type][] = "半角数字で入力してください。";
                    return;
                }
            }
            if($this->search_condition[$count_item.'_from'] !== '' && $this->search_condition[$count_item.'_to'] !== '') {
                if($this->search_condition[$count_item.'_from'] > $this->search_condition[$count_item.'_to']) {
                    $this->errors['searchError/' . $this->search_type][] = "範囲の指定順序が正しくありません。";
                    return;
                }
            }
            if (!$this->nullable && $this->search_condition[$count_item.'_from'] === '' && $this->search_condition[$count_item.'_to'] === '') {
                $this->errors['searchError/' . $this->search_type][] = "絞り込み条件を入力してください。";
                return;
            }
        }

        //開封率に関する項目のバリデーション
        if($this->search_type == CpCreateSqlService::SEARCH_MESSAGE_READ_RATIO) {
            if($this->search_condition['search_message_ratio_from'] === '' && $this->search_condition['search_message_ratio_to'] === '') {
                $this->errors['searchError/'.CpCreateSqlService::SEARCH_MESSAGE_READ_RATIO][] = "絞り込み条件を入力してください。";
                return;
            }
            if($this->search_condition['search_message_ratio_from'] !== '') {
                if(!preg_match("/^[0-9]+$/",$this->search_condition['search_message_ratio_from'])) {
                    $this->errors['searchError/'.CpCreateSqlService::SEARCH_MESSAGE_READ_RATIO][] = "半角数字で入力してください。";
                    return;
                }
            }
            if($this->search_condition['search_message_ratio_to'] !== '') {
                if(!preg_match("/^[0-9]+$/",$this->search_condition['search_message_ratio_to'])) {
                    $this->errors['searchError/'.CpCreateSqlService::SEARCH_MESSAGE_READ_RATIO][] = "半角数字で入力してください。";
                    return;
                }
            }
            if(($this->search_condition['search_message_ratio_from'] !== '' && $this->search_condition['search_message_ratio_to'] !== '') &&
                ($this->search_condition['search_message_ratio_from'] > $this->search_condition['search_message_ratio_to'])) {
                $this->errors['searchError/'.CpCreateSqlService::SEARCH_MESSAGE_READ_RATIO][] = "範囲の指定順序が正しくありません。";
                return;
            }
        }

        //評価のバリデーション
        if($this->search_type == CpCreateSqlService::SEARCH_PROFILE_RATE) {
            if(!$this->search_condition) {
                $this->errors['searchError/'.CpCreateSqlService::SEARCH_PROFILE_RATE][] = "1つ以上選択してください。";
                return;
            }
        }

        //性別のバリデーション
        if($this->search_type == CpCreateSqlService::SEARCH_PROFILE_SEX) {
            if(!$this->search_condition) {
                $this->errors['searchError/'.CpCreateSqlService::SEARCH_PROFILE_SEX][] = "1つ以上選択してください。";
                return;
            }
        }

        //都道府県のバリデーション
        if($this->search_type == CpCreateSqlService::SEARCH_PROFILE_ADDRESS) {
            if(!$this->search_condition) {
                $this->errors['searchError/'.CpCreateSqlService::SEARCH_PROFILE_ADDRESS][] = "1つ以上選択してください。";
                return;
            }
        }

        //年齢のバリデーション
        if($this->search_type == CpCreateSqlService::SEARCH_PROFILE_AGE) {
            if($this->search_condition['search_profile_age_from'] !== '') {
                if(!preg_match("/^[0-9]+$/",$this->search_condition['search_profile_age_from'])) {
                    $this->errors['searchError/'.CpCreateSqlService::SEARCH_PROFILE_AGE][] = "半角数字で入力してください。";
                    return;
                }
            }
            if($this->search_condition['search_profile_age_to'] !== '') {
                if(!preg_match("/^[0-9]+$/",$this->search_condition['search_profile_age_to'])) {
                    $this->errors['searchError/'.CpCreateSqlService::SEARCH_PROFILE_AGE][] = "半角数字で入力してください。";
                    return;
                }
            }
            if(($this->search_condition['search_profile_age_from'] !== '' && $this->search_condition['search_profile_age_to'] !== '') &&
                ($this->search_condition['search_profile_age_from'] > $this->search_condition['search_profile_age_to'])) {
                    $this->errors['searchError/'.CpCreateSqlService::SEARCH_PROFILE_AGE][] = "範囲の指定順序が正しくありません。";
                    return;
            }
            if($this->search_condition['search_profile_age_from'] === '' && $this->search_condition['search_profile_age_to'] === '' && !$this->search_condition['search_profile_age_not_set']) {
                $this->errors['searchError/'.CpCreateSqlService::SEARCH_PROFILE_AGE][] = "絞り込み条件を入力してください。";
                return;
            }
        }

        //アンケート回答状況のバリデーション
        if($this->search_type == CpCreateSqlService::SEARCH_PROFILE_QUESTIONNAIRE_STATUS) {
            if(!$this->search_condition) {
                $this->errors['searchError/'.CpCreateSqlService::SEARCH_PROFILE_QUESTIONNAIRE_STATUS][] = "1つ以上選択してください。";
                return;
            }
        }

        //参加時アンケートのバリデーション
        if(preg_match('/^'.CpCreateSqlService::SEARCH_PROFILE_QUESTIONNAIRE.'\//', $this->search_type)) {
            $question_id = explode('/',$this->search_type)[1];
            $exist = false;
            foreach($this->search_condition as $key=>$value) {
                if(!preg_match('/^switch_type\//', $key)) {
                    $exist = true;
                }
            }
            if(!$exist) {
                $this->errors['searchError/'.CpCreateSqlService::SEARCH_PROFILE_QUESTIONNAIRE.'/'.$question_id][] = "1つ以上選択してください。";
                return;
            }
        }

        //コンバージョンタグバリデーション
        if (preg_match('/^'.CpCreateSqlService::SEARCH_PROFILE_CONVERSION.'\//', $this->search_type)) {
            $conversion_id = explode('/',$this->search_type)[1];
            $from_value = $this->search_condition['search_profile_conversion_from/'.$conversion_id];
            $to_value = $this->search_condition['search_profile_conversion_to/'.$conversion_id];
            
            if($from_value !== '') {
                if(!preg_match("/^[0-9]+$/",$from_value)) {
                    $this->errors['searchError/'.CpCreateSqlService::SEARCH_PROFILE_CONVERSION.'/'.$conversion_id][] = "半角数字で入力してください。";
                    return;
                }
            }
            if($to_value !== '') {
                if(!preg_match("/^[0-9]+$/",$to_value)) {
                    $this->errors['searchError/'.CpCreateSqlService::SEARCH_PROFILE_CONVERSION.'/'.$conversion_id][] = "半角数字で入力してください。";
                    return;
                }
            }
            if(($from_value !== '' && $to_value !== '') &&
                ($from_value > $to_value)) {
                $this->errors['searchError/'.CpCreateSqlService::SEARCH_PROFILE_CONVERSION.'/'.$conversion_id][] = "範囲の指定順序が正しくありません。";
                return;
            }
            if($from_value === '' && $to_value === '') {
                $this->errors['searchError/'.CpCreateSqlService::SEARCH_PROFILE_CONVERSION.'/'.$conversion_id][] = "絞り込み条件を入力してください。";
                return;
            }
        }

        //外部インポートデータのバリデーション
        if(preg_match('/^'.CpCreateSqlService::SEARCH_IMPORT_VALUE.'\//', $this->search_type)) {
            $definition_id = explode('/',$this->search_type)[1];
            if(!$this->search_condition) {
                $this->errors['searchError/'.CpCreateSqlService::SEARCH_IMPORT_VALUE.'/'.$definition_id][] = "1つ以上選択してください。";
                return;
            }
        }

        //いいねのバリデーション
        if($this->search_type == CpCreateSqlService::SEARCH_SOCIAL_ACCOUNT_INTERACTIVE) {
            if(!$this->search_condition) {
                $this->errors['searchError/'.$this->search_type][] = "1つ以上選択してください。";
                return;
            } else {
                foreach($this->search_condition as $key=>$value) {
                    if(preg_match('/^search_social_account_is_replied_count\//', $key) && preg_match('/Y$/', $key) ) {
                        $is_replied_count = true;
                    }
                    if(preg_match('/^search_tw_tweet_reply_count\//', $key) && preg_match('/from$/', $key) ) {
                        $replied_from = $value;
                    }
                    if(preg_match('/^search_tw_tweet_reply_count\//', $key) && preg_match('/to$/', $key) ) {
                        $replied_to = $value;
                    }
                    if(preg_match('/^search_social_account_is_retweeted_count\//', $key) && preg_match('/Y$/', $key) ) {
                        $is_retweeted_count = true;
                    }
                    if(preg_match('/^search_tw_tweet_retweet_count\//', $key) && preg_match('/from$/', $key) ) {
                        $retweeted_from = $value;
                    }
                    if(preg_match('/^search_tw_tweet_retweet_count\//', $key) && preg_match('/to$/', $key) ) {
                        $retweeted_to = $value;
                    }

                    if(preg_match('/^search_social_account_is_liked_count\//', $key) && preg_match('/Y$/', $key) ) {
                        $is_liked_count = true;
                    }
                    if(preg_match('/^search_fb_posts_like_count\//', $key) && preg_match('/from$/', $key) ) {
                        $liked_from = $value;
                    }
                    if(preg_match('/^search_fb_posts_like_count\//', $key) && preg_match('/to$/', $key) ) {
                        $liked_to = $value;
                    }
                    if(preg_match('/^search_social_account_is_commented_count\//', $key) && preg_match('/Y$/', $key) ) {
                        $is_commented_count = true;
                    }
                    if(preg_match('/^search_fb_posts_comment_count\//', $key) && preg_match('/from$/', $key) ) {
                        $commented_from = $value;
                    }
                    if(preg_match('/^search_fb_posts_comment_count\//', $key) && preg_match('/to$/', $key) ) {
                        $commented_to = $value;
                    }
                }
                if($is_replied_count){
                    if($replied_from != '' && $replied_from <1 ){
                        $this->errors['searchError/'.$this->search_type][] = "1以上で入力して下さい。";
                        return;
                    }
                    if($replied_to != '' && $replied_to <1 ){
                        $this->errors['searchError/'.$this->search_type][] = "1以上で入力して下さい。";
                        return;
                    }
                    if($replied_from != '' && $replied_to != '' && $replied_from > $replied_to){
                        $this->errors['searchError/'.$this->search_type][] = "範囲の指定順序が正しくありません。";
                        return;
                    }
                }
                if($is_liked_count){
                    if($liked_from != '' && $liked_from <1 ){
                        $this->errors['searchError/'.$this->search_type][] = "1以上で入力して下さい。";
                        return;
                    }
                    if($liked_to != '' && $liked_to <1 ){
                        $this->errors['searchError/'.$this->search_type][] = "1以上で入力して下さい。";
                        return;
                    }
                    if($liked_from != '' && $liked_to != '' && $liked_from > $liked_to){
                        $this->errors['searchError/'.$this->search_type][] = "範囲の指定順序が正しくありません。";
                        return;
                    }
                }
                if($is_retweeted_count){
                    if($retweeted_from != '' && $retweeted_from <1 ){
                        $this->errors['searchError/'.$this->search_type][] = "1以上で入力して下さい。";
                        return;
                    }
                    if($retweeted_to != '' && $retweeted_to <1 ){
                        $this->errors['searchError/'.$this->search_type][] = "1以上で入力して下さい。";
                        return;
                    }
                    if($retweeted_from != '' && $retweeted_to != '' && $retweeted_from > $retweeted_to){
                        $this->errors['searchError/'.$this->search_type][] = "範囲の指定順序が正しくありません。";
                        return;
                    }
                }
                if($is_commented_count){
                    if($commented_from != '' && $commented_from <1 ){
                        $this->errors['searchError/'.$this->search_type][] = "1以上で入力して下さい。";
                        return;
                    }
                    if($commented_to != '' && $commented_to <1 ){
                        $this->errors['searchError/'.$this->search_type][] = "1以上で入力して下さい。";
                        return;
                    }
                    if($commented_from != '' && $commented_to != '' && $commented_from > $commented_to){
                        $this->errors['searchError/'.$this->search_type][] = "範囲の指定順序が正しくありません。";
                        return;
                    }
                }
            }
        }

        //重複住所バリデーション
        if($this->search_type == CpCreateSqlService::SEARCH_DUPLICATE_ADDRESS) {

            if(!$this->search_condition['search_duplicate_address/'.CpCreateSqlService::SHIPPING_ADDRESS_DUPLICATE.'/'.CpCreateSqlService::HAVE_ADDRESS] && !$this->search_condition['search_duplicate_address/'.CpCreateSqlService::SHIPPING_ADDRESS_DUPLICATE.'/'.CpCreateSqlService::NOT_HAVE_ADDRESS]
                && !$this->search_condition['search_duplicate_address/'.CpCreateSqlService::SHIPPING_ADDRESS_USER_DUPLICATE.'/'.CpCreateSqlService::HAVE_ADDRESS] && !$this->search_condition['search_duplicate_address/'.CpCreateSqlService::SHIPPING_ADDRESS_USER_DUPLICATE.'/'.CpCreateSqlService::NOT_HAVE_ADDRESS]
            ) {

                $this->errors['searchError/'.$this->search_type][] = "絞り込み条件を入力してください。";

                return;

            }

            if($this->search_condition['search_duplicate_address_from'] && $this->search_condition['search_duplicate_address_from'] !== '') {

                if(!preg_match("/^[0-9]+$/",$this->search_condition['search_duplicate_address_from'])) {

                    $this->errors['searchError/'.$this->search_type][] = "半角数字で入力してください。";
                    return;

                }
            }

            if($this->search_condition['search_duplicate_address_to'] && $this->search_condition['search_duplicate_address_to'] !== '') {

                if(!preg_match("/^[0-9]+$/",$this->search_condition['search_duplicate_address_to'])) {

                    $this->errors['searchError/'.$this->search_type][] = "半角数字で入力してください。";
                    return;
                    
                }
            }

            if(($this->search_condition['search_duplicate_address_from'] != null && $this->search_condition['search_duplicate_address_from'] == 0)|| ($this->search_condition['search_duplicate_address_to'] != null && $this->search_condition['search_duplicate_address_to'] == 0)) {
                $this->errors['searchError/'.$this->search_type][] = "1以上で入力して下さい。";
                return;
            }

            if($this->search_condition['search_duplicate_address_from'] && $this->search_condition['search_duplicate_address_from'] !== '' &&
                $this->search_condition['search_duplicate_address_to'] && $this->search_condition['search_duplicate_address_to'] !== '' &&
                $this->search_condition['search_duplicate_address_from'] > $this->search_condition['search_duplicate_address_to']) {

                $this->errors['searchError/'.$this->search_type][] = "範囲の指定順序が正しくありません。";
                return;
            }

        }

        //TODO ハードコーディング: カンコーブランドの追加カラムの絞り込み（バリデータ）
        if ($this->search_type == CpCreateSqlService::SEARCH_CHILD_BIRTH_PERIOD) {
            $relation_id = explode('/', $this->search_type)[1];
            $from_value = $this->search_condition['search_child_birth_period_from'.'/'.$relation_id];
            $to_value = $this->search_condition['search_child_birth_period_to'.'/'.$relation_id];

            if (Util::isNullOrEmpty($from_value) && Util::isNullOrEmpty($to_value)) {
                $this->errors['searchError/' . $this->search_type][] = "絞り込み条件を入力してください。";
                return;
            }

            if (($from_value && !preg_match("/^[0-9]+$/", $from_value)) || ($to_value && !preg_match("/^[0-9]+$/", $to_value)) ) {
                $this->errors['searchError/' . $this->search_type][] = "半角数字で入力してください。";
                return;
            }

            if ($from_value && $to_value && $from_value >= $to_value) {
                $this->errors['searchError/' . $this->search_type][] = "正しい期間を入力してください。";
                return;
            }
        }
    }

    /**
     * Check if datetime format is available
     * @param $date
     * @return bool
     */
    public function isValidDate($date) {
        return date('Y/m/d', strtotime($date)) != '1970/01/01';
    }
}
