<?php write_html($this->csrf_tag()); ?>
<div class="modal1 jsModal" id="modal_demo_reset_confirm">
    <section class="modalCont-small jsModalCont">
        <h1>確認</h1>
        <p>デモ公開時の参加情報を一括クリアします。</p>
        <p class="btnSet">
            <span class="btn2"><a href="#closeModal" class="small1">キャンセル</a></span>
            <span class="btn1"><a href="javascript:void(0)" class="small1 demoResetConfirmed" data-cp-id="" data-url="<?php write_html(Util::rewriteUrl('admin-cp', 'api_reset_demo_data.json')) ?>">クリア</a></span>
        </p>
    </section>
    <!-- /.modal1 --></div>

<div class="modal1 jsModal" id="modal_demo_reset_one_confirm">
    <section class="modalCont-small jsModalCont">
        <h1>確認</h1>
        <p>デモ公開時の自身の参加情報をクリアします。</p>
        <p class="btnSet">
            <span class="btn2"><a href="#closeModal" class="small1">キャンセル</a></span>
            <span class="btn1"><a href="javascript:void(0)" class="small1 demoResetConfirmed" data-cp-id="" data-reset-one="1" data-url="<?php write_html(Util::rewriteUrl('admin-cp', 'api_reset_demo_data.json')) ?>">クリア</a></span>
        </p>
    </section>
    <!-- /.modal1 --></div>

<div class="modal1 jsModal" id="modal_demo_cancel">
    <section class="modalCont-medium jsModalCont">
        <h1>デモ公開を終了し下書きに戻します。</h1>
        <p>デモ公開時の参加情報は削除されます。</p>
        <p>キャンペーンは、以下の「下書きキャンペーン」に保存されます。</p>
        <p><img src="<?php assign($this->setVersion('/img/campaign/imgDemoMode.jpg')) ?>" alt="下書きキャンペーン"></p>
        <p class="btnSet">
            <span class="btn2"><a href="#closeModal" class="large1">キャンセル</a></span>
            <span class="btn3"><a href="javascript:void(0)" class="large1" id="demoCancelConfirmed" data-cp-id="" data-url="<?php write_html(Util::rewriteUrl('admin-cp', 'api_cancel_demo.json')) ?>">デモ終了</a></span>
        </p>
    </section>
    <!-- /.modal1 --></div>

<div class="modal1 jsModal" id="modal_demo_reset_error">
    <section class="modalCont-small jsModalCont">
        <h1>確認</h1>
        <p class="attention1"></p>
        <p class="btnSet">
            <span class="btn2"><a href="#closeModal" class="small1">OK</a></span>
        </p>
    </section>
    <!-- /.modal1 --></div>

<?php write_html($this->scriptTag("admin-cp/CpDemoConfirmBoxService")); ?>