<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoManagerPOSTActionBase');

class edit extends BrandcoManagerPOSTActionBase {

    protected $brand_service;
	protected $brand_administrator_account_info_service;
    protected $page_setting_service;
    protected $brand_business_category_sevice;
    protected $brand_salesforce_service;

    protected $ContainerName = 'edit';
    protected $Form = array(
        'package' => 'brands',
        'action' => 'edit_form/{id}',
    );

    public $NeedManagerLogin = true;
    public $CsrfProtect = true;

    public $update_salesforce_ids;

    protected $ValidatorDefinition = array(
        'name' => array(
            'required' => 1,
            'type' => 'str',
            'length' => 255,
        ),
        'mail_name' => array(
            'required' => 1,
            'type' => 'str',
            'length' => 255,
        ),
        'enterprise_name' => array(
            'required' => 1,
            'type' => 'str',
            'length' => 255,
        ),
        'plan' => array(
            'type'     => 'num',
            'range'    => array(
                '>=' => 1,
                '<=' => 3,
            ),
        ),
        'business_category' => array(
            'required' => 1,
            'type'     => 'num',
            'range'    => array(
                '>=' => 1,
                '<=' => 33
            )
        ),
        'business_size' => array(
            'required' => 1,
            'type'     => 'num',
            'range'    => array(
                '>=' => 1,
                '<=' => 4
            )
        ),
        'operation' => array(
            'required' => 1,
            'type'     => 'num',
            'range'    => array(
                '>=' => 1,
                '<=' => 2
            )
        ),
        'for_production_flg' => array(
            'required' => 1,
            'type'     => 'num',
            'range'    => array(
                '>=' => 0,
                '<=' => 1
            )
        ),
        'segment_group_limit' => array(
            'type' => 'num',
            'range' => array(
                '>=' => 1
            )
        ),
        'conditional_segment_limit' => array(
            'type' => 'num',
            'range' => array(
                '>=' => 1
            )
        ),
        'monipla_pr_allow_type' => array(
            'required' => 1,
            'type' => 'num',
            'range'    => array(
                '>=' => 0,
                '<=' => 2,
            )
        )
    );

    public function doThisFirst() {
        if (!$this->POST['test_page']) {
            $this->POST['test_id'] = "";
            $this->POST['test_pass'] = "";
        }

        if (!$this->POST['memo']) $this->POST['memo'] = "";

        if (in_array(BrandOptions::OPTION_SEGMENT, $this->POST['brand_options']) && $this->POST['is_super_user']) {
            $this->ValidatorDefinition['segment_group_limit']['required'] = true;
            $this->ValidatorDefinition['conditional_segment_limit']['required'] = true;
        }
    }

    public function beforeValidate() {

        //salesforceのValidatorDefinition作成
        $this->brand_salesforce_service = $this->createService('BrandSalesforceService');
        $this->update_salesforce_ids    = $this->getPostSalesforceIdKeys($this->POST);

        foreach ($this->update_salesforce_ids as $key => $id) {
            $salesforce = $this->brand_salesforce_service->getOrCreateBrandSalesforceById($this->POST[$id]);

            //追加するsalesforceに関して、URL、利用期間のPOST値が両方ない場合はValidator不要
            //$this->POST[$id]にはBrandSalesforceのidが格納されている
            if (!$salesforce->id && !$this->POST['salesforce_url_new'] && !$this->POST['start_date_new'] && !$this->POST['end_date_new']) {
                unset($this->update_salesforce_ids[$key]);
                continue;
            }

            $this->ValidatorDefinition += $this->createValidatorForSalesforceUrl('salesforce_url_'.$this->POST[$id]);
            $this->ValidatorDefinition += $this->createValidatorForSalesforceDate('start_date_'.$this->POST[$id]);
            $this->ValidatorDefinition += $this->createValidatorForSalesforceDate('end_date_'.$this->POST[$id]);
        }
    }

    public function validate () {
        if ($this->POST['test_page']) {
            if (!$this->POST['test_id']) $this->Validator->setError('invalid_test_id', 'INVALID_TEST_ID');
            if (!$this->POST['test_pass']) $this->Validator->setError('invalid_test_pass', 'INVALID_TEST_PASS');
        }
        

        // salesforceのURLバリデート
        foreach ($this->update_salesforce_ids as $id) {
            if (!$this->isURL($this->POST['salesforce_url_'.$this->POST[$id]])) {
                $this->Validator->setError('salesforce_url_'.$this->POST[$id], INPUT_URL);
            }

            if ($this->POST['start_date_'.$this->POST[$id]] > $this->POST['end_date_'.$this->POST[$id]]) {
                $this->Validator->setError('date_range_'.$this->POST[$id], 'INVALID_TIME2');
            }
        }

        if (Util::isInvalidBrandName($this->POST['mail_name'])) {
            $this->Validator->setError('mail_name', 'INVALID_BRAND_NAME_FORMAT');
        }
        
        return $this->Validator->isValid();
    }

    function doAction() {
        try {
            $brands = aafwEntityStoreFactory::create('Brands');
            $brands->begin();

            /** @var BrandService $brand_service */
            $this->brand_service = $this->createService('BrandService');
            $brand = $this->brand_service->getBrandById($this->id);
            $brand->name = $this->POST['name'];
            $brand->mail_name = $this->POST['mail_name'];
            $brand->enterprise_name = $this->POST['enterprise_name'];
            $brand->monipla_pr_allow_type = $this->POST['monipla_pr_allow_type'];
            if($this->test_page == 1) {
                $brand->test_page = 1;
            } else {
                $brand->test_page = 0;
            }
            $this->brand_service->updateBrandList($brand);

            /** @var BrandBusinessCategoryService $brand_business_category_service */
            $this->brand_business_category_sevice = $this->createService('BrandBusinessCategoryService');
            $brand_business_category = $this->brand_business_category_sevice->getOrCreateBrandBusinessCategoryByBrandId($this->POST['id']);
            $brand_business_category->brand_id    = $this->POST['id'];
            $brand_business_category->category    = $this->POST['business_category'];
            $brand_business_category->size        = $this->POST['business_size'];

            $this->brand_business_category_sevice->saveBrandBusinessCategory($brand_business_category);

            /** @var ConsultantsManagerService $consultans_manager_service */
            $consultans_manager_service = $this->createService('ConsultantsManagerService');
            $consultants_manager = $consultans_manager_service->getConsultantsManagerByBrandId($this->id);
            $consultants_manager->brand_id = $this->POST['id'];
            $consultants_manager->consultants_manager_id = $this->POST['consultants_manager_id'];
            $consultans_manager_service->updateConsultantsManagerList($consultants_manager);

            /** @var SalesManagerService $sales_manager_service */
            $sales_manager_service = $this->createService('SalesManagerService');
            $sales_manager = $sales_manager_service->getSalesManagerInfoByBrandId($this->id);
            $sales_manager->brand_id = $this->POST['id'];
            $sales_manager->sales_manager_id = $this->POST['sales_manager_id'];
            $sales_manager_service->updateSalesManagerList($sales_manager);

            $brand_page_setting = $this->getPageSettingService()->getPageSettingsByBrandId($this->id);
            if (!$brand_page_setting) {
                $brand_page_setting = $this->getPageSettingService()->createEmptyPageSettings();
                $brand_page_setting->brand_id = $this->id;
            }
            $brand_page_setting->test_id = $this->POST['test_id'];
            $brand_page_setting->test_pass = $this->POST['test_pass'];
            $this->getPageSettingService()->updateBrandPageSetting($brand_page_setting);


            //salesforceの追加、更新
            foreach ($this->update_salesforce_ids as $id) {
                $salesforce = $this->brand_salesforce_service->getOrCreateBrandSalesforceById($this->POST[$id]);

                $salesforce->brand_id       = $this->POST['id'];
                $salesforce->url            = $this->POST['salesforce_url_'.$this->POST[$id]];
                $salesforce->start_date     = $this->POST['start_date_'.$this->POST[$id]];
                $salesforce->end_date       = $this->POST['end_date_'.$this->POST[$id]];

                $this->brand_salesforce_service->saveBrandSalesforce($salesforce);
            }

            //契約プランとBrandOptionの更新
            if ($this->POST['plan']) {
                /** @var BrandContractService $brandContractService */
                $brandContractService = $this->createService('BrandContractService');
                $brandContract = $brandContractService->getBrandContractByBrandId($this->POST['id']);
                $brandContract->operation          = $this->POST['operation'];
                $brandContract->for_production_flg = $this->POST['for_production_flg'];
                $brandContract->memo               = $this->POST['memo'];
                $brandContract = $brandContractService->updateBrandContract($brandContract);
                if ($brandContract->plan != $this->POST['plan']) {
                    $brandContract->plan = $this->POST['plan'];
                    $brandContractService->updateBrandContract($brandContract);
                }

                if ($this->Data['managerAccount']->isSuperUser() && in_array(BrandOptions::OPTION_SEGMENT, $this->POST['brand_options'])) {
                    $segment_service = $this->getService('SegmentService');
                    $segment_service->updateBrandSegmentLimit($this->id, Segment::TYPE_CONDITIONAL_SEGMENT, $this->POST['conditional_segment_limit']);
                    $segment_service->updateBrandSegmentLimit($this->id, Segment::TYPE_SEGMENT_GROUP, $this->POST['segment_group_limit']);
                }

                /** @var BrandOptionsService $brandOptionsService */
                $brandOptionsService = $this->createService('BrandOptionsService');
                $brandOptionsService->updateBrandOptions($this->POST['id'], $this->POST['brand_options']);
            }

            /** @var InquiryBrandService $inquiry_brand_service */
            $inquiry_brand_service = $this->getService('InquiryBrandService');
            $inquiry_brand = $inquiry_brand_service->getRecord(InquiryBrandService::MODEL_TYPE_INQUIRY_BRAND, array('brand_id' => $this->id));
            $inquiry_brand_service->updateInquiryBrand($inquiry_brand->id, array('aa_alert_flg' => ($this->POST['aa_alert_flg']) ? 1 : 0));

            $brands->commit();
        } catch (Exception $e) {
            $brands->rollback();
            $this->Data['saved'] = 1;
            return 'redirect: ' . Util::rewriteUrl('brands', 'edit_form', array($this->POST['id']), array('mode' => ManagerService::ADD_ERROR ), '', true);
        }

        $this->Data['saved'] = 1;
        return 'redirect: ' . Util::rewriteUrl ('brands', 'edit_form', array($this->POST['id']), array('mode' => ManagerService::ADD_FINISH ), '', true);
    }

    public function getPageSettingService() {
        if (!$this->page_setting_service) $this->page_setting_service = $this->createService('BrandPageSettingService');

        return $this->page_setting_service;
    }

    public function createValidatorForSalesforceUrl($salesforceUrlKey) {
        $validator = array(
            $salesforceUrlKey => array(
                'required' => 1,
                'type'     => 'str',
                'length'   => 255,
            )
        );

        return $validator;
    }

    public function createValidatorForSalesforceDate($salesforceDatekey) {
        $validator = array(
            $salesforceDatekey => array(
                'required' => 1,
                'type'     => 'date',
                'length'   => 10,
            )
        );

        return $validator;
    }

    public function getPostSalesforceIdKeys($post) {
        return preg_grep("/salesforce_id_/", array_keys($post));
    }

}