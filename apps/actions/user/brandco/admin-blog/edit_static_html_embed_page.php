<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');
AAFW::import('jp.aainc.classes.validator.StaticHtmlEntryValidator');
AAFW::import('jp.aainc.classes.validator.StaticHtmlEntryTemplateValidator');
AAFW::import('jp.aainc.actions.user.brandco.admin-blog.trait.StaticHtmlEmbedPageTrait');

class edit_static_html_embed_page extends BrandcoPOSTActionBase {

    use StaticHtmlEmbedPageTrait;

    protected $ContainerName = 'edit_static_html_embed_page';
    protected $Form = array(
        'package' => 'admin-blog',
        'action' => 'edit_static_html_embed_page_form/{entryId}',
    );

    public $NeedOption = array();
    public $NeedAdminLogin = true;
    public $CsrfProtect = true;
    /**@var StaticHtmlEntryService $staticHtmlEntryService **/
    protected $staticHtmlEntryService;

    protected $publicFlg;
    protected $loginTypes;
    protected $brand;

    protected $ValidatorDefinition = array(
        'body' => array(
            'type' => 'str',
            'required' => true,
        )
    );

    public function beforeValidate() {

        $this->staticHtmlEntryService = $this->createService('StaticHtmlEntryService');
        $this->brand = $this->getBrand();
        $this->publicFlg = $this->POST['public_flg'];
        $this->loginTypes = $this->POST['login_types'];

    }

    public function validate() {

        // entryチェック
        $idValidator = new StaticEntryValidator(BrandcoValidatorBase::SERVICE_NAME_STATIC_HTML, $this->brand->id);
        if (!$idValidator->isCorrectEntryId($this->entryId)) return false;

        if($this->isInvalidLoginTypeInput()){
            $this->Validator->setError('login_type', 'NOT_CHOOSE');
        }

        if ($this->Validator->getErrorCount()) return false;

        return true;
    }

    function doAction() {

        $staticHtmlEntryTransaction = aafwEntityStoreFactory::create('StaticHtmlEntries');

        try {

            $staticHtmlEntryTransaction->begin();
            $user = $this->getUser();

            // static_html_entry更新
            $data = $this->createEntryData();
            $staticHtmlEntry = $this->saveEntry($this->brand->id,$user->id,$data);
            if (!$staticHtmlEntry) return 'redirect:' . Util::rewriteUrl('admin-blog', 'static_html_entries');

            $this->saveEmbedEntry($staticHtmlEntry);

            $this->saveEmbedLoginType($staticHtmlEntry);

            $this->staticHtmlEntryService->createStaticHtmlEntryUsers($staticHtmlEntry, $user->id);

            $staticHtmlEntryTransaction->commit();

        } catch (Exception $e) {

            $staticHtmlEntryTransaction->rollback();
            $this->logger = aafwLog4phpLogger::getDefaultLogger();
            $this->logger->error('edit_static_html_embed_page error:' . $e);

            return 'redirect: ' . Util::rewriteUrl('admin-blog', 'edit_static_html_embed_page_form', array($staticHtmlEntry->id), array('mid' => 'failed'));
        }

        if ($this->Validator->getErrorCount()) {
            $return = $this->getFormURL();
        } else {
            $this->Data['saved'] = 1;
            $return = 'redirect: ' . Util::rewriteUrl('admin-blog', 'edit_static_html_embed_page_form', array($staticHtmlEntry->id), array('mid' => 'updated'));
        }

        return $return;
    }
}
