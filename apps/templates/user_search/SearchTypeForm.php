<div class="form-group">
    <label>検索方法</label>
    <?php write_html($this->formSelect(
        'search_type',
        PHPParser::ACTION_FORM,
        array(
            'class' => 'form-control input-sm',
            'id' => 'searchType'
        ),
        ManagerUserSearchService::$managerUserSearchType))
    ?>
</div>
