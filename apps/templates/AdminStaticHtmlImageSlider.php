<!-- モーダル -->
<div class="modal1 jsModal" id="pagePartsImageSliderSetting">
    <section class="modalCont-large jsModalCont">
        <h1>画像スライダー設定</h1>
        <div class="pagePartsSetting">
            <div class="pagePartsSettingCont">
                <p class="slideNum">スライダー1画面あたりに表示させる画像数<br>
                    <label for="pcSlideNum">PC</label><input type="text" id="pagePartsImageSliderPcImageCount" maxlength="2">
                    <label for="spSlideNum">スマホ</label><input type="text" id="pagePartsImageSliderSpImageCount" maxlength="2">
                </p>
                <p class="pagePartsErrorMessage" id="imageSliderPcImageCountErrorMessage" data-validate="between|#pagePartsImageSliderSpImageCount|val||1-10"></p>
                <p class="pagePartsErrorMessage" id="imageSliderSpImageCountErrorMessage" data-validate="between|#pagePartsImageSliderSpImageCount|val||1-10"></p>
                <p><small>1〜10で入力してください</small></p>
                <label class="require1"><br />
                <p class="btn3"><a href="javascript:void(0);" data-link="<?php assign(Util::rewriteUrl('admin-blog', 'file_list', null, array('f_id' => BrandUploadFile::POPUP_FROM_STATIC_HTML_TEMPLATE_IMAGE_SLIDER, 'stt' => 2))) ?>" class="jsFileUploaderPopup">画像を一覧から選択</a></p>
                <p class="pagePartsErrorMessage" data-validate="require|#pagePartsImageSliderImage|src|画像を選択して下さい"></p>
                <p class="photo"><img id="pagePartsImageSliderImage" src=""></p>
                <div class="subController">
                <p><label for="pagePartsImageSliderCaption">テキスト</label><input type="text" id="pagePartsImageSliderCaption" maxlength="20"></p>
                <p class="pagePartsErrorMessage" data-validate="max|#pagePartsImageSliderCaption|val||20"></p>
                <p><small>20文字以内で入力してください</small></p>
                <p><label for="pagePartsImageSliderLink">リンクURL</label><input type="text" id="pagePartsImageSliderLink" maxlength="255"></p>
                <p class="pagePartsErrorMessage" data-validate="url|#pagePartsImageSliderLink|val"></p>
                <p class="btnInsert"><span class="btn3"><a href="#addImageSlide" id="pagePartsImageSliderSave" class="small1"></a></span></p>
                </div>
                <!-- /.pagePartsSettingCont --></div>
            <p class="pagePartsErrorMessage" id="imageSliderCommonErrorMessage"></p>
            <ul id="slideContainer" class="pagePartsSettingItems" style="display: none"></ul>
            <p class="btnSet">
                <span class="btn2"><a href="#closeModal" class="small1">キャンセル</a></span>
                <span class="btn3"><a href="#" class="small1 saveAndCloseModal">設定</a></span>
            </p>
            <!-- /.pagePartsSetting --></div>
    </section>
    <!-- /#pagePartsSliderSetting --></div>

<!-- リストのテンプレ -->
<div id='ImageSliderListTemplate' style="display:none">
<li>
<p class="partsName">画像スライダー</p>
<p class="partsIcon"><img src="<?php assign(config('Static.Url')) ?>/img/pageTempParts/iconSlider.gif"></p>
    <p class="btnSet">
    <span class="btn2"><a href="#" class="small1 deletePartsButton">削除</a></span>
    <span class="btn3"><a href="#pagePartsImageSliderSetting" class="small1 openPartsModal">編集</a></span>
    </p>
</li>
</div>