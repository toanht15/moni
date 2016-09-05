<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');

class edit_photo_entry_form extends BrandcoGETActionBase {
    protected $ContainerName = 'edit_photo_entry';
    public $NeedAdminLogin = true;
    public $NeedOption = array();

    public function beforeValidate() {
        $this->Data['entry_id'] = $this->GET['exts'][0];
        $this->Data['brand'] = $this->getBrand();
    }

    public function validate() {
        if ($this->Data['entryId'] != 0) {
            $idValidator = new StreamValidator(BrandcoValidatorBase::SERVICE_NAME_PHOTO, $this->Data['brand']->id);

            if (!$idValidator->isCorrectEntryId($this->Data['entryId'])) {
                return false;
            }
        }

        return true;
    }

    function doAction() {
        $photo_stream_servivce = $this->createService('PhotoStreamService');
        $photo_entry = $photo_stream_servivce->getEntryById($this->Data['entry_id']);

        if (!$photo_entry) {
            return '403';
        }

        $this->Data['entry'] = $photo_entry;
        $this->Data['photo_user'] = $photo_entry->getPhotoUser();

        $cp_user = $photo_entry->getPhotoUser()->getCpUser();
        $this->Data['user'] = $cp_user->getUser();
        $this->Data['cp'] = $cp_user->getCp();

        return 'user/brandco/admin-top/edit_photo_entry_form.php';
    }
}