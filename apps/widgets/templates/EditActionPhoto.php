<?php if($data['is_fan_list_page']): ?>
    <?php $disable = '' ?>
<?php else: ?>
    <?php $disable = ($data['action']->status == CpAction::STATUS_FIX)?'disabled':'' ?>
<?php endif; ?>

<section class="moduleEdit1">

    <?php write_html($this->parseTemplate('CpActionModuleTitle.php', array('disable' => $disable))); ?>
    <?php write_html($this->parseTemplate('CpActionModuleImage.php', array('disable'=>$disable))); ?>
    <?php write_html($this->parseTemplate('CpActionModuleText.php', array('disable'=>$disable))); ?>

    <section class="moduleCont1">
        <h1 class="editCheck1 jsModuleContTile">取得する項目</h1>
        <div class="moduleSettingWrap jsModuleContTarget">
            <ul class="moduleSetting">
                <li><label><?php write_html( $this->formCheckBox( 'title_required', array($this->getActionFormValue('title_required')), array($disable=>$disable, 'class' => 'jsPhotoAction', 'data-require_type' => 'Title'), array('1' => 'タイトル'))); ?></label>
                <li><label><?php write_html( $this->formCheckBox( 'comment_required', array($this->getActionFormValue('comment_required')), array($disable=>$disable, 'class' => 'jsPhotoAction', 'data-require_type' => 'Comment'), array('1' => 'コメント'))); ?></label>
            </ul>
            <!-- /.moduleSettingWrap --></div>
        <!-- /.moduleCont1 --></section>

    <section class="moduleCont1 jsModuleContWrap">
        <h1 class="editShare1 jsModuleContTile">シェア設定</h1>
        <div class="moduleSettingWrap jsModuleContTarget">
            <ul class="moduleSetting">
                <li><label><?php write_html( $this->formCheckBox( 'fb_share_required', array($this->getActionFormValue('fb_share_required')), array($disable=>$disable, 'class' => 'jsPhotoShareAction', 'data-require_type' => 'Facebook'), array('1' => 'Facebook'))); ?></label>
                <li><label><?php write_html( $this->formCheckBox( 'tw_share_required', array($this->getActionFormValue('tw_share_required')), array($disable=>$disable, 'class' => 'jsPhotoShareAction', 'data-require_type' => 'Twitter'), array('1' => 'Twitter'))); ?></label>
            </ul>

            <h2 style="margin-top: 10px">プレースホルダー<small class="textLimit">（最大<?php assign(PhotoUserShare::SHARE_TEXT_LENGTH) ?>文字）</small></h2>
            <p><textarea class="jsPhotoSharePlaceholder" name="share_placeholder" maxlength="<?php assign(PhotoUserShare::SHARE_TEXT_LENGTH) ?>"><?php assign($this->getActionFormValue('share_placeholder')) ?></textarea></p>
            <!-- /.moduleSettingWrap --></div>
        <!-- /.moduleCont1 --></section>

    <section class="moduleCont1">
        <h1 class="editRadio1 jsModuleContTile">検閲</h1>
        <div class="moduleSettingWrap jsModuleContTarget" style="display: block;">
            <p class="supplement1">投稿一覧への写真掲載を検閲するか設定できます。</p>
            <ul class="moduleSetting">
                <li><?php write_html($this->formRadio('panel_hidden_flg', $this->getActionFormValue('panel_hidden_flg'), array($disable => $disable, 'class' => 'setting-radio'), array('1' => 'する'))) ?></li>
                <li><?php write_html($this->formRadio('panel_hidden_flg', $this->getActionFormValue('panel_hidden_flg'), array($disable => $disable, 'class' => 'setting-radio'), array('0' => 'しない（投稿後すぐに掲載されます）'))) ?></li>
            <!-- /.moduleSetting --></ul>
        <!-- /.moduleSettingWrap --></div>
    <!-- /.moduleCont1 --></section>

    <section class="moduleCont1">
        <h1 class="editBtn1 jsModuleContTile">ボタン文言設定</h1>
        <div class="moduleSettingWrap">
            <ul class="moduleSetting">
                <li class="btn3Edit"><span>
                    <?php write_html( $this->formText( 'button_label_text', PHPParser::ACTION_FORM, array('maxlength'=>'80', 'id'=>'btn_text',$disable=>$disable))); ?>
                    <?php if ( $this->ActionError && !$this->ActionError->isValid('button_label_text')): ?>
                        <p class="attention1"><?php assign ( $this->ActionError->getMessage('button_label_text') )?></p>
                    <?php endif; ?>
                </span></li>
                <!-- /.moduleSetting --></ul>
            <!-- /.moduleSettingWrap --></div>
        <!-- /.moduleCont1 --></section>
        <?php write_html(aafwWidgets::getInstance()->loadWidget('CpActionModuleDeadLine')->render([
            'ActionForm'       => $data['ActionForm'],
            'ActionError'      => $data['ActionError'],
            'cp_action'        => $data['action'],
            'is_login_manager' => $data['pageStatus']['isLoginManager'],
            'disable'          => $disable,
        ])); ?>
    <!-- /.moduleEdit1 --></section>

<section class="modulePreview1">
    <header class="modulePreviewHeader">
        <p>スマートフォン<a href="#" class="toggle_switch left jsModulePreviewSwitch">toggle_switch</a>PC</p>
        <!-- /.modulePreviewHeader --></header>

    <div class="displaySP jsModulePreviewArea">
        <section class="messageWrap">
            <section class="message">
                <p class="messageImg"><img src="" width="600" height="300" id="imagePreview"></p>

                <section class="messageText" id="textPreview"></section>

                <p class="module">
                    <label class="fileUpload_img">
                        <span class="thumb"><img src="" id="cp_photo_image"></span>
                        <input type="file" class="action_image">
                    <!-- /.fileUpload_img --></label>
                <!-- /.module --></p>

                <dl class="module">
                    <dt class="require1 jsCpPhotoActionTitle">タイトル</dt>
                    <dd class="jsCpPhotoActionTitle">
                        <input type="text" placeholder="自由記述">
                    </dd>
                    <dt class="require1 jsCpPhotoActionComment">コメント</dt>
                    <dd class="jsCpPhotoActionComment">
                        <textarea placeholder="自由記述"></textarea>
                        <span class="supplement1">（最大300文字）</span>
                    </dd>

                    <dt class="jsCpPhotoShareActionText">SNSに投稿しよう！</dt>
                    <dd class="jsCpPhotoShareActionText">
                        <textarea class="jsCpPhotoSharePlaceholder"  placeholder="" maxlength="94"></textarea>
                        <span class="supplement1">(最大<?php assign(PhotoUserShare::SHARE_TEXT_LENGTH) ?>文字)</span>
                        <ul class="moduleSnsList">
                            <li class="jsCpPhotoShareActionFacebook"><label><input type="checkbox" checked="checked"><img src="<?php assign($this->setVersion('/img/sns/iconSnsFB2.png')) ?>" alt="Facebook"></label></li>
                            <li class="jsCpPhotoShareActionTwitter"><label><input type="checkbox" checked="checked"><img src="<?php assign($this->setVersion('/img/sns/iconSnsTW2.png')) ?>" alt="Twitter"></label></li>
                        <!-- /.moduleSnsList --></ul>
                    </dd>
                <!-- /.module --></dl>

                <div class="messageFooter">
                    <ul class="btnSet">
                        <li class="btn3"><a href="javascript:void(0)" class="large1" id="btnPreview"></a></li>
                    </ul>
                </div>
            </section>
        </section>
    </div>
<!-- /.modulePreview --></section>
