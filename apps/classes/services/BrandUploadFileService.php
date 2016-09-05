<?php
AAFW::import('jp.aainc.lib.base.aafwServiceBase');

class BrandUploadFileService extends aafwServiceBase {
    const PAGE_LIMITED = 20;

    private $brand_upload_files;

    public function __construct() {
        $this->brand_upload_files = $this->getModel('BrandUploadFiles');
    }

    public function createEmptyBrandUploadFile() {
        return $this->brand_upload_files->createEmptyObject();
    }

    public function createBrandUploadFile($brand_upload_file) {
        $this->brand_upload_files->save($brand_upload_file);
    }

    public function updateBrandUploadFile($brand_upload_file) {
        $this->brand_upload_files->save($brand_upload_file);
    }

    public function deleteBrandUploadFile($brand_upload_file) {
        $this->brand_upload_files->delete($brand_upload_file);
    }

    public function getBrandUploadFileById($id) {
        return $this->brand_upload_files->findOne($id);
    }

    /**
     * @param $brand_id
     * @param int $page
     * @param int $limit
     * @param null $order
     * @return mixed
     */
    public function getBrandUploadFilesByBrandId($brand_id, $page = 1, $limit = self::PAGE_LIMITED, $order = null) {
        $filter = array(
            'conditions' => array(
                'brand_id' => $brand_id
            ),
            'pager' => array(
                'page' => $page,
                'count' => $limit
            ),
            'order' => $order
        );

        return $this->brand_upload_files->find($filter);
    }
    
    /**
     * @param $brand_id
     * @return mixed
     */
    public function getBrandUploadFileCountByBrandId($brand_id) {
        $filter = array(
            'brand_id' => $brand_id
        );

        return $this->brand_upload_files->count($filter);
    }
}
