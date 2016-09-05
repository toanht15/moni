<?php

AAFW::import('jp.aainc.lib.base.aafwServiceBase');
AAFW::import('jp.aainc.classes.services.merge.AccountMergeMailService');
AAFW::import('jp.aainc.classes.services.merge.AccountMergeThriftService');
AAFW::import('jp.aainc.classes.services.merge.AfterAccountMergeOrganizeService');
/**
 * 必要なModel,Serviceを決められた順に使用して、アカウントマージに関する処理を行ってくれます
 * Class AccountMergeFacade
 */
class AccountMergeFacade extends aafwServiceBase{

    /**
     * ユーザの情報をチェックして
     * マージ案内のメールを送っても良いユーザなら送ります
     * @param $toAlliedId
     * @param $fromAlliedId
     * @param $cpId
     * @param $clientId
     * @return bool
     */
    public function sendMergeGuideMailIfPossible($toAlliedId,$fromAlliedId,$cpId,$clientId){
        
        /** @var UserService $userService */
        $userService = $this->getService('UserService');
        $user = $userService->getUserByMoniplaUserId($fromAlliedId);

        if($user->mail_address){
            return false;
        }

        $accountMergeThriftService = new AccountMergeThriftService();

        /** @var AccountMergeSuggestions $accountMergeSuggestionStore */
        $accountMergeSuggestionStore = $this->getModel('AccountMergeSuggestions');

        if( !$accountMergeThriftService->hasOnlyMergeAbleUsingApplication($fromAlliedId) ) {
            //マージできそうで出来ないユーザーの母数を調べることも考えてログはく
            aafwLog4phpLogger::getDefaultLogger()->info('AccountMerge SendMergeGuideMail user has not disallowed using_application . toAlliedId='.$toAlliedId. ",fromAlliedId=".$fromAlliedId);
            return false;
        }

        if( $accountMergeThriftService->isDuplicatedSNSAccountType($toAlliedId, $fromAlliedId) ) {
            //マージできそうで出来ないユーザーの母数を調べることも考えてログはく
            aafwLog4phpLogger::getDefaultLogger()->info('AccountMerge SendMergeGuideMail duplicate SNStype. toAlliedId='.$toAlliedId. ",fromAlliedId=".$fromAlliedId);
            return false;
        }


        $accountMergeSuggestion = new AccountMergeSuggestion();
        $accountMergeSuggestion->to_allied_id = $toAlliedId;
        $accountMergeSuggestion->from_allied_id = $fromAlliedId;
        $accountMergeSuggestionStore->save($accountMergeSuggestion);

        /** @var AccountMergeMailService $mergeMailService */
        $mergeMailService = new AccountMergeMailService();
        $mergeMailService->sendMergeGuideMail($toAlliedId, $accountMergeSuggestion->id, $cpId,$clientId);
        
        return true;
    }

    /**
     * ユーザの情報をチェックして
     * マージ可能ならマージを行います。
     * @param AccountMergeSuggestion
     * @return bool
     */
    public function mergeAccountIfPossible($accountMergeSuggestion){
        $accountMergeThriftService = new AccountMergeThriftService();


        if($accountMergeSuggestion == null){
            return false;
        }
        
        /** @var UserService $userService */
        $userService = $this->getService('UserService');
        $user = $userService->getUserByMoniplaUserId($accountMergeSuggestion->from_allied_id);
        //メールアドレスがあると、どこかでキャンペーンに参加に進んだと思われるのでマージさせない
        if($user->mail_address){
            return false;
        }

        if( !$accountMergeThriftService->hasOnlyMergeAbleUsingApplication($accountMergeSuggestion->from_allied_id) ) {
            return false;
        }

        if( $accountMergeThriftService->isDuplicatedSNSAccountType($accountMergeSuggestion->to_allied_id, $accountMergeSuggestion->from_allied_id) ) {
            return false;
        }

        $isSuccessMerge = $accountMergeThriftService->mergeAlliedAccount($accountMergeSuggestion->to_allied_id, $accountMergeSuggestion->from_allied_id);
        if(!$isSuccessMerge){
            aafwLog4phpLogger::getHipChatLogger()->error("AccountMerge thrift error to_allied_id=".$accountMergeSuggestion->to_allied_id." from_allied_id=".$accountMergeSuggestion->from_allied_id);
            return false;
        }
        $accountMergeDeleteService = new AfterAccountMergeOrganizeService();
        $accountMergeDeleteService->organizeMergedUserInfo($accountMergeSuggestion);

        return true;
    }  
}
