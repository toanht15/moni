<?php
AAFW::import('jp.aainc.classes.brandco.cp.SaveActionBase');

class save_action_youtube_channel extends SaveActionBase {
    protected $ContainerName = 'save_action_youtube_channel';
    protected $Form = array(
        'package' => 'admin-cp',
        'action' => '{path}?mid=action-not-filled',
    );

    protected $ValidatorDefinition = array(
        'brand_social_account_id' => array(
            'type' => 'num',
            'range' => array(
                '>' => 0
            )
        )
    );

    public function doThisFirst() {
        if ($this->POST['save_type'] == CpAction::STATUS_FIX) {
            $this->ValidatorDefinition['brand_social_account_id']['required'] = true;
        }

        $this->fetchDeadLineValidator();
    }

    public function validate() {
        if ($this->POST['save_type'] == CpAction::STATUS_FIX) {
            if ($this->POST['intro_flg'] && !$this->POST['entry_'.$this->brand_social_account_id]) {
                $this->Validator->setError('entry', 'NOT_EXIST_CHOICE');
            }
        }

        $this->brand = $this->getBrand();
        $validatorService = new CpValidator($this->brand->id);
        if (!$validatorService->isOwnerOfAction($this->POST['action_id'])) {
            $this->Validator->setError('auth', 'NOT_OWNER');
        }

        if ($this->POST['entry_'.$this->brand_social_account_id]) {
            /** @var YoutubeStreamService $yt_stream_service */
            $yt_stream_service = $this->getService('YoutubeStreamService');
            $entry = $yt_stream_service->getEntryById(explode(',', $this->POST['entry_'.$this->brand_social_account_id])[0]);
            $brand_social_account = $entry->getBrandSocialAccount();
            if ($brand_social_account->brand_id !== $this->brand->id) {
                $this->Validator->setError('entry', 'NOT_OWNER');
            }
        }

        if (!$this->validateDeadLine()) return false;

        return !$this->Validator->getErrorCount();
    }

    function doAction() {

        $data = array();
        $data['intro_flg'] = $this->intro_flg ? : Cp::FLAG_HIDE_VALUE;
        $data['brand_social_account_id'] = $this->brand_social_account_id;
        list($youtube_entry_id, $object_id) = explode(',', $this->POST['entry_'.$this->brand_social_account_id]);
        $data['youtube_entry_id'] = $youtube_entry_id;

        $this->getCpAction()->status = $this->POST['save_type'];
        $this->renewDeadLineData();

        $this->getActionManager()->updateCpActions($this->getCpAction(), $data);

        $this->Data['saved'] = 1;

        if ($this->POST['save_type'] == CpAction::STATUS_FIX) {
        $this->POST['callback'] = $this->POST['callback'].'?mid=action-saved';
        } else {
        $this->POST['callback'] = $this->POST['callback'].'?mid=action-draft';
        }

        $return = 'redirect: ' . $this->POST['callback'];

        return $return;
    }

    protected function setActionManager() {
        $this->cp_action_manager = $this->getService('CpYoutubeChannelActionManager');
    }
}
