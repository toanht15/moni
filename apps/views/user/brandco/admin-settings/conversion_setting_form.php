<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoHeader')->render($data['pageStatus'])) ?>
<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoAccountHeader')->render($data['pageStatus'])) ?>

<div class="adminWrap">
    <?php write_html($this->parseTemplate('SettingSiteMenu.php', $data['pageStatus'])) ?>

    <article class="adminMainCol">
        <h1 class="hd1">コンバージョンタグ作成</h1>

        <?php
        $service_factory = new aafwServiceFactory();
        $conversion_service = $service_factory->create('ConversionService');
        ?>
        <?php if(!$conversion_service->isArrivalLimitCount($this->brand->id)):?>
        <section class="adminCvtagWrap">
            <p><span class="btn3"><a href="<?php assign(Util::rewriteUrl('admin-settings', 'edit_conversion_form', array(0))) ?>">新規作成</a></span></p>
            <!-- /.adminCvtagWrap --></section>
        <?php endif;?>
        <h2 class="hd2">コンバージョンタグ一覧</h2>
        <section class="adminCvtagWrap">

            <table class="adminCvtagTable cv">
                <thead>
                <tr>
                    <th>タグ名</th>
                    <th>メモ</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach($data['conversions'] as $conversion): ?>
                    <tr>
                        <th>
                            <?php assign($conversion->name) ?>
                        </th>
                        <td>
                            <?php assign($conversion->description) ?>
                        </td>
                        <td>
                            <span class="btn1"><a href="<?php assign(Util::rewriteUrl('admin-settings','edit_conversion_form', array($conversion->id))) ?>" class="small2">編集</a></span>
                            <?php if ($this->brand->id != Brand::ANGERS && $this->brand->id != Brand::CHOJYU): ?>
                                <span class="csvBtn"><span class="btn3"><a href="<?php assign(Util::rewriteUrl('admin-settings','csv_conversion_log', array(), array('conversion_id' => $conversion->id))) ?>" class="small2">CSVダウンロード</a></span></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
                <!-- /.adminCvtagTable --></table>
            <!-- /.adminCvtagWrap --></section>

    </article>
</div>

<?php write_html($this->parseTemplate('BrandcoFooter.php', $data['pageStatus'])); ?>