<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');

class save_page_meta_settings extends BrandcoPOSTActionBase {
    protected $ContainerName = 'page_settings';
    protected $Form = array(
        'package' => 'admin-settings',
        'action' => 'page_settings_form?mid=failed'
    );

    public $NeedOption = array();
    public $NeedAdminLogin = true;
    public $CsrfProtect = true;

    protected $ValidatorDefinition = array(
        'meta_title' => array(
            'type' => 'str',
            'length' => 32
        ),
        'meta_description' => array(
            'type' => 'str',
            'length' => 124
        ),
        'meta_keyword' => array(
            'type' => 'str',
            'length' => 511
        ),
        'og_image' => array(
            'type' => 'file',
            'size' => '3MB'
        )
    );

    private $og_image_info;

    public function validate() {
        if ($this->FILES['og_image']) {
            $file_validator = new FileValidator($this->FILES['og_image'], FileValidator::FILE_TYPE_IMAGE);

            if (!$file_validator->isValidFile()) {
                $this->Validator->setError('og_image', 'NOT_MATCHES');
            } else {
                $this->og_image_info = $file_validator->getFileInfo();
            }

            $image_validator = new ImageValidator($this->FILES['og_image']['name']);
            if (!$image_validator->isLargerSize(200, 200)) {
                $this->Validator->setError('og_image', 'INVALID_IMAGE_SIZE');
            }
        }

        return $this->Validator->isValid();
    }

    public function doAction() {
        $page_setting_transaction = aafwEntityStoreFactory::create('BrandPageSettings');

        try {
            $page_setting_transaction->begin();

            if ($this->FILES['og_image']) {
                $this->POST['og_image_url'] = StorageClient::getInstance()->putObject(
                    'brand/' . $this->getBrand()->id . '/page_setting/' . StorageClient::getUniqueId(), $this->og_image_info
                );
            }

            $page_setting_service = $this->createService('BrandPageSettingService');
            $page_setting_service->setPageMetaSetting($this->getBrand()->id, $this->POST);

            $page_setting_transaction->commit();
        } catch (Exception $e) {
            $page_setting_transaction->rollback();
            $logger = aafwLog4phpLogger::getDefaultLogger();
            $logger->error('save_page_meta_settings@doAction Error: ' . $e);

            return 'redirect: ' . Util::rewriteUrl('admin-settings', 'page_settings_form', array(), array('mid', 'failed'));
        }

        $this->Data['saved'] = 1;
        return 'redirect: ' . Util::rewriteUrl('admin-settings', 'page_settings_form', array(), array('mid' => 'updated'));
    }
}