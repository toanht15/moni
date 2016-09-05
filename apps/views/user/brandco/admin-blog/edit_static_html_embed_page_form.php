<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoHeader')->render($data['pageStatus'])) ?>
<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoAccountHeader')->render($data['pageStatus'])) ?>

<article>
    <?php write_html($this->parseTemplate('AdminBlogHeader.php',array('can_use_embed_page' => true))) ?>
    <h1 class="hd1">埋込ページ編集
    <span class="iconHelp">
      <span class="text">ヘルプ</span>
      <span class="textBalloon1">
        <span>作成したページを埋込タグを使って、<br>外部サイトのページの一部として表示させることができます。</span>
      <!-- /.textBalloon1 --></span>
    <!-- /.iconHelp --></span>
    </h1>
    <form id="frmEntry" name="frmEntry" action="<?php assign(Util::rewriteUrl('admin-blog', 'edit_static_html_embed_page')); ?>" method="POST" enctype="multipart/form-data">
        <?php write_html($this->formHidden('entryId', $data['entry']->id)); ?>
        <?php write_html($this->formHidden('page_url', $data['entry']->page_url)); ?>
        <?php write_html($this->csrf_tag()); ?>
        <div class="pageSettingWrap1">
            <dl class="pageEdit1">
                <dt><label for="field1">ページタイトル</label></dt>
                <dd>
                    <?php write_html($this->formText('title', PHPParser::ACTION_FORM, array('class' => 'categoryName', 'maxlength' => '60', 'id' => 'titleInput'))); ?>
                    <small class="textLimit"></small><br>
                    <?php if ($this->ActionError && !$this->ActionError->isValid('title')): ?>
                        <p class="iconError1"><?php assign($this->ActionError->getMessage('title')) ?></p>
                    <?php endif; ?>
                </dd>
                <dt>埋込タグ</dt>
                <dd class="pageLayout">
                    <p>ページを「公開」すると、タグが発行され、コピーすることができます。<br>埋込をしたい外部サイトのページに下記タグをコピー＆ペーストしてください。</p>
                    <div class="inputTag">
                        <p><a href="javascript:void(0);" class="iconCopy1 jsCopyToClipboardBtn" data-clipboard-text="<?php assign($data['embed_content']); ?>">タグをコピー</a></p>
                        <p><textarea name="" id="" cols="30" rows="5" readonly><?php assign($data['embed_content']); ?></textarea></p>
                        <!-- /.inputTag --></div>
                    </p>
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
            <p>リンクを設定する際は以下にご注意ください。<br>
                ・同じウインドウ内でページ遷移をしたい：&lt;a&gt;タグに　<b>target="_top"</b>　を追加<br>
                ・新しいウインドウで表示をしたい：　&lt;a&gt;タグに　<b>target="_blank"</b>　を追加</p>
            <div id="jsPageContBlogEdit">
                <?php write_html($this->formTextarea('body', PHPParser::ACTION_FORM, array('cols' => '40', 'rows' => '4', 'width' => '960', 'height' => '580'))); ?>
                <?php if ($this->ActionError && !$this->ActionError->isValid('body')): ?>
                    <p class="iconError1"><?php assign($this->ActionError->getMessage('body')) ?></p>
                <?php endif; ?>
            </div>
        <!-- /.pageContEdit --></div>
        <div class="pageSettingWrap1">
            <section class="pageBrowse">
                <h1>閲覧設定</h1>
                <ul>
                    <li><?php write_html($this->formRadio('public_flg', PHPParser::ACTION_FORM, array('id' => 'publicFlgInput'), array(StaticHtmlEmbedEntry::PUBLIC_PAGE => '誰でも閲覧可能'), array(), '', false)); ?></li>
                    <li><?php write_html($this->formRadio('public_flg', PHPParser::ACTION_FORM, array('id' => 'publicFlgInput'), array(StaticHtmlEmbedEntry::NOT_PUBLIC_PAGE => '閲覧には登録・ログインが必要'), array(), '', false)); ?>
                        <div class="<?php assign($this->getActionFormValue('public_flg') == StaticHtmlEmbedEntry::PUBLIC_PAGE ? 'loginAccountDisabled' : 'loginAccount' ) ?>">
                            <p><strong>登録・ログインが可能なアカウント</strong></p>
                                <?php if($this->getActionFormValue('public_flg') == StaticHtmlEmbedEntry::PUBLIC_PAGE): ?>
                                    <p><?php foreach($data['sns_login_types'] as $snsId => $snsName): ?><label><input type="checkbox" name="login_types[]" value="<?php assign($snsId)?>" <?php assign(in_array($snsId, $this->getActionFormValue('login_types')) ? "checked=checked" : "" ); ?> disabled><?php assign($snsName)?></label><?php endforeach; ?></p>
                                <?php else: ?>
                                    <p><?php foreach($data['sns_login_types'] as $snsId => $snsName): ?><label><input type="checkbox" name="login_types[]" value="<?php assign($snsId)?>" <?php assign(in_array($snsId, $this->getActionFormValue('login_types')) ? "checked=checked" : "" ); ?>><?php assign($snsName)?></label><?php endforeach; ?></p>
                                <?php endif; ?>
                        <!-- /.loginAccountDisabled --></div>
                    </li>
                </ul>
                <?php if ($this->ActionError && !$this->ActionError->isValid('login_type')): ?>
                    <p class="iconError1"><?php assign($this->ActionError->getMessage('login_type')) ?></p>
                <?php endif; ?>
                <!-- /.pageCategory --></section>
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

        <?php if(!$data['pageStatus']['isAgent'] || ($data['pageStatus']['isAgent'] && $data['entry']->hidden_flg == 1)): ?>
        <div class="pageEditCheck">
            <ul>
                <li class="btn3"><a href="javascript:void(0);" id="submitEntry">内容確定</a></li>
            </ul>
            <!-- /.pageEditCheck --></div>
        <?php endif ?>

        <section class="backPage">
            <p><a href="<?php assign(Util::rewriteUrl('admin-blog', 'static_html_entries')); ?>" class="iconPrev1">ページ一覧へ</a>
            </p>
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

<?php write_html($this->scriptTag('admin-blog/StaticHtmlEmbedPageService')) ?>

<link rel="stylesheet" href="<?php assign($this->setVersion('/css/jqueryUI.css'))?>">
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/i18n/jquery.ui.datepicker-ja.min.js"></script>

<script type="text/javascript" src="<?php assign($this->setVersion('/js/zeroclipboard/ZeroClipboard.min.js')) ?>"></script>
<script type="text/javascript" src="<?php assign($this->setVersion('/ckeditor/ckeditor.js')) ?>"></script>
<?php write_html($this->parseTemplate('BrandcoFooter.php', $data['pageStatus'])); ?>
