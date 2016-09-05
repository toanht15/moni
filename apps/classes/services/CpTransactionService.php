<?php
AAFW::import('jp.aainc.lib.base.aafwServiceBase');

class CpTransactionService extends aafwServiceBase {

    /** @var  CpTransactions $brands */
    protected $cp_transactions;

	public function __construct() {
        $this->cp_transactions = $this->getModel('CpTransactions');
	}

    /**
     * @param $cp_action_id
     */
    public function createCpTransaction($cp_action_id) {
        $cp_transaction = $this->cp_transactions->createEmptyObject();
        $cp_transaction->cp_action_id = $cp_action_id;
        $this->cp_transactions->save($cp_transaction);
    }

    /**
     * @param $cp_action_id
     */
    public function deleteCpTransaction($cp_action_id) {
        $cp_transaction = $this->getCpTransactionByCpActionId($cp_action_id);
        $cp_transaction->del_flg = 1;
        $this->cp_transactions->save($cp_transaction);
    }

    /**
     * @param $cp_action_id
     * @return mixed
     */
    public function getCpTransactionByCpActionId($cp_action_id){
        return $this->cp_transactions->findOne(array('cp_action_id' => $cp_action_id));
    }

    /**
     * ロック
     * @param $cp_action_id
     * @return mixed
     */
    public function getCpTransactionByIdForUpdate($cp_action_id){
        $filter = array(
            'cp_action_id' => $cp_action_id,
            'for_update' => true
        );
        return $this->cp_transactions->findOne($filter);
    }
}