<?php
AAFW::import('jp.aainc.classes.brandco.auth.BrandcoAPICallTemplateBase');
AAFW::import('jp.aainc.classes.core.UserManager');

/*
 * AAIDユーザのパスワードを再発行するAPI
 * パスワードはメールでユーザに送信する
 */

class api_issue_password_v2 extends BrandcoAPICallTemplateBase {
    protected $ContainerName = 'api_issue_password';
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
    private $platform_user;
    private $mail_address;

    public function doThisFirst() {
        // サービスの呼び出し
        $this->brandco_auth_service = $this->getService('BrandcoAuthService');
    }

    public function beforeValidate() {
        try {
            $this->platform_user = $this->getPlatformUserByMailAddress($this->POST['mail_address']);
        } catch (aafwException $e) {
            $this->platform_user = null;
        }

        $this->mail_address = $this->POST['mail_address'];

        parent::beforeValidate();
    }

    public function validate() {
        if ($this->isLogin()) {
            return false;
        }

        if (!$this->platform_user) {
            return false;
        }

        return parent::validate();
    }

    public function doAction() {
        $user_transaction = aafwEntityStoreFactory::create('Users');

        try {
            $user_transaction->begin();

            /** @var UserManager $user_manager */
            $user_manager = new UserManager($this->platform_user);

            // 仮パスワードの発行
            $password = $this->brandco_auth_service->getRandomString();
            $result = $user_manager->resetPassword($password);
            if ($result['mid'] !== 'changed')  {
                throw new aafwException('Thrift error: resetPassword');
            }

            $user_transaction->commit();

            $this->sendMail($this->mail_address, $this->buildMailParams(array(
                'PASSWORD' => $password
            )));
        } catch (aafwException $e) {
            $user_transaction->rollback();

            aafwLog4phpLogger::getDefaultLogger()->error($e);
            aafwLog4phpLogger::getDefaultLogger()->error('api_issue_password#doAction cannot issue password.');
            aafwLog4phpLogger::getHipchatLogger()->error('api_issue_password#doAction cannot issue password.');

            $json_data = $this->createAjaxResponse("ng");
            $this->assign('json_data', $json_data);

            return 'dummy.php';
        }


        return parent::doAction();

    }

    protected function setTemplateId() {
        $this->template_id = self::TEMPLATE_ID_MAIL_LOGIN_FORM;
    }

    protected function setTemplateParams() {
        $this->template_params = array(
            'ActionForm' => array('mail_address' => $this->mail_address),
            'sent_password' => true
        );
    }

    /**
     * TODO: BrandcoAuthTraitにも同じmethodがあるので、マージしたい
     * @param $mail_address
     * @return null
     * @throws aafwException
     */
    protected function getPlatformUserByMailAddress($mail_address) {
        $api_result = $this->brandco_auth_service->getUsersByMailAddress($mail_address);
        if ($api_result->result->status !== Thrift_APIStatus::SUCCESS) {
            throw new aafwException("Error: Thrift API is failed.");
        }

        return $api_result->user[0] ?: null;
    }

    private function sendMail($mail_address, $mail_params) {
        /** @var MailManager $mail_manager */
        $mail_manager = new MailManager();

        // 仮パスワードを記したメールの送信
        $mail_manager->loadMailContent('password_issue');
        $mail_manager->sendNow($mail_address, $mail_params);
    }

    /**
     * 参加フロー (キャンペーンから or 新規登録・ログインページから) の場合でメールパラメータを変える
     * @param array $params
     * @return array
     */
    private function buildMailParams($params = array()) {
        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = $this->getService('CpFlowService');

        // CP情報の取得 (ない場合も有る)
        $cp_id = $this->getSession('cp_id');
        $cp = $cp_id ? $cp_flow_service->getCpById($cp_id) : null;

        // 参加フローによってパラメーターの値を変える
        if (/* キャンペーンから参加の場合 */ $cp && $cp->type == Cp::TYPE_CAMPAIGN) {
            $page_name      = $this->getBrand()->enterprise_name;
            $login_url      = Util::rewriteUrl('my', 'login', array(), array('cp_id' => $cp_id));
            $inquiry_url    = Util::rewriteUrl('inquiry', 'index', array('cp_id' => $cp_id));
        } else /* 新規登録・ログインページから参加の場合 */ {
            $login_url_params   = array(
                'cp_id'     => $cp ? $cp->id : '',
                'msg_token' => $this->getBrandSession('msgToken') ?: ''
            );
            $page_name      = $this->getBrand()->name;
            $login_url      = Util::rewriteUrl('my', 'login', array(), $login_url_params);
            $inquiry_url    = Util::rewriteUrl('inquiry', 'index');
        }

        $params = array_merge($params, array(
            'PAGE_NAME'         => $page_name,
            'SECTION_HEADER'    => 'ログインページ',
            'SECTION_CONTENT'   => $page_name . PHP_EOL . $login_url,
            'OBJECT'            => 'モニプラ',
            'INQUIRY_URL'       => $inquiry_url,
            'MONIPLA_MEDIA_URL' => Util::createApplicationUrl(config('Domain.monipla_media'), array(), array()),
            'ALLIED_ID_URL'     => Util::createApplicationUrl(config('Domain.aaid'), array(), array())
        ));

        return $params;
    }
}
