<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');
AAFW::import('jp.aainc.classes.validator.StaticHtmlEntryValidator');
AAFW::import('jp.aainc.classes.validator.StaticHtmlEntryTemplateValidator');
AAFW::import('jp.aainc.actions.user.brandco.admin-blog.trait.StaticHtmlEmbedPageTrait');

class create_static_html_embed_page extends BrandcoPOSTActionBase {

    use StaticHtmlEmbedPageTrait;

    protected $ContainerName = 'create_static_html_embed_page';

    protected $Form = array(
        'package' => 'admin-blog',
        'action' => 'create_static_html_embed_page_form',
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
        ),
        'title' => array(
            'required' => true,
            'type' => 'str',
            'length' => 60,
        ),
    );

    public function beforeValidate() {

        $this->staticHtmlEntryService = $this->createService('StaticHtmlEntryService');
        $this->brand = $this->getBrand();
        $this->publicFlg = $this->POST['public_flg'];
        $this->loginTypes = $this->POST['login_types'];

    }

    public function validate() {

        if($this->isInvalidLoginTypeInput()){
            $this->Validator->setError('login_type', 'NOT_CHOOSE');
        }

        if ($this->Validator->getErrorCount()) return false;

        return true;
    }

    function doAction() {

        $staticHtmlEntryTransaction = aafwEntityStoreFactory::create('StaticHtmlEntries');

        try{

            $staticHtmlEntryTransaction->begin();

            $user = $this->getUser();
            $data = $this->createEntryData();

            $staticHtmlEntry = $this->saveEntry($this->brand->id,$user->id,$data);
            
            if($staticHtmlEntry->isEmbedPage()){

                $this->saveEmbedEntry($staticHtmlEntry);

                $this->saveEmbedLoginType($staticHtmlEntry);
            }

            // 作成者、編集者を保存
            $this->staticHtmlEntryService->createStaticHtmlEntryUsers($staticHtmlEntry, $user->id);

            $staticHtmlEntryTransaction->commit();

        }catch (Exception $e){
            $staticHtmlEntryTransaction->rollback();
            $this->logger = aafwLog4phpLogger::getDefaultLogger();
            $this->logger->error('create_static_html_embed_page error:' . $e);
            return 'redirect: ' . Util::rewriteUrl('admin-blog', 'create_static_html_embed_page_form', array(), array('mid' => 'register-failed'));
        }

        $this->Data['saved'] = 1;

        return 'redirect: ' . Util::rewriteUrl('admin-blog', 'edit_static_html_embed_page_form', array($staticHtmlEntry->id), array('mid' => 'action-created'));
    }
}
