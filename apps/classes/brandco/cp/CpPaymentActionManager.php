<?php
AAFW::import('jp.aainc.lib.base.aafwObject');
AAFW::import('jp.aainc.classes.entities.CpAction');
AAFW::import('jp.aainc.classes.brandco.cp.interface.CpActionManager');
AAFW::import('jp.aainc.classes.brandco.cp.trait.CpActionTrait');

use Michelf\Markdown;

/**
* Class CpPaymentActionManager
*/
class CpPaymentActionManager extends aafwObject implements CpActionManager {

    use CpActionTrait;

    /** @var CpPaymentActions $cp_concrete_actions */
    protected $cp_concrete_actions;
    protected $logger;

    function __construct() {
        parent::__construct();
        $this->cp_actions = $this->getModel("CpActions");
        $this->cp_concrete_actions = $this->getModel("CpPaymentActions");
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
    }

    /**
     * cp_actionとcp_concrete_action取得
     * @param $cp_action_id
     * @return array|mixed
     */
    public function getCpActions($cp_action_id) {
        $cp_action = $this->getCpActionById($cp_action_id);
        if ($cp_action === null) {
            $cp_concrete_action = null;
        } else {
            $cp_concrete_action = $this->getCpConcreteActionByCpAction($cp_action);
        }
        return array($cp_action, $cp_concrete_action);
    }

    /**
     * cp_actionとcp_concrete_action生成
     * @param $cp_action_group_id
     * @param $type
     * @param $status
     * @param $order_no
     * @return mixed
     */
    public function createCpActions($cp_action_group_id, $type, $status, $order_no) {
        $cp_action = $this->createCpAction($cp_action_group_id, $type, $status, $order_no);
        $cp_concrete_action = $this->createConcreteAction($cp_action);
        return array($cp_action, $cp_concrete_action);
    }

    /**
     * cp_actionとcp_concrete_action削除
     * @param CpAction $cp_action
     * @return mixed
     */
    public function deleteCpActions(CpAction $cp_action) {
        $this->deleteConcreteAction($cp_action);
        $this->deleteCpAction($cp_action);
    }

    /**
     * cp_actionとcp_concrete_action更新
     * @param CpAction $cp_action
     * @param $data
     * @return mixed
     */
    public function updateCpActions(CpAction $cp_action, $data) {
        $this->updateCpAction($cp_action);
        $this->updateConcreteAction($cp_action, $data);
    }

    /**
     * cp_concrete_action取得
     * @param CpAction $cp_action
     * @return mixed
     */
    public function getConcreteAction(CpAction $cp_action) {
        return $this->getCpConcreteActionByCpAction($cp_action);
    }

    /**
     * cp_concrete_action生成
     * @param CpAction $cp_action
     * @return mixed
     */
    public function createConcreteAction(CpAction $cp_action) {

        //TODO : 後でなんとかする。編集画面とか作る
        $product = new Product();
        $product->cp_action_id = $cp_action->id;
        $product->cp_id = $cp_action->getCp()->id;
        $product->brand_shop_id = 1;
        $product->title = "dummy";
        $product->delivery_charge = 100;
        $product->inquiry_time1 = "11:00:00";
        $product->inquiry_time2 = "20:00:00";
        $product->inquiry_name = "brandco";
        $product->inquiry_phone = "11111111111";
        $this->getModel('Products')->save($product);

        $productItem = new ProductItem();
        $productItem->title = "dummy";
        $productItem->description = "description";
        $productItem->product_id = $product->id;
        $productItem->stock = 10;
        $productItem->unit_price = 2000;
        $this->getModel(ProductItems::class)->save($productItem);

        $cp_concrete_action = $this->cp_concrete_actions->createEmptyObject();
        $cp_concrete_action->cp_action_id = $cp_action->id;
        $cp_concrete_action->title = "決済";
        $cp_concrete_action->product_id = $product->id;
        $this->cp_concrete_actions->save($cp_concrete_action);
        return $cp_concrete_action;
    }

    /**
     * cp_concrete_action更新
     * @param CpAction $cp_action
     * @param $data
     * @return mixed|void
     */
    public function updateConcreteAction(CpAction $cp_action, $data) {
        $cp_concrete_action = $this->getCpConcreteActionByCpAction($cp_action);
        $cp_concrete_action->del_flg = 0;
        $cp_concrete_action->title = $data['title'];
        $cp_concrete_action->text = $data['text'];
        $cp_concrete_action->html_content = Markdown::defaultTransform($data['text']);
        $cp_concrete_action->skip_flg  = $data['skip_flg'];
        $this->cp_concrete_actions->save($cp_concrete_action);
    }

    /**
     * cp_concrete_action削除
     * @param $cp_action
     * @return mixed|void
     */
    public function deleteConcreteAction(CpAction $cp_action) {
        $cp_concrete_action = $this->getCpConcreteActionByCpAction($cp_action);
        $cp_concrete_action->del_flg = 1;
        $this->cp_concrete_actions->save($cp_concrete_action);
    }

    /**
     * cp_concrete_action取得
     * @param $cp_action
     * @return mixed
     */
    private function getCpConcreteActionByCpAction(CpAction $cp_action) {
        return $this->cp_concrete_actions->findOne(array("cp_action_id" => $cp_action->id));
    }

    /**
     * @param CpAction $old_cp_action
     * @param $new_cp_action_id
     * @return mixed
     */
    public function copyConcreteAction(CpAction $old_cp_action, $new_cp_action_id) {
        $old_concrete_action = $this->getConcreteAction($old_cp_action);
        $new_concrete_action = $this->cp_concrete_actions->createEmptyObject();
        $new_concrete_action->cp_action_id = $new_cp_action_id;
        $new_concrete_action->html_content = $old_concrete_action->html_content;
        $new_concrete_action->skip_flg = $old_concrete_action->skip_flg;
        $this->cp_concrete_actions->save($new_concrete_action);
    }

    /**
     * @param CpAction $cp_action
     * @param bool $with_concrete_actions
     * @return mixed|void
     * @throws Exception
     */
    public function deletePhysicalRelatedCpActionData(CpAction $cp_action, $with_concrete_actions = false) {
    }

    /**
     * @param CpAction $cp_action
     * @param CpUser $cp_user
     */
    public function deletePhysicalRelatedCpActionDataByCpUser(CpAction $cp_action, CpUser $cp_user) {
    }

}
