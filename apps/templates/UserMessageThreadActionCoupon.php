<section class="message jsMessage inview" id="message_<?php assign($data['message_info']["message"]->id); ?>" >
    <a id="<?php assign('ca_' . $data["message_info"]["cp_action"]->id) ?>"></a>

        <?php
            $service_factory = new aafwServiceFactory();
            /** @var CouponService $coupon_service */
            $coupon_service = $service_factory->create('CouponService');
            $coupon = $coupon_service->getCouponById($data["message_info"]["concrete_action"]->coupon_id);
            $coupon_manager = new CpCouponActionManager();
            $coupon_code_user = $coupon_manager->getReservedCouponCodeUserByUserIdAndActionId($data['cp_user']->user_id, $data['message_info']['cp_action']->id);
            if ($coupon_code_user) {
                $coupon_code = $coupon_service->getCouponCodeById($coupon_code_user->coupon_code_id);
            }
            $cp = $this->getModel('Cps')->findOne($data["cp_user"]->cp_id);

            /** @var CpFlowService $cp_flow_service */
            $cp_flow_service = $service_factory->create('CpFlowService');
        ?>
        <form class="executeCouponActionForm" action="<?php assign(Util::rewriteUrl('messages', "api_execute_coupon_action.json")); ?>" method="POST">

            <?php write_html($this->csrf_tag()); ?>
            <?php write_html($this->formHidden('cp_action_id', $data['message_info']["cp_action"]->id)); ?>
            <?php write_html($this->formHidden('cp_user_id', $data['cp_user']->id)); ?>

            <?php if($data["message_info"]["concrete_action"]->image_url): ?>
                <p class="messageImg"><img src="<?php assign($data["message_info"]["concrete_action"]->image_url); ?>" alt="campaign img"></p>
            <?php endif; ?>
            <div class="messageCoupon">

                <?php if ($coupon_code): ?>
                    <p class="couponName"><?php write_html($this->toHalfContentDeeply($coupon->name)); ?></p>
                    <p class="couponNum"><strong><?php write_html($this->toHalfContentDeeply($coupon_code->code)); ?></strong></p>
                    <?php if ($coupon_code->expire_date != '0000-00-00 00:00:00'): ?>
                        <p class="couponLimit"><?php write_html(date_create($coupon_code->expire_date)->format('Y年m月d日')); ?>まで</p>
                    <?php endif; ?>

                <?php else: ?>
                    <p class="couponName attention1"><?php write_html('クーポンが不足しました。'); ?></p>
                <?php endif; ?>

            </div>
            <?php $message_text = $data['message_info']['concrete_action']->html_content ? $data['message_info']['concrete_action']->html_content : $this->toHalfContentDeeply($data['message_info']['concrete_action']->text); ?>
            <section class="messageText"><?php write_html($message_text); ?></section>
            <div class="messageFooter">
                <ul class="btnSet">
                    <?php if ($data["message_info"]["action_status"]->status == CpUserActionStatus::NOT_JOIN): ?>
                        <li class="btn3" style="display: none"><a class="cmd_execute_coupon_action middle1" data-messageid="<?php assign($data['message_info']["message"]->id); ?>" href="#">次へ</a></li>
                    <?php endif; ?>
                    <!-- /.btnSet --></ul>
            </div>

        </form>

<!-- /.message --></section>


<?php if (config('Stage') === 'product' && $data['message_info']["cp_action"]->isFirstGroupAction() && $cp_flow_service->isLastCpActionInGroup($data['message_info']["cp_action"]->id)): ?>
    <span class="jsGoogleAnalyticsTrackingAction"
          data-product='{"id": "P<?php assign($data['cp_user']->cp_id); ?>", "name": "campaign_<?php assign($data['cp_user']->cp_id); ?>"}'
          data-action="checkout"></span>
    <script>
        if (typeof(GoogleAnalyticsTrackingService) !== 'undefined') {
            GoogleAnalyticsTrackingService.generate("<?php assign(config('Analytics.ID')) ?>", "<?php assign(config('Analytics.TrackerName')) ?>", {'page': "<?php assign(Util::getBaseUrl() . '/messages/thread/' . $data['cp_user']->cp_id . '-purchase'); ?>" });
        }
    </script>
<?php endif ?>

<?php write_html($this->scriptTag('user/UserActionCouponService')); ?>
