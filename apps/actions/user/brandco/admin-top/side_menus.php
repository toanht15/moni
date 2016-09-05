<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
class side_menus extends BrandcoGETActionBase {

    public $NeedOption = array();
	protected $ContainerName = 'update_side_menu';
	const limit = 20;

    public function beforeValidate() {
        $this->deleteErrorSession();
    }

	public function validate () {
		return true;
	}

	function doAction() {
		$brand_side_menu_service = $this->createService('BrandSideMenuService');
		$brand = $this->getBrand();
		$sideMenus = $brand_side_menu_service->getMenusByBrandId($brand->id);

        if($sideMenus) {
            $sideMenusArray = $sideMenus->toArray();
            if ($form = $this->getActionContainer('ValidateError')) {
                for ($i = 0; $i < count($sideMenusArray); $i++) {
                    $sideMenusArray[$i]->link = $form['link_'.$sideMenusArray[$i]->id];
                    $sideMenusArray[$i]->name = $form['title_'.$sideMenusArray[$i]->id];
                }
            }
			$this->assign('sideMenus', $sideMenusArray);
		}

		$this->Data['sideMenusTitle'] = $brand->side_menu_title;
		$this->Data['sideMenusType'] = $brand->side_menu_title_type;
		$this->Data['limit'] = self::limit;
		
		return 'user/brandco/admin-top/side_menus.php';
	}
}