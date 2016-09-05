<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.classes.BrandInfoContainer');

class embed_page extends BrandcoGETActionBase {

    public $NeedOption = array(BrandOptions::OPTION_CMS);
    public $NeedRedirect = true;

    private $static_html_entry_service;

    public function doThisFirst(){
        $this->Data['pageUrl'] = $this->GET['exts'][0];
        $this->static_html_entry_service = $this->createService('StaticHtmlEntryService');
        $brand = $this->getBrand();

        $this->Data['staticHtmlEntry'] = $this->static_html_entry_service->getEntryByBrandIdAndPageUrl($brand->id, $this->Data['pageUrl']);

        if($this->isNeedLoginEmbedPage()){
            $this->NeedUserLogin = true;
        }
    }

	public function validate () {
		return true;
	}

	function doAction() {

        if(!$this->static_html_entry_service->isActivePage($this->Data['staticHtmlEntry']) || !$this->Data['staticHtmlEntry']->embed_flg) {
            return 'user/brandco/embed_error_page.php';
        }

        return 'user/brandco/embed_page.php';
	}

    private function isNeedLoginEmbedPage(){
        if($this->static_html_entry_service->isActivePage($this->Data['staticHtmlEntry']) && $this->canAddEmbedPage() && $this->Data['staticHtmlEntry']->embed_flg && !$this->isPublicEmbedPage()){
            return true;
        }
        return false;
    }

    private function isPublicEmbedPage(){

        $staticHtmlEmbedEntries = $this->Data['staticHtmlEntry']->getStaticHtmlEmbedEntries();

        if(!$staticHtmlEmbedEntries){
            return false;
        }

        $staticHtmlEmbedEntry = $staticHtmlEmbedEntries->current();
        if($staticHtmlEmbedEntry->public_flg == StaticHtmlEmbedEntry::PUBLIC_PAGE){
            return true;
        }
        
        return false;
    }

    public function getPageUrl(){
        return $this->Data['pageUrl'];
    }

    public function isEmbedPage(){
        return true;
    }
}