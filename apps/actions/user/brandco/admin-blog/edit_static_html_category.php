<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');
AAFW::import('jp.aainc.actions.user.brandco.admin-blog.trait.createCategoryTrait');

class edit_static_html_category extends BrandcoPOSTActionBase {
    protected $ContainerName = 'edit_static_html_category_form';
    protected $Form = array(
        'package' => 'admin-blog',
        'action' => 'edit_static_html_category_form/{id}',
    );

    public $NeedOption = array();
    public $NeedAdminLogin = true;
    public $CsrfProtect = true;
    /** @var  StaticHtmlCategoryService $categoryService */
    private $categoryService;
    private $file_info = array();
    private $category;

    protected $ValidatorDefinition = array(
        'id' => array(
            'required' => true,
            'type' => 'num'
        )
    );

    use createCategoryTrait;

    public function doThisFirst() {
        $this->setValidatorDefinition();
    }

    public function validate() {

        $this->categoryService = $this->createService('StaticHtmlCategoryService');

        $this->category = $this->categoryService->getCategoryById($this->POST['id']);

        // カテゴリ存在チェック
        if (!$this->category || $this->category->brand_id != $this->brand->id) return '404';

        return $this->validateCategory(true);
    }

    function doAction() {
        /** @var StaticHtmlCategoryService $category_service */
        $category_service = $this->createService('StaticHtmlCategoryService');

        if ($this->FILES['og_image']) {
            // プロファイル画像 保存
            $this->POST['og_image_url'] = StorageClient::getInstance()->putObject(
                StorageClient::toHash('brand/' . $this->brand->id . '/static_html_categories/' . StorageClient::getUniqueId()), $this->file_info
            );
        }

        $result = $category_service->updateCategory($this->POST, $this->POST['id'], $this->brand->id);

        // SNS削除
        $category_service->deleteStaticHtmlCategorySnsPlugins($result->id);
        // SNSプラグイン保存
        if ($result && count($this->sns_plugins)) {
            $category_service->createStaticHtmlCategorySnsPlugins($result->id, $this->sns_plugins);
        }

        $this->Data['saved'] = 1;

        if (!$result) return 'redirect:' . Util::rewriteUrl('admin-blog', 'static_html_categories', array(), array('mid' => 'failed'));

        return 'redirect: ' . Util::rewriteUrl('admin-blog', 'static_html_categories', array(), array('mid' => 'updated'));
    }
}
