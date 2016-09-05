<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoHeader')->render($data['pageStatus'])) ?>
<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoAccountHeader')->render($data['pageStatus'])) ?>

<article>
    <h1 class="hd1">認証コード設定</h1>
    <section class="couponWrap">
        <dl class="couponDetail1">
            <form name="createCodeAuthForm" action="<?php assign(Util::rewriteUrl( 'admin-code-auth', 'save_code_auth' )); ?>" method="POST">
                <?php write_html($this->csrf_tag()); ?>
                <dt class="require1">認証コード名
                    <span class="iconHelp">
                        <span class="text">ヘルプ</span>
                        <span class="textBalloon1">
                          <span>
                            管理用名称のため、一般公開されません
                          </span>
                        <!-- /.textBalloon1 --></span>
                      <!-- /.iconHelp --></span>
                </dt>
                <dd><?php write_html($this->formText('name', PHPParser::ACTION_FORM)); ?></dd>
                <?php if ( $this->ActionError && !$this->ActionError->isValid('name')): ?>
                    <dt></dt>
                    <dd><p class="attention1"><?php assign ( $this->ActionError->getMessage('name') )?></p></dd>
                <?php endif; ?>
                <dt>メモ
                    <span class="iconHelp">
                        <span class="text">ヘルプ</span>
                        <span class="textBalloon1">
                          <span>
                            管理用メモのため、一般公開されません
                          </span>
                        <!-- /.textBalloon1 --></span>
                    <!-- /.iconHelp --></span>
                </dt>
                <dd><?php write_html($this->formTextArea('description', PHPParser::ACTION_FORM, array('cols'=>30, 'rows'=>2))); ?></dd>
            </form>
        </dl>
    </section>
    <p class="btnSet"><span class="btn3"><a href="javascript: void(0);" onclick="document.createCodeAuthForm.submit(); return false;">追加</a></span></p>
    <ul class="pager2">
        <li class="prev"><a href="<?php assign(Util::rewriteUrl('admin-code-auth', 'code_auth_list')) ?>" class="iconPrev1">認証コード一覧へ</a></li>
    <!-- /.pager2 --></ul>
</article>

<?php write_html($this->parseTemplate('BrandcoFooter.php', $data['pageStatus'])); ?>
