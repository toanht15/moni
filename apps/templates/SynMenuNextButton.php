<?php // Synメニューを開かせるボタン ?>
<?php if( $data['shown_monipla_media_link'] ): ?>
    <section class="message messageText jsMessage jsMessageBackToMonipla">
        <p style="text-align: center;"><strong>ご参加ありがとうございました！</strong></p>
        <div class="messageFooter">
            <ul class="btnSet">
                <li class="btn3"><a class="large1"
                                    href='javascript:openMenu($("#side-menu"));openModalBase($("#side-menu"));'>次へ</a>
                </li>
                <!-- /.btnSet --></ul>
            <p class="date">
                <small><?php assign(date("Y/m/d H:i", strtotime($data['message_info']['message']->created_at))); ?></small>
            </p>
        </div>
        <!-- /.message --></section>
    <script type="text/javascript">
        <!--
        setTimeout(function () {
            $('.jsMessageBackToMonipla').show();
        }, 200);
        -->
    </script>
<?php endif; ?>