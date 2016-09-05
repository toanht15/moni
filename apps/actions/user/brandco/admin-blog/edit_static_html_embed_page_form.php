<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.classes.entities.BrandUploadFile');
AAFW::import('jp.aainc.classes.services.UserService');

class edit_static_html_embed_page_form extends BrandcoGETActionBase {

    protected $ContainerName = 'edit_static_html_embed_page';

    public $NeedOption = array(BrandOptions::OPTION_CMS);
    public $NeedAdminLogin = true;

    protected $staticHtmlEntryService;
    protected $staticHtmlEntry;
    protected $entryId;

    public function beforeValidate() {
        $this->deleteErrorSession();
        $this->Data['entry_id'] = $this->GET['exts'][0];

        // entry_idチェック
        if (!$this->Data['entry_id']) return 'redirect:' . Util::rewriteUrl('admin-blog', 'static_html_entries');

        $this->staticHtmlEntryService = $this->createService('StaticHtmlEntryService');

        // entry取得
        $this->Data['brand'] = $this->getBrand();
        $this->staticHtmlEntry = $this->staticHtmlEntryService->getEntryByBrandIdAndEntryId( $this->Data['brand']->id, $this->Data['entry_id']);
    }

    public function validate() {
        
        if(!$this->canAddEmbedPage()){
            return '404';
        }

        // entryチェック
        $idValidator = new StaticEntryValidator(BrandcoValidatorBase::SERVICE_NAME_STATIC_HTML, $this->Data['brand']->id);
        if (!$idValidator->isCorrectEntryId($this->Data['entry_id'])){
            return false;
        }

        if(!$this->staticHtmlEntry->isEmbedPage()){
            return false;
        }

		return true;
	}

	function doAction() {

        $brandGlobalSettingService = $this->getService('BrandGlobalSettingService');
        $msbcCustomLoginPage = $brandGlobalSettingService->getBrandGlobalSettingByName(BrandInfoContainer::getInstance()->getBrandGlobalSettings(),
            BrandGlobalSettingService::MSBC_CUSTOM_LOGIN_PAGE);

        if(Util::isNullOrEmpty($msbcCustomLoginPage)){
            $this->Data['sns_login_types'] = StaticHtmlExternalPageLoginType::$snsLoginTypeOrder;
        }else{
            $this->Data['sns_login_types'] = StaticHtmlExternalPageLoginType::$msbcSnsLoginTypeOrder;
        }

        if(!$this->canLoginByLinkedIn()){
            unset($this->Data['sns_login_types'][SocialAccountService::SOCIAL_MEDIA_LINKEDIN]);
        }

        // entryを変数へ格納
        $this->Data['entry'] = $this->staticHtmlEntry;

        $this->Data['embed_content'] = $this->createEmbedContent($this->staticHtmlEntry);

        $this->setAuthorUsers();

        // 公開日を設定
        $this->setPublicDate();

        // ActionFormに格納
        $actionForm = $this->staticHtmlEntry->toArray();

        $staticHtmlEmbedEntries = $this->staticHtmlEntry->getStaticHtmlEmbedEntries();

        $actionForm['public_flg'] = $staticHtmlEmbedEntries->current()->public_flg;

        $embedPageLoginTypes = $this->staticHtmlEntry->getStaticHtmlExternalPageLoginTypes();

        $loginTypes = array();

        if($embedPageLoginTypes){
            foreach($embedPageLoginTypes as $embedPageLoginType){
                $loginTypes[] = $embedPageLoginType->social_media_id;
            }
        }
        $actionForm['login_types'] = $loginTypes;

		$this->assign('ActionForm', $actionForm);

		return 'user/brandco/admin-blog/edit_static_html_embed_page_form.php';
	}

    private function setAuthorUsers(){
        $this->Data['author'] = $this->staticHtmlEntryService->getAuthorStaticHtmlEntry($this->Data['entry']);
    }

    private function setPublicDate() {
        if ($this->Data['entry']->public_date == '0000-00-00 00:00:00') {
            $this->Data['entry']->public_date = $this->getToday();
        }else{
            $public_date = $this->Data['entry']->public_date;
            $this->Data['entry']->public_date = $this->formatDate($public_date, 'YYYY/MM/DD');
            $this->Data['entry']->public_time_hh = date('H', strtotime($public_date));
            $this->Data['entry']->public_time_mm = date('i', strtotime($public_date));
        }
    }

    private function createEmbedContent($staticHtmlEntry){

        $parser = new PHPParser();

        $pageUrl =  $staticHtmlEntry->getEmbedUrl();

        $embedContent = $parser->parseTemplate('StaticEmbedPage.php',array('page_url' => $pageUrl));

        return $embedContent;
    }
}
