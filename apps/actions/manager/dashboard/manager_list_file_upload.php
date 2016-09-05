<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoManagerGETActionBase');
class manager_list_file_upload extends BrandcoManagerGETActionBase {

    public function validate() {
        return true;
    }

    function doAction() {
        $storage_client = new StorageClient();
        $prefix = $storage_client->getPrefixByKey('free_area_entry/');
        $this->Data['images'] = $storage_client->listObjects($prefix);
        $this->Data['cback'] = $this->CKEditorFuncNum;
        return 'manager/dashboard/manager_list_file_upload.php';
    }
}