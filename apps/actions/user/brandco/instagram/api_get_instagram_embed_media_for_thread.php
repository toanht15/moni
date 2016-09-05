<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.vendor.instagram.Instagram');

class api_get_instagram_embed_media_for_thread extends BrandcoGETActionBase {

    public $NeedOption = array();

    protected $ContainerName = 'api_get_instagram_media_embed_for_thread';

    protected $AllowContent = array('JSON');

    public function validate() {
        return true;
    }

    function doAction() {
        $instagram = new Instagram();

        $response = $instagram->getEmbedMedia($this->media_url);

        if ($response->html) {
            $response_html = $response->html;
        } else {
            $response_html = '<p class="modalIgAttention"><span>削除などにより投稿の詳細が</span>ご覧いただけません</p>';
        }

        $data = array(
            'embed_media' => $response_html
        );
        $json_data = $this->createAjaxResponse("ok", $data);

        $this->assign('json_data', $json_data);
        return 'dummy.php';
    }
}