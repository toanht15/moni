<?php
AAFW::import('jp.aainc.aafw.base.aafwActionBase');
AAFW::import('jp.aainc.classes.services.base.BrandcoActionBaseService');

class withdraw extends aafwActionBase implements BrandcoActionBaseInterface{

    protected $AllowContent = array('JSON');

    /** @var Logger $logger */
    public $logger;

    use BrandcoActionBaseService;

    public function doThisFirst() {
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
        $this->logger->info("withdraw_api ".$this->request);
    }

    public function validate () {
        if (!$this->app_id) {
            $json_data = $this->createAjaxResponse('ng');
            $error = 'app_id is required!';
            $this->assign('json_data', $json_data, $error);
            return 'dummy.php';
        }
        return true;
    }

    function doService() {
        $hipchat_logger = aafwLog4phpLogger::getHipChatLogger();
        try {
            $signedRequest = $this->getMoniplaCore()->resolveSignedRequest($this->request, config('platform.client_secret'));
            if ($signedRequest) {
                /** @var UserService $user_service */
                $user_service = $this->getService('UserService');
                $user = $user_service->getUserByMoniplaUserId($signedRequest->id);
                if ($user) {
                    /** @var BrandsUsersRelationService $brand_relation_service */
                    $brand_relation_service = $this->getService('BrandsUsersRelationService');
                    $relations = $brand_relation_service->getAllRelationsByUserId($user->id);
                    $brand_service = $this->createService('BrandService');
                    foreach ($relations as $relation) {
                        $brand = $brand_service->getBrandById($relation->brand_id);
                        if ($this->app_id == $brand->app_id) {
                            $brand_relation_service->withdrawByBrandUserRelation($relation, true);
                        }
                    }


                    $this->logger->info('withdraw_api ユーザー' . $user->id . 'が ' . ApplicationService::$ApplicationMaster[$this->app_id]['name'] . 'から退会しました。');
                }
            } else {
                $err_msg = 'The signedRequest could not be resolved: request=' . $this->request;
                $this->logger->error($err_msg);
                $hipchat_logger->error($err_msg);
                $json_data = $this->createAjaxResponse('ng');
                $this->assign('json_data', $json_data);
                return 'dummy.php';
            }

        } catch(Exception $e) {
            $err_msg = 'withdrawFail-'.$e->getMessage() . ", request=" . $this->request;
            $this->logger->error($err_msg, $e);
            $hipchat_logger->error($err_msg, $e);

            $json_data = $this->createAjaxResponse('ng');
            $this->assign('json_data', $json_data);
            return 'dummy.php';
        }
        $json_data = $this->createAjaxResponse('ok');
        $this->assign('json_data', $json_data);
        return 'dummy.php';
    }

}