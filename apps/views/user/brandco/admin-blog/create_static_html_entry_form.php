<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoHeader')->render($data['pageStatus'])) ?>
<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoAccountHeader')->render($data['pageStatus'])) ?>

<article>
    <section class="noticeBar1 jsNoticeBarArea1" id="mid-message">
        <p class="<?php assign(config('@message.adminMessage.updated.class')) ?> jsNoticeBarClose" id="jsMessage1"><?php assign(config('@message.adminMessage.updated.msg')) ?></p>
    </section>
    <?php write_html($this->parseTemplate('AdminBlogHeader.php',array('can_use_embed_page' => $data['can_use_embed_page']))) ?>
    <h1 class="hd1">ページ作成</h1>
    <form id="frmEntry" name="frmEntry" action="<?php assign(Util::rewriteUrl('admin-blog', 'create_static_html_entry')); ?>" method="POST" enctype="multipart/form-data">
        <?php write_html($this->csrf_tag()); ?>
        <div class="pageSettingWrap1">

            <dl class="pageEdit1">
                <dt><label>レイアウト</label></dt>
                <dd class="pageLayout">
                    <?php write_html($this->parseTemplate('LayoutTypeList.php', $data)); ?>
                </dd>
                    <dt><label class="require1" for="field1">ページタイトル</label></dt>
                    <dd>
                        <?php write_html($this->formText('title', PHPParser::ACTION_FORM, array('class' => 'categoryName', 'maxlength' => '100', 'id' => 'titleInput'))); ?>
                        <small class="textLimit"></small><br>
                        <?php write_html($this->formCheckBox2('title_hidden_flg', PHPParser::ACTION_FORM, array('id' => 'titleHiddenFlg'), array('1' => '見出しタイトルを非表示にする'))); ?>
                        <?php if ($this->ActionError && !$this->ActionError->isValid('title')): ?>
                            <p class="iconError1"><?php assign($this->ActionError->getMessage('title')) ?></p>
                        <?php endif; ?>
                    </dd>
                    <dt><label class="require1" for="field2">パーマリンク</label></dt>
                    <dd>
                        <?php assign(Util::getBaseUrl()) ?>page/
                        <?php write_html($this->formText('page_url', PHPParser::ACTION_FORM, array('class' => 'directoryName', 'maxlength' => '20'))); ?>
                        <p><small>※未設定の場合は自動的にパーマリンクが割り振られます。</small></p>
                        <?php if ($this->ActionError && !$this->ActionError->isValid('page_url')): ?>
                            <p class="iconError1"><?php assign($this->ActionError->getMessage('page_url')) ?></p>
                        <?php endif; ?>
                    </dd>
                    <dt class="jsNormalLayout">記述タイプを選ぶ</dt>
                    <dd class="jsNormalLayout">
                        <?php write_html($this->formRadio('write_type', PHPParser::ACTION_FORM, array('id' => 'writeTypeInput'), StaticHtmlEntries::$write_types, array(), '', false)); ?>
                    </dd>
             <!-- /.pageEdit1 --></dl>

            <div class="pageStatus1">
                <h1>ステータス：<span class="iconDraft1">新規作成</span></h1>
                <dl>
                    <dt>作成：</dt>
                    <dd><?php assign(Util::cutTextByWidth($data['pageStatus']['userInfo']->name, 81)); ?> - <?php assign($this->getToday()); ?></dd>
                </dl>
            </div>
            <!-- /.pageSettingWrap1 --></div>

        <div class="pageContEdit">
            <div id="jsPageContBlogEdit" class="jsNormalLayout">
                <p class="pagePreview">
                    <span class="btn2"><a href="javascript:void(0)" id="previewButtonBlog" class="small1">プレビュー</a></span>
                </p>
                <?php write_html($this->formTextarea('body', PHPParser::ACTION_FORM, array('cols' => '40', 'rows' => '4', 'width' => '960', 'height' => '580'))); ?>
                <?php if ($this->ActionError && !$this->ActionError->isValid('body')): ?>
                    <p class="iconError1"><?php assign($this->ActionError->getMessage('body')) ?></p>
                <?php endif; ?>

                <p><a href="javascript:void(0)" class="linkAdd" id="addExtraBody">登録が必要な限定コンテンツを作成</a></p>

                <?php write_html($this->formTextarea('extra_body', PHPParser::ACTION_FORM, array('cols' => '40', 'rows' => '4', 'width' => '960', 'height' => '580'))); ?>
                <?php if ($this->ActionError && !$this->ActionError->isValid('extra_body')): ?>
                    <p class="iconError1" id="extraBodyIconError"><?php assign($this->ActionError->getMessage('extra_body')) ?></p>
                <?php endif; ?>
            </div>
            <div id="jsPageContTempEdit" class="pageContTempEdit jsNormalLayout">
                <p class="pagePreview">
                    <span class="btn2"><a href="javascript:void(0)" id="previewButtonTemplate" class="small1">プレビュー</a></span>
                </p>

                <?php write_html($this->formHidden('template_contents_json', PHPParser::ACTION_FORM, array('id' => 'template_contents_json'))); ?>
                <?php if ($this->ActionError && !$this->ActionError->isValid('template_contents_json')): ?>
                    <p class="iconError1"><?php assign($this->ActionError->getMessage('template_contents_json')) ?></p>
                <?php endif; ?>

                <section>
                <h1>追加可能なパーツ一覧</h1>
                <ul class="pagePartsType">
                    <li>
                        <p>画像スライダー</p>
                        <p><img src="<?php assign($this->setVersion('/img/pageTempParts/iconSlider.gif'))?>"></p>
                        <p class="addParts"><a href="#pagePartsImageSliderSetting" data-type="<?php assign(StaticHtmlTemplate::TEMPLATE_TYPE_IMAGE_SLIDER);?>" class="openPartsModal">追加</a></p>
                    </li>
                    <li>
                        <p>画像+テキスト</p>
                        <p><img src="<?php assign($this->setVersion('/img/pageTempParts/iconFloatImage.gif'))?>"></p>
                        <p class="addParts"><a href="#pagePartsFloatImageSetting" data-type="<?php assign(StaticHtmlTemplate::TEMPLATE_TYPE_FLOAT_IMAGE);?>" class="openPartsModal">追加</a></p>
                    </li>
                    <li>
                        <p>画像全面配置</p>
                        <p><img src="<?php assign($this->setVersion('/img/pageTempParts/iconFullImage.gif'))?>"></p>
                        <p class="addParts"><a href="#pagePartsFullImageSetting" data-type="<?php assign(StaticHtmlTemplate::TEMPLATE_TYPE_FULL_IMAGE);?>" class="openPartsModal">追加</a></p>
                    </li>
                    <li>
                        <p>文章入力</p>
                        <p><img src="<?php assign($this->setVersion('/img/pageTempParts/iconText.gif'))?>"></p>
                        <p class="addParts"><a href="#pagePartsTextSetting" data-type="<?php assign(StaticHtmlTemplate::TEMPLATE_TYPE_TEXT);?>" class="openPartsModal">追加</a></p>
                    </li>
                    <li>
                        <p>Instagram投稿一覧</p>
                        <p><img src="<?php assign($this->setVersion('/img/pageTempParts/inconInstaImages.gif'))?>"></p>
                        <p class="addParts"><a href="#pagePartsInstaSetting" data-type="<?php assign(StaticHtmlTemplate::TEMPLATE_TYPE_INSTAGRAM);?>" class="openPartsModal">追加</a></p>
                    </li>
                    <?php if ($data['can_use_stamp_rally']): ?>
                    <li><p>スタンプラリー
                            <span class="iconHelp">
                            <span class="textBalloon1">
                            <span>スタンプラリー形式でファンごとにキャンペーンの参加記録を表示させる機能です。</span>
                            <!-- /.textBalloon1 --></span>
                            <!-- /.iconHelp --></span>
                        </p>
                        <p><img src="<?php assign($this->setVersion('/img/pageTempParts/iconStampRally.gif'))?>"></p>
                        <p class="addParts"><a href="#jsPagePartsStampRallySetting" data-type="<?php assign(StaticHtmlTemplate::TEMPLATE_TYPE_STAMP_RALLY);?>" class="openPartsModal">追加</a></p>
                    </li>
                    <?php endif ?>
                 <!-- /.pagePartsType --></ul>
                </section>

                <section class="pagePartsList">
                    <h1>設定済みパーツ一覧</h1>
                    <ul id="templatePartsContainer"></ul>
                </section>
            <!-- /#jsPageContTempEdit --></div>

            <div class="pagePlainHtmlContEdit" id="jsPageContPlainHtmlEdit" style="display: none">
                <p class="pagePreview"><span class="btn2"><a href="javascript:void(0)" id="previewButtonPlainBlog" class="small1">プレビュー</a></span></p>
                <section>
                    <h1>HTML記述エリア</h1>
                    <?php write_html($this->formTextarea('body', PHPParser::ACTION_FORM, array('cols' => '40', 'rows' => '4', 'width' => '960', 'height' => '580', 'id' => 'plainBodyAreaText'))); ?>
                    <ul>
                        <li>・プレーンモードでは、「パーマリンク」「HTML記述エリア」「公開設定」の内容のみが該当ページに反映されます。</li>
                        <li>・「ページタイトル」は、ページ一覧の表示のみ適用され、該当ページのHTMLには反映されません。</li>
                        <li>・必ずDOCTYPE宣言から、body,htmlの閉じタグまで記述ください。</li>
                        <li>・&lt;/body&gt;の直前に、必ず以下のタグを挿入ください。<br>プレーンHTMLモードフッタータグ：<#moniplaFooter></li>
                    </ul>
                </section>
            <!-- /.pagePlainHtmlContEdit --></div>
        <!-- /.pageContEdit --></div>

        <div class="pageSettingWrap1">

            <section class="pageCategory jsNormalLayout">
                <h1>カテゴリ</h1>

                <p class="addNewCategory"><a href="javascript:void(0);" class="linkAdd">新規追加</a></p>

                <div class="newCategory" style="display: none">
                    <dl>
                        <dt>カテゴリ名</dt>
                        <dd>
                            <?php write_html($this->formText('name', PHPParser::ACTION_FORM, array('class' => 'categoryName', 'maxlength' => '35', 'id' => 'categorytTitleInput'))); ?>
                            <?php if ($this->ActionError && !$this->ActionError->isValid('name')): ?>
                                <p class="iconError1" id="categoryError"><?php assign($this->ActionError->getMessage('name')) ?></p>
                            <?php endif; ?>
                            <?php write_html($this->formHidden('callback_url', Util::rewriteUrl('admin-blog', 'create_static_html_entry_form'))) ?>
                        </dd>
                        <dt>親カテゴリ</dt>
                        <dd id="TagTreeSelectionDD">
                            <?php write_html($this->parseTemplate('TagTreeSelection.php', array('categories_tree'=>$data['categories_tree']))) ?>
                        </dd>
                    </dl>
                    <p><span class="btn3"><a href="javascript:void(0)" class="small1" data-submit-action="<?php write_html(Util::rewriteUrl('admin-blog', 'api_create_static_html_category.json')) ?>" id="addCategoryButton">追加</a></span></p>
                    <!-- /.newCategory --></div>
                <ul id="TagTreeListUL">
                <?php if(count($data['categories_tree'])): ?>
                    <?php write_html($this->parseTemplate('TagTreeList.php', array ('categories_tree' => $data['categories_tree'], 'type' => StaticHtmlCategory::DISPLAY_LIST_TYPE_CHECKBOX))) ?>
                <?php endif; ?>
                    </ul>
                <!-- /.pageCategory --></section>

            <section class="pagePlugin jsNormalLayout">
                <h1>SNSプラグインの表示</h1>
                <ul>
                    <?php foreach (StaticHtmlEntries::$sns_plugins as $sns_plugin_id => $sns_plugin_label): ?>
                        <li><input type="checkbox" name="sns_plugins[]" value="<?php assign($sns_plugin_id); ?>" <?php assign(in_array($sns_plugin_id, $this->getActionFormValue('sns_plugins')) ? "checked=checked" : "" ); ?>><?php assign($sns_plugin_label) ?></li>
                    <?php endforeach; ?>

                    <?php if ($data['entry']->sns_plugin_tag_text): ?>
                        <li><textarea name="sns_plugin_tag_text"  cols="30" rows="2"><?php assign($data['entry']->sns_plugin_tag_text); ?></textarea></li>
                    <?php else: ?>
                        <li><a href="javascript:void(0);" class="linkAdd" id="snsScriptAdd">追加</a></li>
                        <li style="display: none" id="snsScriptText"><textarea name="sns_plugin_tag_text" cols="30" rows="2"></textarea></li>
                    <?php endif; ?>
                </ul>
                <!-- /.pagePlugin --></section>

            <section class="pageMeta jsNormalLayout">
                <h1>meta情報</h1>
                <dl>
                    <dt>title<small id="meta_title_text_limit"></small></dt>
                    <dd><?php write_html($this->formTextArea('meta_title', PHPParser::ACTION_FORM, array('class' => 'metaText jsMetaDataInput', 'maxlength' => '60', 'cols' => '30', 'rows' => '1', 'data-label' => 'meta_title'))); ?></dd>
                    <dt>description<small id="meta_description_text_limit"></small></dt>
                    <dd><?php write_html($this->formTextArea('meta_description', PHPParser::ACTION_FORM, array('class' => 'metaText jsMetaDataInput', 'maxlength' => '124', 'cols' => '30', 'rows' => '2', 'data-label' => 'meta_description'))); ?></dd>
                    <dt>keyword(,カンマ区切り)<small id="meta_keyword_text_limit"></small></dt>
                    <dd><?php write_html($this->formTextArea('meta_keyword', PHPParser::ACTION_FORM, array('class' => 'metaText jsMetaDataInput', 'maxlength' => '511', 'cols' => '30', 'rows' => '2', 'data-label' => 'meta_keyword'))); ?></dd>
                    <dt>OG Image</dt>
                    <dd>
                        <img <?php write_html( $this->ActionForm['og_image_url'] ? "" : "style='display:none'" );?> src="<?php assign($this->ActionForm['og_image_url'])?>" id="ogImage" width="214" height="180">
                        <input type="file" name="og_image" class="actionImage" maxlength="512">
                    </dd>
                </dl>
                <!-- /.pageMeta --></section>

            <section class="pagePublish">
                <h1>公開設定</h1>
                <dl>
                    <dt>ステータス：<span class="iconDraft1">下書き
                            </span></dt>
                    <dd>
                        <select name="display" id="display"
                                data-uploadurl='<?php assign(Util::rewriteUrl('admin-top', 'ckeditor_upload_file')) ?>'
                                data-listurl='<?php assign(Util::rewriteUrl('admin-blog', 'file_list', null, array('f_id' => BrandUploadFile::POPUP_FROM_STATIC_HTML_ENTRY))) ?>'>
                            <option value="1" selected>下書き</option>
                            <?php if(!$data['pageStatus']['isAgent']):?>
                            <option value="0" <?php if ($data['entry']->hidden_flg === 0) assign('selected') ?>>公開</option>
                            <?php endif ?>
                        </select>
                    </dd>
                    <dt>公開日</dt>
                    <dd>
                        <?php write_html($this->formText(
                            'public_date',
                            PHPParser::ACTION_FORM,
                            array('maxlength' => '10', 'class' => 'jsDate inputDate', 'placeholder' => '年/月/日'))); ?>
                        <?php write_html($this->formSelect(
                            'public_time_hh',
                            PHPParser::ACTION_FORM,
                            array('class' => 'inputTime'), $this->getHours()));?><span class="coron">:</span
                            ><?php write_html($this->formSelect(
                            'public_time_mm',
                            PHPParser::ACTION_FORM,
                            array('class' => 'inputTime'), $this->getMinutes())); ?>
                    </dd>
                </dl>
                <!-- /.pagePublish --></section>
            <!-- /.pageSettingWrap1 --></div>

        <?php if ($data['has_comment_option']): ?>
        <div class="pageCommentPluginSetting jsModuleContWrap">
            <section class="commentPluginWrap">
                <?php $is_using_plugin = $this->getActionFormValue('cp_status') == CommentPlugin::COMMENT_PLUGIN_STATUS_PUBLIC ?>
                <h1 class="commentPlugiTitle jsModuleContTile <?php assign(!$is_using_plugin ? "close" : "") ?>">コメントプラグイン</h1>
                <div class="cf jsModuleContTarget" <?php if (!$is_using_plugin): ?>style="display: none;"<?php endif ?>>
                    <dl class="commentPluginEdit">
                        <dt>ステータス</dt>
                        <dd>
                            <label><input type="radio" name="cp_status" value="<?php assign(CommentPlugin::COMMENT_PLUGIN_STATUS_PUBLIC) ?>"
                                    <?php assign($this->getActionFormValue('cp_status') == CommentPlugin::COMMENT_PLUGIN_STATUS_PUBLIC ? "checked=checked" : "") ?>>表示する</label>
                            <label><input type="radio" name="cp_status" value="<?php assign(CommentPlugin::COMMENT_PLUGIN_STATUS_PRIVATE) ?>"
                                    <?php assign($this->getActionFormValue('cp_status') == CommentPlugin::COMMENT_PLUGIN_STATUS_PRIVATE ? "checked=checked" : "") ?>>表示しない</label>
                        </dd>
                        <dt>SNSシェア</dt>
                        <dd><?php write_html($this->formCheckbox('cp_sns_list', PHPParser::ACTION_FORM, array(), CommentPluginShareSetting::$comment_plugin_share_settings)); ?></dd>
                        <!-- /.commentPluginEdit --></dl>
                    <!-- /.cf --></div>
                <!-- /.commentPluginWrap --></section>
            <!-- /.pageSettingWrap1 --></div>
        <?php endif ?>

        <div class="pageEditCheck">
            <ul>
                <li class="btn3"><a href="javascript:void(0);" id="submitEntry">内容確定</a></li>
            </ul>
            <!-- /.pageEditCheck --></div>

        <section class="backPage">
            <p><a href="<?php assign(Util::rewriteUrl('admin-blog', 'static_html_entries')); ?>" class="iconPrev1">ページ一覧へ</a>
            </p>
            <!-- /.backPage --></section>
    </form>
    <input type="hidden" id="variable-container" data-parts-domain="<?php assign(aafwApplicationConfig::getInstance()->query('Static.Url')) ?>" data-brand_id="<?php assign($this->brand->id);?>" >
</article>

<?php write_html($this->parseTemplate('AdminStaticHtmlImageSlider.php')); ?>
<?php write_html($this->parseTemplate('AdminStaticHtmlFloatImage.php')); ?>
<?php write_html($this->parseTemplate('AdminStaticHtmlFullImage.php')); ?>
<?php write_html($this->parseTemplate('AdminStaticHtmlText.php')); ?>
<?php write_html($this->parseTemplate('AdminStaticHtmlInstagram.php')); ?>
<?php write_html($this->parseTemplate('AdminStaticHtmlStampRally.php')); ?>
<?php write_html($this->parseTemplate('AdminStaticHtmlBoundary.php')); ?>


<div class="modal1 jsModal" id="pagePartsTextSetting">
    <section class="modalCont-large jsModalCont">
        <h1>文章入力</h1>
        <div class="pagePartsSetting">
            <div class="pagePartsSettingCont">
                <p><label for="insertTextCaption">テキスト</label><textarea id="insertTextCaption"></textarea></p>
                <!-- /.pagePartsSettingCont --></div>
            <p class="btnSet">
                <span class="btn2"><a href="#closeModal" class="small1">キャンセル</a></span>
                <span class="btn3"><a href="#closeModal" class="small1">設定する</a></span>
            </p>
            <!-- /.pagePartsSetting --></div>
    </section>
    <!-- /#pagePartsTextSetting --></div>

<?php write_html($this->scriptTag('admin-blog/EditStaticHtmlService')) ?>
<?php write_html($this->scriptTag('admin-blog/PartsTemplateService')) ?>

<link rel="stylesheet" href="<?php assign($this->setVersion('/css/jqueryUI.css'))?>">
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/i18n/jquery.ui.datepicker-ja.min.js"></script>

<script type="text/javascript" src="<?php assign($this->setVersion('/ckeditor/ckeditor.js')) ?>"></script>
<?php write_html($this->parseTemplate('BrandcoFooter.php', $data['pageStatus'])); ?>
