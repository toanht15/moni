<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');
AAFW::import('jp.aainc.classes.brandco.auth.trait.BrandcoAuthTrait');
AAFW::import('jp.aainc.classes.services.monipla.OldMoniplaUserOptinService');

class api_update_user_optin extends BrandcoPOSTActionBase {
    use BrandcoAuthTrait;

    protected   $ContainerName  = 'api_update_user_optin';
    public      $NeedOption     = array();
    protected   $AllowContent   = array('JSON');
    public      $CsrfProtect    = true;

    protected $ValidatorDefinition = array(
        'cp_id' => array(
            'type' => 'num',
            'required' => true,
        ),
        'new_optin_flg' => array(
            'type' => 'num',
            'range' => array(
                '<=' => 1,
                '>=' => 0
            ),
            'required' => true,
        ),
    );

    public function validate() {
        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = $this->createService('CpFlowService');
        $cp = $cp_flow_service->getCpById($this->POST['cp_id']);
        if (!$cp) return false;

        return true;
    }

    public function doAction() {

        if (!$this->Data['pageStatus']['userInfo']->id) {
            $json_data = $this->createAjaxResponse("ng");
        } else {
            //optinフラグの更新
            $res = $this->getOldMoniplaUserOptinService()->update($this->Data['pageStatus']['userInfo']->id, $this->POST['new_optin_flg'], OldMoniplaUserOptinService::FROM_ID_JOINED_CP, $this->POST['cp_id']);
            if ($res->data->status == 'success') {
                $json_data = $this->createAjaxResponse("ok");
            } else {
                $json_data = $this->createAjaxResponse("ng");
            }
        }
        $this->assign('json_data', $json_data);
    }
}
