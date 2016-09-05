<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoManagerGETActionBase');

class edit_form extends BrandcoManagerGETActionBase {


    protected $ContainerName = 'edit';

    public $NeedManagerLogin = true;

    public function beforeValidate () {
        $this->resetValidateError();

        if ( !$this->getActionContainer('Errors') ) {
            $this->Data['mode'] = $this->mode == ManagerService::ADD_FINISH ? ManagerService::ADD_FINISH : $this->mode;
        } else {
            $this->Data['mode'] = ManagerService::ADD_ERROR;
        }
    }

    public function validate () {
        return true;
    }

    function doAction() {
        /** @var BrandService  $brand_service */
        $brand_service = $this->createService('BrandService');
        /** @var BrandPageSettingService  $brand_page_setting_service */
        $brand_page_setting_service = $this->createService('BrandPageSettingService');
        /** @var BrandContractService  $brandContractService */
        $brandContractService = $this->createService('BrandContractService');
        /** @var BrandOptionsService  $brandOptionsService */
        $brandOptionsService = $this->createService('BrandOptionsService');
        /** @var BrandBusinessCategoryService  $brand_business_category_service */
        $brand_business_category_service = $this->createService('BrandBusinessCategoryService');
        /** @var BrandSalesforceService  $brand_salesforce_service */
        $brand_salesforce_service = $this->createService('BrandSalesforceService');
        /** @var ManagerService $manager_service */
        $manager_service = $this->createService('ManagerService');
        $managers = $manager_service->getManagers('mail_address');

        foreach ($managers as $manager) {
            $this->Data['managers'][] = (object)array('id'=> $manager->id, 'name' => $manager->name);
        }
        $array_manager = $this->Data['managers'];
        array_unshift($array_manager, (object)array('id' => 0, 'name' => '指定なし'));
        $this->Data['manager_list'] = array_combine(
            array_map(function($array_manager){return $array_manager->id;}, $array_manager),
            array_map(function($array_manager){return $array_manager->name;}, $array_manager)
        );

        $brand_id = $this->GET['exts'][0];
        $this->Data['brand'] = $brand_service->getBrandById($brand_id);

        /** @var ConsultantsManagerService  $consultans_manager_service */
        $consultans_manager_service = $this->createService('ConsultantsManagerService');
        $consultant_manager = $consultans_manager_service->getConsultantsManagerByBrandId($brand_id);
        $this->Data['consultant_manager'] = $consultant_manager;

        /** @var SalesManagerService $sales_manager_service */
        $sales_manager_service = $this->createService('SalesManagerService');
        $sales_manager = $sales_manager_service->getSalesManagerInfoByBrandId($brand_id);
        $this->Data['sales_manager'] = $sales_manager;

        $actionBrandForm = $this->Data['brand']->toArray();
        if($this->Data['consultant_manager']!=null && $this->Data['sales_manager'] !=null) {
            $actionConsultantsForm = $this->Data['consultant_manager']->toArray();
            $actionSalesForm = $this->Data['sales_manager']->toArray();

            $actionConsultantsSalesForm = array_merge($actionConsultantsForm, $actionSalesForm);
        }

        $actionForm = is_array($actionConsultantsSalesForm) ? array_merge($actionBrandForm, $actionConsultantsSalesForm) : $actionBrandForm;

        if ($this->Data['brand']->test_page) {
            $brand_page_setting = $brand_page_setting_service->getPageSettingsByBrandId($brand_id);
            if ($brand_page_setting->test_id && $brand_page_setting->test_pass) {
                $test_info['test_id'] = $brand_page_setting->test_id;
                $test_info['test_pass'] = $brand_page_setting->test_pass;
            }
        }

        if (!is_array($test_info)) {
            $this->Data['invalidInputData'] = true;
            $test_info = array(
                'test_id' => $brand_page_setting_service->genRandomString(),
                'test_pass' => $brand_page_setting_service->genRandomString()
            );
        }

        if (is_array($test_info)) {
            $actionForm = array_merge($actionForm, $test_info);
        }

        //PRtypeのリストを取得
        $this->Data['monipla_pr_allow_type_list'] = Brand::$monipla_pr_allow_type_list;

        // salesforceの登録リスト取得
        $this->Data['salesforces']     = $brand_salesforce_service->getBrandSalesforcesByBrandId($brand_id);
        $this->Data['salesforceCount'] = $brand_salesforce_service->countSalesforceByBrandId($brand_id);
        if ($this->Data['salesforces']) {
            $actionFormForSalesforces = $this->createActionFormForSalesforce($this->Data['salesforces']);
        }
        if (is_array($actionFormForSalesforces)) {
            $actionForm = array_merge($actionForm, $actionFormForSalesforces);
        }
        $this->Data['haveErrorsForNewSalesforceForm'] = $this->haveErrorsForNewSalesforceForm();

        // Brandの業種を取得
        $this->Data['brandBusinessCategory'] = $brand_business_category_service->getOrCreateBrandBusinessCategoryByBrandId($brand_id);
        $actionFormForBusinessCategory = array('business_category' => $this->Data['brandBusinessCategory']->category);
        $actionFormForBusinessSize     = array('business_size' => $this->Data['brandBusinessCategory']->size);
        $actionForm = array_merge($actionForm, $actionFormForBusinessCategory, $actionFormForBusinessSize);
        $this->Data['brand_business_category_list'] = $brand_business_category_service->getCategoryList();
        $this->Data['brand_business_size_list']     = $brand_business_category_service->getSizeList();

        // Brandの契約プランを取得
        $this->Data['brandContract'] = $brandContractService->getBrandContractByBrandId($brand_id);
        $this->Data['plan_list'] = $brandContractService->getSelectablePlan();
        $actionFormForBrandContact          = array('plan' => $this->Data['brandContract']->plan);
        $actionFormForBrandOperation        = array('operation' => $this->Data['brandContract']->operation);
        $actionFormForBrandForProductionFlg = array('for_production_flg' => $this->Data['brandContract']->for_production_flg);
        $actionFormForBrandMemo             = array('memo' => $this->Data['brandContract']->memo);
        $actionForm = array_merge($actionForm, $actionFormForBrandContact, $actionFormForBrandOperation, $actionFormForBrandForProductionFlg, $actionFormForBrandMemo);
        $this->Data['operation_list'] = $brandContractService->getOperationList();
        $this->Data['for_production_flg_list'] = $brandContractService->getForProductionFlgList();

        $inquiry_brand_service = $this->getService('InquiryBrandService');
        $inquiry_brand = $inquiry_brand_service->getRecord(InquiryBrandService::MODEL_TYPE_INQUIRY_BRAND, array('brand_id' => $brand_id));
        $this->Data['aa_alert_flg'] = $this->Data['ActionError'] ? array($this->Data['ActionForm']['aa_alert_flg'] ? 1 : 0) : array($inquiry_brand->aa_alert_flg);

        /** @var SegmentService $segment_service */
        $segment_service = $this->getService('SegmentService');
        $actionForm['segment_group_limit'] = $segment_service->getSegmentLimit(Segment::TYPE_SEGMENT_GROUP, $brand_id);
        $actionForm['conditional_segment_limit'] = $segment_service->getSegmentLimit(Segment::TYPE_CONDITIONAL_SEGMENT, $brand_id);

        $this->assign('ActionForm', $actionForm);
        return 'manager/brands/edit_form.php';
    }

    /**
     * @param $accountInfoLists
     * @return array
     */
	public function createActionFormForAdministratorAccountInfo($accountInfoLists) {
        $actionFormForAdministratorAccountInfo = array();
        $accountInfoArray = $accountInfoLists->toArray();

        foreach($accountInfoArray as $accountInfo) {
            $actionFormForAdministratorAccountInfo['administrator_account_name_' .$accountInfo->administrator_account_no]         = $accountInfo->name;
            $actionFormForAdministratorAccountInfo['administrator_account_mail_address_' .$accountInfo->administrator_account_no] = $accountInfo->mail_address;
            $actionFormForAdministratorAccountInfo['administrator_account_tel_no1_' .$accountInfo->administrator_account_no]      = $accountInfo->tel_no1;
            $actionFormForAdministratorAccountInfo['administrator_account_tel_no2_' .$accountInfo->administrator_account_no]      = $accountInfo->tel_no2;
            $actionFormForAdministratorAccountInfo['administrator_account_tel_no3_' .$accountInfo->administrator_account_no]      = $accountInfo->tel_no3;
        }

        return $actionFormForAdministratorAccountInfo;
    }

    public function displayNewAdministratorAccountForm() {
        if ($this->getActionContainer('Errors')) {
            return (
                $this->getActionContainer('Errors')->getError('administrator_account_mail_name_'.($this->Data['administratorAccountCount'] + 1)) ||
                $this->getActionContainer('Errors')->getError('administrator_account_mail_address_'.($this->Data['administratorAccountCount'] + 1)) ||
                $this->getActionContainer('Errors')->getError('administrator_account_tel_no1_'.($this->Data['administratorAccountCount'] + 1)) ||
                $this->getActionContainer('Errors')->getError('administrator_account_tel_no2_'.($this->Data['administratorAccountCount'] + 1)) ||
                $this->getActionContainer('Errors')->getError('administrator_account_tel_no3_'.($this->Data['administratorAccountCount'] + 1))
            );
        } else {
            return false;
        }
    }

    public function createActionFormForSalesforce($salesforces) {
        $actionFormForSalesforces = array();
        $salesforcesArray = $salesforces->toArray();

        foreach($salesforcesArray as $salesforce) {
            $actionFormForSalesforces['salesforce_url_' .$salesforce->id] = $salesforce->url;
            $actionFormForSalesforces['start_date_'.$salesforce->id]      = $salesforce->start_date;
            $actionFormForSalesforces['end_date_'.$salesforce->id]        = $salesforce->end_date;
        }

        return $actionFormForSalesforces;
    }

    public function haveErrorsForNewSalesforceForm() {
        if ($this->getActionContainer('Errors')) {
            return (
                $this->getActionContainer('Errors')->getError('salesforce_url_new') ||
                $this->getActionContainer('Errors')->getError('start_date_new') ||
                $this->getActionContainer('Errors')->getError('end_date_new') ||
                $this->getActionContainer('Errors')->getError('date_range_new')
            );
        } else {
            return false;
        }
    }
}