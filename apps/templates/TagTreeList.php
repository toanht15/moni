<?php

function drawTagsTree($tree, $static_html_category_service, $type, $categories_id, $isChild = false) {
    if (!$tree) {
        return;
    } elseif (is_array($tree)) {
        if ($isChild) {
            write_html('<ul>');
        }
        foreach ($tree as $parent => $children) {
            $category = $static_html_category_service->getCategoryById($parent);
            write_html('<li data-category-id="'.$category->id.'" ><label>');
            if ($type == StaticHtmlCategory::DISPLAY_LIST_TYPE_CHECKBOX) {
                write_html('<input name = "category_'.$category->id.'" type="checkbox"');
                if (in_array($category->id, $categories_id)) {
                    write_html(' checked');
                }
                write_html('>');
            }
            write_html(Util::cutTextByWidth($category->name, 195) . '</label></li>');
            if (is_array($children)) {
                drawTagsTree($children, $static_html_category_service, $type, $categories_id, true);
            }
        }
        if ($isChild) {
            write_html('</ul>');
        }
    }
}

$service_factory = new aafwServiceFactory();
/** @var StaticHtmlCategoryService $static_html_tag_service */
$static_html_category_service = $service_factory->create('StaticHtmlCategoryService');
drawTagsTree($data['categories_tree'], $static_html_category_service, $data['type'], $data['categories_id']);
