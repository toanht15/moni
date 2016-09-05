<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoHeader')->render($data['pageStatus'])) ?>
<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoAccountHeader')->render($data['pageStatus'])) ?>

<article>
    <h1 class="hd1">クーポン設定</h1>
    <?php $coupon_action_manager = new CpCouponActionManager() ?>
    <section class="couponWrap">
        <dl class="couponDetail1">
            <dt>クーポン名
                <span class="iconHelp">
                <span class="text">ヘルプ</span>
                <span class="textBalloon1">
                  <span>
                    クーポン名称として、一般公開されます
                  </span>
                <!-- /.textBalloon1 --></span>
              <!-- /.iconHelp --></span>
            </dt>
            <dd><?php assign($data['coupon']->name) ?></dd>
            <dt>メモ
                <span class="iconHelp">
                <span class="text">ヘルプ</span>
                <span class="textBalloon1">
                  <span>
                    管理用メモのため、一般公開されません
                  </span>
                <!-- /.textBalloon1 --></span>
              <!-- /.iconHelp --></span>
            </dt>
            <dd><?php assign($data['coupon']->description ? $data['coupon']->description : 'なし') ?></dd>
            <dt>クーポン配布順序</dt>
            <dd><?php assign(Coupon::$distribution_type_label[$data['coupon']->distribution_type]) ?></dd>
            <dt>枚数</dt>
            <dd>
                <div class="couponCodeWrap">
                    <table class="couponCodeTable1">
                        <thead>
                        <tr>
                            <th>上限数</th>
                            <th>利用状況</th>
                            <th>クーポン種別数</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td><?php assign(number_format($data['coupon_limit'])) ?></td>
                            <td><?php assign($data['coupon_reserved']) ?></td>
                            <td><?php assign($data['coupon']->countReservedNum()) ?></td>
                        </tr>
                        </tbody>
                        <!-- /.couponCodeTable1 --></table>
                    <!-- /.couponCodeWrap --></div>
            </dd>
            <dt>コード</dt>
            <dd>
                <?php if ($data['coupon_codes'] && $data['coupon_codes']->total() > 0): ?>
                <div class="couponCodeWrap">
                    <table class="couponCodeTable1">
                        <thead>
                        <tr>
                            <th>コード</th>
                            <th>期限</th>
                            <th class="upperlimitCode">上限数</th>
                            <th class="distributeCode">配布数</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($data['coupon_codes'] as $coupon_code): ?>
                            <tr>
                                <td><?php assign($coupon_code->code) ?></td>
                                <td>
                                    <?php assign(($coupon_code->expire_date == '0000-00-00 00:00:00') ? 'なし' : date_create($coupon_code->expire_date)->format('Y/m/d')) ?>
                                <td class="upperlimitCode"><?php assign(number_format($coupon_code->max_num)) ?></td>
                                <td class="distributeCode"><?php assign($coupon_action_manager->getCouponCodeReservedNum($coupon_code->id)) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                        <!-- /.couponCodeTable1 --></table>
                    <!-- /.couponCodeWrap --></div>
                    <?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoModalPager')->render(array(
                        'TotalCount' => $data['totalCount'],
                        'CurrentPage' => $this->params['p'],
                        'Count' => $data['pageLimited'],
                    ))) ?>
                    <?php else: ?>
                        なし
                    <?php endif; ?>
                    </dd>
                <!-- /.couponDetail1 --></dl>
                <p class="btnSet"><span class="btn3"><a href="<?php assign(Util::rewriteUrl('admin-coupon', 'edit_coupon_codes', array($data['coupon']->id))) ?>" >編集</a></span></p>
                <ul class="pager2">
                    <li class="prev"><a href="<?php write_html(Util::rewriteUrl('admin-coupon', 'coupon_list')) ?>" class="iconPrev1">クーポン一覧へ</a></li>
                    <!-- /.pager2 --></ul>
    </section>
</article>

<?php write_html($this->parseTemplate('BrandcoFooter.php', array_merge($data['pageStatus'], array('script' => array())))); ?>
