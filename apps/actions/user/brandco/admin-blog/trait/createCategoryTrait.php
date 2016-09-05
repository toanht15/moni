<?php
trait createCategoryTrait {
    public function setValidatorDefinition() {
        $this->ValidatorDefinition = array_merge($this->ValidatorDefinition, array(
            'name' => array(
                'required' => true,
                'type' => 'str',
                'length' => 35,
            ),
            'directory' => array(
                'type' => 'str',
                'length' => 35,
            ),
            'og_image' => array(
                'type' => 'file',
                'size' => '5MB'
            ),
            'is_use_customize' => array(
                'type' => 'num'
            ),
            'parent_id' => array(
                'required' => true,
                'type' => 'num'
            )
        ));
    }

    public function validateCategory($isEdit = false) {
        //フォーム確認
        if (array_key_exists('display', $this->POST)) {
            $this->ContainerName = 'create_static_html_entry';
            $this->Form['action'] = 'create_static_html_entry_form';
        }

        // カテゴリ名重複チェック
        $this->categoryService = $this->createService('StaticHtmlCategoryService');
        if ($temp = $this->categoryService->getCategoryByNameAndBrandId($this->POST['name'], $this->brand->id)) {
            if (!$isEdit || ($isEdit && $temp->id != $this->category->id)) {
                $this->Validator->setError('name', 'EXISTED_CATEGORY');
            }
        }

        // カテゴリパス重複チェック
        if (!$this->isEmpty($this->POST['directory']) && ($temp = $this->categoryService->getCategoryByDirectoryAndBrandId($this->POST['directory'], $this->brand->id))) {
            if (!$isEdit || ($isEdit && $temp->id != $this->category->id)) {
                $this->Validator->setError('directory', 'EXISTED_DIRECTORY_NAME');
            }
        }

        if (!$this->isEmpty($this->POST['directory']) && !ctype_alnum($this->POST['directory'])) {
            $this->Validator->setError('directory', 'NOT_ALPHANUMERIC_CHARACTER');
        }

        // 親カテゴリチェック
        if ($this->POST['parent_id'] && !$this->categoryService->getCategoryById($this->POST['parent_id'])) {
            return false;
        }

        // og_imageチェック
        if ($this->FILES['og_image']) {
            $fileValidator = new FileValidator($this->FILES['og_image'], FileValidator::FILE_TYPE_IMAGE);
            if (!$fileValidator->isValidFile()) {
                $this->Validator->setError('og_image', 'NOT_MATCHES');
            } else {
                $this->file_info = $fileValidator->getFileInfo();
            }
        }

        if ($this->Validator->getErrorCount()) return false;

        return true;
    }
}