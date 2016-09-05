<?php

AAFW::import("jp.aainc.aafw.web.aafwController");
AAFW::import('jp.aainc.classes.core.UserManager');
require_once dirname(__FILE__) . '/../../config/define.php';


class SyncCoreFillUserMailAddress {

    private $logger;
    protected $moniplaCore;

    public function __construct() {
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
    }

    public function doProcess() {
        $storeFactory = new aafwEntityStoreFactory ();
        $usersStore = $storeFactory->create('Users');
        $users = $usersStore->find(array('mail_address' => ''));

        $serviceFactory = new aafwServiceFactory();
        /** @var SocialAccountService $socialAccuntService */
        $socialAccuntService = $serviceFactory->create('SocialAccountService');

        $this->logger->info('SyncCoreFillUserMailAddress - Start');
        $i = 0;
        foreach($users as $user) {

            if($user->mail_address) continue; //念のため

            $socialAccunts = $socialAccuntService->getSocialAccuntsByUserId($user->id);

            if($socialAccunts) {
                $socialAccunt = $socialAccunts->current();
                $user->mail_address = $socialAccunt->mail_address;

            } else{

                $userInfo = $this->getPlatformUserInfo($user->monipla_user_id);
                $userManager = new UserManager($userInfo, $this->getMoniplaCore());

                $user->mail_address = $userManager->getMailAddress();

            }

            if(!$user->mail_address){
                // 最後の手段
                if(!$user->name){
                    continue;
                }

                $validator = new aafwValidator();
                if($validator->isMailAddress($user->name)){
                    $user->mail_address = $user->name;
                } else{
                    continue;
                }
            }
            $usersStore->save($user);

            $i++;
            $this->logger->info('SyncCoreFillUserMailAddress - Updated userId : '.$user->id.' mailAddress : '.$user->mail_address);
        }

        $this->logger->info('SyncCoreFillUserMailAddress - End count : '.$i);

    }

    /**
     * プラットフォームユーザ情報取得
     * @param $moniplaUserId
     * @return mixed
     */
    private function getPlatformUserInfo($moniplaUserId) {
        return $this->getMoniplaCore()->getUserByQuery(array(
            'class' => 'Thrift_UserQuery',
            'fields' => array(
                'socialMediaType' => 'Platform',
                'socialMediaAccountID' => $moniplaUserId,
            )));
    }

    public function getMoniplaCore () {
        if ( $this->moniplaCore == null ) {
            AAFW::import('jp.aainc.vendor.cores.MoniplaCore');
            $this->moniplaCore = \Monipla\Core\MoniplaCore::getInstance();
        }
        return $this->moniplaCore;
    }
}
