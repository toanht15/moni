<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');
class update_categories extends BrandcoPOSTActionBase {
    protected $ContainerName = 'update_categories';
    protected $AllowContent = array('JSON');

    public $NeedOption = array();
    public $NeedAdminLogin = true;
    public $CsrfProtect = true;

    protected $ValidatorDefinition = array(
        'categories_data' => array(
            'type' => 'str',
        )
    );

    public function validate() {
        return true;
    }

    function doAction() {

        /** @var StaticHtmlCategoryService $service */
        $service = $this->createService('StaticHtmlCategoryService');

        if (!$service->isCorrectCategories($this->POST['deleted_categories'], $this->brand->id)
            || !$service->isCorrectCategoriesTreeTypeJson($this->POST['categories_data'], $this->brand->id)
        ) {
            $json_data = $this->createAjaxResponse(Util::rewriteUrl('admin-blog', 'static_html_categories', array(), array('mid' => 'failed')));
            $this->assign('json_data', $json_data);
            return 'dummy.php';
        }
        /** @var BrandCmsSettings $cms_transaction */
        $cms_transaction = aafwEntityStoreFactory::create('BrandCmsSettings');

        try {
            $cms_transaction->begin();

            $this->updateBrandCmsSettings();
            $service->synchCategoriesByPost($this->POST, $this->brand->id);

            $cms_transaction->commit();
        } catch (Exception $e) {
            $cms_transaction->rollback();
            if ($e->errFunction == 'deleteAndSortCategory') {
                $category = $service->getCategoryById($e->entryId);
                $error = array(
                    'message' => '「'.$category->name.'」カテゴリー削除に失敗しました。',
                    'content' => 'カテゴリーあるいは子、孫の投稿がないことを確認してください。'
                );
            } else {
                $error = array(
                    'message' => '操作失敗しました。',
                    'content' => '操作を確認してください。'
                );
            }
            $json_data = $this->createAjaxResponse('ng', array(), $error);
            $this->assign('json_data', $json_data);
            return 'dummy.php';
        }

        $json_data = $this->createAjaxResponse(Util::rewriteUrl('admin-blog', 'static_html_categories', array(), array('mid' => 'updated')));
        $this->assign('json_data', $json_data);

        return 'dummy.php';
    }

    private function updateBrandCmsSettings() {
            /** @var BrandCmsSettingService $brand_cms_setting_service */
            $brand_cms_setting_service = $this->createService('BrandCmsSettingService');
            $brand_cms_setting = $brand_cms_setting_service->getBrandCmsSettingByBrandId($this->Data['brand']->id);
            $brand_cms_setting->category_navi_top_display_flg = $this->category_navi_top_display_flg ? $this->category_navi_top_display_flg : 0;
            $brand_cms_setting_service->updateBrandCmsSetting($brand_cms_setting);
    }
}
