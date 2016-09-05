<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');

abstract class BrandcoServiceLoginActionBase extends BrandcoGETActionBase {
    public $NeedOption = array();

    protected $join_sns_kind =  array(
        'fb'       => 'Facebook',
        'tw'       => 'Twitter',
        'ggl'      => 'Google',
        'yh'       => 'Yahoo',
        'line'     => 'LINE',
        'insta'    => 'Instagram',
        'linkedin' => 'LinkedIn',
        'platform' => 'Platform'
    );

    public $isLoginPage = true;
    protected $ContainerName = 'logging';

    protected $redirectUrl;
    protected $platform;
    protected $verifiedOptinKey;

    public function doThisFirst() {
        // ログインする前にロウイン関連のセッションをリセット
        $this->setSession('loginRedirectUrl', null); // このセッションはNeedUserLoginフラグの立った戻り先URLを保持している
        $this->setSession('authRedirectUrl', null);
        $this->setSession('clientId', null);
        $this->setSession('loginReferer', null);
        $this->setSession('directoryName', null);
        $this->setSession('cp_id', null);
        $this->setSession('entryMailAddress', null);
        $this->setSession('verifiedOptinKey', null);

        if ( !$this->platform ) {
            $this->platform = $this->GET['platform'] ? $this->GET['platform'] : 'platform';
        }
        if ( !$this->redirectUrl ) {
            if ($this->getBrandSession('isCmtPluginMode')) {
                $this->redirectUrl = Util::rewriteUrl('plugin', 'callback');
            } else {
                $this->redirectUrl = $this->GET['redirect_url'];
            }
        }

        $this->verifiedOptinKey = $this->generateVerifiedOptinKey();
        /** @var AdminInviteTokenService $admin_invite_service */
        $admin_invite_service = $this->createService('AdminInviteTokenService');
        if ($admin_invite_service->getValidInvitedToken($this->getBrand()->id)) {
            $this->NeedPublic = true;
        }
    }

    public function validate () {
        return $this->platform;
    }

    function doAction() {
        $sub_action_redirect = $this->doSubAction();
        if ($sub_action_redirect != null) return $sub_action_redirect;

        // sessionに戻り先, clientId, directoryName, リファラをセット
        $this->setSession('authRedirectUrl', $this->redirectUrl);
        $this->setSession('clientId', $this->platform);
        $this->setSession('directoryName', $this->GET['directory_name']);
        $this->setSession('loginReferer', $_SERVER['HTTP_REFERER']);
        $this->setSession('verifiedOptinKey', $this->verifiedOptinKey);

        // SNSでキャンペーン参加する時、セッションに保存する
        if ($this->GET['cp_id']) {
            $this->setSession('cp_id', $this->GET['cp_id']);
        }

        if (is_null($this->getBrand())) {
            aafwLog4phpLogger::getDefaultLogger()->error('BrandcoServiceLoginActionBase@doAction Invalid Brand');
        }

        return $this->getPlatformLoginFormUrl(ApplicationService::getClientId($this->getBrand()), $this->verifiedOptinKey);
    }

    private function getPlatformLoginFormUrl($clientId, $verifiedOptinKey) {
        if (!$this->platform) {
            return 403;
        }
        $domain = $this->config->query('Domain.aaid');
        $query = array(
            'platform' => $this->platform,
            'state' => json_encode(array('nonce' => $verifiedOptinKey))
        );
        $redirectUrl = config('Protocol.Secure') . '://' . $domain . '/my/login_form/' . $clientId . '?' . http_build_query($query);
        return 'redirect: ' . $redirectUrl;
    }

    private function generateVerifiedOptinKey() {
        return uniqid();
    }

    /**
     * @param $sns_type
     * @return mixed
     */
    public function getSNSAccountType($sns_type) {
        return $this->join_sns_kind[$sns_type];
    }

    /**
     * @param $user_info
     * @param $sns_type
     * @return bool
     */
    public function checkValidUserSNSAccountType($user_info, $sns_type) {
        foreach ($user_info['socialAccounts'] as $social_account) {
            if ($social_account->socialMediaType == $sns_type) return true;
        }

        return false;
    }

    abstract function doSubAction();
}