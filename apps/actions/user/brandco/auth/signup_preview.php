<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.classes.services.CpQuestionnaireService');

class signup_preview extends BrandcoGETActionBase {

    public $NeedOption = array();
    protected $ContainerName = 'signup_preview';

    public function validate() {
        if($this->GET['demo_token'] != hash("sha256", $this->brand->created_at)){
            return 404;
        }
        return true;
    }

    public function doAction() {

        /** @var BrandPageSettingService $page_settings_service */
        $page_settings_service = $this->getService('BrandPageSettingService');
        $this->Data['pageSettings'] = $page_settings_service->getPageSettingsByBrandId($this->brand->id);

        if ($this->Data['pageSettings']->privacy_required_address == BrandPageSetting::GET_ALL_ADDRESS || $this->Data['pageSettings']->privacy_required_address == BrandPageSetting::GET_STATE_ADDRESS) {
            /** @var PrefectureService $prefecture_service */
            $prefecture_service = $this->getService('PrefectureService');
            $this->Data['prefectures'] = $prefecture_service->getPrefecturesKeyValue();
        }

        if($this->Data['pageSettings']->privacy_required_sex) {
            $this->Data['sex'] = array('f' => '女性', 'm' => '男性');
        }

        if ($this->Data['pageSettings']) {
            $this->Data['isRequiredPrivacy'] = $this->Data['pageSettings']->privacy_required_name
                || $this->Data['pageSettings']->privacy_required_sex
                || $this->Data['pageSettings']->privacy_required_birthday
                || $this->Data['pageSettings']->privacy_required_address
                || $this->Data['pageSettings']->privacy_required_tel
                || $this->Data['pageSettings']->privacy_required_restricted;
        }

        // データベースから表示する
        $this->Data['profile_questions'] = $this->parseProfileQuestionnaireFromDB();
        $this->Data['preview_url'] = Util::rewriteUrl('auth', 'signup_preview') . '?demo_token=' . $this->GET['demo_token'];

        // プロフィールアンケート
        $this->Data['required_profile_questions'] = $this->Data['profile_questions'] || $this->Data['isRequiredPrivacy'] || $this->Data['mailInfo']['needMailAddress'];
        // 利用規約
        $this->Data['required_agreement'] = !!$this->Data['pageSettings']->agreement;

        //使用規約の表示
        $this->Data['show_agreement_checkbox'] = !!$this->Data['pageSettings']->show_agreement_checkbox;

        return 'user/brandco/auth/signup_preview.php';
    }

    private function parseProfileQuestionnaireFromDB () {

        /** @var CpQuestionnaireService $questionnaire_service */
        $questionnaire_service = $this->getService('CpQuestionnaireService', CpQuestionnaireService::TYPE_PROFILE_QUESTION);
        $questionnaire_relations = $questionnaire_service->getPublicProfileQuestionRelationByBrandId($this->brand->id);

        $questions = array();

        foreach ($questionnaire_relations as $questionnaire_relation) {
            if(!$questionnaire_relation->public) {
                continue;
            }

            $question = $questionnaire_service->getQuestionById($questionnaire_relation->question_id);
            $question_choice_requirement = $questionnaire_service->getRequirementByQuestionId($question->id);

            $question_info = array();

            $question_info["type_id"] = $question->type_id;
            $question_info['question'] = $question->question;
            $question_info['is_multi_answer'] = $question_choice_requirement->multi_answer_flg;
            $question_info['is_requirement'] = $questionnaire_relation->requirement_flg;
            $question_info['is_random_choice'] = $question_choice_requirement->random_order_flg;
            $question_info['is_use_other'] = $question_choice_requirement->use_other_choice_flg;
            $question_info['choices'] = array();

            if ($question->type_id != QuestionTypeService::FREE_ANSWER_TYPE) {
                $choices = $questionnaire_service->getChoicesByQuestionId($question->id);
                foreach ($choices as $choice) {
                    $question_info['choices'][] = $choice->choice;
                }
            }
            $questions[] = $question_info;
        }

        return $questions;
    }
}