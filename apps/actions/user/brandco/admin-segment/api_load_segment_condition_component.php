<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.classes.services.SegmentCreateSqlService');
AAFW::import('jp.aainc.classes.entities.SegmentProvision');

class api_load_segment_condition_component extends BrandcoGETActionBase {

    protected $AllowContent = array('JSON');

    public $NeedOption = array();

    public function validate() {
        return true;
    }

    public function doAction() {
        $condition_key = $this->GET['condition_key'];
        parse_str($this->GET['condition_value'], $condition_value);

        $cur_action_type = $this->GET['action_type'];
        $not_condition_flg = $condition_value['not_condition_flg'];
        unset($condition_value['not_condition_flg']);

        if ($cur_action_type == 1) {
            $or_label_flg = $this->GET['cur_condition_type'] == 'or';
            $or_condition_flg = true;
        } elseif ($cur_action_type == 2) {
            $or_condition_flg = $condition_value['or_condition_flg'] == 'on';
            $or_label_flg = $condition_value['or_label_flg'] == 'on';

            unset($condition_value['or_condition_flg']);
            unset($condition_value['or_label_flg']);
        }
        
        $split_search_key = explode('/', $condition_key);

        if ($split_search_key[0] == CpCreateSqlService::SEARCH_PROFILE_LAST_LOGIN) {
            if (!Util::isNullOrEmpty($condition_value['search_profile_last_login_from'])) {
                $condition_value['search_profile_last_login_from'] .= ' 00:00:00';
            }

            if (!Util::isNullOrEmpty($condition_value['search_profile_last_login_to'])) {
                $condition_value['search_profile_last_login_to'] .= ' 23:59:59';
            }
        }

        if ($split_search_key[0] <= CpCreateSqlService::SEARCH_DUPLICATE_ADDRESS) {
            $search_profile_validator = new SearchProfileValidator($condition_value, $condition_key);
            $search_profile_validator->validate();
            if (!$search_profile_validator->isValid()) {
                $error = current($search_profile_validator->getErrors());
                $json_data = $this->createAjaxResponse("ng", array(), array('error_msg' => $error[0]));
                $this->assign('json_data', $json_data);
                return false;
            }
        }

        if ($split_search_key[0] == CpCreateSqlService::SEARCH_PARTICIPATE_CONDITION) {
            $search_participate_condition_validator = new SearchParticipateConditionValidator($condition_value, $condition_key);
            $search_participate_condition_validator->validate();
            if (!$search_participate_condition_validator->isValid()) {
                $error = current($search_participate_condition_validator->getErrors());
                $json_data = $this->createAjaxResponse("ng", array(), array('error_msg' => $error[0]));
                $this->assign('json_data', $json_data);
                return false;
            }
        }

        if ($split_search_key[0] == CpCreateSqlService::SEARCH_QUESTIONNAIRE) {
            $search_questionnaire_validator = new SearchQuestionnaireValidator($condition_value, $condition_key);
            $search_questionnaire_validator->validate();
            if (!$search_questionnaire_validator->isValid()) {
                $error = current($search_questionnaire_validator->getErrors());
                $json_data = $this->createAjaxResponse("ng", array(), array('error_msg' => $error[0]));
                $this->assign('json_data', $json_data);
                return false;
            }
        }

        if ($split_search_key[0] == CpCreateSqlService::SEARCH_DELIVERY_TIME) {
            if (!$this->nullable && !$condition_value) {
                $error = array("searchError/".CpCreateSqlService::SEARCH_DELIVERY_TIME."/".$split_search_key[1] => array("1つ以上選択してください。"));
                $json_data = $this->createAjaxResponse("ng", array(), array('error_msg' => $error[0]));
                $this->assign('json_data', $json_data);
                return false;
            }
        }

        if ($split_search_key[0] == CpCreateSqlService::SEARCH_SHARE_TYPE) {
            if (!$this->nullable && !$condition_value) {
                $error = array("searchError/".CpCreateSqlService::SEARCH_SHARE_TYPE => array("1つ以上選択してください。"));
                $json_data = $this->createAjaxResponse("ng", array(), array('error_msg' => $error[0]));
                $this->assign('json_data', $json_data);
                return false;
            }
        }

        if ($split_search_key[0] == CpCreateSqlService::SEARCH_SHARE_TEXT) {
            if (!$this->nullable && !$condition_value) {
                $error = array("searchError/" . CpCreateSqlService::SEARCH_SHARE_TEXT => array("1つ以上選択してください。"));
                $json_data = $this->createAjaxResponse("ng", array(), array('error_msg' => $error[0]));
                $this->assign('json_data', $json_data);
                return false;
            }
        }

        if ($split_search_key[0] == CpCreateSqlService::SEARCH_YOUTUBE_CHANNEL_SUBSCRIPTION) {
            if (!$this->nullable && !$condition_value) {
                $error = array("searchError/" . CpCreateSqlService::SEARCH_YOUTUBE_CHANNEL_SUBSCRIPTION."/".$split_search_key[1] => array("1つ以上選択してください。"));
                $json_data = $this->createAjaxResponse("ng", array(), array('error_msg' => $error[0]));
                $this->assign('json_data', $json_data);
                return false;
            }
        }
        if (CpCreateSqlService::isPhotoQuery($split_search_key[0])) {
            $search_photo_validator = new SearchPhotoValidator ($condition_value, $condition_key, false);
            $search_photo_validator->validate();
            if (!$search_photo_validator->isValid()) {
                $error = current($search_photo_validator->getErrors());
                $json_data = $this->createAjaxResponse("ng", array(), array('error_msg' => $error[0]));
                $this->assign('json_data', $json_data);
                return false;
            }
        }

        if (CpCreateSqlService::isInstagramHashtagQuery($split_search_key[0])) {
            $search_instagram_hashtag_validator = new SearchInstagramHashtagValidator ($condition_value, $condition_key);
            $search_instagram_hashtag_validator->validate();
            if (!$search_instagram_hashtag_validator->isValid()) {
                $error = current($search_instagram_hashtag_validator->getErrors());
                $json_data = $this->createAjaxResponse("ng", array(), array('error_msg' => $error[0]));
                $this->assign('json_data', $json_data);
                return false;
            }
        }

        if ($split_search_key[0] == CpCreateSqlService::SEARCH_FB_LIKE_TYPE) {
            if (!$this->nullable && !$condition_value) {
                $error = array("searchError/" . CpCreateSqlService::SEARCH_FB_LIKE_TYPE . '/' . $split_search_key[1] => array("1つ以上選択してください。"));
                $json_data = $this->createAjaxResponse("ng", array(), array('error_msg' => $error[0]));
                $this->assign('json_data', $json_data);
                return false;
            }
        }

        if ($split_search_key[0] == CpCreateSqlService::SEARCH_TW_FOLLOW_TYPE) {
            if (!$this->nullable && !$condition_value) {
                $error = array('searchError/' . CpCreateSqlService::SEARCH_TW_FOLLOW_TYPE . '/' . $split_search_key[1] => array('1つ以上選択してください。'));
                $json_data = $this->createAjaxResponse("ng", array(), array('error_msg' => $error[0]));
                $this->assign('json_data', $json_data);
                return false;
            }
        }

        if ($split_search_key[0] == CpCreateSqlService::SEARCH_TWEET_TYPE) {
            if (!$condition_value) {
                $error = array('searchError/' . CpCreateSqlService::SEARCH_TWEET_TYPE . '/' . $split_search_key[1] => array('1つ以上選択してください。'));
                $json_data = $this->createAjaxResponse("ng", array(), array('error_msg' => $error[0]));
                $this->assign('json_data', $json_data);
                return false;
            }
        }

        if (CpCreateSqlService::isPopularVoteQuery($split_search_key[0])) {
            $search_popular_vote_validator = new SearchPopularVoteValidator ($condition_value, $condition_key);
            $search_popular_vote_validator->validate();
            if (!$search_popular_vote_validator->isValid()) {
                $error = current($search_popular_vote_validator->getErrors());
                $json_data = $this->createAjaxResponse("ng", array(), array('error_msg' => $error[0]));
                $this->assign('json_data', $json_data);
                return false;
            }
        }

        $condition_value['not_flg'] = $not_condition_flg;

        $params = array(
            'brand_id' => $this->getBrand()->id,
            'condition_key' => $this->GET['condition_key'],
            'condition_value' => $condition_value,
            'or_label_flg' => $or_label_flg,
            'or_condition_flg' => $or_condition_flg
        );

        $html = aafwWidgets::getInstance()->loadWidget('SegmentProvisionConditionComponent')->render($params);

        $json_data = $this->createAjaxResponse("ok", array(), array(), $html);
        $this->assign('json_data', $json_data);
        return 'dummy.php';
    }
}
