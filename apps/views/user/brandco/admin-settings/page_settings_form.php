<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoHeader')->render($data['pageStatus'])) ?>
<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoAccountHeader')->render($data['pageStatus'])) ?>

<div class="adminWrap">
    <?php write_html($this->parseTemplate('SettingSiteMenu.php', $data['pageStatus'])) ?>

    <article class="adminMainCol">

        <h1 class="hd1">ページ設定</h1>

        <div class="adminOpenSettingWrap">
            <h2 class="hd2">公開設定</h2>
            <section class="adminPageSettingWrap">
                <form id="frmPublicRadio" name="frmPublicRadio" action="<?php assign(Util::rewriteUrl('admin-settings', 'page_settings')); ?>" method="POST">
                    <p>公開に設定すると、インターネット上に公開されます。</p>
                    <ul class="adminPageSetting">
                        <?php write_html($this->csrf_tag()); ?>
                        <li>
                            <label>
                                <?php write_html($this->formRadio(
                                    'public_settings',
                                    $this->POST ? $this->POST['public_settings'] : ($this->page_settings ? $this->page_settings->public_flg : BrandPageSettingService::STATUS_PUBLIC),
                                    array('class' => 'labelTitle', $disable => $disable),
                                    array(BrandPageSettingService::STATUS_PUBLIC => '公開')
                                )); ?>
                            </label>
                        </li>
                        <li>
                            <label>
                                <?php write_html($this->formRadio(
                                    'public_settings',
                                    $this->POST ? $this->POST['public_settings'] : ($this->page_settings ? $this->page_settings->public_flg : BrandPageSettingService::STATUS_NON_PUBLIC),
                                    array('class' => 'labelTitle', $disable => $disable),
                                    array(BrandPageSettingService::STATUS_NON_PUBLIC => '非公開')
                                )); ?>
                            </label>
                        </li>
                        <!-- /.adminPageSetting --></ul>

                    <?php if ($this->ActionError && !$this->ActionError->isValid('name')): ?>
                        <p class="iconError1"><a href="javascript:void(0);" style="color:#b20000;"><strong>ページタイトル</strong>が入力されていません。</a></p>
                        <ol class="errorGuide">
                            <li>設定</li>
                            <li>ページ設定</li>
                            <li>基本情報設定</li>
                            <li>ページタイトル</li>
                        </ol>
                    <?php endif; ?>
                    <?php if ($this->ActionError && !$this->ActionError->isValid('profile_img_url')): ?>
                        <p class="iconError1"><a href="javascript:void(0);" style="color:#b20000;"><strong>プロフィール画像</strong>が入力されていません。</a></p>
                        <ol class="errorGuide">
                            <li>設定</li>
                            <li>ページ設定</li>
                            <li>基本情報設定</li>
                            <li>プロフィール画像</li>
                        </ol>
                    <?php endif; ?>
                    <?php if ($this->ActionError && !$this->ActionError->isValid('inquiry_brand_receiver')): ?>
                        <p class="iconError1"><a href="<?php assign(Util::rewriteUrl('admin-settings', 'inquiry_settings_form')) ?>" style="color:#b20000;"><strong>問い合わせ先メールアドレス</strong>が入力されていません。</a></p>
                        <ol class="errorGuide">
                            <li>設定</li>
                            <li>通知先メールアドレス設定</li>
                            <li>お問い合わせ受信時の通知先メールアドレスを登録する</li>
                        </ol>
                    <?php endif; ?>

                    <p class="btnSet">
                        <span class="btn3"><a href="#modal1" class="jsOpenModal">保存</a></span>
                    </p>
                </form>

                <!-- /.adminPageSettingWrap --></section>
            <!-- /. --></div>

        <h2 class="hd2">基本情報設定</h2>
        <section class="adminPageSettingWrap">
            <form id="frmProfile" name="frmProfile" action="<?php assign(Util::rewriteUrl( 'admin-settings', 'edit_profile' )); ?>" method="POST" enctype="multipart/form-data">
                <?php write_html($this->csrf_tag()); ?>
                <dl class="editProfile">
                    <dt><label for="#" class="">ページタイトル</dt>

                    <dd>
                        <?php write_html( $this->formText( 'name', PHPParser::ACTION_FORM, array( 'maxlength'=>'35', 'id'=>'profile_name'))); ?>
                        <br><small class="textLimit"></small>
                        <?php if ( $this->ActionError && !$this->ActionError->isValid('name')): ?>
                            <br>
                            <span class="attention1">ページタイトルは<?php assign ( $this->ActionError->getMessage('name') )?></span>
                        <?php endif; ?>
                    </dd>
                    <dt>プロフィール画像</dt>
                    <dd><input type="file" name="profile_img_file" id="input_image"><img src="<?php assign($this->getActionFormValue('profile_img_url'))?>" width="80" height="80" alt="" class="thumbnail">
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
                        <?php write_html( $this->formText( 'color_main', $data['brand']->getColorMain(), array( 'id' => 'color_main', 'maxlength' => '7', 'class' => 'colorPicker jsColorInput' ))); ?>
                        <div id="pickerColorMain" class="jsFarbtastic"></div>
                        <?php if ( $this->ActionError && !$this->ActionError->isValid('color_main')): ?>
                            <br>
                            <span class="attention1"><?php assign ( $this->ActionError->getMessage('color_main') )?></span>
                        <?php endif; ?>
                    </dd>
                    <dt><span class="editLabel">リンクカラー</span></dt>
                    <dd><?php write_html( $this->formRadio( 'color_text', PHPParser::ACTION_FORM, array(), array('#333333' => '黒', '#FFFFFF' => '白'))); ?></dd>
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

                <p class="btnSet">
                    <span class="btn3"><a href="javascript:void(0)" id="edit_profile_submit_btn">保存</a></span>
                </p>

            </form>
            <!-- /.adminPageSettingWrap --></section>

        <?php if(!$data['hide_brand_top_page']):?>
            <h2 class="hd2">トップページレイアウト設定</h2>
            <?php if($data['brand']->hasOption(BrandOptions::OPTION_TOP)):?>
                <section class="adminPageSettingWrap">
                    <h3 class="hd3">
                        コンテンツパネル
                    <span class="iconHelp">
                        <span class="text">ヘルプ</span>
                        <span class="textBalloon1">
                            <span><img src="<?php assign($this->setVersion('/img/setting/imgTopGuideContPan.png')); ?>" height="420" width="350" alt="コンテンツパネルの位置"></span>
                            <!-- /.textBalloon1 --></span>
                      <!-- /.iconHelp --></span>
                    </h3>
                    <p>
                        SNSの連携をして、TOPページのコンテンツパネルに表示する設定ができます。
                        <br>
                        フォロー対象設定などのキャンペーン用連携も同様に扱われ、パネルに表示できます。
                    </p>
                    <p class="btn3"><a href="#selectPanelKind" class="jsOpenModal small1"><span>編集する</span></a></p>
                    <!-- /.adminPageSettingWrap --></section>
            <?php endif;?>

            <?php if($data['brand']->hasOption(BrandOptions::OPTION_TOP)):?>
                <section class="adminPageSettingWrap">
                    <h3 class="hd3">
                        グローバルメニュー
                    <span class="iconHelp">
                        <span class="text">ヘルプ</span>
                        <span class="textBalloon1">
                            <span><img src="<?php assign($this->setVersion('/img/setting/imgTopGuideGNav.png')); ?>" height="250" width="350" alt="グローバルメニューの位置"></span>
                            <!-- /.textBalloon1 --></span>
                      <!-- /.iconHelp --></span>
                    </h3>
                    <p>
                        ヘッダー位置に表示されるナビゲーションを設定できます。
                        <br>
                        スマホでは、ページ右上のメニューボタン内に格納されます。
                    </p>
                    <p class="btn3"><a href="#globalMenus" class="jsOpenModal small1"><span>編集する</span></a></p>
                    <!-- /.adminPageSettingWrap --></section>
            <?php endif;?>

            <?php if($data['brand']->hasOption(BrandOptions::OPTION_TOP)):?>
                <section class="adminPageSettingWrap">
                    <h3 class="hd3">
                        フリーエリア
                    <span class="iconHelp">
                        <span class="text">ヘルプ</span>
                        <span class="textBalloon1">
                            <span><img src="<?php assign($this->setVersion('/img/setting/imgTopGuideFreeArea.png')); ?>" height="250" width="350" alt="フリーエリアの位置"></span>
                            <!-- /.textBalloon1 --></span>
                      <!-- /.iconHelp --></span>
                    </h3>
                    <p>
                        グローバルメニューの下のエリアにHTMLを使って自由にコンテンツを表示させることができます。
                        <br>
                        このエリアはPC限定の表示エリアとなります。
                    </p>
                    <p class="btn3"><a href="#freeAreaEntries" class="jsOpenModal small1"><span>編集する</span></a></p>
                    <!-- /.adminPageSettingWrap --></section>
            <?php endif;?>

            <?php if($data['brand']->hasOption(BrandOptions::OPTION_TOP)):?>
                <section class="adminPageSettingWrap">
                    <h3 class="hd3">
                        サイドメニュー
                    <span class="iconHelp">
                        <span class="text">ヘルプ</span>
                        <span class="textBalloon1">
                            <span><img src="<?php assign($this->setVersion('/img/setting/imgTopGuideSideMenu.png')); ?>" height="280" width="350" alt="サイドメニューの位置"></span>
                            <!-- /.textBalloon1 --></span>
                      <!-- /.iconHelp --></span>
                    </h3>
                    <p>
                        トップページ右側のサイドメニューにリンクを設定できます。
                        <br>
                        スマホではページ下部に表示されます。
                    </p>
                    <p class="btn3"><a href="#sideMenus" class="jsOpenModal small1"><span>編集する</span></a></p>
                    <!-- /.adminPageSettingWrap --></section>
            <?php endif;?>

            <?php if($data['brand']->hasOption(BrandOptions::OPTION_TOP)):?>
                <h2 class="hd2">トップページmeta情報設定</h2>
                <section class="adminPageSettingWrap">
                    <form name="frmMetaInfoSetting" method="POST" action="<?php assign(Util::rewriteUrl('admin-settings', 'save_page_meta_settings')) ?>" enctype="multipart/form-data">
                        <?php write_html($this->csrf_tag()) ?>
                        <dl class="adminPageMeta">
                            <dt>title</dt>
                            <dd>
                                <?php write_html($this->formText('meta_title', PHPParser::ACTION_FORM, array('maxlength' => 32, 'class' => 'jsMetaDataInput'))) ?>
                                <small class="textLimit"></small>
                            </dd>
                            <dt>description</dt>
                            <dd>
                                <?php write_html($this->formTextArea('meta_description', PHPParser::ACTION_FORM, array('maxlength' => 124, 'class' => 'jsMetaDataInput'))) ?>
                                <small class="textLimit"></small>
                            </dd>
                            <dt>keyword</dt>
                            <dd>
                                <?php write_html($this->formTextArea('meta_keyword', PHPParser::ACTION_FORM, array('maxlength' => 511, 'class' => 'jsMetaDataInput'))) ?><small>複数のキーワードを記入する際は半角のカンマ(,)区切りで入力してください</small>
                                <small class="textLimit"></small>
                            </dd>
                            <dt>og:image</dt>
                            <dd>
                                <img src="<?php assign($this->ActionForm['og_image_url'] ? $this->ActionForm['og_image_url'] : $data['brand']->getProfileImage()) ?>" id="og_image_preview" width="80" height="80">
                                <input type="file" name="og_image" class="jsOgImage">
                                <br><small>（推奨:400px × 400px 以上 / 必須:200px × 200px）</small>
                                <?php if ( $this->ActionError && !$this->ActionError->isValid('og_image')): ?>
                                    <br>
                                    <span class="attention1"><?php assign ( str_replace(array('<%width%>', '<%height%>'), array('200', '200'), $this->ActionError->getMessage('og_image')))?></span>
                                <?php endif; ?>
                            </dd>
                            <!-- /.adminPageMeta --></dl>

                        <p class="btnSet">
                            <span class="btn3"><a href="javascript:void(0);" id="meta_setting_confirm_btn">保存</a></span>
                    </form>
                    <!-- /.adminPageSettingWrap --></section>
            <?php endif;?>
        <?php endif;?>

        <?php if($data['pageStatus']['can_set_header_tag_setting']):?>
            <h2 class="hd2">CSSカスタマイズ</h2>
            <section class="adminTagWrap">
                <p>こちらにCSSを記載すると全ページの&lt;header&gt;タグ内に表示され、デザインカスタマイズにお使いいただけます。<br>※&lt;style&gt; 〜 &lt;/style&gt; は自動で挿入されます</p>
                <form id="frmHeaderTag" name="frmHeaderTag" action="<?php assign(Util::rewriteUrl('admin-settings', 'page_settings')); ?>" method="POST">
                    <?php write_html($this->csrf_tag()); ?>
                    <p class="adminTag">
                        <?php write_html($this->formTextArea('header_tag_text', PHPParser::ACTION_FORM,  array('cols' => 90, 'rows' => 10))) ?>
                    </p>
                    <p class="btnSet">
                        <span class="btn3"><a href="javascript:void(0)" id="add_header_tag_text_button">保存</a></span>
                    </p>
                </form>
            <!-- /.adminTagWrap --></section>
        <?php endif; ?>

        <h2 class="hd2">フッター計測タグ管理</h2>
        <section class="adminTagWrap">
            <p>こちらに記載すると、サイト内全てのページのフッター部分に設置されます。トラッキングタグなどはこちらに記載してください。</p>
            <form id="frmCvTag" name="frmCvTag" action="<?php assign(Util::rewriteUrl('admin-settings', 'page_settings')); ?>" method="POST">
                <?php write_html($this->csrf_tag()); ?>
                <p class="adminTag">
                    <?php write_html($this->formTextArea('tag_text', PHPParser::ACTION_FORM,  array('cols' => 90, 'rows' => 10))) ?>
                </p>
                <p class="btnSet">
                    <span class="btn3"><a href="javascript:void(0)" id="add_tag_text_button">保存</a></span>
                </p>
            </form>
            <!-- /.adminPageSettingWrap --></section>

        <?php if ($this->getAction()->isLoginManager()): ?>
            <form id="frmTopPageReplace" name="frmPublicRadio" action="<?php assign(Util::rewriteUrl('admin-settings', 'save_page_settings_manager')); ?>" method="POST">
                <?php write_html($this->csrf_tag()); ?>
                <div class="adminManagerItem">
                    <p class="itemLabel"><span class="labelModeAllied">マネージャー設定項目</span></p>
                    <h2 class="hd2">トップページ差替え</h2>
                    <section class="adminToppageWrap">
                        <p>
                            <?php write_html($this->formText('top_page_url', PHPParser::ACTION_FORM, array('placeholder' => $this->getAction()->getDefaultTopPageUrl()))) ?>
                            <?php if ($this->ActionError && !$this->ActionError->isValid('top_page_url')): ?>
                                <span class="attention1"><?php assign($this->ActionError->getMessage('top_page_url')) ?></span>
                            <?php endif; ?>
                            <br><small>ブランドページ内のURLを入力してください</small>
                        </p>
                        <p class="btnSet">
                            <span class="btn3"><a href="javascript:void(0);" id="top_page_replace">保存</a></span>
                        </p>
                        <!-- /.adminToppageWrap --></section>
                    <!-- /.adminManagerItem --></div>
            </form>
        <?php endif; ?>
    </article>
</div>

<div class="modal1 jsModal" id="modal1">
    <section class="modalCont-medium jsModalCont">
        <h1>公開確認</h1>
        <p>公開設定を変更します。よろしいですか？</p>
        <p class="btnSet">
            <span class="btn2"><a href="#closeModal">キャンセル</a></span>
            <span class="btn3"><a href="#closeModal" onclick="document.forms.frmPublicRadio.submit(); $(window).off('beforeunload'); return false;">保存</a></span>
        </p>
    </section>
    <!-- /.modal1 --></div>

<div class="modal1 jsModal" id="globalMenus">
    <section class="modalCont-large jsModalCont">
        <iframe data-src="<?php assign(Util::rewriteUrl('admin-top', 'global_menus')) ?>" frameborder="0"></iframe>
    </section>
</div>

<div class="modal1 jsModal" id="sideMenus">
    <section class="modalCont-large jsModalCont">
        <iframe data-src="<?php assign(Util::rewriteUrl('admin-top', 'side_menus')) ?>" frameborder="0"></iframe>
    </section>
</div>

<div class="modal1 jsModal" id="freeAreaEntries">
    <section class="modalCont-large jsModalCont">
        <iframe data-src="<?php assign(Util::rewriteUrl('admin-top', 'free_area_entries')) ?>"
                frameborder="0"></iframe>
    </section>
</div>

<div class="modal1 jsModal" id="selectPanelKind">
    <section class="modalCont-large jsModalCont">
        <iframe data-src="<?php assign(Util::rewriteUrl('admin-top', 'select_panel_kind')) ?>"
                frameborder="0"></iframe>
    </section>
</div>


<?php write_html($this->parseTemplate('MetaInfoModal.php')); ?>

<?php $param = array_merge($data['pageStatus'], array('script' => array('admin-settings/PageSettingsFormService', 'EditProfileFormService'))) ?>

<?php write_html($this->parseTemplate('BrandcoFooter.php', $param)); ?>
