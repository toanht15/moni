<!-- モーダル -->
<div class="modal1 jsModal" id="pagePartsTextSetting">
    <section class="modalCont-large jsModalCont">
        <h1>文章入力</h1>
        <div class="pagePartsSetting">
            <div class="pagePartsSettingCont">
                <label class="require1"><p><textarea id="pagePartsTextCaption"></textarea></p>
                <p class="pagePartsErrorMessage" data-validate="max|pagePartsTextCaption|ckeditor||20000@require|pagePartsTextCaption|ckeditor"></p>
                <p><small>20000文字以内で入力してください</small></p>
                <!-- /.pagePartsSettingCont --></div>
            <p class="btnSet">
                <span class="btn2"><a href="#closeModal" class="small1">キャンセル</a></span>
                <span class="btn3"><a href="#" class="small1 saveAndCloseModal">設定</a></span>
            </p>
            <!-- /.pagePartsSetting --></div>
    </section>
    <!-- /#pagePartsTextSetting --></div>

<!-- リストのテンプレ -->
<div id='TextListTemplate' style="display:none">
<li>
<p class="partsName">文章入力</p>
<p class="partsIcon"><img src="<?php assign(config('Static.Url')) ?>/img/pageTempParts/iconText.gif"></p>
    <p class="btnSet">
    <span class="btn2"><a href="#" class="small1 deletePartsButton">削除</a></span>
    <span class="btn3"><a href="#pagePartsTextSetting" class="small1 openPartsModal">編集</a></span>
    </p>
</li>
</div>