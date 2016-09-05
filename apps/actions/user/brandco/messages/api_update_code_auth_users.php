<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');
AAFW::import('jp.aainc.classes.CpInfoContainer');

class api_update_code_auth_users extends BrandcoPOSTActionBase {
    protected $ContainerName = 'api_update_code_auth_users';
    protected $AllowContent = 'JSON';

    public $NeedOption = array();
    public $NeedUserLogin = true;
    public $CsrfProtect = true;

    /** @var CpCodeAuthActionManager $cp_action_manager */
    private $cp_action_manager;
    /** @var CodeAuthenticationService $code_auth_service */
    private $code_auth_service;
    private $tracking_service;

    private $cur_code;

    public function doThisFirst() {
        $this->cp_action_manager = $this->createService('CpCodeAuthActionManager');
        $this->code_auth_service = $this->createService('CodeAuthenticationService');
        $this->tracking_service = $this->getService('CodeAuthUserTrackingService');
    }

    public function validate() {
        $validatorDefinition = array(
            'code_auth_code' => array(
                'required' => true,
                'type' => 'str',
                'length' => 255
            )
        );

        $validator = new aafwValidator($validatorDefinition);
        $validator->validate($this->POST);

        if ($validator->isValid()) {
            $codes = $this->code_auth_service->getCodeAuthCodeByCodeAndCodeAuthId($this->code_auth_code, $this->code_auth_id);

            if (!$codes) {
                $validator->setError('code_auth_code', 'NOT_EXIST_CODE');
            } else {
                foreach ($codes as $code) {
                    $this->cur_code = $code;
                    break;
                }

                /** @var CpUserService $cp_user_service */
                $cp_user_service = $this->createService('CpUserService');
                /** @var CpFlowService $cp_flow_service */
                $cp_flow_service = $this->createService('CpFlowService');

                $cp_action = CpInfoContainer::getInstance()->getCpActionById($this->cp_action_id);
                $cp_action_group = $cp_action->getCpActionGroup();
                $code_auth_action_ids = $cp_flow_service->getCpActionIdsByCpIdAndType($cp_action_group->cp_id, CpAction::TYPE_CODE_AUTHENTICATION);

                $code_auth_user = $this->cp_action_manager->getCodeAuthUser($this->cur_code->id, $this->user_id, $code_auth_action_ids);
                if ($code_auth_user) {
                    $validator->setError('code_auth_code', 'DUPLICATED_CODE');
                } elseif ($this->cur_code->max_num <= $this->cur_code->reserved_num) {
                    $validator->setError('code_auth_code', 'NOT_EXIST_CODE');
                } elseif ($this->cur_code->expire_date != '0000-00-00 00:00:00' && $this->isPast($this->cur_code->expire_date)) {
                    $validator->setError('code_auth_code', 'EXPIRED_CODE');
                }
            }
        }

        if (!$validator->isValid()) {
            $html = "";
            $errors = $validator->getErrors();
            $is_trackable = $this->tracking_service->isTrackingError($errors['code_auth_code']);

            if($validator->getError('code_auth_code')) {
                $errors['code_auth_code'] = $validator->getMessage('code_auth_code');
            }

            if ($is_trackable) {
                $track_log = $this->tracking_service->trackingUser($this->user_id, $this->cp_action_id);
                
                // Support for both customize n default errors
                $errors['code_auth_code'] .= '、3回入力を間違えると1時間のロックがかかります。';

                if ($this->tracking_service->isLockingUser($track_log)) {
                    $errors['code_auth_user_locking'] = 'true';
                    $html = aafwWidgets::getInstance()->loadWidget('MsgCodeAuthCodeList')->render(array('user_id' => $this->user_id, 'cp_user_id' => $this->cp_user_id, 'cp_action_id' => $this->cp_action_id));
                }
            }

            $json_data = $this->createAjaxResponse('ng', array(), $errors, $html);
            $this->assign('json_data', $json_data);
            return false;
        }

        return true;
    }

    public function doAction() {
        try {
            $this->cur_code->reserved_num += 1;
            $this->code_auth_service->updateCodeAuthCode($this->cur_code);

            $code_auth_user = $this->cp_action_manager->createEmptyCodeAuthUser();
            $code_auth_user->code_auth_code_id = $this->cur_code->id;
            $code_auth_user->user_id = $this->user_id;
            $code_auth_user->cp_action_id = $this->cp_action_id;
            $code_auth_user->used_flg = 1;
            $code_auth_user->used_date = date('Y-m-d H:i:s');
            $this->cp_action_manager->createCodeAuthUser($code_auth_user);

            $html = aafwWidgets::getInstance()->loadWidget('MsgCodeAuthCodeList')->render(array('user_id' => $this->user_id, 'cp_user_id' => $this->cp_user_id, 'cp_action_id' => $this->cp_action_id));

            $this->tracking_service->untrackUser($this->user_id, $this->cp_action_id);
            $json_data = $this->createAjaxResponse('ok', array(), array(), $html);
        } catch (Exception $e) {
            $json_data = $this->createAjaxResponse('ng');
        }

        $this->assign('json_data', $json_data);
        return 'dummy.php';
    }
}