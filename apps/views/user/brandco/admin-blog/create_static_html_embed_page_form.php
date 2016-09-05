<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoHeader')->render($data['pageStatus'])) ?>
<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoAccountHeader')->render($data['pageStatus'])) ?>

<article>
    <section class="noticeBar1 jsNoticeBarArea1" id="mid-message">
        <p class="<?php assign(config('@message.adminMessage.updated.class')) ?> jsNoticeBarClose" id="jsMessage1"><?php assign(config('@message.adminMessage.updated.msg')) ?></p>
    </section>
    <?php write_html($this->parseTemplate('AdminBlogHeader.php',array('can_use_embed_page' => true))) ?>
    <h1 class="hd1">埋込ページ作成
    <span class="iconHelp">
      <span class="text">ヘルプ</span>
      <span class="textBalloon1">
        <span>作成したページを埋込タグを使って、<br>外部サイトのページの一部として表示させることができます。</span>
      <!-- /.textBalloon1 --></span>
    <!-- /.iconHelp --></span>
    </h1>
    <form id="frmEntry" name="frmEntry" action="<?php assign(Util::rewriteUrl('admin-blog', 'create_static_html_embed_page')); ?>" method="POST" enctype="multipart/form-data">
        <?php write_html($this->csrf_tag()); ?>
        <div class="pageSettingWrap1">
            <dl class="pageEdit1">
                <dt><label class="require1" for="field1">ページタイトル</label></dt>
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
                        <p><a href="javascript:void(0);" class="iconCopy1">タグをコピー</a></p>
                        <p><textarea name="" id="" cols="30" rows="5" readonly></textarea></p>
                    <!-- /.inputTag --></div>
                    </p>
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
                            <p><?php foreach($data['sns_login_types'] as $snsId => $snsName): ?><label><input type="checkbox" name="login_types[]" value="<?php assign($snsId)?>" <?php assign(in_array($snsId, $this->getActionFormValue('login_types')) ? "checked=checked" : "" ); ?> disabled="disabled"><?php assign($snsName)?></label><?php endforeach; ?></p>
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
</article>

<?php write_html($this->scriptTag('admin-blog/StaticHtmlEmbedPageService')) ?>

<link rel="stylesheet" href="<?php assign($this->setVersion('/css/jqueryUI.css'))?>">
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/i18n/jquery.ui.datepicker-ja.min.js"></script>

<script type="text/javascript" src="<?php assign($this->setVersion('/ckeditor/ckeditor.js')) ?>"></script>
<?php write_html($this->parseTemplate('BrandcoFooter.php', $data['pageStatus'])); ?>
