<?php write_html($this->parseTemplate('BrandcoPopupHeader.php', $data['pageStatus'])); ?>

<article>
    <h1 class="hd1">ファイル</h1>
    <?php write_html($this->formHidden('static_url', "http:" . config('Static.Url'))); ?>

    <section class="fileUploadWrap jsAreaToggleWrap">
        <p class="uploadToggle"><a href="javascript:void(0);" class="linkAdd jsAreaToggle">新規ファイル追加</a></p>

        <form method="POST" name="fileUploadForm" enctype="multipart/form-data">
            <?php write_html($this->csrf_tag()); ?>
            <div class="uploadcont jsAreaToggleTarget">
                <ul class="fileUploadList jsFileUploadList">
                    <!-- /.fileUploadList --></ul>
                <div class="fileUploadAreaWrap jsMultipleFileArea">
                    <p><input type="file" name="file_upload[]" multiple id="multiple_file_input"></p>

                    <div class="fileUploadArea" id="file_upload_area_handler">
                        <p class="or">または</p>

                        <p>ファイルをドラッグ＆ドロップしてください</p>
                        <!-- /.fileUploadArea --></div>
                    <p>
                        <small>※1ファイルの容量の上限は10MBまでです。1度でアップロードできる上限は合計50MBです。</small>
                        <br>
                        <span class="iconError1 jsFileUploadError" style="display: none"></span>
                        <span class="btn3"><a href="javascript:void(0);" class="middle1 jsFileUploader" data-action_url="<?php assign(Util::rewriteUrl('admin-blog', 'upload_file')); ?>">アップロード</a></span>
                    </p>
                    <!-- /.fileUploadAreaWrap --></div>

                <div class="fileUploadAreaWrap jsSingleFileArea">
                    <p><input type="file" name="file_upload"></p>
                    <small>※1ファイルの容量の上限は10MBまでです。</small>
                    <br>
                    <p><span class="btn3"><a href="javascript:void(0);" class="middle1 jsFileUploader" data-action_url="<?php assign(Util::rewriteUrl('admin-blog', 'upload_single_file')); ?>">アップロード</a></span></p>
                </div>
                <!-- /.uploadcont --></div>
            </form>
        <!-- /.fileUploadWrap --></section>

    <form method="POST" name="jsFileActionForm">
        <section class="fileListWrap">

            <table class="fileList1">
                <thead>
                <tr>
                    <th class="thumb"></th>
                    <th class="fileName">ファイル</th>
                    <th class="type">種類</th>
                    <th class="scale">サイズ</th>
                    <th class="size">容量</th>
                    <th class="edit"></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($data['brand_upload_files'] as $brand_upload_file): ?>
                    <?php $upload_file = $brand_upload_file->getUploadFile(); ?>
                    <tr>
                        <td class="thumb">
                            <img src="<?php assign($upload_file->getThumbnailPhoto()); ?>" alt="<?php assign($upload_file->name); ?>" onerror="this.src='<?php assign($upload_file->getFilePreview()); ?>';">
                            <?php if($data['f_id'] != BrandUploadFile::POPUP_FROM_MOVIE_MODULE): ?>
                                <?php if ($upload_file->type == FileValidator::FILE_TYPE_IMAGE): ?>
                                    <span class="btn3"><a href="javascript:void(0);" data-f_id="<?php assign($data['f_id']) ?>" data-status="<?php assign($data['stt']) ?>" data-callback="<?php assign($data['callback']) ?>" data-feed_file_info="<?php assign($upload_file->url) ?>" data-photo_width="<?php assign($upload_file->getPhotoWidth()) ?>" data-photo_height="<?php assign($upload_file->getPhotoHeight()) ?>" class="small1 jsFeedFileInfoBtn">画像を挿入</a></span>
                                <?php endif; ?>
                            <?php elseif($data['f_id'] == BrandUploadFile::POPUP_FROM_MOVIE_MODULE): ?>
                                <?php if($upload_file->type == FileValidator::FILE_TYPE_VIDEO) : ?>
                                    <span class="btn3"><a href="javascript:void(0);" data-f_id="<?php assign($data['f_id']) ?>" data-status="<?php assign($data['stt']) ?>" data-callback="<?php assign($data['callback']) ?>" data-feed_file_info="<?php assign($upload_file->url) ?>" data-photo_width="<?php assign($upload_file->getPhotoWidth()) ?>" data-photo_height="<?php assign($upload_file->getPhotoHeight()) ?>" class="small1 jsFeedFileInfoBtn">ビデオを挿入</a></span>
                                <?php endif ?>
                            <?php endif ?>
                        </td>
                        <td class="fileName">
                            <?php assign($upload_file->name); ?>
                            <small class="supplement1"><?php assign($upload_file->url) ?><br><a href="javascript:void(0);" class="iconCopy1 jsCopyToClipboardBtn" data-clipboard-text="<?php assign($upload_file->url) ?>">URLをコピー</a></small>
                        </td>
                        <td class="type"><?php assign($upload_file->getFileType()) ?></td>
                        <td class="scale"><?php write_html($upload_file->getPhotoSize(UploadFile::FILE_LIST_PATTERN)) ?></td>
                        <td class="size"><?php assign($upload_file->getFileSize()) ?></td>
                        <td class="edit">
                            <a href="<?php assign(Util::rewriteUrl('admin-blog', 'file_edit_form', array($brand_upload_file->id))); ?>" class="iconBtnEdit">編集</a>
                            <a href="javascript:void(0);" class="iconBtnDelete jsFileDeleteBtn" data-brand_upload_file_id="<?php assign('brand_upload_file_id=' . $brand_upload_file->id) ?>">
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
                <!-- /.fileList1 --></table>

            <?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoModalPager')->render(array(
                'TotalCount' => $data['total_file_count'],
                'CurrentPage' => $this->params['p'],
                'Count' => $data['page_limited'],
            ))) ?>
            <!-- /.fileListWrap --></section>
        <?php if($data['f_id'] == BrandUploadFile::POPUP_FROM_STATIC_HTML_TEMPLATE_STAMP_RALLY_CP_PREPARE): ?>
            <?php write_html($this->formHidden('stamp_rally_cp_prepare',1)); ?>
        <?php endif; ?>
    </form>
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

<script>var isLteIE9 = false;</script>
<!--[if lte IE 9]>
    <script>var isLteIE9 = true</script>
<!--<![endif]-->

<script>
    if (isLteIE9 === false) {
        $('.jsSingleFileArea').hide();
    } else {
        $('.jsMultipleFileArea').hide();
    }
</script>

<script src="<?php assign($this->setVersion('/js/zeroclipboard/ZeroClipboard.min.js')) ?>"></script>
<?php write_html($this->parseTemplate('BrandcoPopupFooter.php', array_merge($data['pageStatus'], array('script' => array('admin-blog/FileListService'))))); ?>