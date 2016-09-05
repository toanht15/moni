<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoHeader')->render($data['pageStatus'])) ?>
<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoAccountHeader')->render($data['pageStatus'])) ?>

    <div class="adminWrap">
        <?php write_html($this->parseTemplate('SettingSiteMenu.php', $data['pageStatus'])) ?>

        <article class="adminMainCol">
            <h1 class="hd1">コンバージョンタグ作成</h1>

            <h2 class="hd2"><?php if($data['conversion_id']):?>編集<?php else:?>新規作成<?php endif;?></h2>

            <form id="editConversion" name="editConversion"
                  action="<?php assign(Util::rewriteUrl('admin-settings', 'edit_conversion')); ?>" method="POST">
                <?php write_html($this->csrf_tag()); ?>
                <?php write_html($this->formHidden('conversion_id', $data['conversion_id'])); ?>
                <section class="adminCvtagWrap">
                    <dl class="adminCvtagSetting">
                        <dt class="require1">タグ名
                          <span class="iconHelp">
                              <span class="text">ヘルプ</span>
                              <span class="textBalloon1">
                                <span>
                                  ファン一覧での項目名に使用されます
                                </span>
                              <!-- /.textBalloon1 --></span>
                            <!-- /.iconHelp --></span>
                        </dt>
                        <dd><?php write_html($this->formText('name', PHPParser::ACTION_FORM, array(), array('maxlength'=>50))) ?>
                            <?php if ($this->ActionError && !$this->ActionError->isValid('name')): ?>
                                <p class="attention1"><?php assign($this->ActionError->getMessage('name')) ?></p>
                            <?php endif; ?></dd>
                        <dt>タグの説明
                            <span class="iconHelp">
                                <span class="text"></span>
                                <span class="textBalloon1">
                                  <span>
                                    管理用メモのため、一般公開されません
                                  </span>
                                <!-- /.textBalloon1 --></span>
                          <!-- /.iconHelp --></span>
                        </dt>
                        <dd><?php write_html($this->formTextArea('description', PHPParser::ACTION_FORM)) ?></dd>
                        <!-- /.adminCvtagSetting --></dl>
                    <p class="btnSet"><span class="btn3"><a href="javascript:void(0)" id="save_conversion"><?php if ($this->ActionForm['id']) assign('更新'); else assign('作成'); ?></a></span></p>
                    <!-- /.adminCvtagWrap --></section>
            </form>

            <?php if($this->ActionForm['id']): ?>
                <h2 class="hd2">作成されたコンバージョンタグ</h2>
                <section class="adminCvtagWrap" id="data-container" data-brand-id="<?php assign($data['pageStatus']['brand']->id) ?>"
                         data-conversion-id="<?php assign($this->ActionForm['id']) ?>"
                         data-tracker-domain="<?php assign(aafwApplicationConfig::getInstance()->query('Domain.brandco_tracker')) ?>"
                         data-static-track-domain="<?php assign(aafwApplicationConfig::getInstance()->query('Domain.brandco_static_track')) ?>">

                    <dl class="adminCvtagItem">
                        <dt>カート会社</dt>
                        <dd>
                            <select name="cart_type" id="cartSelector">
                                <option value="0">その他のカート</option>
                                <?php foreach($data['cart_types'] as $key=>$cart): ?>
                                    <option value="<?php assign($key) ?>" <?php if($key == $data['cart_setting']) assign('selected')?> ><?php assign($cart) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </dd>
                        <dt>埋め込みタグ</dt>
                        <dd><textarea id="js_tag" cols="30" rows="5" class="focusText1 mTop10" onclick="this.focus();this.select();" readonly="" style="margin: 10px 0px 0px; height: 374px; width: 728px;"></textarea></dd>

                        <dd>上記のタグをコピーして自社サイトのコンバージョン測定する箇所に貼り付けてください。</dd>
                        <dd>order_no等の引数は半角のみ有効です。全角を入れた場合は正常に保存されない可能性があります。</dd>
                        <!-- /.adminCvtagItem --></dl>
                    <!-- /h3 section --></section>
            <?php endif; ?>
            <ul class="pager2">
                <li class="prev"><a href="<?php assign(Util::rewriteUrl('admin-settings', 'conversion_setting_form')) ?>" class="iconPrev1">タグ一覧へ</a></li>
                <!-- /.pager2 --></ul>
        </article>
    </div>

<?php $param = array_merge($data['pageStatus'], array('script' => array('admin-settings/ConversionSettingsFormService'))) ?>
<?php write_html($this->parseTemplate('BrandcoFooter.php', $param)); ?>