<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');

class watch_video extends BrandcoGETActionBase {

    public $NeedOption = array();
    
    public function validate() {
            return true;
    }

    public function doAction() {
        $this->Data['msg_id'] = $this->GET['msg_id'];
        $this->Data['video_url'] = urldecode($this->GET['video_url']);

        $this->Data['video_speed_list'] = array(
            '1.0' => '標準',
            '1.5' => '1.5倍速',
            '2.0' => '2.0倍速'
        );

        return 'user/brandco/video/watch_video.php';
    }
}