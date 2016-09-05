<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.classes.brandco.cp.trait.DownloadDataTrait');

abstract class DownloadDataActionBase extends BrandcoGETActionBase {

    Use DownloadDataTrait;

    public $NeedOption = array();
    public $NeedAdminLogin = true;

    protected $cp_user_service;
    protected $brands_users_relation_service;
    protected $temp_folder_name;
    protected $zip_file_name;
    protected $is_hide_personal_info;

    protected $csv_file_header = array(
        'モジュールタイトル',
        '会員No',
        'ファイル名'
    );

    public function doThisFirst() {
        ini_set('max_execution_time', 3600);
        $this->Data['cp_id'] =  $this->GET['exts'][0];
        $this->Data['cp_action_id'] =  $this->GET['exts'][1];

        $this->temp_folder_name = $this->getTempFolderName($this->Data['cp_id']);
        $this->zip_file_name = $this->getZipFileName($this->Data['cp_id']);
        $this->initFolder($this->temp_folder_name);

        $this->cp_user_service = $this->createService('CpUserService');
        $this->brands_users_relation_service = $this->createService('BrandsUsersRelationService');

        /** @var BrandGlobalSettingService $brand_global_setting_service */
        $brand_global_setting_service = $this->createService('BrandGlobalSettingService');
        $brand_global_setting = $brand_global_setting_service->getBrandGlobalSettingByName(BrandInfoContainer::getInstance()->getBrandGlobalSettings(), BrandGlobalSettingService::HIDE_PERSONAL_INFO);
        $this->is_hide_personal_info = !Util::isNullOrEmpty($brand_global_setting) ? true : false;
    }

    public function beforeValidate() {
        if (!$this->Data['cp_id'] || !$this->Data['cp_action_id']) {
            return 'redirect: ' . Util::rewriteUrl('', '');
        }

        if (!$this->getBrand()) {
            return 'redirect: ' . Util::rewriteUrl('admin-cp', 'edit_action', array($this->Data['cp_id'] ,$this->Data['cp_action_id']), array());
        }

    }

    public function validate() {
        $validatorService = new CpValidator($this->getBrand()->id);
        return $validatorService->isOwnerOfAction($this->Data['cp_action_id']);
    }
    
    abstract public function getZipFileName($cp_id);
}
