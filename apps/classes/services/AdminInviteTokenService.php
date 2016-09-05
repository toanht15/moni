<?php
AAFW::import('jp.aainc.lib.base.aafwServiceBase');
AAFW::import('jp.aainc.classes.MailManager');
AAFW::import('jp.aainc.services.BrandService');

class AdminInviteTokenService extends aafwServiceBase {

    protected $admin_invite_token;
    protected $password;

    public function __construct() {
        $this->admin_invite_token = $this->getModel('AdminInviteTokens');
    }

    public function inviteAdmin($brandId, $mailAddress) {
        $password = $this->getRandamPassword();
        $oneTimeToken = $this->getOneTimeToken();
        $this->setInviteToken($brandId, $mailAddress, $password, $oneTimeToken);
        $this->sendInviteMail($brandId, $mailAddress, $password, $oneTimeToken);
    }

    /**
     * @param $brandId
     * @param $mailAddress
     * @param $password
     * @param $oneTimeToken
     * 管理者認証に必要なワンタイムトークンの保存
     */
    private function setInviteToken($brandId, $mailAddress, $password, $oneTimeToken) {
        // すでに同一のメールアドレスのデータがないか確認
        $sameInviteToken = $this->getInviteTokenByAddress($brandId, $mailAddress);
        if($sameInviteToken) {
            $this->deleteToken($sameInviteToken);
        }

        $inviteToken = $this->createEmptyInviteToken();
        $inviteToken->brand_id = $brandId;
        $inviteToken->mail_address = $mailAddress;
        $inviteToken->token = $oneTimeToken;
        $inviteToken->password = md5($password);
        $this->createInviteToken($inviteToken);
    }

    public function createInviteToken($inviteToken) {
        $this->admin_invite_token->save($inviteToken);
    }

    public function createEmptyInviteToken() {
        return $this->admin_invite_token->createEmptyObject();
    }

    /**
     * ワンタイムトークンを取得
     */
    private function getOneTimeToken() {
        $token = md5(uniqid(rand(),1));

        return $token;
    }

    /**
     * 4桁のランダムパスワードを取得
     */
    private function getRandamPassword() {
        $password = '';
        for($i = 0; $i < 4; $i++) {
            $password .= mt_rand(0, 9);
        }
        return $password;
    }

    /**
     * トークンが、招待した管理者のトークンであることを確認
     * @param $invite_token
     */
    public function existInviteToken($invite_token) {

        $filter = array(
            'conditions' => array(
                'token' => $invite_token
            ),
        );

        $isInviteToken = $this->admin_invite_token->findOne($filter);

        return $isInviteToken ? $isInviteToken : false;
    }

    /**
     * トークンとパスワードの組み合わせより、招待した管理者であることを確認
     * @param $invite_token
     */
    public function matchInviteAdmin($brandId, $invite_token, $password) {

        $filter = array(
            'conditions' => array(
                'brand_id' => $brandId,
                'token' => $invite_token,
                'password' => md5($password),
            ),
        );

        $matchInviteToken = $this->admin_invite_token->findOne($filter);

        return $matchInviteToken ? true : false;
    }

    public function getInviteTokenByToken($brandId, $token) {
        $filter = array(
            'conditions' => array(
                'brand_id' => $brandId,
                'token' => $token,
            ),
        );

        $inviteToken = $this->admin_invite_token->findOne($filter);

        return $inviteToken;
    }

    public function getInviteTokenByAddress($brandId, $mailAddress) {
        $filter = array(
            'conditions' => array(
                'brand_id' => $brandId,
                'mail_address' => $mailAddress,
            ),
        );

        $inviteToken = $this->admin_invite_token->findOne($filter);

        return $inviteToken;
    }

    /**
     * 認証で一致したワンタイムトークンを認証済みにする
     * @param $brandId
     * @param $adminAddToken
     */
    public function certificatedToken($brandId, $adminAddToken) {
        $inviteToken = $this->getInviteTokenByToken($brandId, $adminAddToken);
        $inviteToken->used_flg = 1;
        $this->admin_invite_token->save($inviteToken);
    }

    public function deleteToken($inviteToken) {
        $this->admin_invite_token->delete($inviteToken);
    }

    /**
     * @param $brandId
     * @param $mailAddress
     * @param $password
     * @param $oneTimeToken
     * 入力されたアドレスにメールを送信
     */
    private function sendInviteMail($brandId, $mailAddress, $password, $oneTimeToken) {
        $service_factory = new aafwServiceFactory();

        /** @var BrandService $brand_service */
        $brand_service = $service_factory->create('BrandService');
        $brand = $brand_service->getBrandById($brandId);

        $base_url = Util::constructBaseURL($brandId, $brand->directory_name, true);

        $mail = new MailManager(array('BccSend'=>false));
        $mailParams['MAIL_ADDRESS'] = $mailAddress;
        $mailParams['URL'] = Util::rewriteUrl('my', 'login', array(), array('invite_token'=>$oneTimeToken), $base_url);
        $mailParams['BRAND_NAME'] = $brand->name;
        $mailParams['PASSWORD'] = $password;
        $mail->loadMailContent('administrator_invite_mail');
        $mail->sendNow($mailAddress, $mailParams);
    }

    /**
     * @param $brand_id
     * @param null $token
     * @return null
     */
    public function getValidInvitedToken($brand_id, $token=null) {
        if (!$token) {
            $token = $_SESSION['invite_token'.$brand_id];
        }
        if ($token == '') {
            return null;
        }
        $inviteToken = $this->existInviteToken($token);
        if (!$inviteToken) {
            return null;
        }
        $serviceFactory =  new aafwServiceFactory();
        /** @var BrandPageSettingService $pageSettingsService */
        $pageSettingsService = $serviceFactory->create('BrandPageSettingService');
        if ($pageSettingsService->isPublic($brand_id) && !$inviteToken->canUse()) {
            return null;
        }
        return $token;
    }

    /**
     * @param $token
     * @return bool|null
     */
    public function canUseInviteToken($token) {
        if (!$inviteToken = $this->existInviteToken($token)) {
            return null;
        }
        return $inviteToken->used_flg != 1;
    }
}
