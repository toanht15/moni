<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoHeader')->render($data['pageStatus'])) ?>
<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoAccountHeader')->render($data['pageStatus'])) ?>

<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
    <div class="adminWrap">
        <?php write_html($this->parseTemplate('SettingSiteMenu.php', $data['pageStatus'])) ?>

        <article class="adminMainCol">

            <h1 class="hd1">ユーザー設定</h1>

            <p class="btnPagePreview"><span class="btn1"><a target="_blank" class="middle1 preview_button" preview_url="<?php assign(Util::rewriteUrl('auth', 'signup_preview') . '?demo_token=' . hash("sha256", $data['brand']->created_at)); ?>">プレビュー</a></span></p>

            <div class="adminUserHead">
                <h2 class="hd2">個人情報</h2>
                <p class="iconHelp">
                    <span class="text">ヘルプ</span>
                      <span class="textBalloon1">
                        <span>
                          ここでは性別・生年月日・住所(都道府県)のみ<br>の取得がオススメです
                        </span>
                      <!-- /.textBalloon1 --></span>
                <!-- /.iconHelp --></p>
            <!-- /.iconHelp --></div>

            <form id="frmPrivacy" name="frmPrivacy" action="<?php assign(Util::rewriteUrl('admin-settings', 'user_settings')); ?>" method="POST" class="frmPrivacy">

                <section class="adminUserDataWrap">
                    <p>ここで設定した項目は、ユーザーが会員登録時に入力必須になります。</p>
                    <?php write_html($this->csrf_tag()); ?>
                    <?php write_html($this->formHidden('mode', BrandPageSettingService::MODE_PRIVACY)); ?>
                    <ul class="adminUserData">
                        <?php if ($data['brand']->id == '452' || $data['brand']->id == '453' || $data['brand']->id == '479'): // 健検HC TODO NECレノボグループ対応  ?>
                            <li><?php write_html( $this->formCheckbox('privacy[]',array(in_array('privacy_required_name', $this->getActionFormValue('privacy'))?'privacy_required_name':''),array(),array('privacy_required_name' => '氏名（かな）'))); ?></li>
                        <?php endif; ?>
                        <li><?php write_html( $this->formCheckbox('privacy[]',array(in_array('privacy_required_sex', $this->getActionFormValue('privacy'))?'privacy_required_sex':''),array(),array('privacy_required_sex' => '性別'))); ?></li>
                        <li><?php write_html( $this->formCheckbox('privacy[]',array(in_array('privacy_required_birthday', $this->getActionFormValue('privacy'))?'privacy_required_birthday':''),array(),array('privacy_required_birthday' => '生年月日'))); ?></li>
                        <li>
                            <?php write_html( $this->formCheckbox('privacy[]',array(in_array('privacy_required_address', $this->getActionFormValue('privacy')) && $this->getActionFormValue('privacy_address') == BrandPageSetting::GET_STATE_ADDRESS ? 'privacy_required_address' : ''),array(),array('privacy_required_address' => '居住地 (都道府県)'))); ?>
                            <?php write_html( $this->formHidden( 'privacy_address', BrandPageSetting::GET_STATE_ADDRESS)); ?>
                        </li>
                        <li class="jsCheckToggleWrap">
                            <?php write_html( $this->formCheckbox('privacy[]',array(in_array('privacy_required_restricted', $this->getActionFormValue('privacy'))?'privacy_required_restricted':''),array('class' => 'jsCheckToggle'),array('privacy_required_restricted' => '年齢制限',))); ?>
                            <?php write_html($this->formSelect('restricted_age', $this->getActionFormValue('restricted_age')?$this->getActionFormValue('restricted_age'):15, array(), range(0, 100))) ?>
                            歳以上
                            <?php if ($this->ActionError && !$this->ActionError->isValid('privacy_required_birthday')): ?>
                                <p class="attention1"><?php assign($this->ActionError->getMessage('privacy_required_birthday')) ?></p>
                            <?php endif; ?>
                            <?php if ($this->ActionError && !$this->ActionError->isValid('restricted_age')): ?>
                                <p class="attention1"><?php assign($this->ActionError->getMessage('restricted_age')) ?></p>
                            <?php endif; ?>
                            <?php if($data['can_set_authentication_page']): ?>
                                <?php write_html($this->formHidden('authentication_page_content',$this->getActionFormValue('authentication_page_content') )); ?>
                                <?php write_html($this->formHidden('upload_url', Util::rewriteUrl('admin-top', 'ckeditor_upload_file'))); ?>
                                <?php write_html($this->formHidden('list_file_url', Util::rewriteUrl ( 'admin-blog', 'file_list', null, array('f_id' => BrandUploadFile::POPUP_FROM_STATIC_HTML_ENTRY)))); ?>
                                <ul class="jsCheckToggleTarget adminUserAuthentication" <?php write_html(in_array('privacy_required_restricted', $this->getActionFormValue('privacy'))? '' : 'style="display: none"')?>>
                                    <li>
                                        <p><label><?php write_html( $this->formCheckbox('age_authentication_flg',array($this->getActionFormValue('age_authentication_flg') ? 'age_authentication_flg' : ''),array(),array('age_authentication_flg' => 'ページ来訪時にも年齢確認をする'))); ?>
                                            <a href="<?php assign($data['authentication_page_preview_url']) ?>" class="openNewWindow1 authenticationPagePrev" target="_blank">ページを確認</a>（<a href="#modal4" class="jsOpenModal small1" id="openAuthenticationPageModal">デザイン変更</a>）
                                        </p>
                                        <p><label>年齢対象外の遷移URL<?php write_html( $this->formText('not_authentication_url',$this->getActionFormValue('not_authentication_url'))); ?></label></p>
                                        <?php if ($this->ActionError && !$this->ActionError->isValid('not_authentication_url')): ?>
                                            <p class="attention1"><?php assign($this->ActionError->getMessage('not_authentication_url')) ?></p>
                                        <?php endif; ?>
                                    </li>
                                </ul>
                            <?php endif; ?>
                        </li>
                        <?php //write_html($this->parseTemplate('ProfileQuestionnaireWidget.php', array('profile_questionnaires'=>$data['profile_questionnaires']))) ?>
                    </ul>

                    <p class="btnSet">
                        <span class="btn2"><a href="javascript:void(0)" class="cancelPrivacy middle1">キャンセル</a></span>
                        <span class="btn3"><a href="javascript:void(0)" id="frmPrivacySubmit" class="middle1">保存</a></span>
                    </p>
                    <!-- /.adminUserDataWrap --></section>
            </form>

            <?php if($data['brand']->hasOption(BrandOptions::OPTION_FAN_LIST)):?>
            <h2 class="hd2">ファン登録時アンケート</h2>
            <section class="adminUserDataWrap">
                <p>この機能を利用して、メールアドレスなどの個人情報を取得することはできません。<br>遵守いただけない場合サービスのご提供を停止、終了する場合があります。</p>
                <?php if ($data['profile_question_error']): ?>
                    <p class="iconError1">アンケートの設定が完了していません</p>
                <?php endif; ?>

                <form id="profileQuestionForm" name="profileQuestionForm" action="<?php assign(Util::rewriteUrl('admin-settings', 'save_profile_question')); ?>" method="POST">
                    <?php write_html($this->csrf_tag()); ?>
                    <?php write_html($this->formHidden('question_order', '')) ?>
                    <ul class="adminUserData" id="ProfileQuestionnaire">
                    <!--アンケートリスト-->
                    <!-- /.adminUserData --></ul>
                </form>
                <a href="javascript:void(0)" class="linkAdd" id="addNewQuestionnaire">フリー項目を追加する</a>

            <p class="btnSet">
                <span class="btn2"><a href="javascript:void(0)" class="cancelPrivacy middle1">キャンセル</a></span>
                <span class="btn3"><a href="javascript:void(0)" id="submitProfileQuestion" class="middle1">保存</a></span>
                <!-- /.btnSet --></p>

            <!-- /.adminUserDataWrap --></section>
            <?php endif;?>

            <h2 class="hd2">利用規約</h2>

            <form id="frmAgreement" name="frmAgreement"
                  action="<?php assign(Util::rewriteUrl('admin-settings', 'user_settings')); ?>" method="POST">
                <section class="adminRuleSettingWrap">
                    <p>貴社の利用規約を、こちらに記載してください。ユーザーが会員登録するときに確認する利用規約に追加されます。Allied ID標準の利用規約があるので、記載は任意です。</p>
                    <?php write_html($this->csrf_tag()); ?>
                    <?php write_html($this->formHidden('mode', BrandPageSettingService::MODE_AGREEMENT)); ?>
                    <p class="adminRuleSetting">
                        <?php write_html($this->formTextArea(
                            'agreement',
                            PHPParser::ACTION_FORM,
                            array('cols' => 30, 'rows' => 10)
                        )); ?>
                        <?php //プレビュー実装されていないので一旦コメントアウト?>
                        <!-- <a href="#" class="openNewWindow1">確認する</a> -->
                    </p>

                    <?php if($data['pageStatus']['isLoginManager']): ?>
                    <p class="labelModeAllied"><label>
                        <?php write_html($this->formCheckbox('show_agreement_checkbox', array($this->getActionFormValue('show_agreement_checkbox')), array(),
                            array('1'=>'利用規約への同意を確認するチェックボックスを表示する'))); ?>
                        </label></p>
                    <?php endif; ?>

                    <p class="btnSet">
                        <span class="btn2"><a href="javascript:void(0)" class="cancelPrivacy middle1">キャンセル</a></span>
                        <span class="btn3"><a href="javascript:void(0)" class="middle1" onclick="document.frmAgreement.submit();return false;" id="frmAgreementSubmit">保存</a></span>
                    </p>
                    <!-- /.adminRuleSettingWrap --></section>
                <?php if ($this->getActionFormValue('agreement')): ?>
                    <p class="btnPagePreview"><span class="btn1"><a target="_blank" class="middle1 preview_button" preview_url="<?php assign(Util::rewriteUrl('', 'agreement')); ?>">プレビュー</a></span></p>
                <?php endif; ?>
            </form>

            <?php if($data['can_use_login_limit_setting']): ?>
                <h2 class="hd2">新規登録・ログインアカウント設定</h2>
                <form action="<?php assign(Util::rewriteUrl('admin-settings', 'save_brand_login_setting')); ?>" method="POST">
                    <?php write_html($this->csrf_tag()); ?>
                    <section class="adminSettingBase">
                        <p>新規登録・ログイン時に利用できるアカウントを設定します。<br>必ず一つ以上お選びください。</p>
                        <ul class="adminUserSns">
                            <li><?php write_html( $this->formCheckBox('brand_login_snses[]', $this->getActionFormValue('brand_login_snses'), array(), array(SocialAccountService::SOCIAL_MEDIA_FACEBOOK => 'Facebook'))); ?></li>
                            <li><?php write_html( $this->formCheckBox('brand_login_snses[]', $this->getActionFormValue('brand_login_snses'), array(), array(SocialAccountService::SOCIAL_MEDIA_TWITTER => 'Twitter'))); ?></li>
                            <li><?php write_html( $this->formCheckBox('brand_login_snses[]', $this->getActionFormValue('brand_login_snses'), array(), array(SocialAccountService::SOCIAL_MEDIA_LINE => 'LINE'))); ?></li>
                            <li><?php write_html( $this->formCheckBox('brand_login_snses[]', $this->getActionFormValue('brand_login_snses'), array(), array(SocialAccountService::SOCIAL_MEDIA_INSTAGRAM => 'Instagram'))); ?></li>
                            <li><?php write_html( $this->formCheckBox('brand_login_snses[]', $this->getActionFormValue('brand_login_snses'), array(), array(SocialAccountService::SOCIAL_MEDIA_YAHOO => 'Yahoo!'))); ?></li>
                            <li><?php write_html( $this->formCheckBox('brand_login_snses[]', $this->getActionFormValue('brand_login_snses'), array(), array(SocialAccountService::SOCIAL_MEDIA_GOOGLE => 'Google'))); ?></li>
                            <li><?php write_html( $this->formCheckBox('brand_login_snses[]', $this->getActionFormValue('brand_login_snses'), array(), array(SocialAccountService::SOCIAL_MEDIA_PLATFORM => 'メールアドレス'))); ?></li>
                            <!-- /.adminUserSns --></ul>
                        <p class="btnSet">
                            <span class="btn2"><a href="javascript:void(0)" class="cancelPrivacy middle1">キャンセル</a></span>
                            <span class="btn3"><a href="javascript:void(0)" class="middle1 jsSettingFormSubmit">保存</a></span>
                        </p>
                        <!-- /.adminSettingBase --></section>
                </form>
            <?php endif; ?>

        </article>
    </div>

<div class="modal1 jsModal" id="modal2">
    <section class="modalCont-small jsModalCont">
        <h1>確認</h1>
        <p><span class="attention1" id="modal2_text"></span></p>
        <p class="btnSet"><span class="btn2"><a href="#closeModal" class="middle1">キャンセル</a></span>
            <span class="btn4"><a id="deleteButton" href="javascript:void(0)" class="middle1">削除する</a></span></p>
    </section>
</div>

<div class="modal1 jsModal jsModalPreview" id="modal3">
    <section class="modalCont-small jsModalCont">
        <h1>確認</h1>
        <p><span class="attention1">編集したデータが保存されていない場合、プレビューに反映されません。プレビューページへ推移してもよろしいでしょうか？</span></p>
        <p class="btnSet">
            <span class="btn2"><a href="#closeModal" class="middle1">キャンセル</a></span>
            <span class="btn3">
                <a href="javascript:void(0)" data-url='<?php assign(Util::rewriteUrl('auth', 'signup_preview') . '?demo_token=' . hash("sha256", $data['brand']->created_at)); ?>' class="middle1" id="submitPreviewButton">プレビュー</a>
            </span>
        </p>
    <!-- /.modalCont-small --></section>
<!-- /.modal1 --></div>

<?php if($data['can_set_authentication_page']): ?>
    <div class="modal1 jsModal" id="modal4">
        <section class="modalCont-large jsModalCont">
            <h1>年齢確認ページ</h1>
            <div class="pagePartsSetting">
                <div class="adminPageAuthenticationEdit">
                    <p>
                        全てのユーザーに遷移先を用意するため、以下2つのリンク(タグ)が含まれたボタンを必ず設置してください。<br>
                        年齢制限を満たす(はい)：a href="##LINKYES##"<br>
                        年齢制限を満たさない(いいえ)：a href="##LINKNO##"<br>
                        ※本来の遷移先と年齢対象外の遷移先に自動で振り分けられます。個別にURLの設定は必要ありません。<br>
                        ※入力を空にするとデザインが初期状態に戻ります。<br>
                    </p>
                    <div class="wysiwygArea">
                        <p class="adminPageAuthenticationPreview"><span class="btn2"><a href="javascript:void(0)" class="small1" id="preview_authentication_page">プレビュー</a></span></p>
                        <div>
                            <p style="display: block; max-width: none;" width="1000" height="470"><textarea id="pageContentSetting"></textarea></p>
                            <p class="iconError1" id="pageContentSettingError" style="display: none;">「##LINKYES##」と「##LINKNO##」のタグが正常に設置されていません。設定条件を再度ご確認ください。</p>
                        </div>
                        <!-- /.wysiwygArea --></div>
                    <!-- /.adminPageAuthenticationEdit --></div>
                <p class="btnSet">
                    <span class="btn2"><a href="#closeModal" class="small1">キャンセル</a></span>
                    <span class="btn3"><a href="javascript:void(0)" id="save_authentication_page" class="small1">設定する</a></span>
                </p>
                <!-- /.pagePartsSetting --></div>
        </section>
    <!-- /#pagePartsTextSetting --></div>
<?php endif; ?>

<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
<script type="text/javascript" src="<?php assign($this->setVersion('/ckeditor/ckeditor.js'))?>"></script>

<?php $param = array_merge($data['pageStatus'], array('script' => array('admin-settings/UserSettingsFormService'))) ?>
<?php write_html($this->parseTemplate('BrandcoFooter.php', $param)); ?>