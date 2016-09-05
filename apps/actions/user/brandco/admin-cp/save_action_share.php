<?php
AAFW::import('jp.aainc.classes.brandco.cp.SaveActionBase');
AAFW::import('jp.aainc.classes.util.MetaDataParser');
AAFW::import('jp.aainc.classes.services.CpShareActionService');

class save_action_share extends SaveActionBase {

    protected $ContainerName = 'save_action_share';
    protected $Form = array(
        'package' => 'admin-cp',
        'action' => '{path}?mid=action-not-filled',
    );

    protected $brand = null;

    protected $ValidatorDefinition = array(
        'placeholder' => array(
            'type' => 'str',
            'length' => CpValidator::MAX_TWITTER_SHARE_TEXT
        )
    );

    public function doThisFirst () {
        if ($this->POST['save_type'] == CpAction::STATUS_FIX) {

            $this->ValidatorDefinition['placeholder']['required'] = true;

        }

        if($this->canShareExternalPage() && $this->POST['share_url_type'] == CpShareActionService::EXTERNAL_SHARE) {
            $this->ValidatorDefinition['share_url'] = array(
                'required' => true,
                'type' => 'str',
                'length' => 512,
                'validator' => array('URL')
            );
        }

        $this->fetchDeadLineValidator();
    }

    public function validate() {
        $this->brand = $this->getBrand();
        $validatorService = new CpValidator($this->brand->id);
        if (!$validatorService->isOwnerOfAction($this->POST['action_id'])) {
            $this->Validator->setError('auth', 'NOT_OWNER');
            return false;
        }

        if (!$this->validateDeadLine()) return false;

        return true;
    }

    function doAction() {

        $data = array();

        $data['placeholder'] = $this->POST['placeholder'];

        if($this->canShareExternalPage()) {

            if($this->POST['share_url_type'] == CpShareActionService::EXTERNAL_SHARE){

                $data['share_url'] = $this->POST['share_url'];

                $metaDataParser = new MetaDataParser();
                $htmlContent = $metaDataParser->getHtmlContent($data['share_url']);
                $metaTags = $metaDataParser->getMetaData($htmlContent);

                $data['meta_data'] = json_encode($metaTags);

            }else {

                $data['share_url'] = '';
                $data['meta_data'] = '';
            }
        }
        
        $this->getCpAction()->status = $this->POST['save_type'];
        $this->renewDeadLineData();

        $this->getActionManager()->updateCpActions($this->getCpAction(), $data);

        $this->Data['saved'] = 1;
        if ($this->POST['save_type'] == CpAction::STATUS_FIX) {
            $this->POST['callback'] = $this->POST['callback'].'?mid=action-saved';
        } else {
            $this->POST['callback'] = $this->POST['callback'].'?mid=action-draft';
        }
        return 'redirect: ' . $this->POST['callback'];
    }

    protected function setActionManager() {
        $this->cp_action_manager = $this->getService('CpShareActionManager');
    }
}
