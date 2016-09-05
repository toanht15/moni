<section class="moduleCont1">
    <h1 class="editCvtag1 jsModuleContTile">タグ設置スペース</h1>
    <div class="moduleSettingWrap jsModuleContTarget">
        <p class="supplement1" style="margin-bottom:10px;">※参加完了時にこちらで入力されたタグが表示されます。<br>
            ※リターゲティングタグや各広告のコンバージョンタグ等をこちらに入力してください。</p>
        <?php if ( $this->ActionError && !$this->ActionError->isValid('cv_tag')): ?>
            <p class="iconError1"><?php assign ( $this->ActionError->getMessage('cv_tag') )?></p>
        <?php endif; ?>
        <p>
            <?php write_html( $this->formTextArea( 'cv_tag', PHPParser::ACTION_FORM, array('maxlength'=>CpValidator::CV_TAG_MAX_LENGTH, 'cols'=>25, 'rows'=>10, 'id'=>'text_area',$data['disable']=>$data['disable']))); ?>
        </p>
        <!-- /.moduleSettingWrap --></div>
    <!-- /.moduleCont1 --></section>
