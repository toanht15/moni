<?php
AAFW::import('jp.aainc.classes.brandco.auth.BrandcoAPICallTemplateBase');

/*
 * Templateをhtml形式で返すAPI
 * ※ ユーザアカウントが存在するかどうか、メールアドレスで判断する
 */

class api_call_template_with_mail_address extends BrandcoAPICallTemplateBase {
    protected $ContainerName = 'api_call_template_with_mail_address';
    protected $ValidatorDefinition = array(
        'mail_address' => array(
            'required' => 1,
            'type' => 'str',
            'length' => 255,
            'validator' => array('MailAddress')
        )
    );

    public function validate() {
        if ($this->isLogin()) {
            return false;
        }

        return parent::validate();
    }

    protected function setTemplateId() {
        // platform_userのメールアドレス・パスワードの有無で、template_idを決定する
        try {
            $platform_user = $this->getPlatformUserByMailAddress($this->POST['mail_address']);
            if (is_null($platform_user)) {
                $this->template_id = self::TEMPLATE_ID_MAIL_SIGNUP_FORM;
            } else {
                if ($platform_user->enabledPassword) {
                    $this->template_id = self::TEMPLATE_ID_MAIL_LOGIN_FORM;
                } else {
                    $this->template_id = self::TEMPLATE_ID_MAIL_LOGIN_GUIDE;
                }
            }
        } catch (aafwException $e) {
            aafwLog4phpLogger::getDefaultLogger()->error($e->getMessage());

            $this->template_id = self::TEMPLATE_ID_NOT_FOUND;
        }
    }

    protected function setTemplateParams() {
        $this->template_params = array(
            'ActionForm' => array('mail_address' => $this->POST['mail_address'])
        );
    }

    /**
     * TODO: BrandcoAuthTraitにも同じmethodがあるので、マージしたい
     * @param $mail_address
     * @return null
     * @throws aafwException
     */
    protected function getPlatformUserByMailAddress($mail_address) {
        /** @var BrandcoAuthService $brandco_auth_service */
        $brandco_auth_service = $this->getService('BrandcoAuthService');

        $api_result = $brandco_auth_service->getUsersByMailAddress($mail_address);
        if ($api_result->result->status !== Thrift_APIStatus::SUCCESS) {
            throw new aafwException("Error: Thrift API is failed.");
        }

        return $api_result->user[0] ?: null;
    }
}
