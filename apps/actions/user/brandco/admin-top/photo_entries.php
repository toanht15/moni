<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.classes.entities.PhotoEntry');

class photo_entries extends BrandcoGETActionBase {
    const PAGE_LIMITED = 20;

    public $NeedOption = array();
    public $NeedAdminLogin = true;

    public function validate() {
        $photo_stream_service = $this->createService('PhotoStreamService');
        $this->Data['stream'] = $photo_stream_service->getStreamByBrandId($this->getBrand()->id);

        if (!$this->Data['stream']->id) {
            return false;
        }

        return true;
    }

    function doAction() {
        try {
            $photo_stream_service = $this->createService('PhotoStreamService');

            $this->Data['page_limited'] = self::PAGE_LIMITED;
            $this->Data['total_entries_count'] = $photo_stream_service->getPhotoEntriesCountByStreamId($this->Data['stream']->id);

            $total_page = floor($this->Data['total_entries_count'] / self::PAGE_LIMITED) + ($this->Data['total_entries_count'] % self::PAGE_LIMITED > 0);
            $this->p = Util::getCorrectPaging($this->p, $total_page);

            $order = array(
                'name' => 'updated_at',
                'direction' => 'desc'
            );

            $this->Data['photo_entries'] = $photo_stream_service->getPhotoEntriesByStreamId($this->Data['stream']->id, $this->p, self::PAGE_LIMITED, $order);
        } catch (Exception $e) {
            $logger = aafwLog4phpLogger::getDefaultLogger();
            $logger->error('photo_entries@doAction Error');
            $logger->error($e);
        }

        return 'user/brandco/admin-top/photo_entries.php';
    }
}