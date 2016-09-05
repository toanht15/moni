<!-- モーダル -->
<div class="modal1 jsModal" id="jsPagePartsStampRallySetting">
    <section class="modalCont-large jsModalCont">
        <h1>スタンプラリー設定</h1>
        <p class="pagePartStampRallyDescription">開催前のキャンペーンのみ対象に指定することが可能です。<br>
            なお、限定公開のキャンペーンは指定することができません。</p>
        <div class="pagePartsSetting">
            <div class="pagePartsSettingCont">
                <div class="pagePartsStampRallySetting">
                    <h2 class="pagePartsSettingStampNum"><label>対象キャンペーン数</label></h2>
                    <div class="pagePartsStampRallySettingInner">
                        <p class="pagePartsSettingStampNum"><input type="text" id="pcSlideNum" maxlength="3">回</p>
                        <p class="pagePartsErrorMessage" id="targetCpNumError"></p>
                        <p>※準備中のパネルを出す枚数の基準となるので、最終的な対象予定数を入力してください。<br>※キャンペーンがまだ存在しない場合でも、対象数に含めてください。</p>
                    <!-- /.pagePartsStampRallySettingInner --></div>

                    <h2 class="pagePartsSettingStampStatusImage">
                        スタンプステータス画像
                        <span class="iconHelp">
                        <span class="textBalloon1">
                        <span>各キャンペーン画像に被せ、ステータスがわかるようにするための画像です。</span>
                        <!-- /.textBalloon1 --></span>
                        <!-- /.iconHelp --></span>
                    </h2>
                    <div class="pagePartsStampRallySettingInner">
                        <p>※JPEG,GIF,PNGの横1000px×縦524px<br>※参加済み・終了画像は、透過PNGを推奨</p>
                        <?php write_html($this->formHidden('static_base_url', config('Static.Url'))) ?>
                        <ul class="pagePartsStampRallyImageSetting">
                            <li>
                                <p>参加済み</p>
                                <p class="statusIcon"><img src="<?php assign(config('Static.Url')) ?>/img/stampRally/stampStatusJoined.png" id="cp_status_joined" data-default_image="<?php assign(config('Static.Url')) ?>/img/stampRally/stampStatusJoined.png"></p>
                                <p class="btnSet">
                                    <span class="btn2"><a href="javascript:void(0);" class="small1 jsResetCpImage">削除</a></span>
                                    <span class="btn3"><a href="javascript:void(0);" data-link="<?php assign(Util::rewriteUrl('admin-blog', 'file_list', null, array('f_id' => BrandUploadFile::POPUP_FROM_STATIC_HTML_TEMPLATE_STAMP_RALLY_CP_STATUS_JOINED, 'stt' => 2))) ?>" class="jsFileUploaderPopup small1">画像変更</a></span>
                                </p>
                            </li>
                            <li>
                                <p>終了</p>
                                <p class="statusIcon"><img src="<?php assign(config('Static.Url')) ?>/img/stampRally/stampStatusFinished.png" id="cp_status_finish" data-default_image="<?php assign(config('Static.Url')) ?>/img/stampRally/stampStatusFinished.png"></p>
                                <p class="btnSet">
                                    <span class="btn2"><a href="javascript:void(0);" class="small1 jsResetCpImage">削除</a></span>
                                    <span class="btn3"><a href="javascript:void(0);" data-link="<?php assign(Util::rewriteUrl('admin-blog', 'file_list', null, array('f_id' => BrandUploadFile::POPUP_FROM_STATIC_HTML_TEMPLATE_STAMP_RALLY_CP_STATUS_FINISH, 'stt' => 2))) ?>" class="jsFileUploaderPopup small1">画像変更</a></span>
                                </p>
                            </li>
                            <li>
                                <p>準備中</p>
                                <p class="statusIcon" style="background: #fff"><img src="<?php assign(config('Static.Url')) ?>/img/stampRally/stampStatusComingsoon.png" id="cp_status_coming_soon" data-default_image="<?php assign(config('Static.Url')) ?>/img/stampRally/stampStatusComingsoon.png"></p>
                                <p class="btnSet">
                                    <span class="btn2"><a href="javascript:void(0);" class="small1 jsResetCpImage">削除</a></span>
                                    <span class="btn3"><a href="javascript:void(0);" data-link="<?php assign(Util::rewriteUrl('admin-blog', 'file_list', null, array('f_id' => BrandUploadFile::POPUP_FROM_STATIC_HTML_TEMPLATE_STAMP_RALLY_CP_PREPARE, 'stt' => 2))) ?>" class="jsFileUploaderPopup small1">画像変更</a></span>
                                </p>
                            </li>
                        <!-- /.pagePartsStampRallyImageSetting --></ul>
                    <!-- /.pagePartsStampRallySettingInner --></div>

                    <h2>対象キャンペーン選択</h2>
                    <?php write_html($this->formHidden('get_list_cp_url', Util::rewriteUrl('admin-blog', 'api_get_stamp_rally_cp.json'))) ?>
                    <?php write_html($this->formHidden('cp_status_joined_image','')) ?>
                    <?php write_html($this->formHidden('cp_status_finished_image','')) ?>
                    <div class="pagePartsStampRallySettingInner" id="stampRallyCpSetting">
                        
                    <!-- /.pagePartsStampRallySettingInner --></div>
                <!-- /.pagePartsStampRallySetting --></div>
            <!-- /.pagePartsSettingCont --></div>
            <p class="btnSet">
                <span class="btn2"><a href="#closeModal" class="small1">キャンセル</a></span>
                <span class="btn3"><a href="#" class="small1 saveAndCloseModal">設定する</a></span>
            </p>
        <!-- /.pagePartsSetting --></div>
     </section>
    <!-- /#jsPagePartsStampRallySetting --></div>

<!-- リストのテンプレ -->
<div id='StampRallyListTemplate' style="display:none">
    <li>
        <p class="partsName">スタンプラリー</p>
        <p class="partsIcon"><img src="<?php assign(config('Static.Url')) ?>/img/pageTempParts/iconStampRally.gif"></p>
        <p class="btnSet">
            <span class="btn2"><a href="#" class="small1 deletePartsButton">削除</a></span>
            <span class="btn3"><a href="#jsPagePartsStampRallySetting" class="small1 openPartsModal">編集</a></span>
        </p>
    </li>
</div>
<div class="modal1 jsModal" id="modal2">
    <section class="modalCont-small jsModalCont">
        <h1>確認</h1>
        <p><span class="attention1">公開以降のキャンペーンは選択を解除すると再選択ができません。解除してよろしいですか？</span></p>
        <p class="btnSet"><span class="btn2"><a href="javascript:void(0)" class="middle1" data-close_modal_id="2">キャンセル</a></span><span class="btn3"><a href="javascript:void(0)" class="middle1 jsRemoveActiveCp">設定する</a></span></p>
    </section>
</div>
