<div class="jsSearchGroup" id="searchGroup<?php assign(ManagerUserSearchService::SEARCH_TYPE_BRC_NO) ?>" style="display: none">
    <div class="form-group">
        <label class="control-label">ブランドID</label>
        <?php write_html( $this->formText(
            'brand_id',
            PHPParser::ACTION_FORM,
            array(
                'class' => 'form-control input-sm',
                'maxlength' => '255',
                'placeholder' => 'Brand ID'
            )
        )) ?>
    </div>
    <div class="form-group">
        <label class="control-label">会員番号</label>
        <?php write_html( $this->formText(
            'brand_user_no',
            PHPParser::ACTION_FORM,
            array(
                'class' =>'form-control input-sm',
                'maxlength'=>'255',
                'placeholder'=> 'ブランドの会員番号を入力して下さい'
            )
        )) ?>
    </div>
</div>
