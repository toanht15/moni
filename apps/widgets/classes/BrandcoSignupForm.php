<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');
AAFW::import('jp.aainc.classes.services.CpQuestionnaireService');
AAFW::import('jp.aainc.classes.services.QuestionTypeService');
AAFW::import('jp.aainc.classes.BrandInfoContainer');

class BrandcoSignupForm extends aafwWidgetBase {

    /** @var CpQuestionnaireService $questionnaire_service */
    private $questionnaire_service;

    private $brands_users_relation_id;

    private $ignore_prefill;

    private $question_map = array();

    private $requirement_map = array();

    private $choice_map = array();

    private $choice_answer_map = array();

    private $free_answer_map = array();

    public function doService( $params = array() ) {
        $service_factory = new aafwServiceFactory();
        $this->questionnaire_service = $service_factory->create('CpQuestionnaireService', CpQuestionnaireService::TYPE_PROFILE_QUESTION);
        $this->brands_users_relation_id = $params['brands_users_relation_id'];
        $this->ignore_prefill = $params['ignore_prefill'];
        $baseParams = $this->getPageBaseData($params);
        $this->prefetchData($baseParams['profile_questions_relations'], $params);

        $params = array_merge($params, $baseParams);

        // プロフィールアンケート
        $params['required_profile_questions'] = $this->isSignup($params);
        // 利用規約
        $params['required_agreement'] = !!$params['pageSettings']->agreement;

        //利用規約への同意を確認するチェックボックスの表示
        $params['show_agreement_checkbox'] = $params['pageSettings']->show_agreement_checkbox ? true : false;

        return $params;
    }

    public function getPageBaseData($params) {
        $brand = $params['brand'];
        $cp = $params['cp'];

        if ($brand === null) {
            return array();
        }

        /** @var BrandPageSettingService $page_settings_service */
        $page_settings_service = $this->getService('BrandPageSettingService');
        $params['pageSettings'] = BrandInfoContainer::getInstance()->getBrandPageSetting();
        $params['isRequiredPrivacy'] = $page_settings_service->isRequiredPrivacyByBrandId($brand->id, BrandInfoContainer::getInstance()->getBrandPageSetting()) || ($cp != null && $cp->isRestrictedCampaign());

        /** @var CpQuestionnaireService $questionnaire_service */
        $questionnaire_service = $this->getService('CpQuestionnaireService', CpQuestionnaireService::TYPE_PROFILE_QUESTION);
        $params['profile_questions_relations'] = $questionnaire_service->getPublicProfileQuestionRelationByBrandId($brand->id);
        $public_count = 0;
        foreach ($params['profile_questions_relations'] as $profile_questions_relation) {
            if ($profile_questions_relation->public) {
                $public_count ++;
            }
        }
        $params['public_exists'] = $public_count > 0;

        /** @var PrefectureService $prefecture_service */
        $prefecture_service = $this->getService('PrefectureService');
        $params['prefectures'] = $prefecture_service->getPrefecturesKeyValue();

        $params['sex'] = array('f' => '女性', 'm' => '男性');

        $attentions = array();
        if ($params['pageSettings']->privacy_required_restricted && !$params['entry_questionnaire_only']) {
            $attentions[] = "※登録は{$params['pageSettings']->restricted_age}歳以上の方限定です。";
        }
        if ($cp->restricted_age_flg) {
            $attentions[] = "※キャンペーン参加は{$cp->restricted_age}歳以上の方限定です。";
        }
        if ($cp->restricted_gender_flg) {
            $gender = Cp::$cp_restricted_gender[$cp->restricted_gender];
            $attentions[] = "※キャンペーン参加は{$gender}の方限定です。";
        }
        if ($cp->restricted_address_flg) {
            $cp_restricted_address_service = $this->getService('CpRestrictedAddressService');
            $attentions[] = "※キャンペーン参加は{$cp_restricted_address_service->getCpRestrictedAddressesString($cp->id)}の住所の方限定です。";
        }
        if ($params['pageStatus']['is_cmt_plugin_mode']) {
            $attentions[] = "※こちらの入力内容は投稿には反映されず、外部に公開されることはありません。";
        }
        $params['attentions'] = implode('<br>', $attentions);

        return $params;
    }

    public function canRenderQuestionnaire($profile_question_relation, $entry_questionnaire_only, $entry_questionnaires) {
        return (!$entry_questionnaire_only && $profile_question_relation->public === "1") || isset($entry_questionnaires[$profile_question_relation->question_id]);
    }

    public function getButtonClass($entry_questionnaire_only) {
        return $entry_questionnaire_only ? "large1" : "middle1";
    }

    public function getButtonText($entry_questionnaire_only) {
        return $entry_questionnaire_only ? "回答" : "次へ";
    }

    public function getQuestionById($question_id) {
        if ($question_id === null) {
            return null;
        }
        return $this->question_map[$question_id];
    }

    public function getRequirementByQuestionId($profile_questionnaire_id) {
        if (Util::isNullOrEmpty($profile_questionnaire_id)) {
            return null;
        }
        return $this->requirement_map[$profile_questionnaire_id];
    }

    public function getChoicesByQuestionId($profile_questionnaire_id) {
        if (Util::isNullOrEmpty($profile_questionnaire_id)) {
            return array();
        }

        return $this->choice_map[$profile_questionnaire_id];
    }

    public function convertChoicesToMap($choices) {
        if ($choices === null) {
            return array();
        }
        $select_choices = array();
        $select_choices[''] = '選択してください';
        foreach($choices as $choice) {
            $select_choices[$choice->id] = $choice->choice;
        }
        return $select_choices;
    }

    // brands_users_relation_idの指定あり(=エントリー・モジュール内での呼び出し=API=画面遷移なし)とsign upで値の再取得元を動的に切り替える。

    public function getSingleChoiceAnswer($questionnaire_questions_relation_id, $choice_id) {
        if (Util::existNullOrEmpty($questionnaire_questions_relation_id, $choice_id) || $this->ignore_prefill === true || $this->brands_users_relation_id === null) {
            return PHPParser::ACTION_FORM;
        }
        if (isset($this->choice_answer_map[$questionnaire_questions_relation_id][$choice_id])) {
            return $choice_id;
        }
        return '';
    }

    public function hasChoiceAnswer($questionnaire_questions_relation_id) {
        if (Util::existNullOrEmpty($questionnaire_questions_relation_id) || $this->ignore_prefill === true || $this->brands_users_relation_id === null) {
            return false;
        }
        return count($this->choice_answer_map[$questionnaire_questions_relation_id]) > 0;
    }

    public function getMultiChoiceAnswer($questionnaire_questions_relation_id, $key = null) {
        if (Util::isNullOrEmpty($questionnaire_questions_relation_id) || $this->ignore_prefill === true || !$this->brands_users_relation_id) {
            if ($key != null) {
                return $this->getActionFormValue($key);
            }
            return PHPParser::ACTION_FORM;
        }
        return array_keys($this->choice_answer_map[$questionnaire_questions_relation_id]);
    }

    public function getOtherText($questionnaire_questions_relation_id, $choice_id) {
        if (Util::isNullOrEmpty($questionnaire_questions_relation_id) || $this->ignore_prefill === true || !$this->brands_users_relation_id) {
            return PHPParser::ACTION_FORM;
        }
        if (isset($this->choice_answer_map[$questionnaire_questions_relation_id][$choice_id])) {
            return $this->choice_answer_map[$questionnaire_questions_relation_id][$choice_id];
        }
        return '';
    }

    public function getFreeAnswer($questionnaire_questions_relation_id) {
        if (Util::isNullOrEmpty($questionnaire_questions_relation_id) || $this->ignore_prefill === true || !$this->brands_users_relation_id) {
            return PHPParser::ACTION_FORM;
        }
        if (isset($this->free_answer_map[$questionnaire_questions_relation_id])) {
            return $this->free_answer_map[$questionnaire_questions_relation_id];
        }

        return PHPParser::ACTION_FORM;
    }

    public function hasFreeAnswer($questionnaire_questions_relation_id) {
        if (Util::isNullOrEmpty($questionnaire_questions_relation_id) || $this->ignore_prefill === true || !$this->brands_users_relation_id) {
            return false;
        }
        return isset($this->free_answer_map[$questionnaire_questions_relation_id]);
     }

    private function prefetchData($profile_questions_relations, $params) {
        $question_ids = array();
        $relation_ids = array();
        foreach ($profile_questions_relations as $profile_questions_relation) {
            if (!$this->canRenderQuestionnaire($profile_questions_relation, $params['entry_questionnaire_only'], $params['entry_questionnaires'])
            ) {
                continue;
            }
            $question_ids[] = $profile_questions_relation->question_id;
            $relation_ids[] = $profile_questions_relation->id;
        }
        if (count($question_ids) > 0) {
            $this->question_map = $this->questionnaire_service->getQuestionMapByIds($question_ids);
            $this->requirement_map = $this->questionnaire_service->getRequirementMapByQuestionIds($question_ids);
            $choice_map = $this->questionnaire_service->getChoiceMapByQuestionIds($question_ids);
            $modified_choice_map = array();
            foreach ($choice_map as $question_id => $choices) {
                $question_requirement = $this->requirement_map[$question_id];
                if ($question_requirement->random_order_flg) {
                    if (end($choices)->other_choice_flg) {
                        $other_choice = end($choices);
                        $choices = array_slice($choices, 0, count($choices) - 1);
                        shuffle($choices);
                        $choices[] = $other_choice;
                    } else {
                        shuffle($choices);
                    }
                }
                $modified_choice_map[$question_id] = $choices;
            }
            $this->choice_map = $modified_choice_map;
        }
        if (count($relation_ids) > 0) {
            $this->choice_answer_map = $this->questionnaire_service->getSingleAndMultiChoiceAnswerMap($this->brands_users_relation_id, $relation_ids);
            $this->free_answer_map = $this->questionnaire_service->getFreeAnswerMap($this->brands_users_relation_id, $relation_ids);
        }
    }

    public function showUpAgeRestriction($data) {
        return !$data['entry_questionnaire_only'] && $data['pageSettings']->privacy_required_restricted;
    }

    public function hasQuestionnaire($data) {
        return $this->isResend($data) || $this->isSignup($data);
    }

    public function isResend($data) {
        return $data['entry_questionnaire_only'];
    }

    public function isSignup($data) {
        return $data['isRequiredPrivacy'] || $data['public_exists'] || $data['mailInfo']['needMailAddress'] || count($data['entry_questionnaires']) > 0;
    }

    public function isProfileChoice($profile_questionnaire) {
        return $profile_questionnaire->type_id == QuestionTypeService::CHOICE_ANSWER_TYPE || $profile_questionnaire->type_id == QuestionTypeService::CHOICE_PULLDOWN_ANSWER_TYPE;
    }

    public function isChoiceAnswer($profile_questionnaire) {
        return $profile_questionnaire->type_id == QuestionTypeService::CHOICE_ANSWER_TYPE;
    }

    public function isChoicePulldown($profile_questionnaire) {
        return $profile_questionnaire->type_id == QuestionTypeService::CHOICE_PULLDOWN_ANSWER_TYPE;

    }
}