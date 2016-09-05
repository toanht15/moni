<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.classes.CpInfoContainer');

class inbox extends BrandcoGETActionBase {

    public $NeedOption = array(BrandOptions::OPTION_MYPAGE, BrandOptions::OPTION_CRM);

    const MSG_INFO_CP = "cp";

    const MSG_INFO_NEWEST = "newest";

    const MSG_INFO_UNREAD = "unread";

    const MSG_INFO_UNREAD_COUNT = "unread_count";

    const MSG_INFO_READ = "read";

    const MSG_INFO_READ_COUNT = "read_count";

    const MSG_INFO_PRODUCTS = "products";

    private static $now_date_cache = null;

    private static $now_year_cache = null;

    public $NeedUserLogin = true;
    public $NeedRedirect = true;

    /** @var  $cp_user_service CPUserService */
    protected $cp_user_service;

    /** @var $user_service UserService */
    protected $user_service;

    /** @var $user_service BrandsUsersRelationService */
    protected $brands_users_relation_service;

    /** @var CpFlowService $cp_flow_service */
    protected $cp_flow_service;

    public function doThisFirst() {
        $now = new DateTime();
        self::$now_date_cache = new DateTime($now->format("y-m-d"));
        self::$now_year_cache = $now->format("Y");
        $this->cp_user_service = $this->createService('CpUserService');
        $this->user_service = $this->createService('UserService');
        $this->brands_users_relation_service = $this->createService('BrandsUsersRelationService');
        $this->cp_flow_service = $this->createService('CpFlowService');
    }

    public function validate() {
        return true;
    }

    function doAction() {
        $this->Data['pageStatus']['og'] = array('title' =>'メッセージ - '.$this->getBrand()->name);
        $this->Data['userInfo'] = $this->user_service->getUserByMoniplaUserId($this->Data['pageStatus']['userInfo']->id);
        $this->Data['brandUserInfo'] = $this->brands_users_relation_service->getBrandsUsersRelation($this->Data['brand']->id, $this->Data['userInfo']->id);
        $cp_users = $this->cp_flow_service->getParticipatedPublicCpUsers($this->Data['userInfo']->id, $this->Data['brandUserInfo']->brand_id);
        $this->Data["message_info_list"] = $this->createMessageInfoList($cp_users);

        return 'user/brandco/mypage/inbox.php';
    }

    private function createMessageInfoList($cp_users) {
        $message_info_list = array();
        foreach ($cp_users as $cp_user) {
            $messages = $this->cp_user_service->getAllCpUserActionMessagesByCpUserIdOrderByActionOrder($cp_user['id'], false, array(CpAction::TYPE_CONVERSION_TAG));
            if (count($messages) == 0) {
                continue;
            }

            list($newestMsg, $unreadMsgs, $readMsgs,$productMsgs) = $this->analyzeMessages($messages);
            $message_info = array(
                self::MSG_INFO_CP => CpInfoContainer::getInstance()->getCpById($cp_user['cp_id']),
                self::MSG_INFO_NEWEST => $newestMsg,
                self::MSG_INFO_UNREAD => $unreadMsgs,
                self::MSG_INFO_PRODUCTS => $productMsgs,
                self::MSG_INFO_UNREAD_COUNT => count($unreadMsgs),
                self::MSG_INFO_READ => $readMsgs,
                self::MSG_INFO_READ_COUNT => count($readMsgs)
            );

            $message_info_list[] = $message_info;
        }

        uasort($message_info_list, array('inbox', 'sortMessageByDate'));

        return $message_info_list;
    }

    private function analyzeMessages($messages) {
        $newestMsg = $messages->current();
        $unreadMsgs = array();
        $readMsgs = array();
        $productMsgs = array();
        foreach ($messages as $msg) {
            if ($msg->read_flg == CpUserActionMessage::STATUS_READ) {
                array_push($readMsgs, $msg);
            } else {
                array_push($unreadMsgs, $msg);
            }
            $cpActionStore = $this->getModel('CpActions');
            /** @var CpAction $cpAction */
            $cpAction = $cpActionStore->findOne($msg->cp_action_id);
            if($cpAction->type == CpAction::TYPE_PAYMENT){
                $cpPaymentAction = $this->getModel(CpPaymentActions::class)->findOne(array('cp_action_id'=>$cpAction->id));
                $orders = $this->getModel(Orders::class)->find(
                    array(
                        'conditions' => array(
                            'user_id'=>$this->Data['userInfo']->id,
                            'product_id'=>$cpPaymentAction->product_id,
                            'payment_status:IS NOT NULL'=> ''
                        ),
                        'order' => array(
                            'name' => 'order_completion_date',
                            'direction' => 'desc'
                        )
                    )
                );
                foreach ($orders as $order) {
                    $product = $this->getModel(Products::class)->findOne($order->product_id);
                    $orderMsg = array(
                        'order_id' => $order->id,
                        'product_image_url' => $product->image_url,
                        'product_title' => $product->title,
                        'order_completion_date'=>$order->order_completion_date
                    );
                    array_push($productMsgs,$orderMsg);
                }
            }
        }

        uasort($readMsgs, array("inbox", "sortMessageEntityByDate"));
        uasort($unreadMsgs, array("inbox", "sortMessageEntityByDate"));

        return array($newestMsg, $unreadMsgs, $readMsgs,$productMsgs);
    }

    private static function sortMessageByDate($a, $b) {
        return ($a[self::MSG_INFO_CP]->created_at < $b[self::MSG_INFO_CP]->created_at ? 1 : -1);
    }

    private static function sortMessageEntityByDate($left, $right) {
        return ($left->created_at > $right->created_at ? 1 : -1);
    }

    public static function canEmphasize($message_info) {
        $ammountOfMsgs = $message_info[self::MSG_INFO_READ_COUNT] + $message_info[self::MSG_INFO_UNREAD_COUNT];
        return !$message_info[self::MSG_INFO_NEWEST]->read_flg && $ammountOfMsgs == 1;
    }

    public static function canShowUnreadMessages($message_info) {
        $ammountOfMsgs = $message_info[self::MSG_INFO_READ_COUNT] + $message_info[self::MSG_INFO_UNREAD_COUNT];
        return $message_info[self::MSG_INFO_UNREAD_COUNT] > 0 && $ammountOfMsgs > 1;
    }

    public static function toInboxDateTime($datetime_str)
    {
        $date_only = explode(" ", $datetime_str)[0];
        $target_date = new DateTime($date_only);

        $target_datetime = new DateTime($datetime_str);
        $is_different_year = strpos($datetime_str, self::$now_year_cache) !== 0;
        if ($is_different_year) {
            return $target_datetime->format("Y/m/d");
        }

        $is_different_day = $target_date->diff(self::$now_date_cache)->d >= 1;
        if ($is_different_day) {
            return $target_datetime->format("m/d");
        }

        return $target_datetime->format("H:i");
    }
}
