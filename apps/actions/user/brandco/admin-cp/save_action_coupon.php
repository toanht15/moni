<?php
AAFW::import('jp.aainc.classes.brandco.cp.SaveActionBase');
AAFW::import('jp.aainc.classes.brandco.cp.CpEntryActionManager');

class save_action_coupon extends SaveActionBase {
    protected $ContainerName = 'save_action_coupon';
    protected $Form = array(
        'package' => 'admin-cp',
        'action' => '{path}?mid=action-not-filled',
    );

    private $file_info = array();

    protected $ValidatorDefinition = array(
        'title' => array(
            'type' => 'str',
            'length' => 50,
        ),
        'image_url' => array(
            'type' => 'str',
            'length' => 512,
            'validator' => array('URL')
        ),
        'image_file' => array(
            'type' => 'file',
            'size' => '5MB'
        ),
        'text' => array(
            'type' => 'str',
            'length' => CpValidator::MAX_TEXT_LENGTH
        ),
        'coupon_id' => array(
            'type' => 'num'
        )
    );

    public function doThisFirst() {
        if ($this->POST['save_type'] == CpAction::STATUS_FIX) {
            $this->ValidatorDefinition['text']['required'] = true;
            $this->ValidatorDefinition['coupon_id']['required'] = true;
            $this->ValidatorDefinition['moduleImage']['required'] = true;
            $this->ValidatorDefinition['title']['required'] = true;
        }

        $this->fetchDeadLineValidator();
    }

    public function validate() {
        if ($this->POST['save_type'] == CpAction::STATUS_FIX && $this->POST['coupon_id'] == 0) {
            $this->Validator->setError('coupon_id', 'NOT_CHOOSE');
            return false;
        }

        $brand = $this->getBrand();
        $validatorService = new CpValidator($brand->id);
        if (!$validatorService->isOwnerOfAction($this->POST['action_id'])) {
            $this->Validator->setError('auth', 'NOT_OWNER');
            return false;
        }

        if($this->FILES['image_file']){
            $fileValidator = new FileValidator($this->FILES['image_file'],FileValidator::FILE_TYPE_IMAGE);
            if(!$fileValidator->isValidFile()){
                $this->Validator->setError('image_file', 'NOT_MATCHES');
                return false;
            }else{
                $this->file_info = $fileValidator->getFileInfo();
            }
        }

        if (!$this->validateDeadLine()) return false;

        if ($this->POST['coupon_id']) {
            /** @var CouponService $coupon_service */
            $coupon_service = $this->createService('CouponService');
            $coupon = $coupon_service->getCouponById($this->POST['coupon_id']);
            if (!$coupon || $coupon->brand_id != $this->brand->id) {
                return '404';
            }
        }

        return true;
    }

    function doAction() {

        $data = array();
        if($this->FILES['image_file']){
            // メインバナー画像 保存
            $data['image_url'] = $this->saveUploadedImage($this->FILES['image_file']['name'], $this->file_info, "cp_action_coupon");
        } else {
            $data['image_url'] = $this->POST['image_url'];
        }

        $data['text'] = $this->POST['text'];
        $data['title'] = $this->POST['title'];
        $data['coupon_id'] = $this->POST['coupon_id'];

        $data['button_label_text'] = $this->POST['button_label_text'];

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
        $this->cp_action_manager = $this->getService('CpCouponActionManager');
    }
}
