<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoManagerGETActionBase');
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');

class index extends BrandcoManagerGETActionBase {

    public $NeedManagerLogin = true;
    public $ManagerPageId    = Manager::MENU_SQL_SELECTOR;

    public function doThisFirst() {
        $this->Data['GET'] = $this->GET;
        $this->Data['limit'] = 50;
    }

    public function validate() {
        return true;
    }

    public function doAction() {

        /** @var SqlSelectorService $sql_selector_service */
        $sql_selector_service = $this->createService('SqlSelectorService');
        /** @var SqlCategoryService $sql_category_service */
        $sql_category_service = $this->createService('SqlCategoryService');
        /** @var SqlSelectorsCategoriesRelationService $sql_selectors_categories_relation */
        $sql_selectors_categories_relation = $this->createService('SqlSelectorsCategoriesRelationService');
        $sql_categories = $sql_category_service->getCategories();

        $sql_selectors = array();
        foreach ($sql_categories as $sql_category) {
            $sql_selectors[$sql_category->id]['name'] = $sql_category->name;

            $sql_relations = $sql_selectors_categories_relation->getSqlRelationsBySqlCategoryId($sql_category->id);
            foreach ($sql_relations as $sql_relation) {
                $sql_selectors[$sql_category->id]['selectors'][] = $sql_selector_service->getSqlSelectorById($sql_relation->sql_selector_id);
            }
        }
        $this->Data['sql_selectors'] = $sql_selectors;

        return 'manager/sql_selector/index.php';
    }
}
