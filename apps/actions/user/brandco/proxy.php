<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');

class proxy extends BrandcoGETActionBase {

    public $NeedOption = array();
    private $url;

    public function validate() {
        if (!$this->u) return false;

        $this->url = base64_decode($this->u);
        if( !$this->isUrl($this->url) || !strstr($this->url, config('@storage.AmazonS3.BucketName') )) return false;

        return true;
    }

    function doAction() {
        $contents = file_get_contents($this->url);
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_buffer($finfo, $contents);
        finfo_close($finfo);
        header('Content-type: ' . $mimeType);
        echo $contents;
        exit;
    }
}
