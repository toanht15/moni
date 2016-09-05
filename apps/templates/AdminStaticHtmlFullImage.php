<!-- モーダル -->
<div class="modal1 jsModal" id="pagePartsFullImageSetting">
    <section class="modalCont-large jsModalCont">
        <h1>画像全面配置</h1>
        <div class="pagePartsSetting">
            <div class="pagePartsSettingCont">
                <label class="require1"><br />
                <p class="btn3"><a href="javascript:void(0);" data-link="<?php assign(Util::rewriteUrl('admin-blog', 'file_list', null, array('f_id' => BrandUploadFile::POPUP_FROM_STATIC_HTML_TEMPLATE_IMAGE_FULL, 'stt' => 2))) ?>" class="jsFileUploaderPopup">画像を一覧から選択</a></p>
                <p class="pagePartsErrorMessage" data-validate="require|#pagePartsFullImageImage|src|画像を選択して下さい"></p>
                <p class="photo"><img id="pagePartsFullImageImage" src=""></p>
                <div class="subController">
                <p><label for="pagePartsFullImageCaption">キャプション</label><input type="text" id="pagePartsFullImageCaption" maxlength="20"></p>
                <p class="pagePartsErrorMessage" data-validate="max|#pagePartsFullImageCaption|val||20"></p>
                <p><small>20文字以内で入力してください</small></p>
                <p><label for="pagePartsFullImageLink">リンクURL</label><input type="text" id="pagePartsFullImageLink" maxlength="255"></p>
                <p class="pagePartsErrorMessage" data-validate="url|#pagePartsFullImageLink|val"></p>
                </div>
                <!-- /.pagePartsSettingCont --></div>
            <p class="btnSet">
                <span class="btn2"><a href="#closeModal" class="small1">キャンセル</a></span>
                <span class="btn3"><a href="#" class="small1 saveAndCloseModal">設定</a></span>
            </p>
            <!-- /.pagePartsSetting --></div>
    </section>
    <!-- /#pagePartsFullImageSetting --></div>

<!-- リストのテンプレ -->
<div id='FullImageListTemplate' style="display:none">
<li>
<p class="partsName">画像全面配置</p>
<p class="partsIcon"><img src="<?php assign(config('Static.Url')) ?>/img/pageTempParts/iconFullImage.gif"></p>
    <p class="btnSet">
    <span class="btn2"><a href="#" class="small1 deletePartsButton">削除</a></span>
    <span class="btn3"><a href="#pagePartsFullImageSetting" class="small1 openPartsModal">編集</a></span>
    </p>
</li>
</div>