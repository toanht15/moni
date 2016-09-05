<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoHeader')->render($data['pageStatus'])) ?>
<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoAccountHeader')->render($data['pageStatus'])) ?>

<article>
    <section class="noticeBar1 jsNoticeBarArea1" id="mid-message">
        <p class="<?php assign(config('@message.adminMessage.updated.class')) ?> jsNoticeBarClose" id="jsMessage1"><?php assign(config('@message.aadminMessage.updated.msg')) ?></p>
    </section>
    <?php write_html($this->parseTemplate('AdminBlogHeader.php',array('can_use_embed_page' => $data['can_use_embed_page']))) ?>
    <h1 class="hd1">ページ編集</h1>
    <form id="frmEntry" name="frmEntry" action="<?php assign(Util::rewriteUrl('admin-blog', 'edit_static_html_entry')); ?>" method="POST" enctype="multipart/form-data">
        <?php write_html($this->formHidden('entryId', $data['entry']->id)); ?>
        <?php write_html($this->csrf_tag()); ?>
        <div class="pageSettingWrap1">
            <dl class="pageEdit1">
                <dt><label>レイアウト</label></dt>
                <dd class="pageLayout">
                    <?php write_html($this->parseTemplate('LayoutTypeList.php', $data)); ?>
                </dd>
                <dt><label class="require1" for="field1">ページタイトル</label></dt>
                <dd>
                    <?php write_html($this->formText('title', PHPParser::ACTION_FORM, array('class' => 'categoryName', 'maxlength' => '60', 'id' => 'titleInput'))); ?>
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
                    <small class="supplement1"><a href="javascript:void(0);" class="iconCopy1 jsCopyToClipboardBtn" data-clipboard-text="<?php assign(Util::getBaseUrl()) ?>page/<?php assign($this->getActionFormValue('page_url')); ?>">URLをコピー</a></small>
                    <p><small>※未設定の場合は自動的にパーマリンクが割り振られます。</small></p>
                    <?php if ($this->ActionError && !$this->ActionError->isValid('page_url')): ?>
                        <p class="iconError1"><?php assign($this->ActionError->getMessage('page_url')) ?></p>
                    <?php endif; ?>
                </dd>
            <!-- /.pageEdit1 --></dl>

            <div class="pageStatus1">
                <h1>ステータス：
                    <?php if ($data['entry']->hidden_flg == 0): ?><span class="iconCheck3">公開中</span>
                    <?php elseif ($data['entry']->hidden_flg == 1): ?><span class="iconDraft1">下書き</span>
                    <?php endif; ?>
                </h1>
                <dl>
                    <dt>作成：</dt>
                    <dd><?php assign(Util::cutTextByWidth($data['author']['create_user'], 81)); ?>
                        - <?php assign($this->formatDate($data['author']['create_date'], 'YYYY/MM/DD')) ?></dd>
                    <dt>編集：</dt>
                    <dd><?php assign(Util::cutTextByWidth($data['author']['update_user'], 81)); ?>
                        - <?php assign($this->formatDate($data['author']['update_date'], 'YYYY/MM/DD')) ?></dd>
                    <dt>公開：</dt>
                    <dd><?php assign(Util::cutTextByWidth($data['author']['update_user'], 81)); ?>
                        - <?php assign($this->formatDate($data['entry']->public_date, 'YYYY/MM/DD')) ?></dd>
                </dl>
            </div>
        <!-- /.pageSettingWrap1 --></div>

        <div class="pageContEdit">
            <div class="pagePlainHtmlContEdit" id="jsPageContPlainHtmlEdit">
                <p class="pagePreview"><span class="btn2"><a href="javascript:void(0)" id="previewButtonBlog" class="small1">プレビュー</a></span></p>
                <section>
                    <h1>HTML記述エリア</h1>
                    <?php write_html($this->formTextarea('body', PHPParser::ACTION_FORM, array('cols' => '40', 'rows' => '4', 'width' => '960', 'height' => '580'))); ?>
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
            <section class="pagePublish">
                <h1>公開設定</h1>
                <dl>
                    <dt>ステータス：
                        <?php if ($data['entry']->hidden_flg == 0): ?><span class="iconCheck3">公開中
                        <?php elseif ($data['entry']->hidden_flg == 1): ?><span class="iconDraft1">下書き
                        <?php endif; ?>
                    </span></dt>
                    <dd>
                        <select name="display" id="display"
                                data-uploadurl='<?php assign(Util::rewriteUrl('admin-top', 'ckeditor_upload_file')) ?>'
                                data-listurl='<?php assign(Util::rewriteUrl('admin-blog', 'file_list', null, array('f_id' => BrandUploadFile::POPUP_FROM_STATIC_HTML_ENTRY))) ?>'>
                            <?php if($data['pageStatus']['isAgent']): ?>
                                <option value="1" <?php assign('selected') ?>>下書き</option>
                            <?php else: ?>
                                <option value="1" <?php if ($data['entry']->hidden_flg == 1 ) assign('selected') ?>>下書き</option>
                                <option value="0" <?php if ($data['entry']->hidden_flg == 0) assign('selected') ?>>公開</option>
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
                <p class="deletePage"><a href="javascript:void(0);" class="linkDelete" data-modal_class='.jsLinkDeleteModal' data-entry='<?php assign('entryId='.$data['entry']->id)?>'>ページ削除</a></p>
            <!-- /.pagePublish --></section>
        <!-- /.pageSettingWrap1 --></div>

        <div class="pageEditCheck">
            <ul>
                <li class="btn3"><a href="javascript:void(0);" id="submitEntry">内容確定</a></li>
            </ul>
        <!-- /.pageEditCheck --></div>

        <section class="backPage">
            <p><a href="<?php assign(Util::rewriteUrl('admin-blog', 'static_html_entries')); ?>" class="iconPrev1">ページ一覧へ</a></p>
        <!-- /.backPage --></section>
    </form>
    <input type="hidden" id="variable-container" data-parts-domain="<?php assign(aafwApplicationConfig::getInstance()->query('Static.Url')) ?>">
</article>

<div class="modal1 jsModal jsLinkDeleteModal" id="modal1">
    <section class="modalCont-small jsModalCont">
        <h1>確認</h1>
        <p class="iconError1">このページを削除しますか？</p>
        <p class="btnSet">
            <span class="btn2"><a href="#closeModal" class="small1">キャンセル</a></span>
            <span class="btn4">
                <a id="delete_area" href="javascript:void(0)"
                   data-url='<?php assign(Util::rewriteUrl('admin-blog', 'api_delete_static_html.json')) ?>'
                   data-callback='<?php assign(Util::rewriteUrl('admin-blog', 'static_html_entries', null, array('mid'=>'action-deleted'))) ?>' class="small1">削除する</a>class="small1">削除</a>
            </span>
        </p>
    <!-- /.modalCont-small --></section>
<!-- /.modal1 --></div>

<?php write_html($this->scriptTag('admin-blog/EditPlainStaticHtmlService')) ?>

<link rel="stylesheet" href="<?php assign($this->setVersion('/css/jqueryUI.css'))?>">
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/i18n/jquery.ui.datepicker-ja.min.js"></script>

<script type="text/javascript" src="<?php assign($this->setVersion('/js/zeroclipboard/ZeroClipboard.min.js')) ?>"></script>
<?php write_html($this->parseTemplate('BrandcoFooter.php', $data['pageStatus'])); ?>
