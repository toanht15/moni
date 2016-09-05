<?php
AAFW::import('jp.aainc.lib.base.aafwServiceBase');

class SqlSelectorsCategoriesRelationService extends aafwServiceBase {

    protected $sql_selectors_categories_relation;

    public function __construct() {
        $this->sql_selectors_categories_relation = $this->getModel('SqlSelectorsCategoriesRelations');
    }
    
    public function getSqlRelationsBySqlCategoryId($sql_category_id) {
        $filter = array(
            'conditions' => array(
                'sql_category_id' => $sql_category_id,
            ),
            'order' => array(
                'id' => 'asc',
            ),
        );
        return $this->sql_selectors_categories_relation->find($filter);
    }
}
