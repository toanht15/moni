<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoHeader')->render($data['pageStatus'])) ?>
<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoAccountHeader')->render($data['pageStatus'])) ?>

    <div class="adminWrap">
        <?php write_html($this->parseTemplate('SettingSiteMenu.php', $data['pageStatus'])) ?>

        <article class="adminMainCol">
            <h1 class="hd1">管理者設定</h1>

            <h2 class="hd2">管理者の招待</h2>

            <form id="frmMailForm" name="frmMailForm"
                  action="<?php assign(Util::rewriteUrl('admin-settings', 'administrator_settings')); ?>" method="POST">
                <section class="adminUserAttendWrap">
                    <p>新しい管理者を招待します。メールアドレスを入力して送信ボタンをクリックしてください。</p>

                    <p class="adminUserAttend">
                        <strong>メールアドレス</strong>
                        <?php if ($this->ActionError && !$this->ActionError->isValid('mail_address')): ?>
                            <span
                                class="attention1"><?php assign($this->ActionError->getMessage('mail_address')) ?></span>
                            <br>
                        <?php endif; ?>
                        <?php write_html($this->csrf_tag()); ?>
                        <?php $domain = Util::getMappedServerName();write_html($this->formEmail(
                            'mail_address',
                            PHPParser::ACTION_FORM,
                            array('placeholder' => 'sample@monipla.com')
                        )); ?><span class="btn3"><a href="javascript:void(0)" id="confirmButton">送信</a></span>
                    </p>
                <!-- /.adminUserAttendWrap --></section>
            </form>

            <h2 class="hd2">管理者一覧</h2>
            <section class="adminUserListWrap">
                <p>現在登録されている管理者の一覧です。</p>
                <ul class="adminUserList">
                <?php foreach ($this->admin_user as $admin_user): ?>
                    <li><img src="<?php assign($admin_user->profile_image_url ? $admin_user->profile_image_url : $this->setVersion('/img/base/imgUser1.jpg')) ?>" height="38" width="38" alt="use name" class="adminUserImg" onerror="this.src='<?php assign($this->setVersion('/img/base/imgUser1.jpg'));?>';"><span
                            class="adminUserName"><?php assign($admin_user->name) ?></span
                            ><?php if($this->loginUserId != $admin_user->id):?><a href="javascript:void(0)"data-option="<?php assign($admin_user->id); ?>" class="adminUserDelete">ユーザーの削除</a><?php endif; ?>
                    </li>
                <?php endforeach; ?>
                </ul>
            <!-- /.adminUserListWrap --></section>

        </article>
    </div>

    <div class="modal1 jsModal" id="modal1">
        <section class="modalCont-medium jsModalCont">
            <h1>管理者の削除</h1>

            <div class="modalAdminUser">
                <p class="userCheck"><img src="" height="38"
                                          width="38" alt="use name" id="userImg" class="adminUserImg"><strong
                        id="userName"></strong>さんを管理者から削除します。</p>
            </div>
            <form name="frmDelete" action="<?php assign(Util::rewriteUrl('admin-settings', 'delete_administrator')); ?>"
                  method="POST">
                <?php write_html($this->csrf_tag()); ?>
                <?php write_html($this->formHidden('admin_uid', '')); ?>
            </form>

            <p class="btnSet">
                <span class="btn2"><a href="#closeModal">キャンセル</a></span>
                <span class="btn4"><a href="javascript:void(0)" id='submitButton' data-type='refresh'>削除</a></span>
            </p>
        </section>
    <!-- /.modal1 --></div>

    <div class="modal2 jsModal" id="modal2">
        <section class="modalCont-small jsModalCont">
            <h1>確認</h1>
            <p><span class="attention1">メールを送信します。</span></p>
            <p class="btnSet"><span class="btn2"><a href="#closeModal" class="middle1">キャンセル</a></span><span class="btn4"><a id="sendMail" href="javascript:void(0)" class="middle1">送信</a></span></p>
        </section>
    </div>

<?php $script = array('DeleteAdministratorService'); ?>
<?php $param = array_merge($data['pageStatus'], array('script' => $script)) ?>
<?php write_html($this->parseTemplate('BrandcoFooter.php', $param)); ?>
