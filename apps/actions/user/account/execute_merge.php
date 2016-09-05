<?php
AAFW::import('jp.aainc.aafw.base.aafwPOSTActionBase');
AAFW::import('jp.aainc.classes.services.merge.AccountMergeUtil');
AAFW::import('jp.aainc.classes.core.UserManager');
AAFW::import('jp.aainc.classes.services.merge.AccountMergeFacade');
class execute_merge extends aafwPOSTActionBase{

    protected $ContainerName = 'merge';
    public $CsrfProtect = true;

    function validate() {
        return true;
    }

    function doAction() {
        $accountMergeFacade = new AccountMergeFacade();

        $decodedParams = AccountMergeUtil::decodeToken($this->POST['token']);
        $accountMergeSuggestionStore = $this->getModel('AccountMergeSuggestions');
        $accountMergeSuggestion = $accountMergeSuggestionStore->findOne($decodedParams['account_merge_suggestion_id']);
        if(!$accountMergeSuggestion){
            return '404';
        }
        $cpId = $decodedParams['cp_id'];

        if(!$accountMergeFacade->mergeAccountIfPossible($accountMergeSuggestion)){
            return "redirect: /account/merge?token=".$this->POST['token']."&status=failure";
        }

        /** @var Cp $cp */
        $cp = $this->getModel('Cps')->findOne($cpId);
        $this->switchLoginSession($accountMergeSuggestion->to_allied_id,$cp->getBrand()->id);

        return 'redirect: '.$cp->getUrl(true);
    }



    /**
     * ログインセッションをマージ後のユーザーに書き換える
     * @param $alliedId
     */
    public function switchLoginSession($alliedId,$brandId){
        $userManager = new UserManager(null);
        /** @var Thrift_UserData $toAlliedUser */
        $toAlliedUser = $userManager->getUserByQuery($alliedId);

        /** @var UserService $userService */
        $userService = $this->createService('UserService');
        $user = $userService->getUserByMoniplaUserId($toAlliedUser->id);

        $this->setSharedCriticalResourceSessionThatMustBeUsedVeryCarefully('pl_monipla_userInfo', $this->getService('BrandcoAuthService')->castSocialAccounts($toAlliedUser));
        $this->setSharedCriticalResourceSessionThatMustBeUsedVeryCarefully('pl_monipla_userId', $user->id);
        $this->setSharedCriticalResourceSessionThatMustBeUsedVeryCarefully('pl_loginBrandIds', array($brandId => 1));
    }
}
