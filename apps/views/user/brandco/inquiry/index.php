<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoHeader')->render($data['pageStatus'])) ?>
<?php if($data['skip_age_authentication']): ?>
    <?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoTopHeader')->render($data['pageStatus'])) ?>
<?php else: ?>
    <?php write_html($this->parseTemplate('AgeAuthenticationHeader.php', $data['pageStatus'])) ?>
<?php endif; ?>
<article>
    <section class="inquiryWrap">
        <form action="<?php assign(Util::rewriteUrl( 'inquiry', 'confirm' )); ?>" method="POST">
            <?php write_html($this->csrf_tag()); ?>
            <?php write_html($this->formHidden('referer', $data['referer'])); ?>
            <?php write_html($this->formHidden('cp_id', $data['cp_id'])); ?>

            <h1 class="hd1">お問い合わせ</h1>
            <span></span>
            <?php if ($data['brand']->id == 479): // 文言ハードコーディング（MONIPLA_OP-1518） ?>
                <p class="supplement1">・お問い合わせの内容によっては窓口が異なる可能性があります。お問い合わせの入力の前に、お問い合わせ窓口をご確認お願いいたします。<br>
                　　お問い合わせ窓口について：<a href="https://kenken.or.jp/page/inquiry_about" target="_blank">https://kenken.or.jp/page/inquiry_about</a><br><br></p>
                <p class="supplement1">・本サイトはアライドアーキテクツ株式会社が提供する「モニプラ」「アライドID」を利用し運営されております。</p>
                <p class="supplement1">・本フォームにていただいたお問い合わせの回答はEメールのみでの対応となっております。お電話での回答は行っておりません。</p>
                <p class="supplement1">・お問い合わせへの回答はご記入いただいたメールアドレスへご連絡いたします。お間違えのないようご記入ください。</p>
                <p class="supplement1">・ドメイン指定受信をご利用の方は「@kenken.or.jp」「@monipla.com」からのメールを受け取れるよう設定の変更をお願いいたします。</p>
                <p class="supplement1">・お返事を差し上げるまでにお時間をいただく場合や、ご質問によっては、お返事を差し上げられない場合もございます。</p>
                <p class="supplement1">・本フォームにていただいたお問い合わせには、営業時間内に対応させていただきます。<br>
                    　　営業時間：10:00～18:00 （土・日・祝日・年末年始・夏期休暇を除く）<br><br></p>
            <?php else: ?>
                <p class="supplement1">・本フォームにていただいたお問い合わせの回答はEメールのみでの対応となっております。お電話での回答は行っておりません。</p>
                <p class="supplement1">・お問い合わせへの回答はご記入いただいたメールアドレスへご連絡いたします。お間違えのないようご記入ください。</p>
                <p class="supplement1">・ドメイン指定受信をご利用の方は「@monipla.com」からのメールを受け取れるよう設定の変更をお願いいたします。</p>
                <p class="supplement1">・お返事を差し上げるまでにお時間をいただく場合や、ご質問によっては、お返事を差し上げられない場合もございます。</p>
                <?php if ($data['brand']->id == 496): // 文言ハードコーディング（SUBWAY-13） ?>
                    <p class="supplement1">※対応時間　平日10：00～18：00<br>
                        　　(土日祝および年末年始を除く)<br><br>
                    </p>
                <?php else: ?>
                    <p class="supplement1">・お問い合わせには、営業時間内に対応させていただきます。<br>
                        　営業時間：10:00～18:00　（土・日・祝日・年末年始・夏期休暇を除く）<br><br>
                    </p>
                <?php endif; ?>
            <?php endif; ?>

            <ul class="commonTableList1">
                <li>
                    <p class="title1">
                        <span class="<?php assign(($data['pageStatus']['isLogin'] && !$data['monipla_flg']) ? 'require1' : ''); ?>">お問い合わせ先</span>
                        <?php if ($this->ActionError && !$this->ActionError->isValid('operator_type')): ?>
                            <span class="iconError1"><?php assign($this->ActionError->getMessage('operator_type')) ?></span>
                        <?php endif; ?>
                        <!-- /.title1 --></p>

                    <?php $operator_type_options = array(
                        'default'   => array('value' => InquiryRoom::TYPE_MANAGER,  'label' => InquiryBrand::MANAGER_BRAND_NAME,    'message' => 'キャンペーン参加状況やメール配信、登録内容などについてはこちら'),
                        'custom'    => array('value' => InquiryRoom::TYPE_ADMIN,    'label' => $data['brand']->name,                'message' => 'キャンペーンにおけるプレゼント賞品内容や配送についてはこちら'),
                    ); ?>
                    <?php if ($data['brand']->id == 479) { // 選択肢ハードコーディング（MONIPLA_OP-1518
                        $operator_type_options = array(
                            'default'   => array('value' => InquiryRoom::TYPE_ADMIN,    'label' => $data['brand']->name,                'message' => '本サイト、日本健康マスター検定についてはこちら'),
                            'custom'    => array('value' => InquiryRoom::TYPE_MANAGER,  'label' => InquiryBrand::MANAGER_BRAND_NAME,    'message' => 'メール配信、登録内容、キャンペーンについてはこちら'),
                        );
                    } ?>
                    <?php if ($data['pageStatus']['isLogin'] && !$data['monipla_flg']) : ?>
                        <?php foreach ($operator_type_options as $operator_type_option): ?>
                            <p class="itemEdit2">
                                <span><?php assign($operator_type_option['message']); ?></span>
                                <?php write_html($this->formRadio('operator_type', $data['operator_type'] ?: $operator_type_options['default']['value'], array('class' => 'customRadio'), array($operator_type_option['value'] => $operator_type_option['label']), array(), ' ')); ?>
                                <!-- /.itemEdit --></p>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="itemEdit">
                            <label><?php assign($operator_type_options['default']['label']); ?></label>
                            <?php write_html($this->formHidden('operator_type',  $operator_type_options['default']['value'])); ?>

                            <!-- /.itemEdit --></p>
                    <?php endif; ?>
                </li>
                <li>
                    <p class="title1">
                        <span class="require1">お問い合わせの種類</span>
                        <!-- /.title1 --></p>
                    <p class="itemEdit">
                        <?php if ($this->ActionError && !$this->ActionError->isValid('category')): ?>
                            <span class="iconError1"><?php assign($this->ActionError->getMessage('category')) ?></span>
                        <?php endif; ?>

                        <span class="editInput">
                            <?php write_html($this->formSelect('category', $data['category'], array(), Inquiry::$category_options)); ?>
                        <!-- /.editInput --></span>
                        <!-- /.itemEdit --></p>
                </li>
                <li>
                    <p class="title1">
                        <span class="require1">お名前</span>
                        <!-- /.title1 --></p>
                    <p class="itemEdit">
                        <?php if ($this->ActionError && !$this->ActionError->isValid('user_name')): ?>
                            <span class="iconError1"><?php assign($this->ActionError->getMessage('user_name')) ?></span>
                        <?php endif; ?>
                        <span class="editInput">
                            <?php write_html($this->formText('user_name', $data['user_name'], array('placeholder' => '山田太郎'))) ?>
                            <!-- /.editInput --></span>
                        <!-- /.itemEdit --></p>
                </li>
                <li>
                    <p class="title1"><span class="require1">メールアドレス</span></p>
                    <p class="itemEdit">
                        <?php if ($this->ActionError && !$this->ActionError->isValid('mail_address')): ?>
                            <span class="iconError1"><?php assign($this->ActionError->getMessage('mail_address')) ?></span>
                        <?php endif; ?>
                        <span class="editInput"><?php write_html($this->formEmail('mail_address', $data['mail_address'], array('placeholder' => 'sample@monipla.com'))) ?></span>
                    </p>
                </li>
                <li>
                    <p class="title1"><span class="require1">お問い合わせ内容</span></p>
                    <p class="itemEdit">
                        <?php if($this->ActionError && !$this->ActionError->isValid('content')):?>
                            <span class="iconError1"><?php assign( $this->ActionError->getMessage('content') )?></span>
                        <?php endif;?>
                        <span class="editInput"><?php write_html( $this->formTextArea('content', $data['content'], array('cols' => '30', 'rows' => '10')))?></span>
                    </p>
                </li>
            </ul>
            <!--        <h2 class="hd2">利用規約</h2>-->
            <section class="ruleAreaWrap1">
                <?php if ($data['brand']->id == 479): // 文言ハードコーディング（MONIPLA_OP-1518） ?>
                    <p class="supplement1"><a href="<?php assign(Util::rewriteUrl('page', 'privacy')); ?>" class="openNewWindow1" target="_blank">一般社団法人 日本健康生活推進協会 個人情報保護方針</a>・<a href="http://www.aainc.co.jp/privacy/" class="openNewWindow1" target="_blank">アライドアーキテクツ株式会社個人情報保護方針</a>についてに同意の上、お問い合わせください。</p>
                <?php else: ?>
                    <p class="supplement1"><a href="http://www.aainc.co.jp/privacy/" class="openNewWindow1" target="_blank">個人情報保護方針について</a>に同意の上、お問い合わせください。</p>
                <?php endif; ?>
            </section>
            <p class="btnSet"><span class="btn3"><a href="javascript:void(0)" class="large3 jsInquirySubmit" data-submit_flg="1"><small>個人情報保護方針について</small>同意して確認</a></span></p>

        </form>
    </section>
</article>

<?php write_html($this->scriptTag('InquiryService')) ?>
<?php write_html($this->parseTemplate('BrandcoFooter.php', $data['pageStatus'])); ?>