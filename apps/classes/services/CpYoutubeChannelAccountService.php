<?php
AAFW::import('jp.aainc.lib.base.aafwServiceBase');

class CpYoutubeChannelAccountService extends aafwServiceBase {

    /** @var CpYoutubeChannelAccounts $cp_yt_channel_accounts */
    protected $cp_yt_channel_accounts;

    public function __construct() {
        $this->cp_yt_channel_accounts = $this->getModel('CpYoutubeChannelAccounts');
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
    }

    /**
     * @param $cp_yt_channel_account_id
     * @return entity
     */
    public function getOrCreateAccount($cp_yt_channel_account_id) {
        $account = $this->getAccount($cp_yt_channel_account_id);
        if (!$account) {
            $account = $this->cp_yt_channel_accounts->createEmptyObject();
        }
        return $account;
    }

    /**
     * @param CpYoutubeChannelAccount $cp_yt_channel_account
     * @return mixed
     */
    public function saveAccount(CpYoutubeChannelAccount $cp_yt_channel_account) {
        return $this->cp_yt_channel_accounts->save($cp_yt_channel_account);
    }

    /**
     * @param $cp_yt_channel_account_id
     * @return entity
     */
    public function getAccount($cp_yt_channel_account_id) {
        $filter = array(
            'cp_youtube_channel_action_id' => $cp_yt_channel_account_id
        );
        return $this->cp_yt_channel_accounts->findOne($filter);
    }

}
