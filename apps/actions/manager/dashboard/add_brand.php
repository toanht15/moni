<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoManagerPOSTActionBase');
AAFW::import('jp.aainc.classes.services.ApplicationService');
class add_brand extends BrandcoManagerPOSTActionBase {

    /** @var BrandService $brand_service */
    protected $brand_service;

    protected $ContainerName = 'add_brand';
    protected $Form = array(
        'package' => 'dashboard',
        'action' => 'add_brand_form',
    );

    public $NeedManagerLogin = true;
    public $ManagerPageId    = Manager::MENU_ADD_BRAND;
    public $CsrfProtect = true;

    protected $ValidatorDefinition = array(
        'plan' => array(
            'required' => 1,
            'type' => 'num',
            'range' => array(
                '>=' => 1,
                '<=' => 4
            )
        ),
        'brand_name' => array(
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
        'directory_name' => array(
            'required' => 1,
            'regex' => '/^[a-zA-Z0-9][\.\-\_a-zA-Z0-9]+[a-zA-Z0-9]*$/',
            'length' => 255,
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
        'salesforce_url' => array(
            'required' => 1,
            'type'     => 'str',
            'length'   => 255,
        ),
        'start_date' => array(
            'required' => 1,
            'type'     => 'date',
            'length'   => 10,
        ),
        'end_date' => array(
            'required' => 1,
            'type'     => 'date',
            'length'   => 10,
        )
    );

    public function validate() {
        // ディレクトリの重複チェック
        $this->brand_service = $this->createService('BrandService');
        $brand = $this->brand_service->getBrandByDirectoryName($this->directory_name);
        if ($brand) {
            $this->Validator->setError('directory_name', 'EXISTED_DIRECTORY_NAME');
        }
        if($this->sales_manager == 0) {
            $this->Validator->setError('sales_manager', 'NOT_REQUIRED');
        }
        if($this->consultants_manager == 0) {
            $this->Validator->setError('consultants_manager', 'NOT_REQUIRED');
        }
        if (Util::isInvalidBrandName($this->POST['mail_name'])) {
            $this->Validator->setError('mail_name', 'INVALID_BRAND_NAME_FORMAT');
        }
        if (!$this->isURL($this->POST['salesforce_url'])) {
            $this->Validator->setError('salesforce_url', INPUT_URL);
        }
        if ($this->POST['start_date'] > $this->POST['end_date']) {
            $this->Validator->setError('date_range', 'INVALID_TIME2');
        }
        return !$this->Validator->getErrorCount();
    }

    function doAction() {

        $brandInfo = $this->POST;

        try {
            $brandInfo['app_id'] = ApplicationService::BRANDCO;

            foreach (BrandOptions::$SERVICE_OPTIONS[$this->plan] as $optionId => $optionStatus) {
                $brandInfo['option_'.$optionId] = $optionStatus;
            }

            $brands = aafwEntityStoreFactory::create('Brands');
            $brands->begin();

            // ブランド追加処理
            /** @var BrandService $brand_service */
            $brand_service = $this->createService('BrandService');
            $new_brand = $brand_service->addBrand($brandInfo);

            $brand_business_category_service = $this->createService('BrandBusinessCategoryService');
            $brand_business_category_service->createBrandBusinessCategory($new_brand->id, $this->POST['business_category'], $this->POST['business_size']);

            $consultans_manager_service = $this->createService('ConsultantsManagerService');
            $consultans_manager_service->addConsultansManager($brandInfo, $new_brand);

            $sales_manager_service = $this->createService('SalesManagerService');
            $sales_manager_service->addSalesManager($brandInfo, $new_brand);

            $page_setting_service = $this->createService('BrandPageSettingService');
            $brand_page_setting = $page_setting_service->createEmptyPageSettings();
            $brand_page_setting->brand_id = $new_brand->id;
            $brand_page_setting->meta_title = $new_brand->name;
            $brand_page_setting->meta_description = "『" . $new_brand->name . "』のブランドページです。SNSアカウントの記事や、どなたでも参加できるキャンペーン情報をお届けします。";
            if ($brandInfo['test_page']) {
                $brand_page_setting->test_id = $page_setting_service->genRandomString();
                $brand_page_setting->test_pass = $page_setting_service->genRandomString();
            }
            $page_setting_service->updateBrandPageSetting($brand_page_setting);

            /** @var PhotoStreamService $photo_stream_service */
            $photo_stream_service = $this->createService('PhotoStreamService');
            $photo_stream = $photo_stream_service->createEmptyStream();
            $photo_stream->brand_id = $new_brand->id;
            $photo_stream_service->createStream($photo_stream);

            $page_stream_service = $this->createService('PageStreamService');
            $page_stream = $page_stream_service->createEmptyStream();
            $page_stream->brand_id = $new_brand->id;
            $page_stream_service->createStream($page_stream);

            /** @var BrandTransactionService $transaction_service */
            $transaction_service = $this->createService('BrandTransactionService');
            $transaction_service->createBrandTransactions($new_brand->id);

            /** @var BrandContractService $brand_contract_service */
            $brand_contract_service = $this->createService('BrandContractService');
            $brand_contract = $brand_contract_service->getEmptyBrandContract();
            $brand_contract->brand_id = $new_brand->id;
            $brand_contract->plan = $brandInfo['plan'];
            $brand_contract->operation = $this->POST['operation'];
            $brand_contract->for_production_flg = $this->POST['for_production_flg'];
            $brand_contract->memo = $this->POST['memo'] ?: '';
            $brand_contract_service->updateBrandContract($brand_contract);

            /** @var BrandCmsSettingService $brand_cms_setting_service */
            $brand_cms_setting_service = $this->createService('BrandCmsSettingService');
            $brand_cms_setting = $brand_cms_setting_service->createEmptyObject();
            $brand_cms_setting->brand_id = $new_brand->id;
            $brand_cms_setting_service->updateBrandCmsSetting($brand_cms_setting);

            /** @var BrandSalesforceService $brand_salesforce_service */
            $brand_salesforce_service = $this->createService('BrandSalesforceService');
            $brand_salesforce_service->createBrandSalesforce($new_brand->id, $this->POST['salesforce_url'], $this->POST['start_date'], $this->POST['end_date']);

            /** @var CpInstagramHashtagStreamService $cp_instagram_hashtag_stream_service */
            $cp_instagram_hashtag_stream_service = $this->getService('CpInstagramHashtagStreamService');
            $cp_instagram_hashtag_stream = $cp_instagram_hashtag_stream_service->createEmptyEntry();
            $cp_instagram_hashtag_stream->brand_id = $new_brand->id;
            $cp_instagram_hashtag_stream_service->updateStream($cp_instagram_hashtag_stream);

            /** @var InquiryBrandService $inquiry_brand_service */
            $inquiry_brand_service = $this->getService('InquiryBrandService');
            $inquiry_brand_service->createInquiryBrand($new_brand->id, array('aa_alert_flg' => $this->POST['aa_alert_flg']));

            if ($brandInfo['option_' . BrandOptions::OPTION_SEGMENT] && $brandInfo['option_' . BrandOptions::OPTION_SEGMENT] == BrandOptions::ON) {
                /** @var SegmentService $segment_service */
                $segment_service = $this->getService('SegmentService');
                $segment_service->updateBrandSegmentLimit($new_brand->id, Segment::TYPE_CONDITIONAL_SEGMENT, BrandSegmentLimit::CONDITIONAL_SEGMENT_DEFAULT_LIMIT);
                $segment_service->updateBrandSegmentLimit($new_brand->id, Segment::TYPE_SEGMENT_GROUP, BrandSegmentLimit::SEGMENT_GROUP_DEFAULT_LIMIT);
            }

            $brands->commit();
        } catch (Exception $e) {
            $brands->rollback();
            return 'redirect: ' . Util::rewriteUrl('dashboard', 'add_brand_form', array(), array('mode' => ManagerService::ADD_ERROR), '', true);
        }

        return 'redirect: ' . Util::rewriteUrl('brands', 'index', array(), array('mode' => ManagerService::ADD_FINISH, 'brand_id' => $new_brand->id, 'account' => Brand::BRAND_DEFAULT), '', true);

        $this->Data['saved'] = 1;

    }
}