<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');

class upload_file extends BrandcoPOSTActionBase {
    protected $ContainerName = 'upload_file';
    protected $Form = array(
        'package' => 'admin-blog',
        'action' => 'file_list?mid=failed'
    );

    public $NeedOption = array();
    public $NeedAdminLogin = true;
    public $CsrfProtect = true;

    private $upload_file_ids;

    public function doThisFirst() {
        $this->upload_file_ids = $this->POST['upload_file_ids'];
    }

    public function validate() {
        return $this->upload_file_ids ? true : false;
    }

    public function doAction() {
        $upload_file_service = $this->createService('UploadFileService');
        $brand_upload_file_service = $this->createService('BrandUploadFileService');

        foreach ($this->upload_file_ids as $upload_file_id) {
            // Update upload file's hidden_flg
            $upload_file = $upload_file_service->getUploadFileById($upload_file_id);
            $upload_file->hidden_flg = 0;
            $upload_file_service->updateUploadFile($upload_file);

            $brand_upload_file = $brand_upload_file_service->createEmptyBrandUploadFile();
            $brand_upload_file->brand_id = $this->getBrand()->id;
            $brand_upload_file->file_id = $upload_file_id;
            $brand_upload_file->pub_date = $upload_file->created_at;
            $brand_upload_file_service->createBrandUploadFile($brand_upload_file);
        }

        $this->Data['saved'] = 1;
        $redirect_url = Util::rewriteUrl('admin-blog', 'file_list', array(), array('mid' => 'action-created'));
        return 'redirect: ' . $redirect_url;
    }
}