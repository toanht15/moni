<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.lib.db.aafwRedisManager');
AAFW::import('jp.aainc.classes.services.GiftMessageService');
AAFW::import('jp.aainc.classes.services.CpUserService');
AAFW::import('jp.aainc.classes.services.GiftCouponConfigService');
AAFW::import('jp.aainc.classes.services.GiftProductConfigService');
AAFW::import('jp.aainc.classes.core.ShippingAddressManager');

class card extends BrandcoGETActionBase {

    public $NeedOption = array();
    public $NeedRedirect = true;

    private $gift_message_id;
    private $gift_param_code;
    private $gift_message_service;

    public function doThisFirst() {
        $params                         = explode(':', $this->GET['exts'][0]);
        $this->gift_message_id          = $params[1];
        $this->gift_param_code          = $params[0];
    }

    public function validate() {

        /** @var GiftMessageService gift_message_service */
        $this->gift_message_service     = $this->createService('GiftMessageService');
        $this->Data['gift_message'] = $this->gift_message_service->getGiftMessageByCode($this->gift_message_id, $this->gift_param_code);

        return $this->Data['gift_message'] != null;

    }

    public function doAction() {
        /** @var UserService $user_service */
        $user_service = $this->createService('UserService');

        /** @var CpUserService $cp_user_service */
        $cp_user_service = $this->createService('CpUserService');

        /** @var  $cp_flow_service CpFlowService */
        $cp_flow_service = $this->createService('CpFlowService');

        /** @var CpGiftActionService $cp_gift_action_service */
        $cp_gift_action_service = $this->createService('CpGiftActionService');

        /** @var BrandContractService $brand_contract_service */
        $brand_contract_service = $this->createService('BrandContractService');

        // ユーザー情報を取得
        $this->Data['loginInfo'] = $this->getLoginInfo();

        if (Util::isNullOrEmpty($this->Data['loginInfo']['userInfo']->id)) {
            $this->Data['userInfo'] = null;
        } else {
            $this->Data['userInfo'] = $user_service->getUserByMoniplaUserId($this->Data['loginInfo']['userInfo']->id);
        }

        //注意事項で、スタンダードプランのみはブランド名が表示される
        $brand_contract = $brand_contract_service->getBrandContractByBrandId($this->brand->id);
        if($brand_contract && $brand_contract->plan == BrandContract::PLAN_MANAGER_STANDARD && $this->brand->hasOption(BrandOptions::OPTION_TOP)) {
            $this->Data['brand_name'] = $this->brand->name;
        } else {
            $this->Data['brand_name'] = 'モニプラ';
        }

        $cp_user = $cp_user_service->getCpUserById($this->Data['gift_message']->cp_user_id);

        $cp = $cp_user->getCp();
        // エントリーアクション情報を取得する
        list(, $concrete_action) = $cp_flow_service->getEntryActionInfoByCpId($cp->id);

        $cp_gift_action = $cp_gift_action_service->getCpGiftActionById($this->Data['gift_message']->cp_gift_action_id);

        $this->Data['brand'] = $this->brand;

        $this->Data['canLoginByLinkedIn'] = $this->canLoginByLinkedIn();

        $this->Data['campaign']['invite_description']   = $cp_gift_action->receiver_text;
        $this->Data['campaign']['title']                = $concrete_action->title;
        $this->Data['campaign']['image_url']            = $concrete_action->image_url;
        $this->Data['campaign']['text']                 = $concrete_action->text;
        $this->Data['campaign']['html_content']         = $concrete_action->html_content;
        $this->Data['campaign']['finished']             = $cp->isOverTime();
        $this->Data['campaign']['link']                 = $cp->getReferenceUrl(false, $this->brand);
        $this->Data['campaign']['ogp_image']            = $cp_gift_action->incentive_type;

        if ($this->Data['userInfo'] != null) {

            if ($this->Data['userInfo']->id == $cp_user->getUser()->id) {           //自分自身で受け取るのは判定する
                $this->Data['gift_myself'] = true;
            } else {
                if ($this->Data['gift_message']->receiver_user_id) {
                    $this->Data['gift_used'] = ($this->Data['gift_message']->receiver_user_id != $this->Data['userInfo']->id);
                } else {
                    $this->Data['gift_message'] = $cp_gift_action->incentive_type == CpGiftAction::INCENTIVE_TYPE_PRODUCT ? $this->Data['gift_message'] : $this->gift_message_service->updateGreetingCardReceiverStatus($this->Data['gift_message']->id, $this->Data['userInfo']->id);
                    $this->Data['gift_used'] = false;
                }
            }

            if ($cp_gift_action->incentive_type == CpGiftAction::INCENTIVE_TYPE_COUPON) {
                $this->Data['coupon_info'] = $this->getCouponInfo($this->Data['gift_message']);
            } else {
                $this->Data['product_info'] = $this->getProductInfo($cp_gift_action->id);

                /** @var PrefectureService $prefectureService */
                $prefectureService = $this->createService('PrefectureService');

                $this->Data['prefectures'] = $prefectureService->getPrefecturesKeyValue();

                //未回答
                $shippingAddressManager = new ShippingAddressManager($this->Data['pageStatus']['userInfo'], $this->getMoniplaCore());
                $shippingAddress = $shippingAddressManager->getShippingAddress();

                foreach ($shippingAddress as $key => $value) {
                    $this->Data['userShippingAddress'][ShippingAddressManager::$AddressParams[$key]] = $value;
                }
            }

        } else {
            $this->Data['gift_used'] = ($this->Data['gift_message']->receiver_user_id != 0);
        }

        return "user/brandco/gift/card.php";
    }

    private function getCouponInfo($gift_message) {

        /** @var GiftCouponConfigService $gift_coupon_config_service */
        $gift_coupon_config_service = $this->createService('GiftCouponConfigService');

        /** @var CouponService $coupon_service */
        $coupon_service = $this->createService('CouponService');

        $gift_coupon_config = $gift_coupon_config_service->getGiftCouponConfig($gift_message->cp_gift_action_id);

        $coupon_code = $coupon_service->getCouponCodeById($gift_message->coupon_code_id);

        $coupon_info = array();
        $coupon_info['code']                  = $coupon_code->code;
        $coupon_info['has_official_page']     = (strpos($coupon_code->code, "http") !== false);
        $coupon_info['expire_date']           = $coupon_code->expire_date;
        $coupon_info['name']                  = $coupon_code->getCoupon()->name;
        $coupon_info['description']           = $gift_coupon_config->message;
        $coupon_info['html_content']          = $gift_coupon_config->html_content;
        return $coupon_info;
    }

    private function getProductInfo($cp_gift_action_id) {
        /** @var GiftProductConfigService $gift_product_config_service */
        $gift_product_config_service = $this->createService('GiftProductConfigService');
        return $gift_product_config_service->getGiftProductConfig($cp_gift_action_id);
    }
}