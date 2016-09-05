<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');
AAFW::import('jp.aainc.actions.user.brandco.admin-blog.trait.createCategoryTrait');

class create_static_html_category extends BrandcoPOSTActionBase {
    protected $ContainerName = 'create_static_html_category_form';
    protected $Form = array(
        'package' => 'admin-blog',
        'action' => 'create_static_html_category_form',
    );

    public $NeedOption = array();
    public $NeedAdminLogin = true;
    public $CsrfProtect = true;
    /** @var  StaticHtmlCategoryService $tagService */
    private $categoryService;
    private $file_info = array();

    protected $ValidatorDefinition = array();

    use createCategoryTrait;

    public function doThisFirst() {
        $this->setValidatorDefinition();
    }

    public function validate() {
        return $this->validateCategory();
    }

    function doAction() {

        if ($this->FILES['og_image']) {
            // プロファイル画像 保存
            $this->POST['og_image_url'] = StorageClient::getInstance()->putObject(
                StorageClient::toHash('brand/' . $this->brand->id . '/static_html_categories/' . StorageClient::getUniqueId()), $this->file_info
            );
        }

        $category = $this->categoryService->createCategory($this->POST, $this->brand->id);

        // SNSプラグイン保存
        if ($category && count($this->sns_plugins)) {
            $this->categoryService->createStaticHtmlCategorySnsPlugins($category->id, $this->sns_plugins);
        }

        if (array_key_exists('display', $this->POST)) {
            $redirect = $this->POST['callback_url'].'?mid=action-created';
        } else {
            $redirect = Util::rewriteUrl('admin-blog', 'static_html_categories');
        }

        $this->Data['saved'] = 1;

        return 'redirect: ' . $redirect;
    }
}
