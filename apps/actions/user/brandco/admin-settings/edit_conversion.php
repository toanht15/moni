<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');

class edit_conversion extends BrandcoPOSTActionBase {
    protected $ContainerName = 'edit_conversion_form';
    protected $Form = array(
        'package' => 'admin-settings',
        'action' => 'edit_conversion_form/{conversion_id}',
    );

    public $NeedOption = array();
    public $NeedAdminLogin = true;
    public $CsrfProtect = true;
    /** @var ConversionService $conversion_service */
    public $conversion_service;

    protected $ValidatorDefinition = array(
        'name' => array(
            'required' => true,
            'type' => 'str',
            'length' => 50,
        ),
        'conversion_id' => array(
            'required' => true,
            'type' => 'num'
        )
    );

    public function validate () {
        $this->conversion_service = $this->createService('ConversionService');
        if ($this->POST['conversion_id'] !== '0') {
            $conversion = $this->conversion_service->getConversionById($this->POST['conversion_id']);
            if (!$conversion || $conversion->brand_id !== $this->brand->id) {
                return false;
            }
        }
        if ($conversion_by_name = $this->conversion_service->getConversionByNameAndBrandId($this->POST['name'], $this->brand->id)) {
            if (($conversion && $conversion->id !== $conversion_by_name->id) || !$conversion) {
                $this->Validator->setError('name', 'EXISTED_CONVERSION');
                return false;
            }
        }

        return true;
    }

    function doAction() {

        if ($this->POST['conversion_id'] === '0') {
            $conversion = $this->conversion_service->createConversion($this->brand->id, $this->POST);
        } else {
            $conversion = $this->conversion_service->updateConversion($this->POST['conversion_id'], $this->POST);
        }
        $this->Data['saved'] = 1;
        return 'redirect: '.Util::rewriteUrl('admin-settings', 'edit_conversion_form', array($conversion->id), array('mid' => 'updated'));
    }
}
