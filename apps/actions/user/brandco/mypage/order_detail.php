<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');

class order_detail extends BrandcoGETActionBase {
    public $NeedOption = array();

    function validate() {
        return true;
    }

    function doAction() {
        $order = $this->getModel('Orders')->findOne($this->GET['exts'][0]);
        $userInfo = $this->getSession('pl_monipla_userInfo');
        if(!$userInfo){
            $this->setSession('loginRedirectUrl', Util::rewriteUrl("mypage","order_detail" ) . $this->GET['exts'][0]);
            return "redirect: ".Util::rewriteUrl("my", "login");
        }
        /** @var $user_service UserService */
        $user_service = $this->createService('UserService');
        $user = $user_service->getUserByMoniplaUserId($userInfo['id']);
        if($order->user_id != $user->id){
            return '404';
        }

        $order->orderItems = $this->getModel('OrderItems')->find(array('order_id'=>$order->id))->toArray();
        $this->Data['order'] = $order;
        $this->Data['mypage_url'] = Util::rewriteUrl("mypage", "inbox");

        return 'user/brandco/mypage/order_detail.php';
    }
}
