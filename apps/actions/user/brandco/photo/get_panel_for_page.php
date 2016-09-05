<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');

class get_panel_for_page extends BrandcoGETActionBase {

    public $NeedOption = array();

    public function validate() {
        if ($this->isEmpty($this->p) || $this->p < 1 || !$this->isNumeric($this->p)) return '403';

        return true;
    }

    function doAction() {
        /** @var PhotoUserService $photo_user_service */
        $photo_user_service = $this->getService('PhotoUserService');

        /** @var PhotoStreamService $photo_stream_service */
        $photo_stream_service = $this->getService('PhotoStreamService');

        if ($this->cp_action_id == -1) {
            // post_list
            $stream = $photo_stream_service->getStreamByBrandId($this->getBrand()->id);
            $this->Data['page_data']['photo_entries'] = $photo_stream_service->getAvailableEntriesByStreamId($stream->id, $this->p);
        } else {
            // cp_actions
            $this->Data['page_data']['photo_entries'] = $photo_user_service->getApprovedPhotoEntriesByActionId($this->cp_action_id, $this->p);
        }

        return 'user/brandco/photo/get_panel_for_page.php';
    }
}