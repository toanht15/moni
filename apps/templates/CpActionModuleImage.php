<section class="moduleCont1">
    <h1 class="editImg1 jsModuleContTile">画像</h1>
    <div class="moduleSettingWrap jsModuleContTarget">
        <?php $defaultValue = $this->ActionForm['image_url'] ? '1' : '2'; ?>
        <?php if ( $this->ActionError && !$this->ActionError->isValid('image_url')): ?>
            <p class="iconError1"><?php assign ( $this->ActionError->getMessage('image_url') )?></p>
        <?php endif; ?>
        <?php if ( $this->ActionError && !$this->ActionError->isValid('image_file')): ?>
            <p class="iconError1"><?php assign ( $this->ActionError->getMessage('image_file') )?></p>
        <?php endif; ?>
        <ul class="moduleSetting">
            <li>
                <?php write_html( $this->formRadio( 'moduleImage', PHPParser::ACTION_FORM, array('class'=>'labelTitle', $data['disable']=>$data['disable']), array('0' => 'アップロード'), array(), " ")); ?>
                <input type="file" name="image_file" id="image_file" class="actionImage" disabled="disabled">
            </li>
            <li>
                <?php write_html( $this->formRadio( 'moduleImage', $defaultValue, array('class'=>'labelTitle',$data['disable']=>$data['disable']), array('1' => '画像URL'), array(), " ")); ?>
                <?php write_html( $this->formText( 'image_url', PHPParser::ACTION_FORM, array('maxlength'=>'512', 'id'=>'image_url', $data['disable']=>$data['disable'], 'class'=>'actionImage'))); ?>
                <!-- Campaign Status 1: STATUS_FIX, 2: DEFAULT -->
                <a href="javascript:void(0);"
                   data-link="<?php assign(Util::rewriteUrl('admin-blog', 'file_list', array(), array('f_id' => BrandUploadFile::POPUP_FROM_PHOTO_MODULE, 'stt' => ($data['disable'] ? 1 : 2)))) ?>"
                   class="openNewWindow1 jsFileUploaderPopup"
                   onclick="return false;">ファイル管理から画像選択</a>
            </li>
            <li>
                <?php write_html( $this->formRadio( 'moduleImage', $defaultValue, array( 'class' => 'labelTitle', $data['disable'] => $data['disable']), array('2' => '使用しない'), array(), " ")); ?>
                <?php write_html( $this->formHidden( 'image_url', "", array( 'class' => 'actionImage', 'id' => 'image_blank', $data['disable'] => $data['disable'] ))); ?>
            </li>
        </ul>
        <!-- /.moduleSettingWrap --></div>
    <!-- /.moduleCont1 --></section>