<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoHeader')->render($data['pageStatus'])) ?>
<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoAccountHeader')->render($data['pageStatus'])) ?>

    <div class="adminWrap">
        <?php write_html($this->parseTemplate('SettingSiteMenu.php',$data['pageStatus'])) ?>

        <article class="adminMainCol">
            <h1 class="hd1">リダイレクトURL<?php if($data['redirector_id']):?>編集<?php else:?>作成<?php endif;?></h1>

            <form id="editRedirectorForm" name="editRedirectorForm"
                  action="<?php assign(Util::rewriteUrl('admin-settings', 'edit_redirector')); ?>" method="POST">
                <?php write_html($this->csrf_tag()); ?>
                <?php write_html($this->formHidden('redirector_id', $data['redirector_id'])); ?>
                <section class="adminRedirectWrap">
                    <dl class="adminRedirectSetting">
                        <dt><label for="input1" class="require1">リダイレクトURL</label></dt>
                        <dd><?php assign(Util::getBaseUrl()); ?>r/<?php write_html($this->formText('sign', PHPParser::ACTION_FORM, array('class' => 'urlParts','maxlength'=>15))) ?>
                            <?php if($data['redirector_id']):?>
                                <a href="javascript:void(0);" class="iconCopy1 jsCopyToClipboardBtn"
                                    data-clipboard-text="<?php assign(Util::getBaseUrl()) ?>r/<?php assign($this->getActionFormValue('sign')); ?>">URLをコピー</a>
                            <?php endif; ?>
                            <?php if ($this->ActionError && !$this->ActionError->isValid('sign')): ?>
                                <p class="attention1"><?php assign($this->ActionError->getMessage('sign')) ?><?php endif; ?></dd>
                        <dt><label for="input2" class="require1">リダイレクト先</label></dt>
                        <dd><?php write_html($this->formText('url', PHPParser::ACTION_FORM, array(), array('maxlength'=>255))) ?>
<?php if ($this->ActionError && !$this->ActionError->isValid('url')): ?>
    <p class="attention1"><?php assign($this->ActionError->getMessage('url')) ?><?php endif; ?></dd>
                        <dt><label for="input3">説明
                                <span class="iconHelp">
                                  <span class="text">ヘルプ</span>
                                  <span class="textBalloon1">
                                    <span>
                                       管理用のメモのため、一般公開されません
                                    </span>
                                  <!-- /.textBalloon1 --></span>
                                <!-- /.iconHelp --></span>
                            </label></dt>
                        <dd><?php write_html($this->formTextArea('description', PHPParser::ACTION_FORM)) ?></dd>
                        <!-- /.adminCvtagSetting --></dl>
                    <input type="hidden" name="del_flg" value="0" class="jsDeleteData">
                    <input type="submit" class="jsSubmitDeleteData" style="display: none">
                    <?php if ($this->redirector_id): ?>
                    <p class="deletePage"><a href="javascript:void(0);" class="linkDelete" data-modal_class='.jsLinkDeleteModal' data-entry='<?php assign('entryId='.$data['entry']->id)?>'>リンク削除</a></p>
                    <?php endif ?>
                    <p class="btnSet"><span class="btn3"><a onclick="document.editRedirectorForm.submit()" href="javascript:void(0);"><?php if($data['redirector_id']):?>更新<?php else:?>作成<?php endif;?></a></span></p>
                <!-- /.adminRedirectWrap --></section>
            </form>
            <ul class="pager2">
                <li class="prev"><a href="<?php assign(Util::rewriteUrl('admin-settings', 'redirector_settings_form')) ?>" class="iconPrev1">リダイレクトURL一覧へ</a></li>
            <!-- /.pager2 --></ul>

        <!-- /.adminMainCol --></article>
    <!-- /.adminWrap --></div>
    <div class="modal1 jsModal jsLinkDeleteModal" id="modal1">
        <section class="modalCont-small jsModalCont">
            <h1>確認</h1>
            <p class="iconError1">このリンクを削除しますか？</p>
            <p class="btnSet">
                <span class="btn2"><a href="#closeModal" class="small1">キャンセル</a></span>
                    <span class="btn4">
                        <a href="javascript:void(0)" class="small1 jsDeleteButton">削除する</a>
                    </span>
            </p>
            <!-- /.modalCont-small --></section>
        <!-- /.modal1 --></div>
<?php write_html($this->scriptTag('admin-settings/EditRedirectorFormService')) ?>
<script src="<?php assign($this->setVersion('/js/zeroclipboard/ZeroClipboard.min.js')) ?>"></script>
<?php write_html($this->parseTemplate('BrandcoFooter.php', $data['pageStatus'])); ?>