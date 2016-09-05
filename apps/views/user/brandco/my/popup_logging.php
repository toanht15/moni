<?php write_html( aafwWidgets::getInstance()->loadWidget('BrandcoHeader')->render($data['pageStatus']) ) ?>

<h1 class="singleWrapHd1">無料登録・ログイン</h1>
<section>
    <div class="registerFansite">
        <p>以下のサイトに登録・ログイン後、入力したコメントが投稿されます。</p>
        <p class="fansiteData">
            <img src="<?php assign($data['pageStatus']['brand']->getProfileImage())?>" width="40" height="40" alt="" class="fansiteImg">
            <strong class="fansiteName"><?php assign($data['pageStatus']['brand']->name) ?></strong>
            <!-- /.fansiteData --></p>
        <!-- /.registerFansite --></div>

    <?php write_html( aafwWidgets::getInstance()->loadWidget('BrandcoLoggingForm')->render(array(
        'pageStatus' => $data['pageStatus'],
        'pageInfo' => $data['pageInfo'],
        'loggingFormInfo' => $data['loggingFormInfo'],
        'ActionForm' => $this->ActionForm,
        'ActionError' => $this->ActionError
    ))); ?>
</section>

<?php write_html($this->parseTemplate('auth/CompletePasswordIssueModal.php')); ?>
<?php write_html( $this->parseTemplate('BrandcoFooter.php', $data['pageStatus']) ); ?>
