<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');
class update_global_menu extends BrandcoPOSTActionBase {

	protected $ContainerName = 'update_global_menu';
	protected $Form = array(
		'package' => 'admin-top',
		'action' => 'global_menus',
	);

    public $NeedOption = array();
	public $NeedAdminLogin = true;
	public $CsrfProtect = true;

    protected $ValidatorDefinition = array(
        'order' => array(
            'type' => 'str'
        )
    );

	public function validate() {
		$split_ids = explode(",", $this->order);
		$menu_ids = array();
        $this->Data['brand'] = $this->getBrand();
        $idValidation = new StaticEntryValidator(BrandcoValidatorBase::SERVICE_NAME_GLOBAL_MENU, $this->Data['brand']->id);

        foreach ($split_ids as $menu_id) {
            if($idValidation->isCorrectEntryId($menu_id)) {
                $menu_ids[] = $menu_id;
                if (!$this->isURL($this->POST['link_'.$menu_id])) {
                    $this->Validator->setError('link_'.$menu_id, 'INVALID_URL');
                }
                if ($this->isEmpty($this->POST['title_'.$menu_id])) {
                    $this->Validator->setError('title_'.$menu_id, 'NOT_REQUIRED');
                }
                if ($this->isEmpty($this->POST['link_'.$menu_id])) {
                    $this->Validator->setError('link_'.$menu_id, 'NOT_REQUIRED');
                }
            }
		}
        if ($this->Validator->getErrorCount() > 0) {
            return false;
        }
		$this->menu_ids = $menu_ids;
		return true;
	}

	function doAction() {

		/** @var  $brand_global_menu_service BrandGlobalMenuService */
		$brand_global_menu_service = $this->createService('BrandGlobalMenuService');
		$brand_global_menu_service->saveMenusByBrandIdAndMenuIdsAndPosts($this->Data['brand']->id, $this->menu_ids, $this->POST);
		$this->Data['saved'] = 1;
		if($this->redirect == 'create_menu'){
			return 'redirect: ' . Util::rewriteUrl('admin-top', 'create_global_menu_form');
		}
		return 'redirect: ' . Util::rewriteUrl('admin-top', 'global_menus', array(), array('close' => 1, 'refreshTop' => 1));
	}
}