<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');
class public_static_html_entries extends BrandcoPOSTActionBase {
    protected $ContainerName = 'public_static_html_entries';

    public $NeedOption = array();
    public $NeedAdminLogin = true;
    public $CsrfProtect = true;
    protected $brand;
    protected $logger;

    public function validate() {
        $this->brand = $this->getBrand();
        $idValidator = new StaticEntryValidator(BrandcoValidatorBase::SERVICE_NAME_STATIC_HTML, $this->brand->id);
        foreach ($this->entries as $entry_id) {
            if (!$idValidator->isCorrectEntryId($entry_id)) return false;
        }
        return true;
    }

    function doAction() {
        $service = $this->createService('StaticHtmlEntryService');

        $transaction = aafwEntityStoreFactory::create('StaticHtmlEntries');

        try{
            $transaction->begin();

            $service->publicEntries($this->brand, $this->entries, $this->getBrandsUsersRelation()->user_id);

            $transaction->commit();

        }catch (Exception $e) {
            $transaction->rollback();
            $this->logger = aafwLog4phpLogger::getDefaultLogger();
            $this->logger->error('public_static_html_entries action error.' . $e);
            return 'redirect:' . Util::rewriteUrl('admin-blog', 'static_html_entries', array(), array('mid' => 'failed'));
        }

        $this->Data['saved'] = 1;
        return 'redirect:' . Util::rewriteUrl('admin-blog', 'static_html_entries', array(), array('mid' => 'action-publish'));
    }
}
