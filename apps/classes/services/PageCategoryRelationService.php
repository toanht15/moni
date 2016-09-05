<?php
AAFW::import('jp.aainc.lib.base.aafwServiceBase');

/**
 * 記事が属するカテゴリーの情報を取得
 * Class PageCategoryRelationService
 */
class PageCategoryRelationService extends aafwServiceBase {

    /**
     * 記事のカテゴリー情報を取得する
     * @param $static_html_entry_id
     * @return array
     */
    public function getCategoriesInfo($static_html_entry_id) {
        $db = new aafwDataBuilder();

        $condition = array(
            "static_html_entry_id" => $static_html_entry_id
        );

        $categories = $db->getCategoriesInfoByStaticHtmlEntryId($condition, array(), null, false, 'StaticHtmlCategory');
        $categories_info = array();

        foreach ($categories as $category) {
            $categories_info[] = $this->getCategoryInfoWithParentCategory($category);
        }

        return $this->filterDuplicateCategories($categories_info);
    }

    /**
     * カテゴリーのより高い階層がある場合は取得する
     * @param $category
     * @return array
     */
    private function getCategoryInfoWithParentCategory($category) {
        /** @var StaticHtmlCategoryService $static_html_category_service */
        $static_html_category_service = $this->getService('StaticHtmlCategoryService');

        $father = $static_html_category_service->getParentOfCategory($category->id);
        if ($father) {
            $grand_father = $static_html_category_service->getParentOfCategory($father->id);

            if ($grand_father) {
                return array(
                    'id' => $grand_father->id,
                    'name' => $grand_father->name,
                    'url' => $static_html_category_service->getUrlByCategory($grand_father),
                    'order_no' => $grand_father->order_no,
                    'categories' => array(
                        array(
                            'id' => $father->id,
                            'name' => $father->name,
                            'url' => $static_html_category_service->getUrlByCategory($father),
                            'order_no' => $father->order_no,
                            'categories' => array(
                                array(
                                    'id' => $category->id,
                                    'name' => $category->name,
                                    'url' => $static_html_category_service->getUrlByCategory($category),
                                    'order_no' => $category->order_no
                                )
                            )
                        )
                    )
                );
            } else {
                return array(
                    'id' => $father->id,
                    'name' => $father->name,
                    'url' => $static_html_category_service->getUrlByCategory($father),
                    'order_no' => $father->order_no,
                    'categories' => array(
                        array(
                            'id' => $category->id,
                            'name' => $category->name,
                            'url' => $static_html_category_service->getUrlByCategory($category),
                            'order_no' => $category->order_no
                        )
                    )
                );
            }
        } else {
            return array(
                'id' => $category->id,
                'name' => $category->name,
                'url' => $static_html_category_service->getUrlByCategory($category),
                'order_no' => $category->order_no
            );
        }
    }

    /**
     * 複製カテゴリーをマージする
     * @param $categories
     * @return array
     */
    private function filterDuplicateCategories($categories) {
        $filter_categories = array();

        foreach ($categories as $key => $category) {
            if (!$filter_categories[$category['id']]) {
                $filter_categories[$category['id']] = $category;
            } else {
                $filter_categories[$category['id']]['categories'] = $this->mergeCategories($filter_categories[$category['id']]['categories'], $category['categories'][0]);
            }
        }

        return $this->orderCategories(array_values($filter_categories));
    }

    /**
     * @param $categories
     * @param $target_category
     * @return array|null
     */
    private function mergeCategories($categories, $target_category) {
        if (!$categories && !$target_category) {
            return null;
        }

        if (!$categories) {
            return array($target_category);
        }

        if (!$target_category) {
            return $categories;
        }

        foreach ($categories as $key => $category) {
            if ($category['id'] == $target_category['id']) {
                $categories[$key]['categories'] = $this->mergeCategories($categories[$key]['categories'], $target_category['categories'][0]);
                $is_merged = true;
                break;
            }
        }

        if (!$is_merged) {
            $categories[] = $target_category;
        }

        return $categories;
    }

    /**
     * @param $target_categories
     * @return mixed
     */
    private function orderCategories($target_categories) {
        usort($target_categories, function ($category1, $category2) {
            return $category1['order_no'] > $category2['order_no'];
        });

        $result = array();
        foreach ($target_categories as $category) {
            //if have child categories, sort child categories
            if (isset($category['categories'])) {
                $category['categories'] = $this->orderCategories($category['categories']);
            }
            unset($category['order_no']);
            $result[] = $category;
        }

        return $result;
    }
}