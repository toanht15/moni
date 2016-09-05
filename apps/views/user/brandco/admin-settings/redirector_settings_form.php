<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoHeader')->render($data['pageStatus'])) ?>
<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoAccountHeader')->render($data['pageStatus'])) ?>

<div class="adminWrap">
    <?php write_html($this->parseTemplate('SettingSiteMenu.php',$data['pageStatus'])) ?>

    <article class="adminMainCol">
        <h1 class="hd1">リダイレクトURL作成</h1>

        <section class="adminRedirectWrap">
            <p><span class="btn3"><a href="<?php assign(Util::rewriteUrl('admin-settings', 'edit_redirector_form', array(0))) ?>">新規作成</a></span></p>
        <!-- /.adminRedirectWrap --></section>

        <?php if($data['redirectors']):?>
        <h2 class="hd2">リダイレクトURL一覧</h2>
        <section class="adminRedirectWrap">

            <table class="adminRedirectTable">
                <thead>
                <tr>
                    <th>リダイレクトURL</th>
                    <th>リダイレクト先</th>
                    <th>メモ</th>
                    <th>作成日</th>
                    <th>クリック数</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach($data['redirectors'] as $redirector): ?>
                    <tr>
                        <th>
                            <?php assign(Util::getBaseUrl()); ?>r/<?php assign($redirector->sign) ?>
                        </th>
                        <td>
                            <?php assign($redirector->url) ?>
                        </td>
                        <td>
                            <?php assign($redirector->description) ?>
                        </td>
                        <td>
                            <?php assign(date('Y/m/d',strtotime($redirector->created_at))) ?>
                        </td>
                        <td>
                            <?php assign(number_format($redirector->countLogs())) ?>
                        </td>
                        <td>
                            <span class="btn1"><a href="<?php assign(Util::rewriteUrl('admin-settings','edit_redirector_form', array($redirector->id))) ?>" class="small2">編集</a></span>
                            <span class="csvBtn"><span class="btn3"><a href="<?php assign(Util::rewriteUrl('admin-settings','csv_redirector_log', array(), array('redirector_id' => $redirector->id))) ?>" class="small2">CSVダウンロード</a></span></span>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            <!-- /.adminRedirectTable --></table>
        <!-- /.adminRedirectWrap --></section>
        <?php endif;?>

    <!-- /.adminMainCol --></article>
<!-- /.adminWrap --></div>

<?php write_html($this->parseTemplate('BrandcoFooter.php', $data['pageStatus'])); ?>