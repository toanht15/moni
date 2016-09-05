<div class="jsSearchGroup" id="searchGroup<?php assign(ManagerUserSearchService::SEARCH_TYPE_BRC_MAIL) ?>" style="display: none">
    <div class="form-group">
        <label class="control-label">
            <?php assign(ManagerUserSearchService::$managerUserSearchType[ManagerUserSearchService::SEARCH_TYPE_BRC_MAIL]) ?>
        </label>
        <?php write_html( $this->formText(
            'brandco_mail_address',
            PHPParser::ACTION_FORM,
            array(
                'class' => 'form-control input-sm',
                'maxlength'=>'255',
                'placeholder'=> '新モニプラに登録しているメールアドレスを入力してください'
            )
        )) ?>
    </div>
</div>