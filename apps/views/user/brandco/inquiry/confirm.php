<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoHeader')->render($data['pageStatus'])) ?>
<?php if($data['skip_age_authentication']): ?>
    <?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoTopHeader')->render($data['pageStatus'])) ?>
<?php else: ?>
    <?php write_html($this->parseTemplate('AgeAuthenticationHeader.php', $data['pageStatus'])) ?>
<?php endif; ?>

<form id="inquiryForm" action="<?php assign(Util::rewriteUrl( 'inquiry', 'complete' )); ?>" method="POST">
    <?php write_html($this->csrf_tag()); ?>
    <?php write_html($this->formHidden('referer', $data['inquiry']['referer'])); ?>
    <?php write_html($this->formHidden('cp_id', $data['inquiry']['cp_id'])); ?>
    <article>
        <section class="inquiryWrap">
            <h1 class="hd1">お問い合わせ</h1>
            <ul class="commonTableList1">
                <li>
                    <p class="title1">お問い合わせ先</p>
                    <p class="item1"><?php assign((InquiryRoom::isManager($data['inquiry']['operator_type'])) ? InquiryBrand::MANAGER_BRAND_NAME : $data['brand']->name); ?></p>
                    <?php write_html($this->formHidden('operator_type', $data['inquiry']['operator_type'])); ?>
                </li>
                <li>
                    <p class="title1">カテゴリー</p>
                    <p class="item1">
                        <?php assign(Inquiry::getCategory($data['inquiry']['category'])); ?>
                    </p>
                    <?php write_html($this->formHidden('category', $data['inquiry']['category'])); ?>
                </li>
                <li>
                    <p class="title1">お名前</p>
                    <p class="item1"><?php assign($data['inquiry']['user_name']); ?></p>
                    <?php write_html($this->formHidden('user_name', $data['inquiry']['user_name'])); ?>
                </li>
                <li>
                    <p class="title1">メールアドレス</p>
                    <p class="item1"><?php assign($data['inquiry']['mail_address']); ?></p>
                    <?php write_html($this->formHidden('mail_address', $data['inquiry']['mail_address'])); ?>
                </li>
                <li>
                    <p class="title1"><span>お問い合わせ内容</span></p>
                    <p class="item1"><?php write_html($this->nl2brAndHtmlspecialchars($data['inquiry']['content'])); ?></p>
                    <?php write_html($this->formHidden('content', $data['inquiry']['content'])); ?>
                </li>
            </ul>
            <p class="btnSet">
                <span class="btn2"><a href="javascript:void(0);" class="large1 jsInquirySubmit" data-submit_flg="0">戻る</a></span>
                <span class="btn3"><a href="javascript:void(0);" class="large1 jsInquirySubmit" data-submit_flg="1">送信</a></span>
            </p>
        </section>
    </article>
</form>

<?php write_html($this->scriptTag('InquiryService')) ?>
<?php write_html($this->parseTemplate('BrandcoFooter.php', $data['pageStatus'])); ?>
