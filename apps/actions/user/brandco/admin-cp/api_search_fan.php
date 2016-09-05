<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');
AAFW::import('jp.aainc.classes.services.CpCreateSqlService');
AAFW::import('jp.aainc.classes.validator.SearchProfileValidator');
AAFW::import('jp.aainc.actions.user.brandco.admin-fan.SearchFanTrait');

class api_search_fan extends BrandcoPOSTActionBase {
    protected $ContainerName = 'show_user_list';
    protected $AllowContent = array('JSON');

    public $NeedOption = array();
    public $NeedAdminLogin = true;
    public $CsrfProtect = true;

    protected $search_condition = array();
    protected $split_search_key;

    use SearchFanTrait;

    public function beforeValidate() {

        if ($this->search_no) {

            // アンケート等のキーは、[サーチタイプ/ID]で構成されているので、サーチタイプだけを取り出す。
            $this->split_search_key = explode('/', $this->POST['search_type']);

            $this->search_condition = $this->getSearchProfileCondition($this->search_type, $this->search_no, $this->POST, $this->nullable);

            if ($this->split_search_key[0] <= CpCreateSqlService::SEARCH_DUPLICATE_ADDRESS) {
                foreach($this->POST as $key => $value) {

                    if (preg_match('/^switch_type\//', $key)) {
                        if (preg_match('/^'.CpCreateSqlService::SEARCH_PROFILE_QUESTIONNAIRE.'\//', $this->search_type)) {
                            $question_id = explode('/', $this->search_type)[1];
                            if (preg_match('/^switch_type\/'.CpCreateSqlService::SEARCH_PROFILE_QUESTIONNAIRE.'\/'.$question_id.'/', $key)) {
                                $this->search_condition[$key] = $value;
                            }
                        }
                    }
                }
            }

            if ($this->split_search_key[0] == CpCreateSqlService::SEARCH_PARTICIPATE_CONDITION) {
                $action_id = explode('/', $this->search_type)[1];
                foreach($this->POST as $key => $value) {
                    if ($this->nullable && $value === '') {
                        continue;
                    }
                    if (preg_match('/^search_participate_condition\/'.$action_id.'\//', $key)) {
                        $split_key = explode('/', $key);
                        // サーチ番号を除いてキーに入れる
                        $this->search_condition[$split_key[0].'/'.$split_key[1].'/'.$split_key[2]] = $value;
                    }

                    // スピードくじの場合は回数も入れる
                    if (preg_match('/^search_count_instant_win_from\/'.$action_id.'\//', $key) || preg_match('/^search_count_instant_win_to\/'.$action_id.'\//', $key)) {
                        $split_key = explode('/', $key);
                        // サーチ番号を除いてキーに入れる
                        $this->search_condition[$split_key[0].'/'.$split_key[1]] = $value;
                    }

                    if (preg_match('/^switch_type\//', $key)) {
                        $action_id = explode('/', $this->search_type)[1];
                        if (preg_match('/^switch_type\/'.CpCreateSqlService::SEARCH_PARTICIPATE_CONDITION.'\/'.$action_id.'/', $key)) {
                            $this->search_condition[$key] = $value;
                        }
                    }
                }
            }

            if ($this->split_search_key[0] == CpCreateSqlService::SEARCH_DELIVERY_TIME) {
                $action_id = $this->split_search_key[1];
                foreach($this->POST as $key => $value) {
                    if (preg_match('/^search_delivery_time\/'.$action_id.'/', $key)) {
                        $this->search_condition[$key] = $value;
                    }
                }
            }

            // PHOTO SHARE SNS
            if ($this->split_search_key[0] == CpCreateSqlService::SEARCH_PHOTO_SHARE_SNS) {
                $action_id = $this->split_search_key[1];
                foreach($this->POST as $key => $value) {
                    if (preg_match('/^search_photo_share_sns\/'.$action_id.'/', $key)) {
                        $this->search_condition[$key] = $value;
                    }
                    if ($key == 'switch_type/'.CpCreateSqlService::SEARCH_PHOTO_SHARE_SNS.'/'.$action_id) {
                        $this->search_condition[$key] = $value;
                    }
                }
            }

            if ($this->split_search_key[0] == CpCreateSqlService::SEARCH_PHOTO_SHARE_TEXT) {
                $action_id = $this->split_search_key[1];
                foreach($this->POST as $key => $value) {
                    if (preg_match('/^search_photo_share_text\/'.$action_id.'/', $key)) {
                        $this->search_condition[$key] = $value;
                    }
                }
            }

            if ($this->split_search_key[0] == CpCreateSqlService::SEARCH_PHOTO_APPROVAL_STATUS) {
                $action_id = $this->split_search_key[1];
                foreach($this->POST as $key => $value) {
                    if (preg_match('/^search_photo_approval_status\/'.$action_id.'/', $key)) {
                        $this->search_condition[$key] = $value;
                    }
                }
            }

            if ($this->split_search_key[0] == CpCreateSqlService::SEARCH_SHARE_TYPE) {
                foreach ($this->POST as $key => $value) {
                    if (preg_match('/^search_share_type/', $key)) {
                        $split_key = explode('/', $key);
                        // サーチ番号を除いてキーに入れる
                        $this->search_condition[$split_key[0].'/'.$split_key[1]] = $value;
                    }
                }
            }

            if ($this->split_search_key[0] == CpCreateSqlService::SEARCH_SHARE_TEXT) {
                foreach ($this->POST as $key => $value) {
                    if (preg_match('/^search_share_text/', $key)) {
                        $split_key = explode('/', $key);
                        // サーチ番号を除いてキーに入れる
                        $this->search_condition[$split_key[0].'/'.$split_key[1]] = $value;
                    }
                }
            }

            if ($this->split_search_key[0] == CpCreateSqlService::SEARCH_INSTAGRAM_HASHTAG_DUPLICATION) {
                $action_id = $this->split_search_key[1];
                foreach($this->POST as $key => $value) {
                    if (preg_match('/^search_instagram_hashtag_duplicate\/'.$action_id.'/', $key)) {
                        $this->search_condition[$key] = $value;
                    }
                }
            }

            if ($this->split_search_key[0] == CpCreateSqlService::SEARCH_INSTAGRAM_HASHTAG_REVERSE_POST_TIME) {
                $action_id = $this->split_search_key[1];
                foreach($this->POST as $key => $value) {
                    if (preg_match('/^search_instagram_hashtag_reverse\/'.$action_id.'/', $key)) {
                        $this->search_condition[$key] = $value;
                    }
                }
            }

            if ($this->split_search_key[0] == CpCreateSqlService::SEARCH_INSTAGRAM_HASHTAG_APPROVAL_STATUS) {
                $action_id = $this->split_search_key[1];
                foreach($this->POST as $key => $value) {
                    if (preg_match('/^search_instagram_hashtag_approval_status\/'.$action_id.'/', $key)) {
                        $this->search_condition[$key] = $value;
                    }
                }
            }

            if ($this->split_search_key[0] == CpCreateSqlService::SEARCH_YOUTUBE_CHANNEL_SUBSCRIPTION) {
                $action_id = $this->split_search_key[1];
                foreach($this->POST as $key => $value) {
                    if (preg_match('/^search_ytch_subscription_type\/'.$action_id.'/', $key)) {
                        $split_key = explode('/', $key);
                        $this->search_condition[$split_key[0].'/'.$split_key[1].'/'.$split_key[2]] = $value;
                    }
                }
            }

            if ($this->split_search_key[0] == CpCreateSqlService::SEARCH_FB_LIKE_TYPE) {
                $action_id = $this->split_search_key[1];

                foreach ($this->POST as $key => $value) {
                    if (preg_match('/^search_fb_like_type\/' . $action_id . '/', $key)) {
                        $split_key = explode('/', $key);
                        $this->search_condition[$split_key[0] . '/' . $split_key[1] . '/' . $split_key[2]] = $value;
                    }
                }
            }

            if ($this->split_search_key[0] == CpCreateSqlService::SEARCH_TW_FOLLOW_TYPE) {
                $action_id = $this->split_search_key[1];
                foreach ($this->POST as $key => $value) {
                    if (preg_match('/^search_tw_follow_type\/' . $action_id . '/', $key)) {
                        $split_key = explode('/', $key);
                        $this->search_condition[$split_key[0] . '/' . $split_key[1] . '/' . $split_key[2]] = $value;
                    }
                }
            }

            if ($this->split_search_key[0] == CpCreateSqlService::SEARCH_TWEET_TYPE) {
                $action_id = $this->split_search_key[1];
                foreach ($this->POST as $key => $value) {
                    if (preg_match('/^search_tweet_type\/' . $action_id . '/', $key)) {
                        $split_key = explode('/', $key);
                        $this->search_condition[$split_key[0] . '/' . $split_key[1] . '/' . $split_key[2]] = $value;
                    }
                }
            }

            if ($this->split_search_key[0] == CpCreateSqlService::SEARCH_POPULAR_VOTE_CANDIDATE) {
                $action_id = $this->split_search_key[1];
                foreach($this->POST as $key => $value) {
                    if (preg_match('/^search_popular_vote_candidate\/' . $action_id . '/', $key)) {
                        $this->search_condition[$key] = $value;
                    }
                    if ($key == 'switch_type/'.CpCreateSqlService::SEARCH_POPULAR_VOTE_CANDIDATE.'/'.$action_id) {
                        $this->search_condition[$key] = $value;
                    }
                }
            }

            if ($this->split_search_key[0] == CpCreateSqlService::SEARCH_POPULAR_VOTE_SHARE_SNS) {
                $action_id = $this->split_search_key[1];
                foreach ($this->POST as $key => $value) {
                    if (preg_match('/^search_popular_vote_share_sns\/' . $action_id . '/', $key)) {
                        $this->search_condition[$key] = $value;
                    }
                    if ($key == 'switch_type/' . CpCreateSqlService::SEARCH_POPULAR_VOTE_SHARE_SNS . '/' . $action_id) {
                        $this->search_condition[$key] = $value;
                    }
                }
            }

            if ($this->split_search_key[0] == CpCreateSqlService::SEARCH_POPULAR_VOTE_SHARE_TEXT) {
                $action_id = $this->split_search_key[1];
                foreach($this->POST as $key => $value) {
                    if (preg_match('/^search_popular_vote_share_text\/'.$action_id.'/', $key)) {
                        $this->search_condition[$key] = $value;
                    }
                }
            }
        }
    }

    public function validate() {
        $brand = $this->getBrand();

        // キャンペーンIDとアクションIDに関するバリデート
        $cp_validator = new CpValidator($brand->id);
        if (!$cp_validator->isOwner($this->cp_id)) {
            $errors['cp_id'] = '操作が失敗しました。';
            $json_data = $this->createAjaxResponse("ng", array(), $errors);
            $this->assign('json_data', $json_data);
            return false;
        }

        if (!$this->isFacebookMarketing && !$this->isFanListDownload) {
            if (!$cp_validator->isOwnerOfAction($this->action_id)) {
                $errors['action_id'] = '操作が失敗しました。';
                $json_data = $this->createAjaxResponse("ng", array(), $errors);
                $this->assign('json_data', $json_data);
                return false;
            }
        }

        if ($this->search_no) {
            if ($this->split_search_key[0] <= CpCreateSqlService::SEARCH_DUPLICATE_ADDRESS) {
                $search_profile_validator = new SearchProfileValidator($this->search_condition, $this->search_type, $this->nullable);
                $search_profile_validator->validate();
                if (!$search_profile_validator->isValid()) {
                    $errors = $search_profile_validator->getErrors();
                    $json_data = $this->createAjaxResponse("ng", array(), $errors);
                    $this->assign('json_data', $json_data);
                    return false;
                }
            }

            if ($this->split_search_key[0] == CpCreateSqlService::SEARCH_PARTICIPATE_CONDITION) {
                $search_paticipate_condition_validator = new SearchParticipateConditionValidator($this->search_condition, $this->search_type, $this->nullable);
                $search_paticipate_condition_validator->validate();
                if (!$search_paticipate_condition_validator->isValid()) {
                    $errors = $search_paticipate_condition_validator->getErrors();
                    $json_data = $this->createAjaxResponse("ng", array(), $errors);
                    $this->assign('json_data', $json_data);
                    return false;
                }
            }

            if ($this->split_search_key[0] == CpCreateSqlService::SEARCH_QUESTIONNAIRE) {
                $search_questionnaire_validator = new SearchQuestionnaireValidator($this->search_condition, $this->search_type, $this->nullable);
                $search_questionnaire_validator->validate();
                if (!$search_questionnaire_validator->isValid()) {
                    $errors = $search_questionnaire_validator->getErrors();
                    $json_data = $this->createAjaxResponse("ng", array(), $errors);
                    $this->assign('json_data', $json_data);
                    return false;
                }
            }

            if ($this->split_search_key[0] == CpCreateSqlService::SEARCH_DELIVERY_TIME) {
                if (!$this->nullable && !$this->search_condition) {
                    $error = array("searchError/".CpCreateSqlService::SEARCH_DELIVERY_TIME."/".$this->split_search_key[1] => array("1つ以上選択してください。"));
                    $json_data = $this->createAjaxResponse("ng", array(), $error);
                    $this->assign('json_data', $json_data);
                    return false;
                }

                if (!$cp_validator->isOwnerOfAction($this->split_search_key[1])) {
                    $json_data = $this->createAjaxResponse("ng", array(), "invalid action");
                    $this->assign('json_data', $json_data);
                    return false;
                }
            }

            if ($this->split_search_key[0] == CpCreateSqlService::SEARCH_SHARE_TYPE) {
                if (!$this->nullable && !$this->search_condition) {
                    $error = array("searchError/".CpCreateSqlService::SEARCH_SHARE_TYPE => array("1つ以上選択してください。"));
                    $json_data = $this->createAjaxResponse("ng", array(), $error);
                    $this->assign('json_data', $json_data);
                    return false;
                }
            }

            if ($this->split_search_key[0] == CpCreateSqlService::SEARCH_SHARE_TEXT) {
                if (!$this->nullable && !$this->search_condition) {
                    $error = array("searchError/" . CpCreateSqlService::SEARCH_SHARE_TEXT => array("1つ以上選択してください。"));
                    $json_data = $this->createAjaxResponse("ng", array(), $error);
                    $this->assign('json_data', $json_data);
                    return false;
                }
            }

            if ($this->split_search_key[0] == CpCreateSqlService::SEARCH_YOUTUBE_CHANNEL_SUBSCRIPTION) {
                if (!$this->nullable && !$this->search_condition) {
                    $error = array("searchError/" . CpCreateSqlService::SEARCH_YOUTUBE_CHANNEL_SUBSCRIPTION."/".$this->split_search_key[1] => array("1つ以上選択してください。"));
                    $json_data = $this->createAjaxResponse("ng", array(), $error);
                    $this->assign('json_data', $json_data);
                    return false;
                }
            }
            if (CpCreateSqlService::isPhotoQuery($this->split_search_key[0])) {
                $search_photo_validator = new SearchPhotoValidator ($this->search_condition, $this->search_type, $this->nullable);
                $search_photo_validator->validate();
                if (!$search_photo_validator->isValid()) {
                    $errors = $search_photo_validator->getErrors();
                    $json_data = $this->createAjaxResponse("ng", array(), $errors);
                    $this->assign('json_data', $json_data);
                    return false;
                }
            }

            if (CpCreateSqlService::isInstagramHashtagQuery($this->split_search_key[0])) {
                $search_instagram_hashtag_validator = new SearchInstagramHashtagValidator ($this->search_condition, $this->search_type, $this->nullable);
                $search_instagram_hashtag_validator->validate();
                if (!$search_instagram_hashtag_validator->isValid()) {
                    $errors = $search_instagram_hashtag_validator->getErrors();
                    $json_data = $this->createAjaxResponse("ng", array(), $errors);
                    $this->assign('json_data', $json_data);
                    return false;
                }
            }

            if ($this->split_search_key[0] == CpCreateSqlService::SEARCH_FB_LIKE_TYPE) {
                if (!$this->nullable && !$this->search_condition) {
                    $error = array("searchError/" . CpCreateSqlService::SEARCH_FB_LIKE_TYPE . '/' . $this->split_search_key[1] => array("1つ以上選択してください。"));
                    $json_data = $this->createAjaxResponse("ng", array(), $error);
                    $this->assign('json_data', $json_data);
                    return false;
                }
            }

            if ($this->split_search_key[0] == CpCreateSqlService::SEARCH_TW_FOLLOW_TYPE) {
                if (!$this->nullable && !$this->search_condition) {
                    $error = array('searchError/' . CpCreateSqlService::SEARCH_TW_FOLLOW_TYPE . '/' . $this->split_search_key[1] => array('1つ以上選択してください。'));
                    $json_data = $this->createAjaxResponse("ng", array(), $error);
                    $this->assign('json_data', $json_data);
                    return false;
                }
            }

            if ($this->split_search_key[0] == CpCreateSqlService::SEARCH_TWEET_TYPE) {
                if (!$this->search_condition) {
                    $error = array('searchError/' . CpCreateSqlService::SEARCH_TWEET_TYPE . '/' . $this->split_search_key[1] => array('1つ以上選択してください。'));
                    $json_data = $this->createAjaxResponse("ng", array(), $error);
                    $this->assign('json_data', $json_data);
                    return false;
                }
            }

            if (CpCreateSqlService::isPopularVoteQuery($this->split_search_key[0])) {
                $search_popular_vote_validator = new SearchPopularVoteValidator ($this->search_condition, $this->search_type, $this->nullable);
                $search_popular_vote_validator->validate();
                if (!$search_popular_vote_validator->isValid()) {
                    $errors = $search_popular_vote_validator->getErrors();
                    $json_data = $this->createAjaxResponse("ng", array(), $errors);
                    $this->assign('json_data', $json_data);
                    return false;
                }
            }

            //TODO ハードコーディング: カンコーブランドの追加カラムの絞り込み (条件バリデータ)
            if ($this->split_search_key[0] == CpCreateSqlService::SEARCH_CHILD_BIRTH_PERIOD) {
                $search_profile_validator = new SearchProfileValidator($this->search_condition, $this->search_type, $this->nullable);
                $search_profile_validator->validate();
                if (!$search_profile_validator->isValid()) {
                    $errors = $search_profile_validator->getErrors();
                    $json_data = $this->createAjaxResponse("ng", array(), $errors);
                    $this->assign('json_data', $json_data);
                    return false;
                }
            }
        }
        return true;
    }

    function doAction() {
        //TODO ハードコーディング: カンコーブランドの追加カラムの絞り込み
        if($this->search_type == CpCreateSqlService::SEARCH_CHILD_BIRTH_PERIOD) {
            $this->search_condition = $this->convertChildBirthPeriodToSearchCondition($this->search_condition, $this->search_type);
        }

        if ($this->search_no) {
            if ($this->search_condition && count($this->search_condition) > 0) {
                $session = $this->getSearchSession();
                $session[$this->search_type] = $this->search_condition;
                $this->setSearchSession($session);
            } else {
                $session = $this->getSearchSession();
                unset($session[$this->search_type]);
                $this->setSearchSession($session);
            }
        } elseif ($this->order) {
            $this->setBrandSession('orderCondition', null);
            $this->setBrandSession('orderCondition', array($this->search_type => intval($this->order)));
        } else {        // 検索番号もソートもない場合はクリアの時
            $session = $this->getSearchSession();
            $session = $this->resetSnsActionSearchCondition($session, $this->POST['search_type'], $this->POST['sns_action_key']);
            $this->setSearchSession($session);
        }

        $json_data = $this->createAjaxResponse("ok");
        $this->assign('json_data', $json_data);

        return 'dummy.php';
    }

    private function getSearchSession(){

        if ($this->isFacebookMarketing) {
            return $this->getBrandSession('searchConditionFacebookMarketing');
        }

        return $this->getSearchConditionSession($this->cp_id);
    }

    private function setSearchSession($session){
        if ($this->isFacebookMarketing) {
            $this->setBrandSession('searchConditionFacebookMarketing', $session);
        } else {
            $this->setSearchConditionSession($this->cp_id,$session);
        }
    }
}
