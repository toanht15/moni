<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');

class edit_redirector extends BrandcoPOSTActionBase {
    protected $ContainerName = 'edit_redirector_form';
    protected $Form = array(
        'package' => 'admin-settings',
        'action' => 'edit_redirector_form/{redirector_id}',
    );

    public $NeedAdminLogin = true;
    public $CsrfProtect = true;
    /** @var RedirectorService $redirector_service */
    public $redirector_service;

    protected $ValidatorDefinition = array();

    public function doThisFirst() {
        if ($this->POST['del_flg'] != '1') {
            $this->ValidatorDefinition = array(
                'sign' => array(
                    'required' => true,
                    'regex' => '/^[a-zA-Z0-9][\-\_a-zA-Z0-9]*[a-zA-Z0-9]$/',
                    'length' => 15,
                ),
                'url' => array(
                    'required' => true,
                    'validator' => array('URL'),
                    'length' => 255,
                ),
                'redirector_id' => array(
                    'required' => true,
                    'type' => 'num',
                )
            );
        }
    }

    public function validate () {
        $this->redirector_service = $this->createService('RedirectorService');
        if ($this->POST['redirector_id'] !== '0') {
            $redirector = $this->redirector_service->getRedirectorById($this->POST['redirector_id']);
            if (!$redirector || $redirector->brand_id !== $this->brand->id) {
                return false;
            }
        }
        if ($redirector = $this->redirector_service->getRedirectorBySignAndBrandId($this->POST['sign'], $this->brand->id)) {
            if ($redirector->id !== $this->POST['redirector_id']) {
                $this->Validator->setError('sign', 'EXISTED_REDIRECTOR');
                return false;
            }
        }

        return true;
    }

    function doAction() {
        if ($this->POST['redirector_id'] !== '0' && $this->POST['del_flg'] === '1') {
            $this->redirector_service->deleteRedirector($this->POST['redirector_id']);

            return 'redirect: ' . Util::rewriteUrl('admin-settings', 'redirector_settings_form');
        } else if ($this->POST['redirector_id'] === '0' && $this->POST['del_flg'] === '1') {

            return 'redirect: ' . Util::rewriteUrl('admin-settings', 'redirector_settings_form');
        } else {
            if ($this->POST['redirector_id'] === '0') {
                $redirector = $this->redirector_service->createRedirector($this->brand->id, $this->POST);
            } else {
                $redirector = $this->redirector_service->updateRedirector($this->POST['redirector_id'], $this->POST);
            }
            $this->Data['saved'] = 1;

            return 'redirect: ' . Util::rewriteUrl('admin-settings', 'edit_redirector_form', array($redirector->id), array('mid' => 'updated'));
        }
    }
}
