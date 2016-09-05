<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');
class update_side_menu extends BrandcoPOSTActionBase {

	protected $ContainerName = 'update_side_menu';

	protected $Form = array(
		'package' => 'admin-top',
		'action' => 'side_menus',
	);

    public $NeedOption = array();
	public $NeedAdminLogin = true;
	public $CsrfProtect = true;
    private $file_info = array();

	protected $ValidatorDefinition = array (
        'side_menu_title' => array(
            'type' => 'str',
            'length' => 20,
        ),
        'menuImage' => array (
                'type' => 'file',
                'size' => '5MB'
        )
	);

	public function validate() {
		$split_ids = explode(",", $this->order);
		$menu_ids = array();
        $this->Data['brand'] = $this->getBrand();
        $idValidation = new StaticEntryValidator(BrandcoValidatorBase::SERVICE_NAME_SIDE_MENU, $this->Data['brand']->id);

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

		if ($this->FILES ['menuImage']) {
			$fileValidator = new FileValidator ( $this->FILES ['menuImage'],FileValidator::FILE_TYPE_IMAGE );
			if (!$fileValidator->isValidFile ()) {
				$this->Validator->setError('menuImage', 'NOT_MATCHES');
				return false;
			}else{
                $this->file_info = $fileValidator->getFileInfo();
            }
		}
		return true;
	}

	function doAction() {

		/** @var  $brand_side_menu_service BrandSideMenuService */
		$brand_side_menu_service = $this->createService('BrandSideMenuService');
		$brand_service = $this->createService('BrandService');

		if($this->POST['side_menu_title_type'] == Brand::menuSideTypeImage){
			if ($this->FILES ['menuImage']) {
                $this->Data['brand']->side_menu_title = StorageClient::getInstance()->putObject(
                    StorageClient::toHash('brand/' . $this->Data['brand']->id . '/side_menu/' . StorageClient::getUniqueId()), $this->file_info
                );
			}
		}else{
            $this->Data['brand']->side_menu_title = $this->POST['side_menu_title'];
		}
        $this->Data['brand']->side_menu_title_type = $this->POST['side_menu_title_type'];
		$brand_service->updateBrand($this->Data['brand']);

		$brand_side_menu_service->saveMenusByBrandIdAndMenuIdsAndPosts($this->Data['brand']->id, $this->menu_ids, $this->POST);

        $this->Data['saved'] = 1;

		if($this->redirect == 'create_menu'){
			return 'redirect: ' . Util::rewriteUrl('admin-top', 'create_side_menu_form');
		}

		return 'redirect: ' . Util::rewriteUrl('admin-top', 'side_menus', array(), array('close' => 1, 'refreshTop' => 1));
	}
}