<?php
AAFW::import('jp.aainc.classes.brandco.auth.BrandcoAPICallTemplateBase');

/*
 * Templateをhtml形式で返すAPI
 */

class api_call_template extends BrandcoAPICallTemplateBase {
    protected $ContainerName = 'api_call_template';

    public function validate() {
        if ($this->isLogin()) {
            return false;
        }

        return parent::validate();
    }

    public function setTemplateId() {
        $this->template_id = $this->POST['template_id'];
    }

    public function setTemplateParams() {
        $cp_id = $this->getSharedCriticalResourceSessionThatMustBeUsedVeryCarefully('cp_id');
        if ($cp_id) {
            $redirect_url = Util::rewriteUrl('campaigns', $cp_id);
        } else {
            $redirect_url = Util::rewriteUrl('auth', 'signup');
        }

        $this->template_params = array(
            'cp_id' => $cp_id,
            'redirect_url' => $redirect_url
        );
    }
}