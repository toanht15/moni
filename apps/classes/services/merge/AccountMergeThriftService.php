<?php

AAFW::import('jp.aainc.lib.base.aafwServiceBase');
AAFW::import('jp.aainc.classes.core.UserManager');

/**                              
 * Thrift経由でCoreに登録されている情報からアカウントマージ可能かチェックしたり
 * 実際にアカウントマージを行うを関数が用意されている
 * Class AccountMergeThriftService
 */
class AccountMergeThriftService extends aafwServiceBase {

    //新モニ関連以外のapplicationId達
    private $disallowApplicationIds = [
        2,//monipla
        3,//find
        4,//mpfb
        5,//mptw
        6,//mpmx
        7,//find_web
        8,//mpjp
        9,//greensnap
        11,//showcase
        12,//monipla_shot
        13,//greensnap_mobile
        14,//monipo
        15,//watav
        16,//moniplus
        17,//9b0f31b845b6bbb5a07f1b36cbda8e30bd871496(テスト用の何か)
        18,//tegakari
        19,//com_campaign
        20,//survey
    ];

    /**
     * 指定したallied_idのユーザが新モニ関連のアプリケーションにしか登録してないかどうか
     * 新モニ関連のみの場合はtrue
     * @param $alliedId
     * @return bool
     */
    public function hasOnlyMergeAbleUsingApplication($alliedId) {
        $result = $this->getCore()->getUsingApplications(
            array(
                'class' => 'Thrift_UsingApplicationParameter',
                'fields' => array('userId' => $alliedId)
            )
        );

        if( $result->result->status !== Thrift_APIStatus::SUCCESS ) {
            return false;
        }
        $disableUsingApplications = array_filter(
            $result->applicationList,
            function ($usingApplication) {
                return in_array($usingApplication->id, $this->disallowApplicationIds);
            }
        );
        return count($disableUsingApplications) === 0;
    }

    /**
     * @param $toAlliedId
     * @param $fromAlliedId
     * @return bool
     */
    public function mergeAlliedAccount($toAlliedId, $fromAlliedId) {

        $params = array(
            array(
                'class' => 'Thrift_SocialAccount',
                'fields' => array(
                    'socialMediaType' => 'Platform',
                    'socialMediaAccountID' => $toAlliedId,
                    'name' => 'dummy',
                    'validated' => 1,
                )
            ),
            array(
                'class' => 'Thrift_SocialAccount',
                'fields' => array(
                    'socialMediaType' => 'Platform',
                    'socialMediaAccountID' => $fromAlliedId,
                    'name' => 'dummy',
                    'validated' => 1,
                )
            )
        );
        $mergeResult = $this->getCore()->mergeAccount($params);
        return $mergeResult->status === Thrift_APIStatus::SUCCESS;
    }



    /**
     * マージするアカウント同士でSNSのタイプが同じもので連携がされているかチェック
     * 1つのアカウントに複数の同じSNSタイプが紐づくのを防ぎたい
     * @param $toAlliedId
     * @param $fromAlliedId
     * @return bool
     */
    public function isDuplicatedSNSAccountType($toAlliedId, $fromAlliedId) {
        $userManager = new UserManager(null);
        /** @var Thrift_UserData $toAlliedUser */
        $toAlliedUser = $userManager->getUserByQuery($toAlliedId);
        /** @var Thrift_UserData $fromAlliedUser */
        $fromAlliedUser = $userManager->getUserByQuery($fromAlliedId);
        //どちらかのユーザーの取得に失敗した場合は安全のためduplicateしてるとみなします
        if( $toAlliedUser->result->status !== Thrift_APIStatus::SUCCESS || $fromAlliedUser->result->status !== Thrift_APIStatus::SUCCESS ) {
            return true;
        }
        //SNS連携がない場合は被る可能性ない
        if( !count($toAlliedUser->socialAccounts) || !count($fromAlliedUser->socialAccounts) ) {
            return false;
        }

        //重複したSNSタイプがあるか探し出します
        /** @var Thrift_SocialAccount $baseSocialAccount*/
        foreach ($toAlliedUser->socialAccounts as $baseSocialAccount) {
            /** @var Thrift_SocialAccount $targetSocialAccount*/
            foreach ($fromAlliedUser->socialAccounts as $targetSocialAccount) {
                if($baseSocialAccount->socialMediaType == $targetSocialAccount->socialMediaType){
                    return true;
                }
            }
        }

        return false;

    }

    public function getCore() {
        if( $this->moniplaCore == null ) {
            $this->moniplaCore = \Monipla\Core\MoniplaCore::getInstance();
        }
        return $this->moniplaCore;
    }
}
