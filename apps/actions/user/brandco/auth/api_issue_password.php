<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');
AAFW::import('jp.aainc.classes.core.UserManager');

class api_issue_password extends BrandcoPOSTActionBase {
    public $NeedOption = array();
    public $CsrfProtect = true;
    
    protected $ContainerName = 'api_issue_password';
    protected $AllowContent = array('JSON');
    protected $ValidatorDefinition = array(
        'mail_address' => array(
            'required' => 1,
            'type' => 'str',
            'length' => 255,
            'validator' => array('MailAddress')
        )
    );

    /** @var BrandcoAuthService $brandco_auth_service */
    private $brandco_auth_service;
    private $mail_address;
    private $user;
    private $logger;
    private $hipchat_logger;

    public function doThisFirst() {
        // サービスの呼び出し
        $this->brandco_auth_service = $this->getService('BrandcoAuthService');

        // ロガーの呼び出し
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
        $this->hipchat_logger = aafwLog4phpLogger::getHipChatLogger();

        $this->mail_address = $this->POST['mail_address'];

        if ($this->hasAdminInviteToken($this->getBrand()->id)) {
            $this->NeedPublic = true;
        }
    }

    public function validate() {
        if ($this->isLogin()) {
            $this->Validator->setError('already_login', '1');
        }

        // 該当のユーザがいるか検証
        $result = $this->brandco_auth_service->getUsersByMailAddress($this->mail_address);
        if (!$result->user || $result->user[0]->enabledPassword === '1') {
            $this->Validator->setError('already_login', '1');

            return false;
        } else {
            // いた場合はuser情報をグローバル変数に格納する
            $this->user = $result->user[0];
        }

        return true;
    }

    public function getFormURL () {
        $errors = array();
        foreach ($this->Validator->getError() as $key => $value) {
            $errors[$key] = $this->Validator->getMessage($key);
        }
        $json_data = $this->createAjaxResponse("ng", array('redirect_url' => Util::rewriteUrl('', '')), $errors);
        $this->assign('json_data', $json_data);

        return false;
    }

    public function doAction() {
        $user_transaction = aafwEntityStoreFactory::create('Users');

        try {
            $user_transaction->begin();

            /** @var UserManager $user_manager */
            $user_manager = new UserManager($this->user);

            // 仮パスワードの発行
            $password = $this->brandco_auth_service->getRandomString();

            $result = $user_manager->resetPassword($password);
            if ($result['mid'] !== 'changed')  {
                throw new aafwException('Thrift error: resetPassword');
            }

            $user_transaction->commit();

            $this->sendMail($password);

            $php_parser = new PHPParser();
            $html = Util::sanitizeOutput($php_parser->parseTemplate('auth/LoginForm.php', array(
                'ActionForm' => array('mail_address' => $this->mail_address),
                'pageStatus' => array('brand' => $this->getBrand()),
                'page_type' => $this->POST['page_type']
            )));

            $json_data = $this->createAjaxResponse("ok", array(), array(), $html);
        } catch (aafwException $e) {
            $user_transaction->rollback();

            $this->logger->error($e);
            $this->logger->error('api_issue_password#doAction cannot issue password.');
            $this->logger->hipchat_error('api_issue_password#doAction cannot issue password.');

            $json_data = $this->createAjaxResponse("ng");
        }

        $this->assign('json_data', $json_data);

        return 'dummy.php';
    }

    /**
     * @param $password
     * @throws aafwException
     */
    private function sendMail($password) {

        $from_address = $this->getMailFromAddress();
        $params = array();

        if($from_address) {
            $params['FromAddress'] = $from_address;
        }
        
        /** @var MailManager $mail_manager */
        $mail_manager = new MailManager($params);

        // 仮パスワードを記したメールの送信
        $mail_manager->loadMailContent('password_issue');
        $mail_manager->sendNow($this->mail_address, $this->createMailParams(array(
            'PASSWORD' => $password,
        )));
    }

    /**
     * @param array $params
     * @return array
     */
    private function createMailParams($params = array()) {
        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = $this->getService('CpFlowService');

        $cp_id = $this->getSession('cp_id');
        $cp = $cp_flow_service->getCpById($cp_id);

        if ($cp && $cp->type == Cp::TYPE_CAMPAIGN) {
                // キャンペーンから参加の場合
                $params['PAGE_NAME'] = $this->getBrand()->enterprise_name;
                $params['SECTION_HEADER'] = 'ログインページ';
                $params['SECTION_CONTENT'] = $params['PAGE_NAME'] . PHP_EOL . Util::rewriteUrl('my', 'login', array(), array('cp_id' => $cp_id));
                $params['OBJECT'] = 'モニプラ';
                $params['INQUIRY_URL'] = Util::rewriteUrl('inquiry', 'index', array('cp_id' => $cp_id));
        } else {
            // 新規登録・ログインページから参加の場合

            // リンクパラメータの作成
            $link_parameters = array();
            if ($cp) {
                $link_parameters['cp_id'] = $cp->id;
            }
            if ($this->getBrandSession('msgToken')) {
                $link_parameters['msg_token'] = $this->getBrandSession('msgToken');
            }

            $url = Util::rewriteUrl('my', 'login', array(), $link_parameters);

            $params['PAGE_NAME'] = $this->getBrand()->name;
            $params['SECTION_HEADER'] = 'ログインページ';
            $params['SECTION_CONTENT'] = $params['PAGE_NAME'] . PHP_EOL . $url;
            $params['OBJECT'] = 'モニプラ';
            $params['INQUIRY_URL'] = Util::rewriteUrl('inquiry', 'index');
        }
        $params['MONIPLA_MEDIA_URL'] = Util::createApplicationUrl(config('Domain.monipla_media'), array(), array());
        $params['ALLIED_ID_URL'] = Util::createApplicationUrl(config('Domain.aaid'), array(), array());

        return $params;
    }

    /**
     * @param $brand_id
     * @return mixed
     */
    public function hasAdminInviteToken($brand_id) {
        $admin_invite_token_service = $this->getService('AdminInviteTokenService');
        return $admin_invite_token_service->getValidInvitedToken($brand_id);
    }
}
