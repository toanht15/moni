<?php
AAFW::import('jp.aainc.classes.cms_tree.CmsCategory');
AAFW::import('jp.aainc.aafw.factory.aafwServiceFactory');

class CmsTreeCreator {

    private static $instance;
    private $top_categories;
    private $tree = array();
    /** @var StaticHtmlCategoryService $static_html_category_service*/
    private $static_html_category_service;
    /** @var StaticHtmlEntryService $static_html_entry_service*/
    private $static_html_entry_service;

    private function __construct(){}

    public static function getInstance($top_categories){
        if (self::$instance == null) self::$instance = new CmsTreeCreator();
        self::$instance->top_categories = $top_categories;

        $service_factory = new aafwServiceFactory();
        self::$instance->static_html_category_service = $service_factory->create('StaticHtmlCategoryService');
        self::$instance->static_html_entry_service = $service_factory->create('StaticHtmlEntryService');

        return self::$instance;
    }

    /**
     * 親カテゴリーからツリー構造作成する
     */
    public function create() {
        $this->tree = array();

        foreach ($this->top_categories as $top_category) {
            // カテゴリー作成
            $url = $this->static_html_category_service->getUrlByCategory($top_category);
            $tree_category = new CmsCategory($top_category->name, $url);
            $this->createTree($tree_category, $top_category);
            array_push($this->tree, $tree_category);
        }
        return $this->tree;
    }

    /**
     * 子以降を再帰的に取得する
     * @param CmsCategory $tree_category
     * @param $category
     */
    function createTree(CmsCategory $tree_category, $category){
        // 紐づくページを取得
        $posts = $this->static_html_category_service->getAllPostByCategoryId($category->id);
        if (count($posts)){
            $post_count = $this->static_html_entry_service->countPublicEntryByIds($posts);
            $tree_category->setPageCount($post_count);
        }

        // 子カテゴリー取得
        $children = $this->static_html_category_service->getAllChildrenOfCategory($category->id);
        if (count($children) > 0) {
            $child_tree_category = null;
            foreach($children as $child_id){
                $category = $this->static_html_category_service->getCategoryById($child_id);
                $url = $this->static_html_category_service->getUrlByCategory($category);
                $child_tree_category = new CmsCategory($category->name, $url);
                $tree_category->add($child_tree_category);
                $this->createTree($child_tree_category, $category);
            }
        }
    }

    /**
     * 親カテゴリを表示する
     * PC の場合は<span>を表示しない
     * @param $cms_categories
     */
    public function writeHtml($cms_categories) {
        foreach($cms_categories as $cms_category) {
            if ( Util::isSmartPhone()) {
                write_html('<li>');
                write_html('<span>' . $cms_category->getName() . '(' . $cms_category->getPageCount() . ')</span>');
                if ($cms_category->getPageCount()){
                    write_html('<ul class="sideNaviInner"><li>');
                    $this->writeHtmlCategory($cms_category);
                    write_html('</li></ul>');
                }
                write_html('</li>');
            }else{
                write_html('<ul class="sideNaviInner"><li>');
                $this->writeHtmlCategory($cms_category);
                write_html('</li></ul>');
            }
        }
    }

    /**
     * 子カテゴリ以降を再帰的にカテゴリを表示する
     * PC の場合は<span>を表示しない
     * 子供以降は<span>を表示しない
     * @param $cms_categories
     */
    public function writeHtmlChildren($cms_categories) {
        foreach($cms_categories as $cms_category) {
            write_html('<ul><li>');
            $this->writeHtmlCategory($cms_category);
            write_html('</li></ul>');
        }
    }

    /**
     * 親カテゴリーの表示
     * @param CmsCategory $cms_category
     */
    public function writeHtmlCategory(CmsCategory $cms_category){
        $cms_category->printTree();
        if (count($cms_category->getCategories())){
            $this->writeHtmlChildren($cms_category->getCategories());
        }
    }

    /**
     * @return array
     */
    public function getTree() {
        return $this->tree;
    }

}