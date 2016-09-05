<?php
AAFW::import('jp.aainc.lib.base.aafwServiceBase');

class SqlCategoryService extends aafwServiceBase
{
    protected $sql_category;

    public function __construct() {
        $this->sql_category = $this->getModel('SqlCategories');
    }

    public function getCategories() {
        $filter = array(
            'order' => array(
                'id' => 'asc',
            ),
        );

        return $this->sql_category->find($filter);
    }
}
