<?php
function drawTagsSeletion ($tree, $static_html_category_service, $father_category = null, $current_id = null) {
    if (!$tree) {
        return;
    }elseif(is_array($tree)) {
        foreach ($tree as $parent=>$children) {
            $category = $static_html_category_service->getCategoryById($parent);
            if ($category->depth >= 2 || ($current_id && $category->id == $current_id)) continue;
            write_html('<option value="'.$category->id.'" data-directory = "'.$static_html_category_service->getDirectoryByCategory($category).'"');
            if ($father_category && $father_category == $category->id) {
                write_html(' selected');
            }
            write_html('>'.drawSpace($category->depth). Util::cutTextByWidth($category->name,195));
            write_html('</option>');
            drawTagsSeletion($children, $static_html_category_service, $father_category, $current_id);
        }
    }
}
function drawSpace($number) {
    $space = '';
    for($i=0; $i<$number; $i++) {
        $space .= '&nbsp&nbsp&nbsp';
    }
    return $space;
}
?>

<select name="parent_id" id="category_selection" data-base-folder = "<?php assign(Util::rewriteUrl('', 'categories')) ?>">
    <option value="0" data-directory="">なし</option>
    <?php
    $service_factory = new aafwServiceFactory();
    /** @var StaticHtmlCategoryService $static_html_tag_service */
    $static_html_category_service = $service_factory->create('StaticHtmlCategoryService');
    drawTagsSeletion($data['categories_tree'], $static_html_category_service, $data['father_category'], $data['current_category_id']);
    ?>
</select>