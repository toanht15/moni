<?php
AAFW::import('jp.aainc.lib.base.aafwServiceBase');

class UploadFileService extends aafwServiceBase {
    protected $upload_files;

    public function __construct() {
        $this->upload_files = $this->getModel('UploadFiles');
    }

    public function createEmptyUploadFile() {
        return $this->upload_files->createEmptyObject();
    }

    public function updateUploadFile($upload_file) {
        $this->upload_files->save($upload_file);
    }

    public function createUploadFile($upload_file) {
        return $this->upload_files->save($upload_file);
    }

    public function deleteUploadFile($upload_file) {
        $this->upload_files->delete($upload_file);
    }

    public function getUploadFileById($file_id) {
        return $this->upload_files->findOne($file_id);
    }
}