<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoManagerPOSTActionBase');

class edit_manager extends BrandcoManagerPOSTActionBase {

    protected $ContainerName = 'edit_manager';
    protected $Form = array(
        'package' => 'dashboard',
        'action' => 'edit_manager_form/{id}',
    );

    public $NeedManagerLogin = true;

    protected $ValidatorDefinition = array(
        'username' => array(
            'required' => 1,
            'type' => 'str',
            'length' => 255,
        )
    );

    public function validate() {
        return !$this->Validator->getErrorCount();
    }

    function doAction() {
        try {
            $manager_service = $this->createService('ManagerService');
            $manager = $manager_service->getManagerById($this->POST['id']);
            $manager_service->changeManagerName($manager, $this->POST['username']);

            $this->Data['saved'] = 1;

            return 'redirect: ' . Util::rewriteUrl('dashboard', 'edit_manager_form', array($this->POST['id']),
                array('mode' => ManagerService::ADD_FINISH), '', true);
        } catch (DBException $e) {
            $logger = aafwLog4phpLogger::getDefaultLogger();
            $logger->error($e->getMessage());
            return 'redirect: ' . Util::rewriteUrl('dashboard', 'edit_manager_form', array($this->POST['id']),
                array('mode' => ManagerService::ADD_ERROR), '', true);
        }
    }
}
