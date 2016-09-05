<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');

class api_reset_demo_data extends BrandcoPOSTActionBase
{
    protected $ContainerName = 'api_reset_demo_data';
    protected $AllowContent = array('JSON');

    public $NeedOption = array();
    public $NeedLogin = true;
    public $CsrfProtect = true;

    public function validate()
    {
        $brand = $this->getBrand();

        /** @var CpFlowService $service */
        $service = $this->createService('CpFlowService');
        $this->Data["cp"] = $service->getCpById($this->POST['cp_id']);

        if ($this->Data["cp"] && $this->Data["cp"]->status != Cp::STATUS_DEMO) {
            $json_data = $this->createAjaxResponse("ng");
            $this->assign('json_data', $json_data);

            return false;
        }

        $validatorService = new CpValidator($brand->id);
        if (!$validatorService->isOwner($this->POST['cp_id'])) {

            $json_data = $this->createAjaxResponse("ng");
            $this->assign('json_data', $json_data);

            return false;
        }
        return true;
    }

    function doAction()
    {
        /** @var CpFlowService $service */
        $service = $this->createService('CpFlowService');

        $model = $service->getCpStore();
        try {
            $model->begin();
            if ($this->reset_one_flg) {
                $brandco_user_relation = $this->getBrandsUsersRelation();
                /** @var CpUserService $cp_user_service */
                $cp_user_service = $this->getService("CpUserService");
                $cp_user = $cp_user_service->getCpUserByCpIdAndUserId($this->POST['cp_id'], $brandco_user_relation->user_id);
                if (!$cp_user) {
                    $json_data = $this->createAjaxResponse("ng", array(), array('message' => 'キャンペーンに参加していません。'));
                    $this->assign('json_data', $json_data);
                    return 'dummy.php';
                }
                $service->resetDemoUserDataByCpUser($cp_user);
            } else {
                $service->resetDemoUserDataByCpId($this->POST['cp_id']);
            }
            $model->commit();
        } catch (Exception $e) {
            $model->rollback();
            $logger = aafwLog4phpLogger::getDefaultLogger();
            $logger->error("api_reset_demo_data#doAction false cp_id=".$this->POST['cp_id']);
            $logger->error($e);

            $json_data = $this->createAjaxResponse("ng");
            $this->assign('json_data', $json_data);
            return 'dummy.php';
        }

        if ($this->Data["cp"]->join_limit_flg == Cp::JOIN_LIMIT_ON) {
            $json_data = $this->createAjaxResponse("ok", array("call_back_url" => Util::rewriteUrl("admin-cp", "public_cps", array(), array("mid" => "reset-demo-data", "type" => Cp::TYPE_CAMPAIGN))));
        } else {
            $json_data = $this->createAjaxResponse("ok");
        }

        $this->assign('json_data', $json_data);
        return 'dummy.php';
    }
}
