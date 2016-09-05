<?php write_html($this->parseTemplate('BrandcoPopupHeader.php', $data['pageStatus'])); ?>

<article>
    <h1 class="hd1">ファイル編集</h1>

    <form method="POST" name="fileEditForm" id="file_edit_form" action="<?php assign(Util::rewriteUrl('admin-blog', 'file_edit')); ?>">
        <?php write_html($this->formHidden('brand_upload_file_id', $data['brand_upload_file']->id)) ?>
        <?php write_html($this->csrf_tag()); ?>

        <dl class="fileEdit1">
            <dt><label class="require1" for="file_name_text">ファイル名</label></dt>
            <dd>
                <?php write_html($this->formText('name', PHPParser::ACTION_FORM, array('class' => 'categoryName', 'maxlength' => 50, 'id' => 'file_name_text'))); ?>
                <small class="textLimit" id="file_name_limit"></small>
                <br>
                <small>※ページに埋め込み済のファイルは、リンク切れにご注意ください</small>
                <?php if ($this->ActionError && !$this->ActionError->isValid('name')): ?>
                    <br><span class="iconError1"><?php assign($this->ActionError->getMessage('name')) ?></span>
                <?php endif; ?>
            </dd>
            <dt><label for="field2">ファイルの説明</label></dt>
            <dd>
                <?php write_html($this->formTextArea('description', PHPParser::ACTION_FORM, array('class' => 'fileDescription', 'cols' => 30, 'rows' => 2, 'id' => 'field2'))); ?>
            </dd>
            <dt>ファイルの種類</dt>
            <dd class="type">
                <?php assign($data['upload_file']->getFileType()) ?>
                <img src="<?php assign($data['upload_file']->getThumbnailPhoto()); ?>" width="40" height="40" alt="<?php assign($data['upload_file']->getFileType()); ?>" onerror="this.src=' . <?php assign($data['upload_file']->getFilePreview()); ?> . ';">
            </dd>
            <dt>大きさ</dt>
            <dd><?php assign($data['upload_file']->getPhotoSize(UploadFile::FILE_EDIT_PATTER)) ?><?php assign($data['upload_file']->getFileSize()) ?></dd>
            <dt>URL</dt>
            <dd><span class="url"><?php assign($data['upload_file']->url) ?></span><a
                        href="javascript:void(0);" class="iconCopy1 jsCopyToClipboardBtn"
                        data-clipboard-text="<?php assign($data['upload_file']->url) ?>">URLをコピー</a></dd>
            <!-- /.fileEdit1 --></dl>

        <div class="fileEditCheck">
            <ul>
                <li class="btn3"><a href="javascript:void(0);" class="jsFileEditSubmitBtn">保存</a></li>
                <li class="btn4"><a href="javascript:void(0);" class="small1 jsFileDeleteBtn" data-brand_upload_file_id="<?php assign('brand_upload_file_id=' . $data['brand_upload_file']->id) ?>">削除</a></li>
                </ul>
            <!-- /.fileEditCheck --></div>
    </form>

    <section class="backPage">
        <p><a href="<?php assign(Util::rewriteUrl('admin-blog', 'file_list')) ?>" class="iconPrev1">ファイル一覧へ</a></p>
        <!-- /.backPage --></section>
    </article>

<div class="modal1 jsModal" id="modal1">
    <section class="modalCont-small jsModalCont">
        <h1>このファイルを削除しますか？</h1>
        <p>ご確認お願い致します</p>
        <p class="btnSet">
            <span class="btn2"><a href="#closeModal" class="small1">キャンセル</a></span>
            <span class="btn4"><a id="delete_area" href="javascript:void(0)"
                                  data-url='<?php assign(Util::rewriteUrl('admin-blog', 'api_delete_file_upload.json')) ?>'
                                  data-callback='<?php assign(Util::rewriteUrl('admin-blog', 'file_list', null, array('mid'=>'action-deleted'))) ?>' class="small1">削除</a>
            </span>
        </p>
    </section>
    <!-- /.modal1 --></div>

<script src="<?php assign($this->setVersion('/js/zeroclipboard/ZeroClipboard.min.js')) ?>"></script>
<?php write_html($this->parseTemplate('BrandcoPopupFooter.php', array_merge($data['pageStatus'], array('script' => array('admin-blog/FileEditService'))))); ?>
