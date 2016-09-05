<?php
AAFW::import('jp.aainc.classes.cms_tree.CmsTrees');

class CmsPage extends CmsTree {

    private $name;
    private $url;

    public function __construct($name, $url){
        $this->name = $name;
        $this->url = $url;
    }

    function getName() {
        // TODO: Implement getName() method.
        return $this->name;
    }

    function getPageCount() {
        // TODO: Implement getPageNum() method.
    }

    function printTree() {
        // TODO: Implement printTree() method.
    }

    /**
     * @return mixed
     */
    public function getUrl() {
        return $this->url;
    }
}