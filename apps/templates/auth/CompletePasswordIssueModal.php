<div class="modal1 jsModal" id="modalCompletePasswordIssue">
    <section class="modalCont-medium jsModalCont">

        <div class="jsModalConfirmPasswordIssue">
            <div class="modalAction">
                <h1>仮パスワードを発行してもよろしいですか？</h1>
                <p class="btnSet">
                    <span class="btn2"><a href="#closeModal">キャンセル</a></span>
                    <span class="btn4"><a href="#" class="large2 jsIssuePassword">仮パスワードを発行</a></span>
                </p>
            <!-- /.modalAction --></div>
        </div>

        <div class="jsModalCompletePasswordIssue">    
            <div class="modalMail">
                <p>入力されたメールアドレス宛に発行した仮パスワードをお送りしましたので、<br>ご確認のうえ応募にお進みください。</p>
                <p>しばらくたった後もメールが届いていない場合は<a href="<?php assign(config('Protocol.Secure') . '://' . config('Domain.brandco') . '/monipla/inquiry'); ?>" target="_blank">お問い合わせ</a>をお願いします。</p>
            <!-- /.modalMail --></div>
            <p>
                <a href="#closeModal" class="modalCloseBtn">キャンセル</a>
            </p>
        </div>

        <!-- /.modalMail --></div>
    <!-- /.modalCont-ig --></section>
<!-- /.modal1 --></div>
