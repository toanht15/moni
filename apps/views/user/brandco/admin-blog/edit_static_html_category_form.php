<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoHeader')->render($data['pageStatus'])) ?>
<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoAccountHeader')->render($data['pageStatus'])) ?>

<article>
    <?php write_html($this->parseTemplate('AdminBlogHeader.php')) ?>

    <h1 class="hd1">カテゴリ作成</h1>

    <form id="addCategoryForm" name="addCategoryForm" action="<?php  assign(Util::rewriteUrl( 'admin-blog', 'edit_static_html_category' )); ?>" method="POST" enctype="multipart/form-data">
    <?php write_html($this->formHidden('id', PHPParser::ACTION_FORM)) ?>
    <?php write_html($this->csrf_tag()); ?>
    <dl class="categoryEdit1">
        <dt><label for="field1">カテゴリ名</label></dt>
        <dd>
            <?php write_html($this->formText('name', PHPParser::ACTION_FORM, array('class'=>'categoryName', 'id'=>'field1', 'maxlength'=>35))) ?>
            <small class="textLimit">（<span>0</span>文字／35文字）</small>
            <?php if ($this->ActionError && !$this->ActionError->isValid('name')): ?>
                <p class="iconError1"><?php assign($this->ActionError->getMessage('name')) ?></p>
            <?php endif; ?>
        </dd>
        <dt><label for="field2">ディレクトリ名</label></dt>
        <dd><?php write_html('<a id="folder_name">'.$data['path'].'</a>'.$this->formText('directory', PHPParser::ACTION_FORM, array('class'=>'directoryName', 'id'=>'field2','maxlength'=>35))) ?></dd>
        <p><small>※未設定の場合は自動的にカテゴリー名が割り振られます。</small></p>
        <?php if ($this->ActionError && !$this->ActionError->isValid('directory')): ?>
            <p class="iconError1"><?php assign($this->ActionError->getMessage('directory')) ?></p>
        <?php endif; ?>
        <dt>親カテゴリ</dt>
        <dd>
            <?php write_html($this->parseTemplate('TagTreeSelection.php', array('categories_tree'=>$data['categories_tree'], 'father_category' => $data['father_id'], 'current_category_id' => $data['category']->id))) ?>
        </dd>
        <dt>デザイン</dt>
        <dd class="jsCheckToggleWrap">
            <label>
                <?php write_html($this->formCheckbox('is_use_customize', array($this->getActionFormValue('is_use_customize')), array('class'=>'jsCheckToggle'), array('1'=>'カスタマイズ'))) ?>
            </label><span class="btn2"><a href="javascript:void(0)" class="small1" id="categories_preview">プレビュー</a></span>
            <div class="viewCustom jsCheckToggleTarget">
                <?php write_html($this->formTextarea('customize_code', PHPParser::ACTION_FORM, array('cols' => '40', 'rows' => '4'))); ?>
                <?php if ($this->ActionError && !$this->ActionError->isValid('customize_code')): ?>
                    <p class="iconError1"><?php assign($this->ActionError->getMessage('customize_code')) ?></p>
                <?php endif; ?>
                <?php /**
                %title%: カテゴリーのタイトル</br>
                %page_image_url%: ページの画像リンク</br>
                %page_url%: ページリンク</br>
                %page_description%: ページの文書</br>
                %page_title%: ページのタイトル</br>
                %page_pub_date%: ページ公開日付</br>
                %loop_start%: ループスタート</br>
                %loop_end%: ループ終わり</br>
                **/
                ?>
            </div>
        </dd>
        <dt>SNSプラグインの表示</dt>
        <dd>
            <ul>
                <?php foreach (StaticHtmlEntries::$sns_plugins as $sns_plugin_id => $sns_plugin_label): ?>
                    <li><input type="checkbox" name="sns_plugins[]" value="<?php assign($sns_plugin_id); ?>" <?php assign(in_array($sns_plugin_id, $this->getActionFormValue('sns_plugins')) ? "checked=checked" : "" ); ?>><?php assign($sns_plugin_label) ?></li>
                <?php endforeach; ?>

                <?php if ($data['category']->sns_plugin_tag_text): ?>
                    <li><textarea name="sns_plugin_tag_text"  cols="30" rows="2"><?php assign($data['category']->sns_plugin_tag_text); ?></textarea></li>
                <?php else: ?>
                    <li><a href="javascript:void(0);" class="linkAdd" id="snsScriptAdd">追加</a></li>
                    <li style="display: none" id="snsScriptText"><textarea name="sns_plugin_tag_text" cols="30" rows="2"></textarea></li>
                <?php endif; ?>
            </ul>
        </dd>
        <dt>meta情報</dt>
        <dd>
            <dl>
                <dt>title</dt>
                <dd>
                    <?php write_html($this->formTextarea('title', PHPParser::ACTION_FORM, array('cols' => '30', 'rows' => '2', 'class' => 'metaText'))) ?>
                    <?php if ($this->ActionError && !$this->ActionError->isValid('title')): ?>
                        <p class="iconError1"><?php assign($this->ActionError->getMessage('title')) ?></p>
                    <?php endif; ?>
                </dd>
                <dt>description</dt>
                <dd>
                    <?php write_html($this->formTextarea('description', PHPParser::ACTION_FORM, array('cols' => '30', 'rows' => '2', 'class' => 'metaText'))) ?>
                    <?php if ($this->ActionError && !$this->ActionError->isValid('description')): ?>
                        <p class="iconError1"><?php assign($this->ActionError->getMessage('description')) ?></p>
                    <?php endif; ?>
                </dd>
                <dt>keyword(,カンマ区切り)</dt>
                <dd>
                    <?php write_html($this->formTextarea('keyword', PHPParser::ACTION_FORM, array('cols' => '30', 'rows' => '2', 'class' => 'metaText'))) ?>
                    <?php if ($this->ActionError && !$this->ActionError->isValid('keyword')): ?>
                        <p class="iconError1"><?php assign($this->ActionError->getMessage('keyword')) ?></p>
                    <?php endif; ?>
                </dd>
                <dt>OG Image</dt>
                <dd>
                    <p><img <?php write_html( $this->ActionForm['og_image_url'] ? "" : "style='display:none'" );?> src="<?php assign($this->ActionForm['og_image_url'])?>" id="ogImage" width="214"></p>
                    <input type="file" name="og_image" class="actionImage">
                </dd>
            </dl>
        </dd>
        <!-- /.categoryEdit1 --></dl>
    </form>

    <div class="categoryEditCheck">
        <ul>
            <li class="btn3"><a href="javascript:void(0)" id="save_category">保存</a></li>
        </ul>
        <!-- /.categoryEditCheck --></div>

    <section class="backPage">
        <p><a href="<?php write_html(Util::rewriteUrl('admin-blog', 'static_html_categories')) ?>" class="iconPrev1">カテゴリ一覧へ</a></p>
        <!-- /.backPage --></section>
</article>
<script type="text/javascript" src="<?php assign($this->setVersion('/ckeditor/ckeditor.js')) ?>"></script>
<?php write_html($this->parseTemplate('BrandcoFooter.php', array_merge($data['pageStatus'], array('script'=>array('admin-blog/CreateCategoryService'))))); ?>