<?php write_html($this->parseTemplate('BrandcoModalHeader.php', array(
    'brand' => $data['brand'],
))) ?>
    <link rel="stylesheet" href="<?php assign($this->setVersion('/css/farbtastic.css'))?>">
    <script src="<?php assign($this->setVersion('/js/min/farbtastic-all.min.js'))?>"></script>
    <form id="frmProfile" name="frmProfile"
          action="<?php assign(Util::rewriteUrl( 'admin-top', 'edit_profile' )); ?>"
          method="POST" enctype="multipart/form-data">
        <?php write_html($this->csrf_tag()); ?>
        <article class="modalInner-large">
            <header><h1>基本情報</h1></header>
            <section class="modalInner-cont">
                <dl class="editProfile">
                    <dt><label for="#" class="">ページタイトル</dt>
                    <dd>
                        <p class="supplement1">ページヘッダーやメール通知に表示される名称</p>
                        <?php write_html( $this->formText( 'name', PHPParser::ACTION_FORM, array( 'maxlength'=>'35', 'id'=>'profile_name'))); ?>
                        <br><small class="textLimit">（<span>0</span>文字／35文字）</small>
                        <?php if ( $this->ActionError && !$this->ActionError->isValid('name')): ?>
                            <br>
                            <span class="attention1">ページタイトルは<?php assign ( $this->ActionError->getMessage('name') )?></span>
                        <?php endif; ?>
                    </dd>
                    <dt>プロフィール画像</dt>
                    <dd>
                        <p class="supplement1">ページヘッダーやメール通知に表示される名称</p>
                        <input type="file" name="profile_img_file" id="input_image"><img src="<?php assign($this->getActionFormValue('profile_img_url'))?>" width="80" height="80" alt="" class="thumbnail">
                        <br><small>（推奨:400px × 400px 以上 / 必須:200px × 200px）</small>
                        <?php if ( $this->ActionError && !$this->ActionError->isValid('profile_img_file')): ?>
                            <br>
                            <span class="attention1"><?php assign ( str_replace(array('<%width%>', '<%height%>'), array('200', '200'), $this->ActionError->getMessage('profile_img_file')))?></span>
                        <?php endif; ?>
                    </dd>
                    <dt>favicon（ファビコン）</dt>
                    <dd><p class="supplement1">ブラウザタブやアドレスバーに表示されるアイコン</p><input type="file" name="favicon_img_file" id="favicon_img_file"><img src="<?php assign($this->getActionFormValue('favicon_img_url'))?>" width="16" height="16" alt="" class="thumbnail-small">
                        <br><small>（推奨:マルチアイコンファイル[.ico] / 必須:16px か 32pxのアイコンファイル[.ico]）</small>
                        <?php if ( $this->ActionError && !$this->ActionError->isValid('favicon_img_file')): ?>
                            <br>
                            <span class="attention1"><?php assign ( str_replace(array('<%width%>', '<%height%>'), array('16', '16'), $this->ActionError->getMessage('favicon_img_file')))?></span>
                        <?php endif; ?>
                    </dd>
                </dl>
                <dl class="editProfile">
                    <dt><span class="editLabel">メインカラー</span></dt>
                        <dd>
                            <p class="supplement1">￼リンクパネルのヘッダー色</p>
                            <?php write_html( $this->formText( 'color_main', $data['brand']->getColorMain(), array( 'id' => 'color_main', 'maxlength' => '7', 'class' => 'colorPicker jsColorInput' ))); ?>
                            <div id="pickerColorMain" class="jsFarbtastic"></div>
                            <?php if ( $this->ActionError && !$this->ActionError->isValid('color_main')): ?>
                            <br>
                            <span class="attention1"><?php assign ( $this->ActionError->getMessage('color_main') )?></span>
                            <?php endif; ?>
                        </dd>
                    <dt><span class="editLabel">リンクカラー</span></dt>
                    <dd>
                        <p class="supplement1">テキストリンク色</p>
                        <?php write_html( $this->formRadio( 'color_text', PHPParser::ACTION_FORM, array(), array('#333333' => '黒', '#FFFFFF' => '白'))); ?></dd>
                    <dt>背景</dt>
                    <dd>
                        <?php write_html( $this->formText( 'color_background', $data['brand']->getColorBackground(), array( 'id' => 'color_background', 'maxlength' => '7', 'class' => 'colorPicker jsColorInput' ))); ?>
                        <div id="pickerColorBackground" class="jsFarbtastic"></div>
                        <?php if ( $this->ActionError && !$this->ActionError->isValid('color_background')): ?>
                            <br>
                            <span class="attention1"><?php assign ( $this->ActionError->getMessage('color_background') )?></span>
                        <?php endif; ?>
                    </dd>
                    <?php $disable = 'disabled'; if($this->getActionFormValue('background_img_url')) $disable = '';?>
                    <dd><input type="file" name="background_img_file" <?php if(!$this->getActionFormValue('background_img_url')) write_html('class="background_img_file"'); ?> id="background_img_file"><img src="<?php assign($this->getActionFormValue('background_img_url'));?>" width="80" height="80" alt="" class="thumbnail1">
                        <?php write_html( $this->formCheckBox( 'background_img_delete_flg', array($this->getActionFormValue('background_img_delete_flg') ? '1' : ''), array($disable=>$disable), array('1' => '背景画像の削除'))); ?>
                        <?php if ( $this->ActionError && !$this->ActionError->isValid('background_img_file')): ?>
                            <br>
                            <span class="attention1"><?php assign ( $this->ActionError->getMessage('background_img_file') )?></span>
                        <?php endif; ?>
                    </dd>
                    <dd>
                        <?php write_html( $this->formCheckBox( 'background_img_repeat', array($this->getActionFormValue('background_img_x') ? 'x' : '', $this->getActionFormValue('background_img_y') ? 'y' : ''), array($disable=>$disable), array('x' => '横に繰り返す', 'y' => '縦に繰り返す'))); ?>
                    </dd>
                </dl>

                <!-- /.modalInner-cont --></section>
            <footer>
                <p class="btnSet"><span class="btn2"><a href="#closeModalFrame">キャンセル</a></span><span class="btn3"><a href="javascript:void(0)" id="submit">保存</a></span></p>
            </footer>
        </article>
    </form>

<?php write_html($this->parseTemplate('BrandcoModalFooter.php', array('script'=>array('EditProfileFormService')))) ?>