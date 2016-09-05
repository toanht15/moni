<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
class edit_rss_entry_form extends BrandcoGETActionBase {
	protected $ContainerName = 'edit_rss_entry';

    public $NeedOption = array();
	public $NeedAdminLogin = true;

	public function beforeValidate () {
		$this->Data['entryId'] = $this->GET['exts'][0];
        $this->deleteErrorSession();
	}

	public function validate () {

        $brand = $this->getBrand();
        $idValidator = new StreamValidator(BrandcoValidatorBase::SERVICE_NAME_RSS, $brand->id);
        if(!$idValidator->isCorrectEntryId($this->Data['entryId'])) return false;

		return true;
	}

	function doAction() {
		$service = $this->createService('RssStreamService');
        $this->Data['entry'] = $service->getEntryById($this->Data['entryId']);

        if ($form = $this->getActionContainer('ValidateError')) {
            $this->Data['entry']->link = $form['link'];
            $this->Data['entry']->image_url = $form['image_url'];
            $this->Data['entry']->panel_text = $form['panel_text'];
        }
        $this->assign('ActionForm', $this->Data['entry']->toArray());

		return 'user/brandco/admin-top/edit_rss_entry_form.php';
	}
}