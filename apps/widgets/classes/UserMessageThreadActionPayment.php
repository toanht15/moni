<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');

class UserMessageThreadActionPayment extends aafwWidgetBase {

    public function doService($params = array()) {

        $newReturnParam = array();
        $newReturnParam['cp_payment_action'] = $params['message_info']['concrete_action'];

        $product = $this->getModel(Products::class)->findOne($params['message_info']['concrete_action']->product_id);
        $product->product_items = $this->getModel(ProductItems::class)->find(
            array('product_id' => $product->id)
        )->toArray();

        $filter = array(
            'conditions' => array(
                'product_id' => $product->id,
                'user_id' => $params['cp_user']->user_id
            ),
            'order' => array(
                'name' => 'id',
                'direction' => 'DESC'
            ),
        );
        $lastOrder = $this->getModel(Orders::class)->findOne($filter);
        if($lastOrder){
            /** @var OrderItem $orderItem */
            $lastOrder->product = $this->getModel(Products::class)->findOne($lastOrder->product_id);
        }
        $newReturnParam['product'] = $product;
        $newReturnParam['lastOrder'] = $lastOrder;
        $newReturnParam['created_at'] = $params['message_info']['message']->created_at;
        $newReturnParam['is_finished'] = $params['message_info']['action_status']->status == 1 ? true : false;
        $newReturnParam['is_opening_flg'] = $params['message_info']['cp_action']->isOpeningCpAction();
        $newReturnParam['cp_user_id'] = $params['cp_user']->id;
        $newReturnParam['cp_action_id'] = $params['message_info']['cp_action']->id;
        $newReturnParam['mypage_url'] = Util::rewriteUrl("mypage", "inbox");
        $newReturnParam['cp_info'] = $params['cp_info'];
        $newReturnParam['message_id'] = $params['message_info']['message']->id;
        $cp = $this->getModel(Cps::class)->findOne($params['cp_user']->cp_id);
        $newReturnParam['is_permanent'] = $cp->isPermanent();
        $newReturnParam['share_flg'] = $cp->share_flg;
        $newReturnParam['skip_flg'] = $params['message_info']['concrete_action']->skip_flg;
        return $newReturnParam;
    }
}
