<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');
AAFW::import('jp.aainc.classes.services.DashboardService');
AAFW::import('jp.aainc.classes.services.CpQuestionnaireService');

class dashboard_list extends BrandcoGETActionBase {
    protected $ContainerName = 'dashboard_list';
    /** @var $dashboard_service DashboardService */
    protected $dashboard_service;
    /** @var $cp_questionnaire_service CpQuestionnaireService */
    protected $cp_questionnaire_service;
    protected $use_profile_question;
    public $NeedAdminLogin = true;
    public $NeedOption = array(BrandOptions::OPTION_DASHBOARD);

    public function validate() {
        return true;
    }

    function doAction() {
        $this->dashboard_service = new DashboardService($this->Data['brand']);
        $this->Data['date_type'] = $this->GET['date_type'] ? $this->GET['date_type'] : DashboardService::DATE_SUMMARY;
        $this->Data['error']['date'] = '';
        // 対象日付の指定が累積の時と期間の時で処理を分けている
        // ここから累積
        if($this->Data['date_type'] == DashboardService::DATE_SUMMARY) {
            $this->Data['summary_date_type'] = $this->GET['summary_date_type'] ? $this->GET['summary_date_type'] : DashboardService::SUMMARY_TODAY;
            if($this->Data['summary_date_type'] == DashboardService::SUMMARY_CUSTOM) {
                $this->Data['error']['date'] = $this->dashboard_service->getSummaryDateError($this->GET['summary_date']);
            }
            // エラーがなければ、対象となる期間を取得
            if(!$this->Data['error']['date']) {
                $this->Data['summary_date'] = $this->dashboard_service->getSummaryDate($this->Data['summary_date_type'], $this->GET['summary_date'])[1];
                $brand_relation_from = $this->Data['brand']->created_at;
                $brand_relation_to = $this->Data['summary_date'];
                list($this->Data['title_date'], $this->Data['title_date_text']) = $this->dashboard_service->getSummaryTitleDate($this->Data['summary_date_type'], $this->Data['summary_date']);
            }
            // 日付指定時のスタイルは動的に切り替えるため、以下で取得
            list($this->Data['summary_date_li_style'], $this->Data['term_date_li_style'], $this->Data['summary_date_span_style']) = $this->dashboard_service->getSummaryElementStyle($this->Data['summary_date_type']);

        // ここから期間
        } else {
            $this->Data['term_date_type'] = $this->GET['term_date_type'] ? $this->GET['term_date_type'] : DashboardService::TERM_TODAY;
            if($this->Data['term_date_type'] == DashboardService::TERM_CUSTOM) {
                $this->Data['error']['date'] = $this->dashboard_service->getTermDateError($this->GET['from_date'],$this->GET['to_date']);
            }
            // エラーがなければ、対象となる期間を取得
            if(!$this->Data['error']['date']) {
                list($this->Data['from_date'], $this->Data['to_date']) = $this->dashboard_service->getTermDate($this->Data['term_date_type'], $this->GET['from_date'], $this->GET['to_date']);
                $brand_relation_from = $this->Data['from_date'];
                $brand_relation_to = $this->Data['to_date'];
                list($this->Data['title_date'], $this->Data['title_date_text']) = $this->dashboard_service->getTermTitleDate($this->Data['term_date_type'], $this->Data['from_date'], $this->Data['to_date']);
            }
            // 日付指定時のスタイルは動的に切り替えるため、以下で取得
            list($this->Data['summary_date_li_style'], $this->Data['term_date_li_style'], $this->Data['term_date_span_style']) = $this->dashboard_service->getTermElementStyle($this->Data['term_date_type']);
        }

        /** @var BrandPageSettingService $brand_page_setting_service */
        $brand_page_setting_service = $this->createService('BrandPageSettingService');
        $this->Data['page_settings'] = $brand_page_setting_service->getPageSettingsByBrandId($this->Data['brand']->id);

        // datepicherで選択できる最小日付と最大日付
        $this->Data['min_date'] = date('Y/m/d', strtotime($this->Data['brand']->created_at));
        $this->Data['max_date'] = date('Y/m/d', strtotime('today'));

        $this->Data['all_fan_count'] = $this->dashboard_service->getAllFanCount($brand_relation_from, $brand_relation_to);

        /** @var BrandGlobalSettingService $brand_global_setting_service */
        $brand_global_setting_service = $this->createService('BrandGlobalSettingService');
        $this->Data['can_download_brand_user_list'] = $this->Data['pageStatus']['manager']->canView() ||
            $brand_global_setting_service->getBrandGlobalSetting($this->Data['brand']->id, BrandGlobalSettingService::CAN_LOOK_DAILY_PV);

        $this->Data['summary_options'] = $this->dashboard_service->getSummaryDatePicker();
        $this->Data['term_options'] = $this->dashboard_service->getTermDatePicker();

        $this->cp_questionnaire_service = $this->createService('CpQuestionnaireService', CpQuestionnaireService::TYPE_PROFILE_QUESTION);
        $questionnaires_questions_relations = $this->cp_questionnaire_service->getPublicProfileQuestionRelationByBrandId($this->Data['brand']->id);
        $this->Data['use_profile_question'] = $this->cp_questionnaire_service->useProfileQuestion($questionnaires_questions_relations);

        return 'user/brandco/admin-dashboard/dashboard_list.php';
    }

    public function getQuestionnaireQuestion($question_id) {
        return $this->cp_questionnaire_service->getQuestionById($question_id);
    }

    public function getQuestionRequirement($question_id) {
        return $this->cp_questionnaire_service->getRequirementByQuestionId($question_id);
    }

    public function getQuestionHeight($question_requirement) {
        return $this->dashboard_service->getQuestionHeight($question_requirement);
    }

}
