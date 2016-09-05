<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
class global_menus extends BrandcoGETActionBase {
    protected $ContainerName = 'update_global_menu';
    public $NeedOption = array();

    public function beforeValidate() {
        $this->deleteErrorSession();
    }

	public function validate() {
		return true;
	}

	function doAction() {
		
		$brand_global_menu_service = $this->createService('BrandGlobalMenuService');
		$brand = $this->getBrand();
		$globalMenus = $brand_global_menu_service->getMenusByBrandId($brand->id);

        if ($globalMenus) {

            $globalMenusArray = $globalMenus->toArray();
            if ($form = $this->getActionContainer('ValidateError')) {
                for ($i = 0; $i < count($globalMenusArray); $i++) {
                    $globalMenusArray[$i]->link = $form['link_'.$globalMenusArray[$i]->id];
                    $globalMenusArray[$i]->name = $form['title_'.$globalMenusArray[$i]->id];
                }
            }
			$this->assign('globalMenus', $globalMenusArray);
		}

		return 'user/brandco/admin-top/global_menus.php';
	}
}