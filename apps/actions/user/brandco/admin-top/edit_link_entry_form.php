<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
class edit_link_entry_form extends BrandcoGETActionBase {
	protected $ContainerName = 'edit_link_entry';

    public $NeedOption = array();
	public $NeedAdminLogin = true;

	public function beforeValidate () {
        $this->deleteErrorSession();
		$this->Data['entryId'] = $this->GET['exts'][0];
	}

	public function validate () {

        $this->Data['brand'] = $this->getBrand();
        if ($this->Data['entryId'] != 0) {
            $idValidator = new StaticEntryValidator(BrandcoValidatorBase::SERVICE_NAME_LINK, $this->Data['brand']->id);
            if (!$idValidator->isCorrectEntryId($this->Data['entryId'])){
                return false;
            }
        }

		return true;
	}

	function doAction() {
		$link_entry_service = $this->createService('LinkEntryService');
		$linkEntry = $link_entry_service->getEntryByBrandIdAndEntryId($this->Data['brand']->id, $this->Data['entryId']);
		if(!$linkEntry) {
			$linkEntry = $link_entry_service->createEmptyEntry();
		}
		$this->Data['entry'] = $linkEntry;
        if ($form = $this->getActionContainer('ValidateError')) {
            $this->Data['entry']->title = $form['title'];
            $this->Data['entry']->link = $form['link'];
            $this->Data['entry']->image_url = $form['image_url'];
            $this->Data['entry']->body = $form['body'];
        }
		$this->assign('ActionForm', $linkEntry->toArray());

		return 'user/brandco/admin-top/edit_link_entry_form.php';
	}
}