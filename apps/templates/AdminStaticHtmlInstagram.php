<!-- モーダル -->
<div class="modal1 jsModal" id="pagePartsInstaSetting">
    <section class="modalCont-large jsModalCont">
        <h1>Instagram投稿一覧</h1>
        <div class="pagePartsSetting">
            <div class="pagePartsSettingCont">
                <p>モニプラキャンペーンを経てInstagramに投稿された写真のみ出力可能です。</p>
                <p>
                    <label for="insertUrl">外部出力APIのURL<span class="iconHelp">
                          <span class="text">ヘルプ</span>
                          <span class="textBalloon1">
                            <span>
                              　出力をする写真を集めたキャンペーンの<br>「投稿管理」より外部出力APIのURL作成から<br>
                              　URLを取得してください
                            </span>
                          <!-- /.textBalloon1 --></span>
                    <!-- /.iconHelp --></span>
                    </label>
                </p>
                <div class="jsPagePartsInstagramApiUrl">
                    <p class="api_url_box">
                        <input type="text" id="api_url_1" class="url" maxlength="250">
                        <a href="javascript:void(0)" class="iconBtnDelete jsDeleteApiUrlBox">削除</a>
                        <span class="pagePartsErrorMessage" id="apiUrlError_1"></span>
                    </p>
                <!-- /.jsPagePartsInstagramApiUrl --></div>
                <p class="pagePartsErrorMessage" id="notInputUrlError"></p>
                <p><a href="javascript:void(0)" class="linkAdd">新規追加</a></p>
                <p class="supplement1">※画像は新着順に表示されます。<br>
                    ※URLは6個まで指定可能です。</p>
                <!-- /.pagePartsSettingCont --></div>
            <p class="btnSet">
                <span class="btn2"><a href="#closeModal" class="small1">キャンセル</a></span>
                <span class="btn3"><a href="#" class="small1 saveAndCloseModal">設定する</a></span>
            </p>
            <!-- /.pagePartsSetting --></div>
    </section>
    <!-- /#pagePartsTextSetting --></div>

<!-- リストのテンプレ -->
<div id='InstagramListTemplate' style="display:none">
    <li>
        <p class="partsName">Instagram画像一覧</p>
        <p class="partsIcon"><img src="<?php assign(config('Static.Url')) ?>/img/pageTempParts/inconInstaImages.gif"></p>
        <p class="btnSet">
            <span class="btn2"><a href="#" class="small1 deletePartsButton">削除</a></span>
            <span class="btn3"><a href="#pagePartsInstaSetting" class="small1 openPartsModal">編集</a></span>
        </p>
    </li>
</div>