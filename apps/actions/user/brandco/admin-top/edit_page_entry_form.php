<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');

class edit_page_entry_form extends BrandcoGETActionBase {
    protected $ContainerName = 'edit_page_entry';

    public $NeedOption = array();
    public $NeedAdminLogin = true;

    public function beforeValidate () {
        $this->deleteErrorSession();
        $this->Data['entry_id'] = $this->GET['exts'][0];
        $this->Data['brand'] = $this->getBrand();
    }

    public function validate () {
        if ($this->Data['entry_id'] != 0) {
            $idValidator = new StreamValidator(BrandcoValidatorBase::SERVICE_NAME_PAGE, $this->Data['brand']->id);

            if (!$idValidator->isCorrectEntryId($this->Data['entry_id'])) {
                return false;
            }
        }

        return true;
    }

    function doAction() {
        $page_stream_service = $this->createService('PageStreamService');
        $page_entry = $page_stream_service->getEntryById($this->Data['entry_id']);

        if (!$page_entry) return '403';

        /** @var StaticHtmlCategoryService $static_html_category_service */
        $static_html_category_service = $this->getService('StaticHtmlCategoryService');
        $category = $static_html_category_service->getStaticHtmlCategoryByStaticHtmlEntryId($page_entry->static_html_entry_id);

        $this->Data['entry'] = $page_entry;
        $this->Data['category'] = $category;
        $this->Data['static_html_entry'] = $page_entry->getStaticHtmlEntry();
        $this->assign('ActionForm', $page_entry->toArray());

        return 'user/brandco/admin-top/edit_page_entry_form.php';
    }
}