<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');
class delete_static_html_entries extends BrandcoPOSTActionBase {
    protected $ContainerName = 'delete_static_html_entries';

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
        /** @var StaticHtmlEntryService $service */
        $service = $this->createService('StaticHtmlEntryService');

        $transaction = aafwEntityStoreFactory::create('StaticHtmlEntries');

        try{
            $transaction->begin();

            $service->deleteEntries($this->brand, $this->entries, $this->getBrandsUsersRelation()->user_id);

            $transaction->commit();
        }catch (Exception $e){
            $transaction->rollback();
            $this->logger = aafwLog4phpLogger::getDefaultLogger();
            $this->logger->error('delete_static_html_entries action error.' . $e);
            return 'redirect:' . Util::rewriteUrl('admin-blog', 'static_html_entries', array(), array('mid' => 'failed'));
        }

        return 'redirect:' . Util::rewriteUrl('admin-blog', 'static_html_entries', array(), array('mid' => 'action-deleted'));
    }
}