<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoHeader')->render($data['pageStatus'])) ?>

<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoTopHeader')->render($data['pageStatus'])) ?>

    <article class="singleWrap">
        <h1 class="hd1">退会手続き</h1>

        <p class="withdrawSite">以下のファンサイトを退会します。<strong>「<?php assign($data['pageStatus']['brand']->name) ?>」</strong></p>

        <h2 class="hd2">注意事項</h2>
        <ul class="commonList1">
            <li>退会が完了すると、サービスの会員登録情報、利用履歴等はすべて閲覧不可能となり、会員機能もご利用できなくなります。</li>
            <li>参加中のキャンペーンへの登録は無効となります。</li>
            <li>投稿物やアンケート回答結果、投票などのデータは削除されません。削除を希望される場合は退会前に<a href="<?php assign(Util::rewriteUrl('', 'inquiry')) ?>">こちら</a>よりお問い合わせください。</li>
            <li>モニプラを退会する場合は<a href="<?php assign('http://'.config('Domain.aaid').'/my/application') ?>">こちら</a>より退会手続きを行ってください。モニプラを退会すると登録中の全てのファンサイトを退会します。</li>
            <li>Allied IDを退会する場合は<a href="<?php assign('http://'.config('Domain.aaid').'/withdraw/withdraw_form') ?>">こちら</a>より退会手続きを行ってください。</li>
            <!-- /.commonList1 --></ul>

        <form name = "withdraw_form" id="withdraw_form" action="<?php assign(Util::rewriteUrl( 'mypage', 'withdraw')); ?>" method="POST">
            <?php write_html($this->csrf_tag()); ?>
        <ul class="commonTableList1">
            <li>
                <p class="title1">
                    <span class="require1">退会の理由</span>
                    <!-- /.title1 --></p>
                <ul class="itemEdit">
                    <?php if ( $this->ActionError && !$this->ActionError->isValid('withdraw_reason')): ?>
                        <p class="attention1"><?php assign ( $this->ActionError->getMessage('withdraw_reason') )?></p>
                    <?php endif; ?>

                    <?php foreach (WithdrawLog::$withdraw_reason as $reason): ?>
                        <li><?php write_html($this->formCheckBox2('withdraw_reason[]', $this->getActionFormValue('withdraw_reason'), array('class'=>'customCheck'), array($reason =>$reason))) ?>
                        <?php if ($reason == "その他"): ?>
                            <?php write_html($this->formTextarea('other_reason', PHPParser::ACTION_FORM, array('cols' => 30, 'rows' => 10))) ?>
                        <?php endif; ?>
                            </li>
                    <?php endforeach; ?>
                    <!-- /.itemEdit --></ul>
            </li>
            <li>
                <p class="title1">
                    <span>その他理由・ご意見など</span>
                    <!-- /.title1 --></p>
                <p class="itemEdit">
          <span class="editInput">
              <?php write_html($this->formTextarea('feedback', PHPParser::ACTION_FORM, array('cols' => 30, 'rows' => 10))) ?>
          <!-- /.editInput --></span>
                    <!-- /.itemEdit --></p>
            </li>
            <!-- /.commonTableList1 --></ul>
            </form>

        <p class="btnSet"><span class="btn2"><a href="<?php assign(Util::rewriteUrl('mypage','inbox')) ?>" class="middle1">戻る</a></span><span class="btn3"><a href="javascript:void(0)" class="large3" onclick="Brandco.unit.openModal('#modal1');"><small>アンケートに回答して</small>退会手続きへ</a></span></p>

        <!-- /.singleWrap --></article>

    <div class="modal2 jsModal" id="modal1">
        <section class="modalCont-small jsModalCont" id="jsModalCont">
            <h1>確認</h1>
            <p><span class="attention1">退会が完了しますと、退会前の状態に戻すことはできません。退会をしてもよろしいですか？</span></p>
            <p class="btnSet">
            <span class="btn2">
                <a href="#closeModal" class="middle1">キャンセル</a>
            </span>
            <span class="btn4"><a id="withdrawConfirm" href="javascript:void(0)" onclick="document.withdraw_form.submit()" class="middle1">OK</a>
            </span></p>
        </section>
    </div>

<?php write_html($this->parseTemplate('BrandcoFooter.php', $data['pageStatus'])); ?>