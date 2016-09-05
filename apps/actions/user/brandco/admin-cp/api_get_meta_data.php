<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.classes.util.MetaDataParser');

class api_get_meta_data extends BrandcoGETActionBase {

    public $NeedOption = array();
    public $NeedAdminLogin = true;
    public $CsrfProtect = true;

    protected $ContainerName = 'api_get_meta_data';
    protected $AllowContent = array('JSON');

    public function validate() {

        if(!$this->GET['url']) {
            $this->sendErrorMessage();
            return false;
        }
        return true;
    }

    public function doAction() {

        $metaDataParser = new MetaDataParser();
        $htmlContent = $metaDataParser->getHtmlContent($this->GET['url']);
        $metaTags = $metaDataParser->getMetaData($htmlContent);

        if(!$metaTags) {
            $this->sendErrorMessage();
            return 'dummy.php';
        }

        $this->assign('json_data', $this->createAjaxResponse('ok', $metaTags));

        return 'dummy.php';
    }

    private function sendErrorMessage(){
        $json_data = $this->createAjaxResponse('ng', array(), array('error_message' => '外部ページURLが不正です'));
        $this->assign('json_data', $json_data);
    }
}
