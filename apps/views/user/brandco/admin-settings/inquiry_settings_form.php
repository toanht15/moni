<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoHeader')->render($data['pageStatus'])) ?>
<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoAccountHeader')->render($data['pageStatus'])) ?>

<div class="adminWrap">
    <?php write_html($this->parseTemplate('SettingSiteMenu.php', $data['pageStatus'])) ?>

    <article class="adminMainCol jsSettings">
        <h1 class="hd1">通知先メールアドレス設定</h1>
        <h2 class="hd2">メールアドレスの登録</h2>

        <form action="<?php assign(Util::rewriteUrl('admin-settings', 'add_inquiry_brand_receiver')); ?>" method="POST" class="jsReceiverAddForm">
            <?php write_html($this->csrf_tag()); ?>
            <section class="adminUserAttendWrap">
                <p>お問い合わせ受信時などの通知先メールアドレスです。必ず1件以上ご登録ください。</p>

                <p class="adminUserAttend">
                    <strong>メールアドレス</strong>
                    <?php if ($this->ActionError && !$this->ActionError->isValid('mail_address')): ?>
                        <span class="attention1"><?php assign($this->ActionError->getMessage('mail_address')) ?></span><br>
                    <?php endif; ?>
                    <?php write_html($this->formEmail('mail_address', $this->getActionFormValue('mail_address'), array('placeholder' => 'sample@monipla.com'))); ?>
                    <span class="btn3"><a href="javascript:void(0)" class="jsOpenReceiverAddModal" data-open_modal_type="ReceiverAdd">登録</a></span>
                </p>
                <!-- /.adminUserAttendWrap --></section>
        </form>

        <h2 class="hd2">通知先一覧</h2>
        <?php write_html($this->csrf_tag()); ?>
        <section class="adminUserListWrap">
            <ul class="adminUserList">
                <?php foreach ($data['inquiry_brand_receivers'] as $inquiry_brand_receiver): ?>
                    <li>
                        <span class="adminUserUrl"><?php assign(Util::cutTextByWidth($inquiry_brand_receiver->mail_address, 220)); ?></span>
                        <?php if ($data['n_inquiry_brand_receivers'] > 1) : ?>
                        <a href="javascript:void(0)" class="adminUserDelete jsOpenReceiverDeleteModal"
                           data-open_modal_type="ReceiverDelete"
                           data-inquiry_brand_receiver_id="<?php assign($inquiry_brand_receiver->id); ?>"
                           data-inquiry_brand_receiver_mail_address="<?php assign($inquiry_brand_receiver->mail_address); ?>">ユーザーの削除</a>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
            <!-- /.adminUserListWrap --></section>

    </article>
</div>

<div class="modal1 jsModal" id="modalReceiverDelete">
    <section class="modalCont-medium jsModalCont">
        <h1>通知先メールアドレスの削除</h1>

        <div class="modalAdminUser">
            <p class="userCheck">
                <strong class="jsMailAddress"></strong>を通知先から削除します。</p>
        </div>

        <form action="<?php assign(Util::rewriteUrl('admin-settings', 'delete_inquiry_brand_receiver')); ?>" method="POST" class="jsReceiverDeleteForm">
            <?php write_html($this->csrf_tag()); ?>
            <?php write_html($this->formHidden('inquiry_brand_receiver_id', '', array('class' => 'jsInquiryBrandReceiverId'))); ?>

            <p class="btnSet">
                <span class="btn2"><a href="javascript:void(0)" class="jsCloseReceiverDeleteModal" data-close_modal_type="ReceiverDelete">キャンセル</a></span>
                <span class="btn4"><a href="javascript:void(0)" class="jsReceiverDelete" data-submit_form_class="jsReceiverDeleteForm">削除</a></span>
            </p>
        </form>
    </section>
    <!-- /.modal1 --></div>

<div class="modal2 jsModal" id="modalReceiverAdd">
    <section class="modalCont-small jsModalCont">
        <h1>確認</h1>
        <p><span class="attention1">メールアドレスを登録します。</span></p>
        <p class="btnSet">
            <span class="btn2"><a href="javascript:void(0)" class="middle1 jsCloseReceiverAddModal" data-close_modal_type="ReceiverAdd">キャンセル</a></span>
            <span class="btn4"><a href="javascript:void(0)" class="middle1 jsReceiverAdd" data-submit_form_class="jsReceiverAddForm">登録</a></span>
        </p>
    </section>
</div>

<?php $param = array_merge($data['pageStatus'], array('script' => array(
    'admin-settings/PageSettingsFormService', 'admin-settings/InquirySettingsFormService'
))) ?>
<?php write_html($this->parseTemplate('BrandcoFooter.php', $param)); ?>
