<?php
AAFW::import('jp.aainc.lib.base.aafwServiceBase');

class MenuService extends aafwServiceBase {
    protected $menus;

    public function __construct() {
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
    }

    public function getMenusByBrandId($brandId) {
        $filter = array(
            'conditions' => array(
                'brand_id' => $brandId,
            ),
            'order' => array(
                'name' => 'list_order',
                'direction' => 'asc',
            ),
        );

        return $this->menus->find($filter);
    }

    public function getDisplayMenuByBrandId($brandId) {
        $filter = array(
            'conditions' => array(
                'brand_id' => $brandId,
                'hidden_flg' => 0
            ),
            'order' => array(
                'name' => 'list_order',
                'direction' => 'asc',
            ),
        );

        return $this->menus->find($filter);
    }

    public function getEntryById($id){
        $filter = array(
            'conditions' => array(
                'id' => $id
            )
        );
        return $this->menus->findOne($filter);
    }

    public function getMenuByBrandIdAndMenuId($brandId, $menuId) {
        if(is_null($brandId)) return;
        if(is_null($menuId)) return;
        $filter = array(
            'conditions' => array(
                'id' => $menuId,
                'brand_id' => $brandId,
            ),
        );

        return $this->menus->findOne($filter);
    }

    public function getMaxOrder($brandId) {
        $filter = array(
            'conditions' => array(
                'brand_id' => $brandId,
            ),
        );

        return $this->menus->getMax('list_order', $filter);
    }

    public function createEmptyMenu() {
        return $this->menus->createEmptyObject();
    }

    public function createMenu($menu) {
        $this->menus->save($menu);
    }

    public function updateMenu($menu) {
        $this->menus->save($menu);
    }

    public function deleteMenu($menu) {
        $this->menus->delete($menu);
    }

    public function deletePhysicalMenu($menu) {
        $this->menus->deletePhysical($menu);
    }

    /**
     * @param $brand_id
     * @return mixed
     */
    public function getAllMenus($brand_id) {
        $filter = array(
            'conditions' => array(
                'brand_id' => $brand_id,
            ),
        );

        return $this->menus->find($filter);
    }

    /**
     * @param $brand_id
     */
    public function deletePhysicalAllMenus($brand_id) {
        $menus = $this->getAllMenus($brand_id);
        foreach($menus as $menu) {
            $this->deletePhysicalMenu($menu);
        }

    }
}
