<?php
AAFW::import('jp.aainc.classes.core.UserAttributeManager');
AAFW::import('jp.aainc.classes.core.ShippingAddressManager');
AAFW::import('jp.aainc.classes.core.UserManager');
AAFW::import('jp.aainc.classes.services.CpQuestionnaireService');
AAFW::import('jp.aainc.classes.services.QuestionTypeService');
AAFW::import('jp.aainc.classes.brandco.auth.trait.BrandcoAuthTrait');
AAFW::import('jp.aainc.classes.brandco.toolkit.ExecuteActionBase');
AAFW::import('jp.aainc.classes.validator.user.PreJoinActionValidator');
AAFW::import('jp.aainc.classes.services.monipla.MoniplaLotteryService');

class api_update_personal_info_and_execute_entry extends ExecuteActionBase{

    use BrandcoAuthTrait;

    public $NeedOption = array();
    private $accessToken;
    private $refreshToken;
    private $userInfo;
    private $userManager;
    private $userTransaction;
    private $userAttributeManager;
    private $shippingAddressManager;
    private $cp_user;
    private $user;
    private $cp;

    protected $ContainerName = 'api_update_personal_info_and_execute_entry';

    protected $ValidatorDefinition = array();

    public function doThisFirst() {
        $this->userTransaction = aafwEntityStoreFactory::create('Users');

        /** @var BrandPageSettingService $page_settings_service */
        $page_settings_service = $this->createService('BrandPageSettingService');
        $this->Data['pageSettings'] = $page_settings_service->getPageSettingsByBrandId($this->getBrand()->id);

        /** @var CpUserService $cp_user_service */
        $cp_user_service = $this->createService('CpUserService');
        $this->cp_user = $cp_user_service->getCpUserById($this->cp_user_id);

        /** @var UserService $user_service */
        $user_service = $this->createService('UserService');
        $user = $this->user = $user_service->getUserByBrandcoUserId($this->cp_user->user_id);

        /** @var UserApplicationService $user_application_service */
        $user_application_service = $this->createService('UserApplicationService');
        $brandco_user_application = $user_application_service->getUserApplicationByUserIdAndAppId($this->cp_user->user_id, $this->getBrand()->app_id);

        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = $this->getService('CpFlowService');
        $this->cp = $cp_flow_service->getCpById($this->cp_user->cp_id);

        $this->accessToken = $brandco_user_application->access_token;
        $this->refreshToken = $brandco_user_application->refresh_token;
        $this->userInfo = $this->getUserInfoByMoniplaUserId($user->monipla_user_id);

        $monipla_core = $this->getMoniplaCore();
        $this->userManager = new UserManager($this->userInfo, $monipla_core);
        $this->userAttributeManager = new UserAttributeManager($this->userInfo, $monipla_core, $this->Data['pageSettings']);
        $this->shippingAddressManager = new ShippingAddressManager($this->userInfo, $monipla_core);

        $this->setValidatorDefinition();
    }

    //ValidatorDefinitionをチェックして、エラーが発生したらgetFormURLを呼ぶ
    public function getFormURL () {
        $errors = array();
        foreach ($this->Validator->getError() as $key => $value) {
            //使用規約のチェックボックスのエラー場合は、エラーメッセージをセットする
            if ($key == "agree_agreement") {
                if ($this->getBrand()->id == Brand::CLUB_LAVIE || $this->getBrand()->id == Brand::LAVIE_SPECIALFAN
                    || $this->getBrand()->id == Brand::CLUB_LENOVO || $this->getBrand()->id == Brand::LENOVO_SPECIALFAN) { // ハードコーディング
                    $errors[$key] = '次へ進むには' . $this->getBrand()->name . ' メンバー規約への同意にチェックを入れてください';
                } else {
                    $errors[$key] = '次へ進むには' . $this->getBrand()->name . ' 利用規約への同意にチェックを入れてください';
                }
                continue;
            }

            $errors[$key] = $this->Validator->getMessage($key);
        }
        $json_data = $this->createAjaxResponse("ng", array(), $errors);
        $this->assign('json_data', $json_data);

        return false;
    }

    public function validate() {

        $validator = new PreJoinActionValidator($this->cp_user_id, $this->cp_action_id, $this->cp_user);
        $validator->validate();

        if (!$validator->isValid()) {
            $errors = $validator->getErrors();
            $json_data = $this->createAjaxResponse("ng", array(), $errors);
            $this->assign('json_data', $json_data);

            return false;
        }

        return $this->validateData();
    }

    function saveData() {
        if ($this->userInfo->result->status != Thrift_APIStatus::SUCCESS) {
            throw new Exception('Invalid user');
        }

        /** @var BrandsUsersRelationService $brand_user_relation_service */
        $brand_user_relation_service = $this->createService('BrandsUsersRelationService');
        $brands_users_relation = $this->getBrandsUsersRelation();

        try{

            $this->updatePersonalInfo($this->Data['pageSettings'], $this);

            $brands_users_relation->personal_info_flg = BrandsUsersRelation::SIGNUP_WITH_INFO;
            $brand_user_relation_service->updatePersonalInfo($brands_users_relation->id, BrandsUsersRelation::SIGNUP_WITH_INFO);

        } catch(Exception $e) {
            aafwLog4phpLogger::getDefaultLogger()->error('Update Personal Info Error.' . $e);
        }

        $this->createProfileQuestionAnswers($brands_users_relation);

        // 宝くじ
        $this->getLotteryCode($this->user);

        // メール送信
        $this->sendEntryMail($brands_users_relation->user_id, $this->cp_user->cp_id);
    }

    /**
     * @param $user
     */
    protected function getLotteryCode($user) {
        if (!$user) {
            /** @var UserService $user_service */
            $user_service = $this->createService('UserService');
            $user = $user_service->getUserByBrandcoUserId($this->cp_user->user_id);
        }

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
}