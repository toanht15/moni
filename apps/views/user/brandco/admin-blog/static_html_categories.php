<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoHeader')->render($data['pageStatus'])) ?>
<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoAccountHeader')->render($data['pageStatus'])) ?>

<?php
function drawTagsTree($tree, $flag = 0, $static_html_category_service) {
    if (!$tree) {
        return;
    } elseif (is_array($tree)) {
        foreach ($tree as $parent => $children) {
            $category = $static_html_category_service->getCategoryById($parent);
            write_html('<li data-category-id="' . $category->id . '" class="categoryEntry">' . '<p class="category1">');
            write_html('<span class="categoryMove">順番を入れ替える</span><a href="' . Util::rewriteUrl('admin-blog', 'edit_static_html_category_form', array($category->id)) . '" id="category_name_' . $category->id . '">' . $category->name . '</a>');
            write_html('<span class="directory" id="directory_' . $category->id . '" data-directory="' . $category->directory . '">' . '/' . $category->directory . '/</span>');
            write_html('<span class="categoryAction">');
            if ($category->depth < 2) {
                write_html('<a href="javascript:void(0)" class="iconBtnAdd">子カテゴリを追加する</a>');
            } else {
                write_html('<a href="javascript:void(0)" class="iconBtnAdd" style="display: none">子カテゴリを追加する</a>');
            }
            write_html('<a href="javascript:void(0)" class="iconBtnNonDisplay">カテゴリを削除する</a></span>');
            write_html('<!-- /.category1 --></p>');
            write_html('<ul class="categoryList1">');
            write_html('<li>
            <p class="categoryNew" style="display: none" id="category2_' . $category->id . '">
                  <input type="text" name="name" placeholder="カテゴリ名">
                  <input type="text" name="directory" placeholder="ディレクトリ名">
                  <span class="btn3"><a href="javascript:void(0)" class="small1 submitCategory">追加する</a></span>
                  <span class="categoryAction"><a href="javascript:void(0)" class="iconBtnDelete jsCategoryCancel">キャンセル</a></span>
              <!-- /.categoryNew --></p></li>
        ');
            if (is_array($children)) {
                drawTagsTree($children, 0, $static_html_category_service);
            }
            write_html('<!-- /.categoryList1 --></ul>');
            write_html('</li>');
        }
    }
}

?>

<article>
    <?php write_html($this->parseTemplate('AdminBlogHeader.php',array('can_use_embed_page' => $data['can_use_embed_page']))) ?>
    <h1 class="hd1">カテゴリ<span class="btn3"><a href="javascript:void(0)" class="small1 jsCategoryNew">新規作成</a></span></h1>
    <p style="margin-bottom:30px;">
        <?php write_html($this->formCheckBox('category_navi_top_display_flg',array($this->getActionFormValue('category_navi_top_display_flg')), array('id' => 'category_navi_top_display_flg'), array('1' => 'カテゴリー一覧をトップページに表示する'))) ?>
    </p>
        <?php write_html($this->csrf_tag()); ?>
        <ul class="categoryList1 categoriesTree">
            <?php
            $service_factory = new aafwServiceFactory();
            /** @var StaticHtmlCategoryService $static_html_tag_service */
            $static_html_category_service = $service_factory->create('StaticHtmlCategoryService');
            drawTagsTree($data['categories_tree'], 1, $static_html_category_service);
            ?>
        </ul>
    <div class="categoryEditCheck">
        <ul>
            <li class="btn3"><a href="javascript:void(0)" class="save_all">保存</a></li>
        </ul>
    <!-- /.categoryEditCheck --></div>
</article>

<div class="modal1 jsModal" id="modal1">
    <section class="modalCont-small jsModalCont">
        <h1>本当に削除しますか？</h1>
        <p id="deleteMessage"></p>
        <p class="btnSet">
            <span class="btn2"><a href="#closeModal" class="small1">キャンセル</a></span>
            <span class="btn4"><a href="javascript:void(0)" class="small1" id="deleteCategoryButton">削除</a></span>
        </p>
    </section>
<!-- /.modal1 --></div>

<div class="modal2 jsModal" id="modal2">
    <section class="modalCont-small jsModalCont">
        <h1 id="errorHeader" class="iconError1"></h1>
        <p id="errorMessage"></p>
        <p class="btnSet">
            <span class="btn2"><a href="javascript: location.reload();" class="small1">OK</a></span>
        </p>
    </section>
    <!-- /.modal1 --></div>

<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
<script type="text/javascript" src="<?php assign($this->setVersion('/js/jquery.mjs.nestedSortable.js')) ?>"></script>

<?php write_html($this->parseTemplate('BrandcoFooter.php', array_merge($data['pageStatus'], array('script' => array('admin-blog/StaticHtmlCategoriesService'))))); ?>
