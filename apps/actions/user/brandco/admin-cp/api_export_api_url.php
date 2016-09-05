<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');

class api_export_api_url extends BrandcoPOSTActionBase {
    protected $ContainerName = 'photo_campaign';
    protected $AllowContent = 'JSON';

    public $CsrfProtect = true;
    public $NeedOption = array();
    public $NeedAdminLogin = true;

    public function validate() {
        if (!$this->POST['cp_id'] || !$this->POST['cp_action_type']) {
            return false;
        }
        return true;
    }

    public function doAction() {
        /** @var ContentApiCodeService $api_code_service */
        $api_code_service = $this->getService('ContentApiCodeService');
        $code_prefix = 'bc-' . $this->getBrand()->id . '-';

        try {
            if (!$this->isLoginManager()) {
                throw new Exception('Access denied');
            }

            if ($this->POST['cp_action_type'] == CpAction::TYPE_QUESTIONNAIRE) {
                $api_code = $api_code_service->getApiCodeByCpActionId($this->POST['cp_action_id']);

                if ($api_code) {
                    $api_code->extra_data = json_encode($this->POST['export_question_ids']);
                    $api_code_service->updateApiCode($api_code);

                    $json_data = $this->createAjaxResponse('ok', array('api_url' => $api_code_service->getApiUrl($api_code->code, $this->POST['cp_action_type'])));
                    $this->assign('json_data', $json_data);
                    return 'dummy.php';
                }
            }

            $code = $api_code_service->generateCode($code_prefix);
            $tmp_api_code = $api_code_service->getApiCodeByCode($code);

            if ($tmp_api_code) {
                throw new Exception('This code is already registered');
            }

            $api_code = $api_code_service->createEmptyApiCode();
            $api_code->cp_id = $this->POST['cp_id'];
            $api_code->code = $code;
            $api_code->cp_action_type = $this->POST['cp_action_type'];
            $api_code->cp_action_id = $this->POST['cp_action_id'] ?: 0;

            if ($this->POST['export_question_ids']) {
                $api_code->extra_data = json_encode($this->POST['export_question_ids']);
            }

            $api_code_service->createApiCode($api_code);

            $json_data = $this->createAjaxResponse('ok', array('api_url' => $api_code_service->getApiUrl($api_code->code, $this->POST['cp_action_type'])));
        } catch (Exception $e) {
            $json_data = $this->createAjaxResponse('ng', array(), $e->getMessage());
        }

        $this->assign('json_data', $json_data);
        return 'dummy.php';
    }
}