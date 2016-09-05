<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.vendor.instagram.Instagram');

class api_get_instagram_embed_media extends BrandcoGETActionBase {

    public $NeedOption = array();

    protected $ContainerName = 'api_get_instagram_media_embed';

    protected $AllowContent = array('JSON');

    public function validate() {
        return true;
    }

    function doAction() {
        $instagram = new Instagram();
        $response = $instagram->getEmbedMedia($this->media_url);
        $response_html = $response->html;

        $instagram_stream_service = $this->createService('InstagramStreamService');
        $instagram_entry = $instagram_stream_service->getEntryById($this->entry_id);
        if ($instagram_entry && $instagram_entry->panel_comment) {
            $parser = new PHPParser();
            $response_html .= '<p class="panelComment">' . $parser->toHalfContentDeeply($instagram_entry->panel_comment) . '</p>';
        }

        $data = array(
            'embed_media' => $response_html
        );
        $json_data = $this->createAjaxResponse("ok", $data);

        $this->assign('json_data', $json_data);
        return 'dummy.php';
    }
}