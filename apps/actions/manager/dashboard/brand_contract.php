<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoManagerGETActionBase');
AAFW::import('jp.aainc.classes.text.TextTemplate');

class brand_contract extends BrandcoManagerGETActionBase {
    protected $ContainerName = 'brand_contract';

    public $NeedManagerLogin = true;

    private $brand_id;
    private $action_form = array();

    public function beforeValidate() {
        $this->resetValidateError();
        $this->resetResult();

        $this->brand_id = $this->GET['exts'][0];
        if (!$this->brand_id) return 'redirect:' . Util::rewriteUrl('','', array(), array(), '', true);
    }

    public function validate () {
        return true;
    }

    function doAction() {

        /** @var BrandService $brand_service */
        $brand_service = $this->createService('BrandService');
        $this->Data['brand'] = $brand_service->getBrandById($this->brand_id);
        if (!$this->Data['brand']) return '403';

        $this->Data['brand_contract'] = $this->Data['brand']->getBrandContract();

        $text_template = new TextTemplate();

        if (!$this->Data['brand_contract']->closed_title) {
            $this->Data['brand_contract']->closed_title = $text_template->loadContent('brand_contract_title', null, false);
        }

        if (!$this->Data['brand_contract']->closed_description) {
            $this->Data['brand_contract']->closed_description = $text_template->loadContent('brand_contract_body', null, false);
        }

        $this->action_form = $this->Data['brand_contract']->toArray();
        $this->formatDateTime();

        $this->assign('ActionForm', $this->action_form);

        return 'manager/dashboard/brand_contract.php';
    }

    private function formatDateTime() {
        $this->action_form['contract_end_date'] = date('Y/m/d', strtotime($this->Data['brand_contract']->contract_end_date));
        $this->action_form['contract_end_time_hh'] = date('H', strtotime($this->Data['brand_contract']->contract_end_date));
        $this->action_form['contract_end_time_mm'] = date('i', strtotime($this->Data['brand_contract']->contract_end_date));

        $this->action_form['display_end_date'] = date('Y/m/d', strtotime($this->Data['brand_contract']->display_end_date));
        $this->action_form['display_end_time_hh'] = date('H', strtotime($this->Data['brand_contract']->display_end_date));
        $this->action_form['display_end_time_mm'] = date('i', strtotime($this->Data['brand_contract']->display_end_date));
    }
}
