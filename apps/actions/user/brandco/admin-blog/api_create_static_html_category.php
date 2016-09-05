<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');
AAFW::import('jp.aainc.actions.user.brandco.admin-blog.trait.createCategoryTrait');

class api_create_static_html_category extends BrandcoPOSTActionBase {
    protected $ContainerName = 'api_create_static_html_category';
    protected $AllowContent = array('JSON');

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
        $validate = $this->validateCategory();
        if (!$validate) {
            $json_data = $this->createAjaxResponse('ng', array(), array('name' => $this->Validator->getMessage('name')));
            $this->assign('json_data', $json_data);
        }
        return $validate;
    }

    public function getFormURL() {
        $json_data = $this->createAjaxResponse('ng', array(), array('name' => $this->Validator->getMessage('name')));
        $this->assign('json_data', $json_data);
        return 'dummy.php';
    }

    function doAction() {

        $category = $this->categoryService->createCategory($this->POST, $this->brand->id);

        if (!$category) {
            $json_data = $this->createAjaxResponse('ng', array(), array('カテゴリーが作成できませんでした。'));
        } else {
            $category_data = $this->categoryService->getCategoriesTree($this->brand->id);
            $parser = new PHPParser();
            $tag_tree_selection = $parser->parseTemplate('TagTreeSelection.php', array('categories_tree'=>$category_data));
            $tag_tree = $parser->parseTemplate('TagTreeList.php', array ('categories_tree' => $category_data, 'type' => StaticHtmlCategory::DISPLAY_LIST_TYPE_CHECKBOX));
            $json_data = $this->createAjaxResponse('ok', array('categories_selection' => $tag_tree_selection, 'categories_checkbox' => $tag_tree), array(), array());
        }
        $this->assign('json_data', $json_data);
        return 'dummy.php';
    }
}
