<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');

class withdraw extends BrandcoPOSTActionBase{
    public $NeedUserLogin = true;
    public $CsrfProtect = true;

    protected $ContainerName = 'withdraw';
    protected $Form = array (
        'package' => 'mypage',
        'action' => 'withdraw_form',
    );

    protected $ValidatorDefinition = array(
        'withdraw_reason' => array(
            'required' => true
        )
    );

    public function validate () {
        if (!is_array($this->withdraw_reason)) {
            $this->Validator->setError('withdraw_reason', 'NOT_EXIST_CHOICE');
            return false;
        }
        if (in_array("その他", $this->withdraw_reason) && !$this->other_reason) {
            $this->Validator->setError('withdraw_reason', 'INVALID_OTHER_CHOICE');
            return false;
        }
        return true;
    }

    function doAction() {

        /** @var BrandsUsersRelationService $brands_users_relation_service */
        $brands_users_relation_service = $this->createService('BrandsUsersRelationService');

        try {
            $brands_users_relation_service->brands_users_relations->begin();

            /** @var BrandsUsersRelation $brands_users_relation */
            $brands_users_relation = $this->getBrandsUsersRelation();

            $withdraw_log = $brands_users_relation_service->withdrawByBrandUserRelation($brands_users_relation, false);

            foreach ($this->withdraw_reason as $reason) {
                if ($reason == "その他") {
                    $reason .= '（'.$this->other_reason.'）';
                }
                $brands_users_relation_service->createWithdrawReason($withdraw_log->id, $reason, 1);
            }

            if ($this->feedback) {
                $brands_users_relation_service->createWithdrawReason($withdraw_log->id, $this->feedback, 2);
            }

            //ログアウト
            $this->setLogout($brands_users_relation);
            $this->resetActionContainerByType();

            $this->Data['saved'] = true;

            $brands_users_relation_service->brands_users_relations->commit();

        } catch (Exception $e) {
            $brands_users_relation_service->brands_users_relations->rollback();
            $logger = aafwLog4phpLogger::getDefaultLogger();
            $logger->error($e);
            return 'redirect: ' . Util::rewriteUrl ( 'mypage', 'withdraw_form', array(), array('mid' => 'notice-send-failed') );
        }

        return 'redirect: ' . Util::rewriteUrl ( '', '' , array(), array('mid' => 'withdraw_success'));
    }
}
