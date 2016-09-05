<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoHeader')->render($data['pageStatus'])) ?>

<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoTopHeader')->render($data['pageStatus'])) ?>

    <article>
        <section class="messageWrap">
            <div class="message">
                <h1 class="messageHd1">お客様の注文</h1>
                <div class="messageOrder">
                    <?php foreach ($data['order']->orderItems as $orderItem):?>
                    <p class="productImg"><img src="<?php assign($orderItem->getProductItem()->image_url)?>" alt="<?php assign($orderItem->product_item_title)?>">
                        <!-- /.productImg --></p>
                    <div class="settlementInfo">
                        <div class="settlementInfoInner">
                            <h2 class="title"><?php assign($orderItem->product_item_title)?></h2>
                            <p class="number">数量
                                <span><?php assign($orderItem->sales_count)?>点</span>
                            </p>
                            <!-- /.headInfo --></div>
                        <!-- /.settlementInfo --></div>
                    <?php endforeach;?>
                    <dl class="total">
                        <dt>商品の小計：</dt>
                        <dd><?php assign(number_format($data['order']->sub_total_cost))?>円</dd>
                        <?php if($data['order']->delivery_charge > 0):?>
                        <dt>送料・手数料：</dt>
                        <dd><?php assign(number_format($data['order']->delivery_charge))?>円</dd>
                        <?php endif;?>
                        <dt>注文合計：</dt>
                        <dd class="price"><strong><?php assign(number_format($data['order']->total_cost))?></strong>円</dd>
                        <!-- /.total --></dl>
                    <p class="orderDate">注文日：<?php assign(date('Y月m月d日 H:i',strtotime($data['order']->order_completion_date)))?></p>

                    <section>
                        <h2 class="hd2">配送先情報</h2>
                        <p>〒<?php assign($data['order']->zip_code1)?>-<?php assign($data['order']->zip_code2)?><br>
                            <?php assign($data['order']->pref_name.$data['order']->address1.$data['order']->address2)?><br>
                            <?php assign($data['order']->address3)?><br>
                            電話番号：<?php assign($data['order']->tel_no1)?>-<?php assign($data['order']->tel_no2)?>-<?php assign($data['order']->tel_no3)?></p>
                    </section>
                    <?php if($data['order']->pay_type === (string)Order::payType_Credit):?>
                        <section>
                            <h2 class="hd2">お支払い方法</h2>
                            <p>クレジットカード決済</p>
                            <p class="ChoiceCrad">下4桁 <?php assign($data['order']->payment_credit)?></p>
                            <div class="conveniOrderWrap">
                                <dl class="conveniOrderList">
                                    <dt>注文ID</dt>
                                    <dd><?php assign($data['order']->gmo_payment_order_id)?></dd>
                                </dl>
                                <h3 class="hd3">注意事項</h3>
                                <p class="supplement1">・お申込み情報に関する情報をメールでもお送りしております。必ずご確認をお願いします。</p>
                                <!-- /.conveniInfoWrap --></div>
                        </section>
                    <?php elseif($data['order']->pay_type === (string)Order::payType_Convenience):?>
                        <section>
                            <h2 class="hd2">お支払い方法</h2>
                            <p><?php assign($data['order']->convenience_name)?>で決済（<a target="_blank" href="https://www.gmo-pg.com/service/convenience_store/various_user3/">コンビニでのお支払い方法</a>）</p>
                            <p>お支払いを確認後、発送手続き開始となります。</p>
                            <div class="conveniOrderWrap">
                                <dl class="conveniOrderList">
                                    <dt>支払状況</dt>
                                    <?php if($data['order']->payment_status == 'REQSUCCESS'):?>
                                        <dd>入金前</dd>
                                    <?php elseif($data['order']->payment_status == 'PAYSUCCESS'):?>
                                        <dd>入金済み</dd>
                                    <?php elseif($data['order']->payment_status == 'CANCEL'):?>
                                        <dd><span class="error">キャンセル（再購入より購入いただけます）</span></dd>
                                    <?php endif;?>
                                    <dt>支払期限</dt>
                                    <dd><em><?php assign(date("Y年m月d日", strtotime($data['order']->payment_term_date)))?></em></dd>
                                    <dt>注文ID</dt>
                                    <dd><?php assign($data['order']->gmo_payment_order_id)?></dd>
                                    <?php if(Order::isNeedConfirmNumber($data['order']->convenience_code)):?>
                                    <dt>確認番号</dt>
                                    <dd><?php assign($data['order']->payment_conf_no)?></dd>
                                    <?php endif;?>
                                    <dt><?php assign(Order::paymentNumberName($data['order']->convenience_code))?></dt>
                                    <dd><?php assign($data['order']->payment_receipt_no)?></dd>
                                </dl>
                                <h3 class="hd3">注意事項</h3>
                                <p class="supplement1">
                                    ・支払い方法に関する方法をメールでもお送りしております。必ずご確認のうえご対応をお願いします。<br>
                                    ・期日までに支払いが行われない場合、ご注文はキャンセルとなります。<br>
                                    ・お支払いいただいたコンビニでの返金は承っておりませんので、ご注意ください。</p>
                                <!-- /.conveniInfoWrap --></div>
                        </section>
                    <?php endif;?>
                    <!-- /.messageOrder --></div>
                <!-- /.message --></div>
            <div class="messageListFooter">
                <ul class="pager2">
                    <li class="prev"><a href="<?php assign($data['mypage_url'])?>" class="iconPrev1">メッセージ一覧へ</a></li>
                    <!-- /.pager2 --></ul>
                <!-- /.messageListFooter --></div>
            <p class="pageTop"><a href="#top"><span>ページTOPへ</span></a></p>
            <!-- /.messageWrap --></section>
    </article>

<?php write_html($this->parseTemplate('BrandcoFooter.php', $data['pageStatus'])); ?>