<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoHeader')->render($data['pageStatus'])) ?>
<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoAccountHeader')->render($data['pageStatus'])) ?>

<?php $service_factory = new aafwServiceFactory();
/** @var CouponService $coupon_service */
$coupon_service = $service_factory->create('CouponService');
/** @var CpFlowService $cp_flow_service */
$cp_flow_service = $service_factory->create('CpFlowService');
$coupon_action_manager = new CpCouponActionManager();
?>
<article>
    <h1 class="hd1">クーポン設定</h1>
    <p class="supplement1">ユーザーへ配布するクーポンを登録できます。</p>
    <section class="couponWrap" style="margin-top:30px;">
        <p><span class="btn3"><a href="<?php write_html(Util::rewriteUrl('admin-coupon', 'create_coupon')) ?>">新規作成</a></span></p>
        <!-- /.couponWrap --></section>
    <h2 class="hd2">クーポン一覧</h2>
    <section class="couponWrap">
        <table class="couponTable1">
            <thead>
            <tr>
                <th>クーポン名</th>
                <th>メモ</th>
                <th>上限数</th>
                <th>利用中のキャンペーンなど</th>
                <th>クーポン種別数</th>
                <th>配布数</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($data['coupons'] as $coupon): ?>
                <?php $coupon_actions = $coupon_action_manager->getCpCouponActionsByCouponId($coupon->id); ?>
                <?php $cp_actions_num = $coupon_actions ? $coupon_actions->total() : 0 ?>

                <?php
                $col_count = $cp_actions_num;
                $coupon_action1 = null;
                if ($col_count >= 1) {
                    foreach ($coupon_actions as $coupon_action_tmp) {
                        $cp_action_tmp = $cp_flow_service->getCpActionById($coupon_action_tmp->cp_action_id);
                        $cp_tmp = $cp_action_tmp->getCp();
                        if (!$cp_tmp) {
                            $col_count --;
                        } elseif (!$coupon_action1) {
                            $coupon_action1 = $coupon_action_tmp;
                            $cp_action = $cp_flow_service->getCpActionById($coupon_action1->cp_action_id);
                            $cp = $cp_action->getCp();
                        }
                    }
                }
                if ($col_count == 0) {
                    $col_count = 1;
                }
                ?>
                <tr>
                    <th rowspan="<?php assign($col_count) ?>"><a href="<?php write_html(Util::rewriteUrl('admin-coupon', 'coupon_codes', array($coupon->id))) ?>"><?php assign($coupon->name) ?></a></th>
                    <td rowspan="<?php assign($col_count) ?>"><?php assign($coupon->description) ?></td>
                    <td rowspan="<?php assign($col_count) ?>">
                        <?php list($reserved_num, $coupon_limit) = $coupon_service->getCouponStatisticByCouponId($coupon->id) ?>
                        <?php assign(number_format($coupon_limit)) ?>
                    </td>
                        <?php if ($cp_actions_num >= 1 && $cp) : ?>
                            <?php $coupon_code_users = $coupon_action_manager->getReservedCouponCodeUserByActionId($coupon_action1->cp_action_id); ?>
                            <td><a href="<?php assign(Util::rewriteUrl('admin-cp', 'edit_action', array($cp->id, $cp_flow_service->getFirstActionOfCp($cp->id)->id))) ?>" class="<?php assign($cp->type == Cp::TYPE_CAMPAIGN ? 'typeCampaign1' : 'typeMail1') ?>">
                                        <?php assign($cp->getTitle().'(S'.$cp_action->getStepNo().')') ?></a>
                                </td>
                            <td><?php assign($coupon->countReservedNumByCouponAction($coupon_action1)) ?></td>
                            <td><?php assign($coupon_code_users ? $coupon_code_users->total() : 0) ?></td>
                        <?php elseif ($cp_actions_num < 1 || $cp_actions_num == 1 && !$cp): ?>
                            <td>なし</td>
                            <td>0</td>
                            <td>0</td>
                        <?php endif; ?>
                </tr>
                <?php if ($cp_actions_num > 1): ?>
                    <?php foreach ($coupon_actions as $coupon_action): ?>
                        <?php if ($coupon_action == $coupon_action1) continue; ?>
                        <?php
                        $cp_action = $cp_flow_service->getCpActionById($coupon_action->cp_action_id);
                        $cp = $cp_action->getCp();
                        $coupon_code_users = $coupon_action_manager->getReservedCouponCodeUserByActionId($coupon_action->cp_action_id);
                        if (!$cp) continue;
                        ?>
                        <tr>
                            <td>
                                <a href="<?php assign(Util::rewriteUrl('admin-cp', 'edit_action', array($cp->id, $cp_flow_service->getFirstActionOfCp($cp->id)->id))) ?>" class="<?php assign($cp->type == Cp::TYPE_CAMPAIGN ? 'typeCampaign1' : 'typeMail1') ?>">
                                    <?php assign($cp->getTitle().'(S'.$cp_action->getStepNo().')') ?></a>
                            </td>
                            <td><?php assign($coupon->countReservedNumByCouponAction($coupon_action)) ?></td>
                            <td><?php assign($coupon_code_users ? $coupon_code_users->total() : 0) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            <?php endforeach; ?>
            </tbody>
            <!-- /.couponTable1 --></table>
        <!-- /.couponWrap --></section>
    <?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoModalPager')->render(array(
        'TotalCount' => $data['totalCount'],
        'CurrentPage' => $this->params['p'],
        'Count' => $data['pageLimited'],
    ))) ?>
</article>

<?php write_html($this->parseTemplate('BrandcoFooter.php', $data['pageStatus'])); ?>
