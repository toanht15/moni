<div class="jsSearchGroup" id="searchGroup<?php assign(ManagerUserSearchService::SEARCH_TYPE_BRC_UID) ?>" style="display: none">
    <div class="form-group">
        <label class="control-label">
            <?php assign(ManagerUserSearchService::$managerUserSearchType[ManagerUserSearchService::SEARCH_TYPE_BRC_UID]) ?>
        </label>
        <?php write_html( $this->formText(
            'brandco_user_id', PHPParser::ACTION_FORM,
            array(
                'class' =>'form-control input-sm',
                'maxlength'=>'10',
                'placeholder'=> '新モニプラのユーザーIDを入力して下さい'
            )
        )); ?>
    </div>
</div>
