<div class="jsSearchGroup" id="searchGroup<?php assign(ManagerUserSearchService::SEARCH_TYPE_SNS_UID) ?>" style="display: none">
    <div class="form-group">
        <label class="control-label">
            SNS
        </label>
        <?php write_html($this->formSelect(
            'social_media_id',
            PHPParser::ACTION_FORM,
            array(
                'class' =>'form-control input-sm'
            ),
            SocialAccountService::$managerUserSearchTarget
        )) ?>
    </div>
    <div class="form-group">
        <label>SNS ユーザーID</label>
        <?php write_html( $this->formText(
            'social_media_account_id',
            PHPParser::ACTION_FORM,
            array(
                'class' =>'form-control input-sm',
                'maxlength'=>'255',
                'placeholder'=> 'ユーザーIDを入力して下さい'
            )
        )) ?>
    </div>
</div>
