<?php if($data['is_fan_list_page']): ?>
    <?php $disable = '' ?>
<?php else: 
        $disable = '';
        if($data['action']->status == CpAction::STATUS_FIX){
            $disable = 'disabled';
            write_html($this->formHidden('is_status_fixed', 1));
        }
    ?>
<?php endif; ?>
<section class="moduleEdit1">
    <?php write_html($this->parseTemplate('CpActionModuleTitle.php', array('disable' => $disable))); ?>
    <?php write_html($this->parseTemplate('CpActionModuleImage.php', array('disable' => $disable))); ?>
    <?php write_html($this->parseTemplate('CpActionModuleText.php', array('disable' => $disable))); ?>

    <section class="moduleCont1">
         
        <h1 class="editMovie1 jsModuleContTile">動画選択</h1>
        <div class="moduleSettingWrap jsModuleContTarget">
            <ul class="moduleSetting">

                <li>
                    <label><?php write_html( $this->formRadio( 'module_movie', PHPParser::ACTION_FORM, array('class' => 'labelTitleMovie', $disable => $disable), array(CpMovieAction::IS_YOUTUBE_SELECT => '連携YouTubeチャンネルから選択'), array(), " ")); ?></label>
                    <?php write_html( $this->formSelect( 'movie_object_id_select', PHPParser::ACTION_FORM, array($disable => $disable, 'class' => 'actionMovie', 'id' => 'movie_select'), $data['entries'])); ?>

                    <?php if ( $this->ActionError && !$this->ActionError->isValid('movie_object_id_select')): ?>
                        <p class="attention1"><?php assign ( $this->ActionError->getMessage('movie_object_id_select') )?></p>
                    <?php endif; ?>
                </li>
                <li>
                    <label><?php write_html( $this->formRadio( 'module_movie', PHPParser::ACTION_FORM, array('class' => 'labelTitleMovie', $disable => $disable,), array(CpMovieAction::IS_YOUTUBE_ID => 'YouTubeURLを指定'), array(), " ")); ?></label>
                    <span class="youtubeUrl">https://www.youtube.com/watch?v=<?php write_html( $this->formText( 'movie_object_id_url', PHPParser::ACTION_FORM, array('maxlength'=>'11', $disable => $disable, 'class' => 'actionMovie', 'id' => 'movie_url'))); ?></span>
                    <?php if ( $this->ActionError && !$this->ActionError->isValid('movie_object_id_url')): ?>
                        <p class="attention1"><?php assign ( $this->ActionError->getMessage('movie_object_id_url') )?></p>
                    <?php endif; ?>
                </li>
                <?php if($data['video_upload_enable']): ?>
                <li>
                    <label><?php write_html( $this->formRadio( 'module_movie', PHPParser::ACTION_FORM, array('class' => 'labelTitleMovie', $disable => $disable), array(CpMovieAction::IS_UPLOADED => 'その他'), array(), " ")); ?></label><br>
                    <small>※MP4形式のみ（Internet Explore 9は非対応です）</small>
                    <ul>
                        <li>
                            <label><?php write_html( $this->formRadio( 'upload_movie', PHPParser::ACTION_FORM, array('class' => 'labelTitleMovieUpload', $disable => $disable), array(CpMovieAction::IS_UPLOADED_FILE => '動画ファイルをアップロード'), array(), " ")); ?></label>
                            <input type="file" name="video_file" id="video_file" class="actionVideo">
                            <?php if ( $this->ActionError && !$this->ActionError->isValid('video_file')): ?>
                                <p class="attention1"><?php assign ( $this->ActionError->getMessage('video_file') )?></p>
                            <?php endif; ?>
                        </li>
                        <li>
                            <label><?php write_html( $this->formRadio( 'upload_movie', PHPParser::ACTION_FORM, array('class' => 'labelTitleMovieUpload', $disable => $disable,'checked'=>'checked'), array(CpMovieAction::IS_UPLOADED_URL => '動画URLを指定'), array(), " ")); ?></label>
                            <span class="videoUrl"><?php write_html( $this->formText( 'movie_upload_url', PHPParser::ACTION_FORM, array($disable => $disable, 'id' => 'movie_url_uploaded'))); ?></span>
                            <?php if ( $this->ActionError && !$this->ActionError->isValid('movie_object_id_url')): ?>
                                <p class="attention1"><?php assign ( $this->ActionError->getMessage('movie_object_id_url') )?></p>
                            <?php endif; ?>
                            <a href="javascript:void(0);"data-link="<?php assign(Util::rewriteUrl('admin-blog', 'file_list', array(), array('f_id' => BrandUploadFile::POPUP_FROM_MOVIE_MODULE, 'stt' => ($data['disable'] ? 1 : 2)))) ?>" class="openNewWindow1 jsFileUploaderPopup" onclick="return false;">ファイル管理から動画選択</a>
                        </li>
                    </ul>
                    <label><?php write_html( $this->formCheckBox( 'popup_view_flg', array($this->getActionFormValue("popup_view_flg")), array($disable => $disable,'class'=>'jsViewPopUp'), array('1' => '動画を別ウィンドウで再生する'))); ?></label>
                </li>
                <?php endif; ?>
            </ul>
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
                <p class="messageImg"><img src="" alt="" id="imagePreview"></p>
                <section class="messageText" id="textPreview"></section>
                <div id="player">
                
                    <?php if($data['video_upload_enable']): ?>
                        <ul class="btnSet" id="jsButtonPreview" style="display: none">
                            <li class="btn3"><a id="popup_video_link" href="javascript:void(0);"data-link="<?php assign(Util::rewriteUrl('video', 'watch_video', array(), array('video_url' => ($data['ActionForm']['movie_url']) ))) ?>"class="movie1 jsFileUploaderPopup"onclick="return false;">再生する</a></li>
                        </ul>
                        <video controls id = "upload_url" style="width: 100%;height: auto;display: none;"><source id="scr_video" src="<?php assign($data['ActionForm']['movie_url']) ?>" type="video/mp4"></video>
                        <div id="yt_player" style="display: none"></div>
                    <?php else: ?>
                        <div id="yt_player"></div>
                    <?php endif; ?>
                </div>
                <div class="messageMovie">
                    <p class="moveiAttention jsMovieText" id="state_playing">視聴後に次のステップへ進みます</p>
                    <!-- /.messageMovie --></div>
                <div class="messageFooter">
                </div>
                <!-- /.message --></section>
        </section>

    </div>
    <!-- /.modulePreview --></section>
