<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoHeader')->render($data['pageStatus'])) ?>
<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoAccountHeader')->render($data['pageStatus'])) ?>
<?php
$service_factory = new aafwServiceFactory();
$code_auth_action_manager = $service_factory->create('CpCodeAuthActionManager');
?>

<article>
    <h1 class="hd1">認証コード設定</h1>
    <section class="couponWrap">
        <dl class="couponDetail1">
            <dt>認証コード名</dt>
            <dd><?php assign($data['code_auth']->name) ?></dd>
            <dt>メモ</dt>
            <dd><?php assign($data['code_auth']->description ? $data['code_auth']->description : 'なし') ?></dd>
            <dt></dt>
            <dd>
                <div class="couponCodeWrap">
                    <table class="couponCodeTable1">
                        <thead>
                        <tr>
                            <th>上限数</th>
                            <th>認証済み合計</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td><?php assign(number_format($data['code_auth_limit'])) ?></td>
                            <td><?php assign(number_format($data['code_auth_reserved'])) ?></td>
                        </tr>
                        </tbody>
                        <!-- /.couponCodeTable1 --></table>
                    <!-- /.couponCodeWrap --></div>
            </dd>
            <dt>コード</dt>
            <dd>
                <?php if ($data['code_auth_codes'] && $data['code_auth_codes']->total() > 0): ?>
                <div class="couponCodeWrap">
                    <table class="couponCodeTable1">
                        <thead>
                        <tr>
                            <th>コード</th>
                            <th>期限</th>
                            <th class="upperlimitCode">上限数</th>
                            <th class="distributeCode">認証済みコード数</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($data['code_auth_codes'] as $code_auth_code): ?>
                            <tr>
                                <td><?php assign($code_auth_code->code) ?></td>
                                <td>
                                    <?php assign(($code_auth_code->expire_date == '0000-00-00 00:00:00') ? 'なし' : date_create($code_auth_code->expire_date)->format('Y/m/d')) ?>
                                <td class="upperlimitCode"><?php assign(number_format($code_auth_code->max_num)) ?></td>
                                <td class="distributeCode"><?php assign($code_auth_action_manager->countCodeAuthUsersByCodeAuthCodeId($code_auth_code->id)) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                        <!-- /.couponCodeTable1 --></table>
                    <!-- /.couponCodeWrap --></div>
                    <?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoModalPager')->render(array(
                        'TotalCount' => $data['total_count'],
                        'CurrentPage' => $this->params['p'],
                        'Count' => $data['page_limited'],
                    ))) ?>
                    <?php else: ?>
                        なし
                    <?php endif; ?>
                    </dd>
                <!-- /.couponDetail1 --></dl>
                <p class="btnSet"><span class="btn3"><a href="<?php assign(Util::rewriteUrl('admin-code-auth', 'edit_code_auth_codes', array($data['code_auth']->id))) ?>" >編集</a></span></p>
                <ul class="pager2">
                    <li class="prev"><a href="<?php write_html(Util::rewriteUrl('admin-code-auth', 'code_auth_list')) ?>" class="iconPrev1">認証コード一覧へ</a></li>
                    <!-- /.pager2 --></ul>
    </section>
</article>

<?php write_html($this->parseTemplate('BrandcoFooter.php', array_merge($data['pageStatus'], array('script' => array())))); ?>
