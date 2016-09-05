<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');
class create_side_menu extends BrandcoPOSTActionBase {
	protected $ContainerName = 'create_side_menu';
	protected $Form = array (
		'package' => 'admin-top',
		'action' => 'create_side_menu_form',
	);

    public $NeedOption = array();
	public $NeedAdminLogin = true;
	public $CsrfProtect = true;

	protected $ValidatorDefinition = array(
		'name' => array(
			'required' => true,
			'type' => 'str',
			'length' => 35,
		),
		'image_url' => array(
			'type' => 'str',
			'length' => 255,
			'validator' => array('URL')
		),
		'link' => array(
			'required' => true,
			'type' => 'str',
			'length' => 255,
			'validator' => array('URL')
		),
	);

	public function validate () {
		return true;
	}

	function doAction() {
		$brand_side_menu_service = $this->createService('BrandSideMenuService');
		$brand = $this->getBrand();
		$sideMenu = $brand_side_menu_service->createEmptyMenu();
		$sideMenu->brand_id = $brand->id;
		$sideMenu->list_order = $brand_side_menu_service->getMaxOrder($brand->id) + 1; // 末尾に追加する
		$sideMenu->name = $this->name;
		$sideMenu->link = $this->link;
		$sideMenu->image_url = $this->image_url;
		$sideMenu->hidden_flg = $this->display;
		if($this->is_blank_flg == 'x'){
			$sideMenu->is_blank_flg = '1';
		}else $sideMenu->is_blank_flg = '0';

		$brand_side_menu_service->createMenu($sideMenu);

		$this->menuId = $sideMenu->id;

		if($this->Validator->getErrorCount()) {
			$return = $this->getFormURL();
		} else{
            $this->Data['saved'] = 1;
			$return = 'redirect: '.Util::rewriteUrl('admin-top', 'side_menus');
		}
		return $return;
	}
}