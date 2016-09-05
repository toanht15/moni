<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.classes.services.StreamService');

class edit_instagram_panel_form extends BrandcoGETActionBase {
    protected $ContainerName = 'edit_panel';

    public $NeedOption = array();

    public $NeedAdminLogin = true;

    public function beforeValidate () {
        $this->Data['brandSocialAccountId'] = $this->GET['exts'][0];
        $this->Data['entryId'] = $this->GET['exts'][1];
        $this->Data['type'] = StreamService::POST_TYPE_PANEL;
        $this->deleteErrorSession();
    }

    public function validate () {

        $brand = $this->getBrand();
        $idValidator = new StaticEntryValidator(BrandcoValidatorBase::SERVICE_NAME_BRAND_SOCIAL_ACCOUNT, $brand->id);
        if (!$idValidator->isCorrectEntryId($this->Data['brandSocialAccountId'])) return false;
        $idValidator = new StreamValidator(BrandcoValidatorBase::SERVICE_NAME_INSTAGRAM, $brand->id);
        if(!$idValidator->isCorrectEntryId($this->Data['entryId'])) return false;

        $service = $this->createService('BrandSocialAccountService');
        $this->Data['stream'] = $service->getStreamByBrandSocialAccountId($this->Data['brandSocialAccountId']);
        if(!$this->Data['stream']) return '403';

        return true;
    }

    function doAction() {

        $service = $this->createService(get_class($this->Data['stream']).'Service');

        $this->Data['entry'] = $service->getEntryById($this->Data['entryId']);

        //get page name
        $brandSocialAccount = $this->Data['stream']->getBrandSocialAccount();
        $this->Data['pageName'] = $brandSocialAccount->name;

        if ($form = $this->getActionContainer('ValidateError')) {
            $this->Data['entry']->link = $form['link'];
            $this->Data['entry']->image_url = $form['image_url'];
            $this->Data['entry']->panel_text = $form['panel_text'];
        }
        $this->assign('ActionForm', $this->Data['entry']->toArray());

        return 'user/brandco/admin-top/edit_instagram_panel_form.php';
    }
}