<?php
AAFW::import('jp.aainc.classes.cms_tree.CmsTree');

class CmsCategory extends CmsTree {

    private $name;
    private $url;
    private $categories = array();
    private $page_count = 0;

    public function __construct($name, $url){
        $this->name = $name;
        $this->url = $url;
    }

    function getName() {
        // TODO: Implement getName() method.
        return $this->name;
    }

    /**
     * ディレクトリツリーを表示する
     */
    function printTree() {
        // TODO: Implement printTree() method.
        if ($this->getPageCount()){
            write_html('<a href="' . $this->getUrl() .'">');
            write_html($this->getName() . "(" . $this->getPageCount() . ")");
            write_html('</a>');
        }else{
            write_html('<span class="noPage">' . $this->getName() . "(" . $this->getPageCount() . ")" .'</span>');
        }
    }

    public function add(CmsCategory $cms_category){
        array_push($this->categories, $cms_category);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPageCount() {
        return $this->page_count;
    }

    /**
     * @param mixed $page_count
     */
    public function setPageCount($page_count) {
        $this->page_count = $page_count;
    }

    /**
     * @return array
     */
    public function getCategories() {
        return $this->categories;
    }

    /**
     * @return mixed
     */
    public function getUrl() {
        return $this->url;
    }
}
