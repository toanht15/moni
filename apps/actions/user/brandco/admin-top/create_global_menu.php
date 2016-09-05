<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');
class create_global_menu extends BrandcoPOSTActionBase {
	protected $ContainerName = 'create_global_menu';
    public $NeedOption = array();
	protected $Form = array (
		'package' => 'admin-top',
		'action' => 'create_global_menu_form',
	);

	public $NeedAdminLogin = true;
	public $CsrfProtect = true;

	protected $ValidatorDefinition = array(
		'name' => array(
			'required' => true,
			'type' => 'str',
			'length' => 35,
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
		$brand_global_menu_service = $this->createService('BrandGlobalMenuService');
		$brand = $this->getBrand();
		$globalMenu = $brand_global_menu_service->createEmptyMenu();
		$globalMenu->brand_id = $brand->id;
		$globalMenu->list_order = $brand_global_menu_service->getMaxOrder($brand->id) + 1; // 末尾に追加する
		$globalMenu->name = $this->name;
		$globalMenu->link = $this->link;
		$globalMenu->hidden_flg = $this->display;
		if($this->is_blank_flg == 'x'){
			$globalMenu->is_blank_flg = '1';
		}else {
			$globalMenu->is_blank_flg = '0';
		}

		$brand_global_menu_service->createMenu($globalMenu);

		$this->menuId = $globalMenu->id;

		if($this->Validator->getErrorCount()) {
			$return = $this->getFormURL();
		} else{
            $this->Data['saved'] = 1;
			$return = 'redirect: '.Util::rewriteUrl('admin-top', 'global_menus');
		}
		return $return;
	}
}