<?php
/**
 * ドメイン移行対応（負債）
 */
AAFW::import('jp.aainc.aafw.base.aafwGETActionBase');
AAFW::import('jp.aainc.vendor.cores.MoniplaCore');
class refresh_token extends aafwGETActionBase {
    private $moniplaCore = null;
    protected $ValidatorDefinition = array (
        'clientId' => array ( 'requried' => true),
        'refreshToken' => array ( 'requried' => true),
    );
    public function doThisFirst () {
        $this->disablePlugins();
    }

    public function validate() {
        return true;
    }
    /**
     * @param null $moniplaCore
     */
    public function setMoniplaCore($moniplaCore)
    {
        $this->moniplaCore = $moniplaCore;
    }

    /**
     * @return null
     */
    public function getMoniplaCore()
    {
        if ( $this->moniplaCore == null ) $this->moniplaCore = \Monipla\Core\MoniplaCore::getInstance();
        return $this->moniplaCore;
    }

    function doAction()
    {
        $result = $this->getMoniplaCore()->refreshAccessToken ( array (
            'class' => 'Thrift_RefreshTokenParameter',
            'fields' => array (
                'clientId' => $this->clientId,
                'refreshToken' => $this->refreshToken,
            )));
        if($result->result->status == Thrift_APIStatus::SUCCESS) {
            $result->expiredIn = 900;
            $this->assign('accessToken', $result->accessToken);
            $this->assign('refreshToken', $result->refreshToken);
            $this->assign('expired_in', $result->expiredIn);
            $this->assign('expired_at', $result->expiredIn + time());
        }
        else {
            $this->assign('accessToken','');
            $this->assign('refreshToken', '');
            $this->assign('expired_in', '');
            $this->assign('expired_at', '');
            $this->assign('error', $result->result->errors[0]->message);
        }
        $this->Data["json_data"] = $this->Data;
        return 'bin: json';
    }
}