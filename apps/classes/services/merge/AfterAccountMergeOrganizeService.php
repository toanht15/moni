<?php

AAFW::import('jp.aainc.lib.base.aafwServiceBase');

/**
 * アカウントマージ成功後、マージで消えた方のユーザのデータを削除したり更新するクラス
 * Class AccountMergeDeleteService
 */
class AfterAccountMergeOrganizeService extends aafwServiceBase {
    /**
     * マージされた側のユーザーの情報を新モニのDBから削除したり書き換えたりする
     * @param AccountMergeSuggestion
     * @throws aafwException
     */
    public function organizeMergedUserInfo($accountMergeSuggestion) {

        /** @var Users $userStore */
        $userStore = $this->getModel('Users');
        /** @var User $deleteTargetUser */
        $deleteTargetUser = $userStore->findOne(array('monipla_user_id'=>$accountMergeSuggestion->from_allied_id));
        /** @var User $toUser */
        $toUser = $userStore->findOne(array('monipla_user_id'=>$accountMergeSuggestion->to_allied_id));

        if(!$deleteTargetUser && !$toUser){
            return;
        }
        $userStore->begin();
        try {
            $this->deleteUserApplication($deleteTargetUser->id);

            $this->deleteBrandUserRelations($deleteTargetUser->id);

            $this->deletejoinedCpInfo($deleteTargetUser->id);

            $this->replaceSocialAccounts($deleteTargetUser->id,$toUser->id);

            $this->updateMergedAccountMergeSuggestion($accountMergeSuggestion->to_allied_id,$accountMergeSuggestion->from_allied_id);

            $userStore->delete($deleteTargetUser);

        }catch (Exception $e){
            $userStore->rollback();
            aafwLog4phpLogger::getDefaultLogger()->error($e->getMessage().' AccountMergeAfter Error toAlliedId='.$accountMergeSuggestion->to_allied_id. ",fromAlliedId=".$accountMergeSuggestion->from_allied_id);
            aafwLog4phpLogger::getHipChatLogger()->error($e->getMessage().'マージ後の後処理エラー toAlliedId='.$accountMergeSuggestion->to_allied_id. ",fromAlliedId=".$accountMergeSuggestion->from_allied_id);
        }
        $userStore->commit();
    }

    //マージで消えたユーザーのuserApplicationを削除します
    private function deleteUserApplication($deleteUserId){
        /** @var UserApplications $userApplicationStore */
        $userApplicationStore = $this->getModel('UserApplications');
        $userApplications = $userApplicationStore->find(array('user_id' => $deleteUserId));
        foreach ($userApplications as $userApplication) {
            $userApplicationStore->delete($userApplication);
        }
    }

    /**
     * マージで消えたユーザーのbrands_users_relationsを削除します
     * @param $deleteUserId
     */
    private function deleteBrandUserRelations($deleteUserId){
        /** @var BrandsUsersRelations $brandsUsersRelationStore */
        $brandsUsersRelationStore = $this->getModel('BrandsUsersRelations');
        $brandsUsersRelations = $brandsUsersRelationStore->find(array('user_id' => $deleteUserId));
        foreach ($brandsUsersRelations as $brandsUsersRelation) {
            $brandsUsersRelationStore->delete($brandsUsersRelation);
        }
    }

    /**
     * キャンペーン参加に関する情報を消す
     * マージで消えてるユーザはcp_users,cp_action_statuses,cp_action_messagesにしか情報がないはず
     * @param $deleteUserId
     */
    private function deletejoinedCpInfo($deleteUserId){
        /** @var CpUsers $cpUserStore */
        $cpUserStore = $this->getModel('CpUsers');
        $cpUsers = $cpUserStore->find(array('user_id' => $deleteUserId));
        foreach ($cpUsers as $cpUser) {
            /** @var CpUserActionStatuses $cpUserActionStatusStore */
            $cpUserActionStatusStore = $this->getModel('CpUserActionStatuses');
            $cpUserActionStatuses = $cpUserActionStatusStore->find(array('cp_user_id' => $cpUser->id));
            foreach ($cpUserActionStatuses as $cpUserActionStatus) {
                $cpUserActionStatusStore->delete($cpUserActionStatus);
            }

            /** @var CpUserActionMessages $cpUserActionMessageStore */
            $cpUserActionMessageStore = $this->getModel('CpUserActionMessages');
            $cpUserActionMessages = $cpUserActionMessageStore->find(array('cp_user_id' => $cpUser->id));
            foreach ($cpUserActionMessages as $cpUserActionMessage) {
                $cpUserActionMessageStore->delete($cpUserActionMessage);
            }

            $cpUserStore->delete($cpUser);
        }
    }

    /**
     * マージされて消えたsocial_accountsテーブルのuser_idを
     * マージ後のuser_idに書き換える
     * @param $deleteUserId
     * @param $toUserId
     */
    private function replaceSocialAccounts($deleteUserId,$toUserId){

        //toUserがない場合。AAIDログインしかしてない人の場合はマージされたsocialAccountをdelするだけにする
        if(!$toUserId){
            /** @var SocialAccounts $socialAccountStore */
            $socialAccountStore = $this->getModel('SocialAccounts');
            $socialAccounts = $socialAccountStore->find(array('user_id' => $deleteUserId));
            foreach ($socialAccounts as $socialAccount){
                $socialAccount->del_flg = 1;
                $socialAccountStore->save($socialAccount);
            }
        }else{
            /** @var SocialAccounts $socialAccountStore */
            $socialAccountStore = $this->getModel('SocialAccounts');
            $socialAccounts = $socialAccountStore->find(array('user_id' => $deleteUserId));
            foreach ($socialAccounts as $socialAccount) {
                $alreadyExistSocialAccounts = aafwDataBuilder::newBuilder()->getSocialAccountAfterMerge(
                    array('social_media_id'=>$socialAccount->social_media_id,
                        'social_media_account_id'=>$socialAccount->social_media_account_id,
                        'user_id'=>$toUserId) ,null,null,null,'SocialAccount');
                //ソーシャルアカウントの連携を付け外しされると、social_account情報がすでに存在している場合があるので、その場合はdel_flg=0に戻す
                if($alreadyExistSocialAccounts[0]->id > 0){
                    $updateFromSocialAccounts = "/* AfterAccountMerge UPDATE social_media_account */ UPDATE social_accounts SET del_flg = 0 WHERE id = ".$alreadyExistSocialAccounts[0]->id;
                    aafwDataBuilder::newBuilder()->getBySQL($updateFromSocialAccounts, null);
                    //deleteをつかうとupdateAtを強制的に更新しようとしてSQLエラーになるのでdel_flg立ててsaveしてます
                    $socialAccount->del_flg = 1;
                    $socialAccountStore->save($socialAccount);
                }else{
                    $socialAccount->user_id = $toUserId;
                    $socialAccountStore->save($socialAccount);
                }
            }
        }
    }

    /**
     * 引数で与えられたtoAlliedIdとfromAlliedIdで引っかかるユーザーすべてを
     * マージ済みフラグを立てて更新する
     * @param $toAlliedId
     * @param $fromAlliedId
     */
    private function updateMergedAccountMergeSuggestion($toAlliedId,$fromAlliedId){
        /** @var AccountMergeSuggestions $accountMergeSuggestionStore */
        $accountMergeSuggestionStore = $this->getModel('AccountMergeSuggestions');
        $accountMergeSuggestions = $accountMergeSuggestionStore->find(array(
            'to_allied_id'=>$toAlliedId,
            'from_allied_id'=>$fromAlliedId
        ));

        foreach ($accountMergeSuggestions as $accountMergeSuggestion){
            $accountMergeSuggestion->merged = 1;
            $accountMergeSuggestionStore->save($accountMergeSuggestion);
        }
    }
}