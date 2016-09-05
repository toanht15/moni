<?php
require_once dirname(__FILE__) . '/../../config/define.php';
AAFW::import('jp.aainc.lib.base.aafwObject');
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');
AAFW::import('jp.aainc.classes.services.CpQuestionnaireService');
AAFW::import('jp.aainc.classes.stores.ProfileQuestionChoiceAnswers');
AAFW::import('jp.aainc.classes.stores.ProfileQuestionFreeAnswers');

class ShiftUserProfileQuestionnaire {

    public $logger;
    public $service_factory;

    public function __construct() {
        ini_set('memory_limit', '256M');
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
        $this->service_factory = new aafwServiceFactory();
    }

    public function doProcess() {

        /** @var BrandService $brand_service */
        $brand_service = $this->service_factory->create('BrandService');

        /** @var ProfileQuestionnaireService $profile_questionnaire_service */
        $profile_questionnaire_service = $this->service_factory->create('ProfileQuestionnaireService');

        /** @var CpQuestionnaireService $cp_questionnaire_service */
        $cp_questionnaire_service = $this->service_factory->create('CpQuestionnaireService', CpQuestionnaireService::TYPE_PROFILE_QUESTION);

        /** @var BrandsUsersRelationService $brands_users_relation_service */
        $brands_users_relation_service = $this->service_factory->create('BrandsUsersRelationService');

        /** @var BrandGlobalSettingService $brand_global_setting_service */
        $brand_global_setting_service = $this->service_factory->create('BrandGlobalSettingService');

        $db = new aafwDataBuilder();

        $brands = $brand_service->getAllBrands();

        try {
            foreach ($brands as $brand) {
                if(!$brand_global_setting_service->getBrandGlobalSetting($brand->id, 'finish_profile_batch')) {
                    $this->logger->info('ShiftUserProfileQuestionnaire - Start brand_id:'. $brand->id);
                    $brands_users_relations = $brands_users_relation_service->getBrandsUsersRelationsByBrandId($brand->id);
                    foreach ($brands_users_relations as $brands_users_relation) {

                        $profile_questionnaire_answers = $profile_questionnaire_service->getProfileQuestionnaireAnswersByRelateId($brands_users_relation->id);
                        foreach ($profile_questionnaire_answers as $profile_questionnaire_answer) {

                            $new_profile_questionnaires_question_id = $profile_questionnaire_service->getNewProfileQuestionByOldQuestion($profile_questionnaire_answer->question_id)->new_question_id;
                            $profile_questionnaires_question = $cp_questionnaire_service->getQuestionById($new_profile_questionnaires_question_id);
                            $profile_questionnaires_questions_relation = $cp_questionnaire_service->getRelationByProfileQuestionId($new_profile_questionnaires_question_id);

                            if (!$profile_questionnaires_questions_relation) continue;
                            if ($profile_questionnaires_question->type_id != QuestionTypeService::CHOICE_ANSWER_TYPE) continue;
                            $profile_questionnaire_requirement = $cp_questionnaire_service->getRequirementByQuestionId($profile_questionnaires_question->id);
                            if (!$profile_questionnaire_requirement->multi_answer_flg) continue;

                            $each_profile_question_answers = explode(',', $profile_questionnaire_answer->answer);

                            // 更新日付が2015年2/24日以降だった場合、モニプラのキャンペーン情報がマージされているデータなので、更新しない
                            $exist_choice_answer = $cp_questionnaire_service->existChoiceAnswer($brands_users_relation->id, $profile_questionnaires_questions_relation->id);
                            if($exist_choice_answer && $exist_choice_answer->updated_at > '2015-02-24 00:00:00') {
                                continue;
                            }
                            foreach ($each_profile_question_answers as $each_profile_question_answer) {
                                $filter = array(
                                    'choice' => $each_profile_question_answer,
                                    'question_id' => $new_profile_questionnaires_question_id
                                );

                                $choice = $db->getProfileQuestionnaireAnswer($filter, null, null, true);
                                if ($choice['list'][0]['id']) {
                                    // 同一の答えが既に保存されていないか確認
                                    $exist_same_choice_answer = $cp_questionnaire_service->existSameChoiceAnswer($brands_users_relation->id, $profile_questionnaires_questions_relation->id, $choice['list'][0]['id']);
                                    if(!$exist_same_choice_answer->id) {
                                        $cp_questionnaire_service->setQuestionChoiceAnswer($profile_questionnaires_questions_relation->id, $brands_users_relation->id, $profile_questionnaire_answer->question_id, $choice['list'][0]['id']);
                                    }
                                }
                            }
                        }
                    }
                    $global_setting = $brand_global_setting_service->createBrandGlobalSetting();
                    $global_setting->brand_id = $brand->id;
                    $global_setting->name = 'finish_profile_batch';
                    $global_setting->content = '1';
                    $brand_global_setting_service->saveGlobalSetting($global_setting);
                    $this->logger->info('ShiftUserProfileQuestionnaire - Finish brand_id:'. $brand->id);
                }
            }
        } catch(Exception $e) {
            throw $e;
        }
    }
}