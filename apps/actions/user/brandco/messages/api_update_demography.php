<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.ExecuteActionBase');
AAFW::import('jp.aainc.classes.brandco.auth.trait.BrandcoAuthTrait');
AAFW::import('jp.aainc.classes.validator.user.PreJoinActionValidator');
AAFW::import('jp.aainc.classes.core.UserManager');
AAFW::import('jp.aainc.classes.core.UserAttributeManager');
AAFW::import('jp.aainc.classes.core.ShippingAddressManager');
AAFW::import('jp.aainc.classes.CpInfoContainer');
AAFW::import('jp.aainc.classes.services.monipla.MoniplaLotteryService');

class api_update_demography extends ExecuteActionBase {

    use BrandcoAuthTrait;

    protected $ValidatorDefinition = array();
    protected $ContainerName = 'api_update_demography';
    protected $AllowContent = array('JSON');

    public $NeedOption = array();

    private $cp;
    private $cp_user;
    private $user;
    private $user_info;

    public $userManager;
    public $userAttributeManager;
    public $shippingAddressManager;

    private $cp_user_service;

    public function doThisFirst() {
        $page_settings_service = $this->createService('BrandPageSettingService');
        $this->Data['pageSettings'] = $page_settings_service->getPageSettingsByBrandId($this->getBrand()->id);

        $this->cp_user_service = $this->getService('CpUserService');
        $this->cp_user = $this->cp_user_service->getCpUserById($this->cp_user_id);

        $this->cp = CpInfoContainer::getInstance()->getCpById($this->cp_user->cp_id);

        $user_service = $this->getService('UserService');
        $this->user = $user_service->getUserByBrandcoUserId($this->cp_user->user_id);

        $brandco_auth_service = $this->getService('BrandcoAuthService');
        $this->user_info = $brandco_auth_service->getUserInfoByQuery($this->user->monipla_user_id);

        $this->userManager = new UserManager($this->user_info, $this->getMoniplaCore());

        $this->setValidatorDefinition();
        $this->updateValidatorDefinitionByCp();
    }

    //ValidatorDefinitionをチェックして、エラーが発生したらgetFormURLを呼ぶ
    public function getFormURL () {
        $errors = array();
        foreach ($this->Validator->getError() as $key => $value) {
            $errors[$key] = $this->Validator->getMessage($key);
        }
        $json_data = $this->createAjaxResponse("ng", array(), $errors);
        $this->assign('json_data', $json_data);

        return false;
    }

    public function validate() {
        return true;
    }

    public function doAction() {
        $illegalDemography = false;
        $cp_user_store = aafwEntityStoreFactory::create('CpUsers');

        try {
            $cp_user_store->begin();

            if ($this->user_info->result->status != Thrift_APIStatus::SUCCESS) {
                throw new Exception('Invalid User');
            }

            $pre_join_validator = new PreJoinActionValidator($this->cp_user_id, $this->cp_action_id);
            $pre_join_validator->validate();

            if (!$pre_join_validator->isValid()) {
                $exception = new DemographyException();
                $exception->setParams($pre_join_validator->getErrors());
                throw $exception;
            }

            if (!$this->validateData()) {
                $exception = new DemographyException();
                $exception->setParams($this->Validator->getErrors());
                throw $exception;
            }

            if ($this->cp->restricted_age_flg && $this->getUserAge($this->birthDay) < $this->cp->restricted_age) {
                $this->Validator->setError('restrictedAge', 'auth.restricted_age_not_match');
            }

            if ($this->cp->restricted_gender_flg && $this->getUserGender($this->sex) != $this->cp->restricted_gender) {
                $this->Validator->setError('restrictedSex', 'auth.restricted_gender_not_match');
            }

            if ($this->cp->restricted_address_flg) {
                $cp_restricted_address_service = $this->getService('CpRestrictedAddressService');

                if (!in_array($this->prefId, $cp_restricted_address_service->getCpRestrictedAddressIds($this->cp->id))) {
                    $this->Validator->setError('restrictedAddress', 'auth.restricted_address_not_match');
                }
            }

            $this->userAttributeManager = new UserAttributeManager($this->user_info, $this->getMoniplaCore(), $this->Data['pageSettings']);
            $this->shippingAddressManager = new ShippingAddressManager($this->user_info, $this->getMoniplaCore());

            if ($this->Validator->isValid()) {
                $this->cp_user->demography_flg = CpUser::DEMOGRAPHY_STATUS_COMPLETE;
                $cur_status = CpUserActionStatus::JOIN;
            } else {
                $this->cp_user->demography_flg = CpUser::DEMOGRAPHY_STATUS_NOT_MATCH;
                $cur_status = CpUserActionStatus::CAN_NOT_JOIN;
            }

            $this->cp_user_service->updateCpUser($this->cp_user);
            $this->cp_user_service->joinAction($this->cp_user_id, $this->cp_action_id, $this->SERVER['HTTP_USER_AGENT'], Util::isSmartPhone(), $cur_status);

            $this->updatePersonalInfo($this->Data['pageSettings'], $this);
            $this->updatePersonalInfoByCp($this->Data['pageSettings'], $this);

            $brand_user_relation_service = $this->createService('BrandsUsersRelationService');
            $brands_users_relation = $this->getBrandsUsersRelation();

            if ($brands_users_relation->personal_info_flg != BrandsUsersRelation::SIGNUP_WITH_INFO) {
                aafwLog4phpLogger::getDefaultLogger()->warn('SIGNUP_WITHOUT_INFO brands_users_relation_id = ' . $brands_users_relation->id);
                aafwLog4phpLogger::getDefaultLogger()->warn(json_encode(debug_backtrace(), JSON_PRETTY_PRINT));
            }

            $brands_users_relation->personal_info_flg = BrandsUsersRelation::SIGNUP_WITH_INFO;
            $brand_user_relation_service->createBrandsUsersRelation($brands_users_relation);

            $this->createProfileQuestionAnswers($brands_users_relation);

            if (!$this->Validator->isValid()) {
                $exception = new DemographyException();
                $errors = $this->Validator->getError();
                $illegalDemography = true;

                $exception->setParams($errors);
                throw $exception;
            }

            $cp_flow_service = $this->getService('CpFlowService');
            $next_action = $cp_flow_service->getCpNextActionByCpActionId($this->cp_action_id);

            if (!$next_action->id) {

                $data = array('next_action' => false, 'sns_action' => false);
                $json_data = $this->createAjaxResponse("ok", $data);
            } else {
                $cp_action = CpInfoContainer::getInstance()->getCpActionById($next_action->cp_next_action_id);
                $manager = $cp_action->getActionManagerClass();
                $concrete_action = $manager->getConcreteAction($cp_action);

                list($message, $action_status) = $this->cp_user_service->sendActionMessage($this->cp_user_id, $cp_action, $concrete_action, true);

                $message_info = array(
                    "cp_action" => $cp_action,
                    "concrete_action" => $concrete_action,
                    "message" => $message,
                    "action_status" => $action_status
                );

                $cp_status = RequestuserInfoContainer::getInstance()->getStatusByCp($this->cp);
                $cp_info = $cp_flow_service->getCampaignInfo($this->cp, $this->brand, null, $cp_status);

                // HTMLを作成
                $parser = new PHPParser();
                $html = $parser->parseTemplate(
                    'CpMessageAction.php',
                    array(
                        'cp_user' => $this->cp_user,
                        'message_info' => $message_info,
                        'pageStatus' => $this->Data['pageStatus'],
                        'cp_info'=>$cp_info
                    )
                );

                $data = array(
                    'next_action' => true,
                    'sns_action'  => in_array($cp_action->type, $this->snsPermitStatuses),
                    'message_id'  => $message->id
                );

                $json_data = $this->createAjaxResponse("ok", $data, array(), $html);

                // 宝くじ
                $this->getLotteryCode($this->user);

                // メール送信
                $this->sendEntryMail($this->cp_user->user_id, $this->cp_user->cp_id);
            }

            $cp_user_store->commit();
        } catch (DemographyException $e) {
            $data = array();
            $errors = array();

            if ($illegalDemography) {
                $data['illegalDemography'] = 'true';
                foreach ($e->getParams() as $key => $value) {
                    if ($key == 'restrictedAge') {
                        $errors[$key] = sprintf($this->Validator->getMessage($key), $this->cp->restricted_age);
                    }

                    if ($key == 'restrictedSex') {
                        $errors[$key] = sprintf($this->Validator->getMessage($key), Cp::$cp_restricted_gender[$this->cp->restricted_gender]);
                    }

                    if ($key == 'restrictedAddress') {
                        $errors[$key] = sprintf($this->Validator->getMessage($key), $cp_restricted_address_service->getCpRestrictedAddressesString($this->cp->id));
                    }
                }
                $cp_user_store->commit();
            } else {
                foreach ($e->getParams() as $key => $value) {
                    $errors[$key] = $this->Validator->getMessage($key);
                }
                $cp_user_store->rollback();
            }
            $json_data = $this->createAjaxResponse('ng', $data, $errors);
        } catch (Exception $e) {
            $json_data = $this->createAjaxResponse('ng');
            $cp_user_store->rollback();
        }

        $this->assign('json_data', $json_data);
        return 'dummy.php';
    }

    public function updateValidatorDefinitionByCp() {
        if (!$this->Data['pageSettings']->privacy_required_birthday && $this->cp->restricted_age_flg) {
            $this->ValidatorDefinition['birthDay']['required'] = true;
            $this->birthDay = $this->birthDay_y . '-' . $this->birthDay_m . '-' . $this->birthDay_d;
        }

        if (!$this->Data['pageSettings']->privacy_required_sex && $this->cp->restricted_gender_flg) {
            $this->ValidatorDefinition['sex']['required'] = true;
        }
    }

    public function updatePersonalInfoByCp($config, $object) {
        if(!$config->privacy_required_sex && $this->cp->restricted_gender_flg) {
            $object->userAttributeManager->setSex($object->sex);
        }

        if (!$config->privacy_required_birthday && $this->cp->restricted_age_flg) {
            $object->userAttributeManager->setBirthday($object->birthDay_y, $object->birthDay_m, $object->birthDay_d);
        }

        if(!$config->privacy_required_address && $this->cp->restricted_address_flg) {
            $object->shippingAddressManager->setAddress($object);
        }

    }

    /**
     * @param $user
     */
    protected function getLotteryCode($user) {
        if (!$this->cp->isDemo()) {
            /** @var MoniplaLotteryService $monipla_lottery_service */
            $monipla_lottery_service = $this->getService('MoniplaLotteryService');
            $monipla_lottery_service->getCode($user);
        }
    }

    /**
     * @param $user_id
     * @param $cp_id
     */
    protected function sendEntryMail($user_id, $cp_id) {
        /** @var UserMailService $user_mail_service */
        $user_mail_service = $this->getService('UserMailService');

        $user_mail_service->sendEntryMail($user_id, $cp_id);
    }

    public function saveData() {}
}

class DemographyException extends Exception {
    private $params;

    public function setParams($params) {
        $this->params = $params;
    }

    public function getParams() {
        return $this->params;
    }
}