<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');
AAFW::import('jp.aainc.classes.validator.CpActionDeadLineValidator');

abstract class SaveActionBase extends BrandcoPOSTActionBase {

    public $NeedOption = array();
    public $NeedAdminLogin = true;
    public $CsrfProtect = true;

    protected $cp_action_manager;
    protected $concrete_action;
    protected $cp_action;
    protected $cp;

    protected $ValidatorDefinition;
    protected $deadLineValidator;

    /**
     * @return mixed
     */
    protected function getCpAction() {
        if (!$this->cp_action) {
            $cp_actions = $this->getActionManager()->getCpActions($this->POST['action_id']);

            $this->cp_action = $cp_actions[0];
            $this->concrete_action = $cp_actions[1];
        }

        return $this->cp_action;
    }

    /**
     * @return mixed
     */
    protected function getConcreteAction() {
        if (!$this->concrete_action) {
            $cp_actions = $this->getActionManager()->getCpActions($this->POST['action_id']);

            $this->cp_action = $cp_actions[0];
            $this->concrete_action = $cp_actions[1];
        }

        return $this->concrete_action;
    }

    /**
     * @return mixed
     */
    protected function getCp() {
        if (!$this->cp) {
            $cp_flow_service = $this->getService('CpFlowService');

            $this->cp = $cp_flow_service->getCpByCpAction($this->getCpAction());
        }

        return $this->cp;
    }

    /**
     * @return mixed
     */
    protected function getActionManager() {
        if (!$this->cp_action_manager) {
            $this->setActionManager();
        }

        return $this->cp_action_manager;
    }

    /**
     * @return bool
     */
    protected function isPermanent() {
        return $this->getCp()->isPermanent();
    }

    /**
     * @return bool
     */
    protected function isCpActionDeadLineAvailable() {
        return !$this->isPermanent() && !$this->getCpAction()->isOpeningCpAction();
    }

    /**
     * 常設キャンペーン以外締切日のバリデーション規則の取得
     */
    protected function fetchDeadLineValidator() {
        if ($this->isCpActionDeadLineAvailable()) {
            $this->deadLineValidator = new CpActionDeadLineValidator(
                $this->POST['end_type'],
                $this->POST['end_date'],
                $this->POST['end_hh'],
                $this->POST['end_mm'],
                $this->isLoginManager()
            );
            $this->ValidatorDefinition = array_merge(
                $this->ValidatorDefinition,
                $this->deadLineValidator->getValidationColumnAndRule()
            );
        }
    }

    /**
     * 常設キャンペーン以外締切日のバリデーションを実行する
     * @return bool
     */
    protected function validateDeadLine() {
        if ($this->isCpActionDeadLineAvailable()) {
            if (!$this->deadLineValidator->validate()) {
                foreach ($this->deadLineValidator->getErrors() as $key => $value) {
                    $this->Validator->setError($key, $value);
                }
                return false;
            }
        }
        return true;
    }

    /**
     * 締切日の設定
     */
    protected function renewDeadLineData() {
        if ($this->isCpActionDeadLineAvailable()) {
            // 締め切り日の設定
            $this->getCpAction()->end_type = $this->POST['end_type'];
            if ($this->POST['end_type'] == CpAction::END_TYPE_ORIGINAL) {
                $this->getCpAction()->end_at =
                    $this->POST['end_date'] . ' ' .
                    $this->POST['end_hh'] . ':' . $this->POST['end_mm'] . ':00';
            } else {
                $this->getCpAction()->end_at = '0000-00-00 00:00:00';
            }
        }
    }

    /**
     * @param $file_path
     * @param $file_info
     * @param $cp_action_name
     * @return mixed|null
     */
    protected function saveUploadedImage($file_path, $file_info, $cp_action_name) {
        $brand_id = $this->getBrand()->id;
        $upload_file_transaction = $this->getModel('UploadFiles');

        try {
            $upload_file_transaction->begin();

            $storage_client = StorageClient::getInstance();
            $upload_file_service = $this->createService('UploadFileService');

            $object_key = StorageClient::toHash('brand/' . $brand_id . '/upload_file/'. $cp_action_name. '/'. StorageClient::getUniqueId() . '/');
            $image_url = $storage_client->putObject($object_key . $file_info['name'], $file_info, StorageClient::ACL_PUBLIC_READ, false);

            $file_data = array();
            if ($file_info['file_type'] == FileValidator::FILE_TYPE_IMAGE) {
                $file_data = ImageCompositor::thumbnailImage($file_path);

                if ($file_data != false) {
                    $thumbnail_file_info = pathinfo($file_info['name']);
                    $file_data['thumbnail_url'] = urldecode($storage_client->putObject($object_key . $thumbnail_file_info['filename'] . '_t.' . $thumbnail_file_info['extension'], $file_info, StorageClient::ACL_PUBLIC_READ, false));
                }
            }

            $user_service = $this->createService('UserService');
            $user = $user_service->getUserByMoniplaUserId($this->Data['pageStatus']['userInfo']->id);

            $file = $upload_file_service->createEmptyUploadFile();
            $file->user_id = $user->id;
            $file->name = $file_info['name'];
            $file->type = $file_info['file_type'];
            $file->size = $file_info['size'];
            $file->url = urldecode($image_url);
            $file->extra_data = json_encode($file_data);
            $file->hidden_flg = 0;
            $upload_file = $upload_file_service->createUploadFile($file);

            $upload_file_transaction->commit();
        } catch(Exception $e){
            $upload_file_transaction->rollback();
            return null;
        }

        $brand_upload_file_service = $this->createService('BrandUploadFileService');
        $brand_upload_file = $brand_upload_file_service->createEmptyBrandUploadFile();
        $brand_upload_file->brand_id = $brand_id;
        $brand_upload_file->file_id = $upload_file->id;
        $brand_upload_file->pub_date = date('Y-m-d H:i:s');
        $brand_upload_file_service->createBrandUploadFile($brand_upload_file);

        return $image_url;
    }

    abstract protected function setActionManager();
}