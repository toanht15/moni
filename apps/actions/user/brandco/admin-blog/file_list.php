<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.classes.entities.BrandUploadFile');

class file_list extends BrandcoGETActionBase {
    protected $ContainerName = 'upload_file';

    public $NeedOption = array();
    public $NeedAdminLogin = true;

    private $popup_params;

    public function doThisFirst() {
        if ($_GET['f_id']) {
            $this->setBrandSession('popup_params', null);

            $this->setBrandSession('popup_params', $_GET);
        }
    }

    public function validate() {
        $this->popup_params = $this->getBrandSession('popup_params');
        if (BrandUploadFile::isPopupAccessible($this->popup_params['f_id'])) {
            return true;
        }

        return false;
    }

    public function doAction() {
        $brand_upload_file_service = $this->createService('BrandUploadFileService');

        // For paging information
        $this->Data['page_limited'] = BrandUploadFileService::PAGE_LIMITED;
        $this->Data['total_file_count'] = $brand_upload_file_service->getBrandUploadFileCountByBrandId($this->getBrand()->id);

        $total_page = floor($this->Data['total_file_count'] / BrandUploadFileService::PAGE_LIMITED) + ($this->Data['total_file_count'] % BrandUploadFileService::PAGE_LIMITED > 0);
        $this->p = Util::getCorrectPaging($this->p, $total_page);
        $order = array(
            'name' => 'pub_date',
            'direction' => 'desc'
        );
        $this->Data['brand_upload_files'] = $brand_upload_file_service->getBrandUploadFilesByBrandId($this->getBrand()->id, $this->p, BrandUploadFileService::PAGE_LIMITED, $order);
        
        $this->Data['callback'] = $this->popup_params['CKEditorFuncNum'];
        $this->Data['f_id'] = $this->popup_params['f_id'];
        $this->Data['stt'] = $this->popup_params['stt'] ? $this->popup_params['stt'] : 0;

        return 'user/brandco/admin-blog/file_list.php';
    }
}