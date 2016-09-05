<!-- モーダル -->
<div class="modal1 jsModal" id="pagePartsFloatImageSetting">
    <section class="modalCont-large jsModalCont">
        <h1>画像+テキスト</h1>
        <div class="pagePartsSetting">
            <div class="pagePartsSettingCont">
                <label class="require1">
                <br /><p class="btn3"><a href="javascript:void(0);" data-link="<?php assign(Util::rewriteUrl('admin-blog', 'file_list', null, array('f_id' => BrandUploadFile::POPUP_FROM_STATIC_HTML_TEMPLATE_IMAGE_FLOAT, 'stt' => 2))) ?>" class="jsFileUploaderPopup">画像を一覧から選択</a></p>
                </label>
                <p class="pagePartsErrorMessage" data-validate="require|#pagePartsFloatImageImage|src|画像を選択して下さい"></p>
                <div class="subController">
                <p class="floatPos"><label class="require1">画像配置：</label><label for="pagePartsFloatImageViewType<?php asign(StaticHtmlFloatImage::IMAGE_POSITION_LEFT);?>">
                                             <input type="radio" name="pagePartsFloatImageViewType" id="pagePartsFloatImageViewType<?php asign(StaticHtmlFloatImage::IMAGE_POSITION_LEFT);?>" value="<?php asign(StaticHtmlFloatImage::IMAGE_POSITION_LEFT);?>"><?php asign(StaticHtmlFloatImage::$image_positions[StaticHtmlFloatImage::IMAGE_POSITION_LEFT]);?></label>
                                             <label for="pagePartsFloatImageViewType<?php asign(StaticHtmlFloatImage::IMAGE_POSITION_RIGHT);?>">
                                             <input type="radio" name="pagePartsFloatImageViewType" id="pagePartsFloatImageViewType<?php asign(StaticHtmlFloatImage::IMAGE_POSITION_RIGHT);?>" value="<?php asign(StaticHtmlFloatImage::IMAGE_POSITION_RIGHT);?>"><?php asign(StaticHtmlFloatImage::$image_positions[StaticHtmlFloatImage::IMAGE_POSITION_RIGHT]);?></label></p>
                <p class="pagePartsErrorMessage" data-validate="require|input[name='pagePartsFloatImageViewType']:radio:checked|radio|選択して下さい"></p>
                <p class="floatPos"><label class="require1">スマホでの画像とテキストの配置：</label><label for="pagePartsFloatImageSmartphoneFloatOffFlg<?php asign(StaticHtmlFloatImage::SP_FLOAT_OFF);?>">
                            <input type="radio" name="pagePartsFloatImageSmartphoneFloatOffFlg" id="pagePartsFloatImageSmartphoneFloatOffFlg<?php asign(StaticHtmlFloatImage::SP_FLOAT_OFF);?>" value="<?php asign(StaticHtmlFloatImage::SP_FLOAT_OFF);?>"><?php asign(StaticHtmlFloatImage::$smartphone_floates[StaticHtmlFloatImage::SP_FLOAT_OFF]);?></label>
                            <label for="pagePartsFloatImageSmartphoneFloatOffFlg<?php asign(StaticHtmlFloatImage::SP_FLOAT_ON);?>">
                            <input type="radio" name="pagePartsFloatImageSmartphoneFloatOffFlg" id="pagePartsFloatImageSmartphoneFloatOffFlg<?php asign(StaticHtmlFloatImage::SP_FLOAT_ON);?>" value="<?php asign(StaticHtmlFloatImage::SP_FLOAT_ON);?>"><?php asign(StaticHtmlFloatImage::$smartphone_floates[StaticHtmlFloatImage::SP_FLOAT_ON]);?></label></p>
                <p class="pagePartsErrorMessage" data-validate="require|input[name='pagePartsFloatImageSmartphoneFloatOffFlg']:radio:checked|radio|選択して下さい"></p>
                <p class="photo"><img id="pagePartsFloatImageImage" src="" ></p>
                <p><label for="pagePartsFloatImageCaption">キャプション</label><input type="text" id="pagePartsFloatImageCaption" maxlength="20"></p>
                <p class="pagePartsErrorMessage" data-validate="max|#pagePartsFloatImageCaption|val||20"></p>
                <p><small>20文字以内で入力してください</small></p>
                <p><label for="pagePartsFloatImageText">テキスト</label><textarea id="pagePartsFloatImageText" maxlength="20000"></textarea></p>
                <p class="pagePartsErrorMessage" data-validate="max|#pagePartsFloatImageText|val||20000"></p>
                <p><small>20000文字以内で入力してください</small></p>
                <p><label for="pagePartsFloatImageLink">リンクURL</label><input type="text" id="pagePartsFloatImageLink" maxlength="255"></p>
                <p class="pagePartsErrorMessage" data-validate="url|#pagePartsFloatImageLink|val"></p>
                </div>
                <!-- /.pagePartsSettingCont --></div>
            <p class="btnSet">
                <span class="btn2"><a href="#closeModal" class="small1">キャンセル</a></span>
                <span class="btn3"><a href="#" class="small1 saveAndCloseModal">設定</a></span>
            </p>
            <!-- /.pagePartsSetting --></div>
    </section>
    <!-- /#pagePartsFloatImageSetting --></div>

<!-- リストのテンプレ -->
<div id='FloatImageListTemplate' style="display:none">
<li>
<p class="partsName">画像+テキスト</p>
<p class="partsIcon"><img src="<?php assign(config('Static.Url')) ?>/img/pageTempParts/iconFloatImage.gif"></p>
    <p class="btnSet">
    <span class="btn2"><a href="#" class="small1 deletePartsButton">削除</a></span>
    <span class="btn3"><a href="#pagePartsFloatImageSetting" class="small1 openPartsModal">編集</a></span>
    </p>
</li>
</div>