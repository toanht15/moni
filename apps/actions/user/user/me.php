<?php
/**
 * ドメイン移行対応（負債）
 */
AAFW::import('jp.aainc.aafw.base.aafwGETActionBase');
AAFW::import('jp.aainc.vendor.cores.MoniplaCore');
class me extends aafwGETActionBase {
    protected $ValidatorDefinition = array (
        'accessToken' => array ( 'required' => true),
    );

    private $moniplaCore = null;

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
     * @return MoniplaCore
     */
    public function getMoniplaCore()
    {
        if ( $this->moniplaCore == null ) $this->moniplaCore = \Monipla\Core\MoniplaCore::getInstance();
        return $this->moniplaCore;
    }

    public function doAction() {
        if ($this->accessToken) {
            try {
                $result = $this->getMoniplaCore()->getUser(array(
                    'class' => 'Thrift_AccessTokenParameter',
                    'fields' => array(
                        'accessToken' => $this->accessToken,
                    )
                ));
                if ($result->result->status == Thrift_APIStatus::SUCCESS) {
                    $this->assign('name', $result->name);
                    $this->assign('id', $result->id);
                    $this->assign('socialAccounts', array_map(function ($elm) {
                        $array = (array)$elm;
                        if (isset($array['mailAddress'])) {
                            unset($array['mailAddress']);
                        }
                        return $array;
                    }, $result->socialAccounts));
                    $result = $this->getMoniplaCore()->getSummaryPointByUser(array(
                        'class' => 'Thrift_UserQuery',
                        'fields' => array(
                            'id' => $result->id,
                        )
                    ));
                    $this->assign('point', $result->plusPoints);
                } else {
                    $this->assign('error', $result->result->errors[0]->message);
                }
            } catch (Exception $e) {
                $logger = aafwLog4phpLogger::getDefaultLogger();
                $logger->error($e);
            }
        }
        $this->Data["json_data"] = $this->Data;
        return 'bin: json';
    }
}
