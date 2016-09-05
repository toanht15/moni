<?php

AAFW::import('jp.aainc.lib.base.aafwServiceBase');
AAFW::import('jp.aainc.classes.MailManager');
AAFW::import('jp.aainc.classes.services.merge.AccountMergeUtil');

/**                             
 * アカウントマージの案内メールを送るクラス
 * Class AccountMergeMailService
 */
class AccountMergeMailService extends aafwServiceBase {
    public function sendMergeGuideMail($toAlliedId, $accountMergeSuggestionId, $cpId,$clientId) {
        $mailManager = new MailManager();
        $mailManager->loadSubject('account_merge_guide_mail');
        $mailManager->loadBodyPlain('account_merge_guide_mail');
        /** @var Cp $cp */
        $cp = $this->getModel('Cps')->findOne($cpId);
        $replaceParams = array(
            'MERGE_URL' => config('Protocol.Secure') . '://' . config('Domain.brandco') .
                '/account/merge?token=' . AccountMergeUtil::encodeToken($accountMergeSuggestionId, $cpId,$clientId),
            'CP_TITLE' => $cp->getTitle(),
            'BRAND_NAME' => $cp->getBrand()->enterprise_name
        );
        $userManager = new UserManager(null);
        $toAlliedUser = $userManager->getUserByQuery($toAlliedId);
        if( $toAlliedUser->mailAddress ) {
            $mailManager->sendNow($toAlliedUser->mailAddress, $replaceParams);
        }
    }
}
