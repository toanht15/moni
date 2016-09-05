<?php

AAFW::import('jp.aainc.aafw.base.aafwGETActionBase');
AAFW::import('jp.aainc.classes.services.merge.AccountMergeUtil');
AAFW::import('jp.aainc.classes.core.UserManager');
AAFW::import('jp.aainc.vendor.cores.MoniplaCore');
AAFW::import('jp.aainc.classes.services.ApplicationService');

class merge extends aafwGETActionBase {

    function validate() {
        return true;
    }

    function doAction() {

        $decodedParams = AccountMergeUtil::decodeToken($this->GET['token']);
        $accountMergeSuggestionStore = $this->getModel('AccountMergeSuggestions');
        $accountMergeSuggestion = $accountMergeSuggestionStore->findOne($decodedParams['account_merge_suggestion_id']);
        if(!$accountMergeSuggestion){
            return '404';
        }

        $loginUser = (object)$this->getSharedCriticalResourceSessionThatMustBeUsedVeryCarefully("pl_monipla_userInfo");
        if(!$loginUser->id){
            $alliedIdForDomainMapping = AccountMergeUtil::getAlliedIdFromRedis($accountMergeSuggestion->from_allied_id);
            if($alliedIdForDomainMapping){
                $userManager = $this->createService('UserManager');
                $loginUser = $userManager->getUserByQuery($alliedIdForDomainMapping);
            }
        }
        $cp = $this->getModel('Cps')->findOne($decodedParams['cp_id']);
        if(!$loginUser->id){
            //ログインしてない場合はログインさせる
            //directoryNameはcallback.phpでvalidate通すために入れてる
            $this->setSharedCriticalResourceSessionThatMustBeUsedVeryCarefully('directoryName', $cp->getBrand()->directory_name);
            AccountMergeUtil::setAuthRedirectUrlToRedis($accountMergeSuggestion->from_allied_id,config('Protocol.Secure') . '://' . config('Domain.brandco'). $this->SERVER['REQUEST_URI'] );
            return 'redirect: '.$this->resolveLoginUrl($accountMergeSuggestion->from_allied_id,$decodedParams['client_id'],$cp->getBrand());
        }

        //ログインしてるユーザーとtokenから抽出したマージ対象のユーザーのIDが違う場合は404
        if($loginUser->id != $accountMergeSuggestion->from_allied_id){
            return '404';
        }

        $userManager = $this->createService('UserManager');
        $toAlliedUser = $userManager->getUserByQuery($accountMergeSuggestion->to_allied_id);

        $this->Data['fromAlliedUser'] = $loginUser;
        $this->Data['toAlliedUser'] = $toAlliedUser;
        $this->Data['token'] = $this->GET['token'];
        $this->Data['joinedCount'] = $this->countJoined($accountMergeSuggestion->to_allied_id);
        $this->Data['isFailure'] = $this->GET['status'] == "failure";
        AccountMergeUtil::delAlliedIdFromRedis($accountMergeSuggestion->from_allied_id);

        return "/user/account/merge.php";
    }

    /**           
     * alliedIDを元にそのユーザーのキャンペーン参加数を返す
     * @param $toAlliedId int
     * @return int
     */
    public function countJoined($toAlliedId){
        $userService = new UserService();
        $user = $userService->getUserByMoniplaUserId($toAlliedId);
        /** @var CpUsers $cpUserStore */
        $cpUserStore = $this->getModel('CpUsers');
        return $cpUserStore->count(array('user_id'=>$user->id));
    }


    /**
     * @param $fromAlliedId int
     * @param $clientId string キャンペーン参加時に使用していたSNSType
     * @param Brand $brand
     * @return string
     */
    public function resolveLoginUrl($fromAlliedId,$clientId,$brand){
        /** @var UserManager $userManager */
        $userManager = new UserManager(null);
        /** @var aafwApplicationConfig $config */
        $config = aafwApplicationConfig::getInstance();
        $domain = $config->query('Domain.aaid');
        $query = array(
            'platform' => $clientId,
        );

        $applicationId = ApplicationService::getClientId($brand);
        $redirectUrl = config('Protocol.Secure') . '://' . $domain . '/my/login_form/' . $applicationId . '?' . http_build_query($query);
        return $redirectUrl;
    }
    
}