<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');
class edit_profile extends BrandcoPOSTActionBase {
    protected $ContainerName = 'edit_profile_form';
    protected $Form = array(
        'package' => 'admin-top',
        'action' => 'edit_profile_form',
    );

    public $NeedOption = array();
    public $NeedAdminLogin = true;
    public $CsrfProtect = true;
    private $profile_file_info = array();
    private $background_file_info = array();

    protected $ValidatorDefinition = array(
        'name' => array(
            'required' => true,
            'type' => 'str',
            'length' => 30,
        ),
        'profile_img_file' => array(
            'type' => 'file',
            'size' => '3MB',
        ),
        'background_img_file' => array(
            'type' => 'file',
            'size' => '5MB',
        ),
    );

    public function validate() {
        $this->color_main = $this->normalizeColor($this->color_main);
        $this->color_background = $this->normalizeColor($this->color_background);
        if (!$this->isColor($this->color_main)) {
            $this->Validator->setError('color_main', 'NOT_MATCHES');
        }
        if (!$this->isColor($this->color_background)) {
            $this->Validator->setError('color_background', 'NOT_MATCHES');
        }
        if (!$this->isColor($this->color_text)) {
            $this->Validator->setError('color_text', 'NOT_MATCHES');
        }


        if($this->FILES['profile_img_file']){
        	$fileValidator = new FileValidator($this->FILES['profile_img_file'],FileValidator::FILE_TYPE_IMAGE);

        	if(!$fileValidator->isValidFile()){
        		$this->Validator->setError('profile_img_file', 'NOT_MATCHES');
        	}else{
                $this->profile_file_info = $fileValidator->getFileInfo();
            }

            $imageValidator = new ImageValidator($this->FILES['profile_img_file']['name']);
            if(!$imageValidator->isLargerSize(200, 200)) {
                $this->Validator->setError('profile_img_file', 'INVALID_IMAGE_SIZE');
            }

        }

        if($this->FILES['favicon_img_file']){
            $fileValidator = new FileValidator($this->FILES['favicon_img_file'],FileValidator::FILE_TYPE_IMAGE);

            if(!$fileValidator->isValidFile()){
                $this->Validator->setError('favicon_img_file', 'NOT_MATCHES');
            }else{
                $this->favicon_file_info = $fileValidator->getFileInfo();
            }

            $imageValidator = new ImageValidator($this->FILES['favicon_img_file']['name']);
            if(!$imageValidator->isLargerSize(16, 16)) {
                $this->Validator->setError('favicon_img_file', 'INVALID_IMAGE_SIZE');
            }

        }

        if ($this->FILES['background_img_file']) {
            $fileValidator = new FileValidator($this->FILES['background_img_file'], FileValidator::FILE_TYPE_IMAGE);
            if (!$fileValidator->isValidFile()) {
                $this->Validator->setError('background_img_file', 'NOT_MATCHES');
            } else {
                $this->background_file_info = $fileValidator->getFileInfo();
            }
        }

        if ($this->POST['name'] != $this->getBrand()->name && Util::isInvalidBrandName($this->POST['name'])) {
            $this->Validator->setError('name', 'INVALID_BRAND_NAME_FORMAT');
        }

        if ($this->Validator->getErrorCount()) return false;

        return true;
    }

    function doAction() {
        $brand_service = $this->createService('BrandService');
        $brand = $this->getBrand();

        if ($this->FILES['background_img_file'] && !$this->delete_background_img) {
            // メインバナー画像 保存
            $this->background_img = StorageClient::getInstance()->putObject(
                StorageClient::toHash('brand/' . $brand->id . '/background/' . StorageClient::getUniqueId()), $this->background_file_info
            );
        }

        // メインバナー画像　削除
        if ($this->background_img_delete_flg) {
            StorageClient::getInstance()->deleteObject(StorageClient::getInstance()->getImageKey($brand->background_img_url));
            $this->background_img = "";
        }

        if ($this->FILES['profile_img_file']) {
            // プロファイル画像 保存
            $this->profile_img_url = StorageClient::getInstance()->putObject(
                StorageClient::toHash('brand/' . $brand->id . '/profile/' . StorageClient::getUniqueId()), $this->profile_file_info
            );
        }

        if ($this->FILES['favicon_img_file']) {
            // ファビコン画像 保存
            $this->favicon_img_url = StorageClient::getInstance()->putObject(
                StorageClient::toHash('brand/' . $brand->id . '/favicon/' . StorageClient::getUniqueId()), $this->favicon_file_info
            );
        }

        $brand->mail_name = $this->name != $brand->name ? $this->name : null;
        $brand->name = $this->name;
        $brand->color_main = $this->color_main;
        $brand->color_background = $this->color_background;
        $brand->color_text = $this->color_text;
        $brand->profile_img_url = $this->profile_img_url;
        $brand->background_img_url = $this->background_img;
        $brand->background_img_x = 0;
        $brand->background_img_y = 0;
        foreach ($this->background_img_repeat as $background_img_repeat) {
            if ($background_img_repeat == 'x') {
                $brand->background_img_x = 1;
            }
            if ($background_img_repeat == 'y') {
                $brand->background_img_y = 1;
            }
        }
        $brand_service->updateBrand($brand);

        // favicon_urlの更新
        $page_setting = $brand->getBrandPageSetting();
        $page_setting->favicon_url = $this->favicon_img_url;
        $brand_page_setting_service = $this->createService('BrandPageSettingService');
        $brand_page_setting_service->updateBrandPageSetting($page_setting);

        $this->resetActionContainerByName();
        return 'redirect: ' . Util::rewriteUrl('admin-top', 'edit_profile_form', array(), array('close' => 1, 'refreshTop' => 1));
	}

    public static function isColor($color) {
        if (1 === preg_match('/#?[ABCDEFabcdef0-9]{3,6}$/', $color)) {
            return true;
        };
        return false;
    }

    public static function normalizeColor($color) {
        return preg_replace("/^([^#]+)$/", "#$1", $color);
    }
}