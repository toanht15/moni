<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');

/*
 * Templateをhtml形式で返すAPIのActionBase
 * 外部から呼ばれないようCSRFトークンで認証する
 * 呼ぶことが可能なTemplateを絞るため、TemplateIDで管理する
 *
 * 抽象メソッド:
 * setTemplateId
 * setTemplateParams
 */

abstract class BrandcoAPICallTemplateBase extends BrandcoPOSTActionBase {
    // フレームワーク依存の変数
    public $NeedOption = array();
    public $CsrfProtect = true;

    protected $AllowContent = array('JSON');
    protected $ValidatorDefinition = array();

    // クラス独自の定数
    const TEMPLATE_ID_NOT_FOUND             = -1;
    const TEMPLATE_ID_AUTH_FORM             = 1;
    const TEMPLATE_ID_MAIL_AUTH_FORM_WRAP   = 2;
    const TEMPLATE_ID_MAIL_AUTH_FORM        = 3;
    const TEMPLATE_ID_MAIL_LOGIN_FORM       = 4;
    const TEMPLATE_ID_MAIL_LOGIN_GUIDE      = 5;
    const TEMPLATE_ID_MAIL_SIGNUP_FORM      = 6;

    // クラス独自の変数
    protected $allowed_templates = array(
        self::TEMPLATE_ID_AUTH_FORM             => 'auth/AuthForm.php',
        self::TEMPLATE_ID_MAIL_AUTH_FORM_WRAP   => 'auth/MailAuthFormWrap.php',
        self::TEMPLATE_ID_MAIL_AUTH_FORM        => 'auth/MailAuthForm.php',
        self::TEMPLATE_ID_MAIL_LOGIN_FORM       => 'auth/MailLoginForm.php',
        self::TEMPLATE_ID_MAIL_LOGIN_GUIDE      => 'auth/MailLoginGuide.php',
        self::TEMPLATE_ID_MAIL_SIGNUP_FORM      => 'auth/MailSignupForm.php'
    );
    protected $template_id = self::TEMPLATE_ID_NOT_FOUND;
    protected $template_params = array();

    abstract protected function setTemplateId();

    abstract protected function setTemplateParams();

    public function beforeValidate() {
        $this->setTemplateId();
        $this->setTemplateParams();
    }

    public function validate() {
        if (!array_key_exists($this->template_id, $this->allowed_templates)) {
            return false;
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
        /** @var PHPParser $php_parser */
        $php_parser = new PHPParser();
        $html = Util::sanitizeOutput($php_parser->parseTemplate($this->allowed_templates[$this->template_id], $this->template_params));

        $json_data = $this->createAjaxResponse("ok", array(), array(), $html);
        $this->assign('json_data', $json_data);

        return 'dummy.php';
    }
}
