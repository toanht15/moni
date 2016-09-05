<div class="modal1 jsModal jsAuthModal" id="modalAuth">
    <?php write_html($this->csrf_tag()); ?>
    <section class="modalCont-auth jsModalCont">
        <h1 class="singleWrapHd1">お持ちのアカウントで簡単応募！</h1>

        <div class="jsAuthModalSliderScreen jsSliderScreen">
            <?php write_html($this->parseTemplate('auth/AuthForm.php')); ?>
        </div>

        <p>
            <a href="javascript:void(0);" class="modalCloseBtn jsCloseAuthModal">閉じる</a>
        </p>
        <!-- /.modalCont-auth --></section>
    <!-- /.modal1 --></div>

<?php write_html($this->scriptTag('auth/AuthModalService'))?>
<?php write_html($this->scriptTag('auth/MailAuthFormService'))?>
