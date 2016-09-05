<?php write_html($this->scriptTag("user/UserActionPaymentService")); ?>
<?php if($data['is_opening_flg']):?>
<section class="message jsMessage" id="message_<?php assign($data['message_id']); ?>" >
    <a id="<?php assign('ca_' . $data["cp_action_id"]) ?>"></a>
        <h1 class="messageHd1">「<?php assign($data['product']->title)?>」のご購入</h1>
        <div class="messageSettlement">
            <div class="settlementInfo">
                <p class="productImg">
                    <img src="<?php assign($data['product']->image_url)?>" alt="<?php assign($data['product']->title)?>">
                </p>
                <div class="settlementInfoInner">
                    <h2 class="title"><?php assign($data['product']->product_items[0]->title)?></h2>
                    <p class="price"><strong><?php assign($data['product']->product_items[0]->unit_price)?></strong>円（税込）</p>
                    <p class="number">数量
                        <select name="" id="" disabled="disabled">
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                            <option value="6">6</option>
                            <option value="7">7</option>
                            <option value="8">8</option>
                            <option value="9">9</option>
                            <option value="10">10</option>
                        </select>
                    </p>
                    <p class="btn4"><span href="<?php assign(Util::rewriteUrl('products','detail',array($data['product']->id)))?>" class="large1"><span class="iconCart">購入する</span></span></p>
                    <!-- /.settlementInfoInner --></div>
                <p class="text"><?php write_html($this->nl2brAndHtmlspecialchars($data['product']->product_items[0]->description))?>
                </p>
                <!-- /.settlementInfo --></div>
            <!-- /.messageSettlement --></div>
    <?php if($data['share_flg']):?>
        <div class="campaignShare">
            <p>このキャンペーンを友達に知らせよう</p>
            <ul class="snsBtns-box">
                <li><div class="fb-like" data-href="" data-layout="button_count" data-action="like" data-show-faces="false" data-share="false"></div></li
                ><li><a href="https://twitter.com/share" data-url="<?php assign($data["cp_info"]["cp"]["url"]) ?>" class="twitter-share-button" data-lang="ja" data-count="vertical" data-text="<?php assign($data['cp_info']['tweet_share_text']) ?>">ツイート</a></li
                ><li class="line"><span><script type="text/javascript" src="//media.line.me/js/line-button.js?v=20140411" ></script><script type="text/javascript">new media_line_me.LineButton({"pc":false,"lang":"ja","type":"a", "withUrl":false, "text": "<?php assign($data["cp_info"]["cp"]["url"]) ?>"});</script></span></li
                ><li><div class="g-plusone" data-size="medium" data-href="<?php assign($data["cp_info"]["cp"]["url"]) ?>"></div></li
                    <!-- /.snsBtns --></ul>
            <!-- /.campaignShare --></div>
    <?php endif;?>
        <ul class="campaignData">
            <?php if(!$data['is_permanent']):?>
                <li class="term"><span class="itemTitle">開催期間</span><span class="itemData"><?php assign($data["cp_info"]["cp"]["start_datetime"]); ?> ～ <?php assign($data["cp_info"]["cp"]["end_datetime"]); ?></span></li>
            <?php endif;?>
            <li class="sponsor"><span class="itemTitle">開催</span><span class="itemData"><?php assign($data["cp_info"]["cp"]["sponsor"]); ?></span></li>
            <li class="attention"><span class="itemTitle">注意事項</span><span class="itemData">
              <?php write_html($this->toHalfContentDeeply($data['cp_info']['cp']['recruitment_note'],false)) ?></span></li>
            <!-- /.campaignData --></ul>

        <div class="messageFooter">
            <p class="date"><small><?php assign(date("Y/m/d H:i", strtotime($data['created_at']))); ?></small></p>
        </div>

    <!-- /.message --></section>
<?php endif;?>
<?php if(!$data['is_finished']):?>
    <section class="message jsMessage" id="message_<?php assign($data['message_id']); ?>" >
        <a id="<?php assign('ca_' . $data["cp_action_id"]) ?>"></a>
        <form name="frmOrder" id="js-order-form" method="get" action="<?php assign(Util::rewriteUrl('products','detail',array($data['product']->id)))?>">
            <h1 class="messageHd1">「<?php assign($data['product']->title)?>」のご購入</h1>
            <div class="messageSettlement">
            <div class="settlementInfo">
                <p class="productImg"><img src="<?php assign($data['product']->image_url)?>" alt="<?php assign($data['product']->title)?>"></p>
                <div class="settlementInfoInner">
                    <?php foreach ($data['product']->product_items as $productItem):?>
                        <h2 class="title"><?php assign($productItem->title)?></h2>
                        <p class="price"><strong><?php assign($productItem->unit_price)?></strong>円（税込）</p>
                        <?php if($productItem->stock > 0):?>
                            <p class="number">数量
                                <select name="order_count[<?php assign($productItem->id)?>]" id="">
                                    <?php for($i=1; $i <= ($productItem->stock >= 10 ? 10 : $productItem->stock); $i++):?>
                                        <option value="<?php assign($i)?>"><?php assign($i)?></option>
                                    <?php endfor;?>
                                </select>
                            </p>
                        <?php else:?>
                            販売終了しました
                        <?php endif;?>
                    <?php endforeach;?>
                    <p class="text"><?php write_html($this->nl2brAndHtmlspecialchars($productItem->description))?>
                    <p class="btn4"><a href="javascript:document.frmOrder.submit()" class="large1"><span class="iconCart">購入する</span></a></p>
                    <!-- /.settlementInfoInner --></div>
                <p class="text"><?php assign($data['product']->description)?></p>
            <!-- /.settlementInfo --></div>
        <!-- /.messageSettlement --></div>
            <?php if($data['skip_flg']):?>
                <p class="messageSkip"><a class="cmd_execute_payment_skip_action" href="javascript:void(0)"
                                          data-url="<?php assign(Util::rewriteUrl('messages', 'api_execute_payment_action.json'))?>"
                                          data-cp_action_id="<?php assign($data['cp_action_id']);?>"
                                          data-cp_user_id="<?php assign($data['cp_user_id'])?>"
                    ><small>購入せず次へ</small></a></p>
            <?php endif;?>
        </form>
        <!-- /.message --></section>

<?php else:?>

<?php if($data['lastOrder']):?>
<section class="message_thanks">
    <h1 class="messageHd1">Thank you! <span>ご購入いただきありがとうございました！</span></h1>
    <div class="messageInner">
        <div class="messageOrderContent jsModuleContWrap">
            <div class="settlementInfo jsModuleContText">
                <p class="productImg"><img src="<?php assign($data['lastOrder']->product->image_url)?>" alt="<?php assign($data['lastOrder']->product->title)?>" data-pin-nopin="true"></p>
                <div class="settlementInfoInner">
                    <h2 class="title"><?php assign($data['lastOrder']->product->title)?></h2>
                    <p class="orderDate">注文日：<?php assign(date('Y月m月d日',strtotime($data['lastOrder']->order_completion_date)))?></p>
                    <!-- /.settlementInfoInner --></div>
                <!-- /.settlementInfo --></div>
            <div class="messageOrder jsModuleContTarget" style="display:block;">
                <dl class="total">
                    <dt>商品の小計：</dt>
                    <dd><?php assign(number_format($data['lastOrder']->sub_total_cost))?>円</dd>
                    <?php if($data['lastOrder']->delivery_charge > 0):?>
                    <dt>送料・手数料：</dt>
                    <dd><?php assign(number_format($data['lastOrder']->delivery_charge))?>円</dd>
                    <?php endif;?>
                    <dt>注文合計：</dt>
                    <dd class="price"><strong><?php assign(number_format($data['lastOrder']->total_cost))?></strong>円</dd>
                    <!-- /.total --></dl>
                <p class="orderDate">注文日：<?php assign(date('Y月m月d日 H:i',strtotime($data['lastOrder']->order_completion_date)))?></p>

                <section>
                    <h2 class="hd2">配送先情報</h2>
                    <p>〒<?php assign($data['lastOrder']->zip_code1)?>-<?php assign($data['lastOrder']->zip_code2)?><br>
                        <?php assign($data['lastOrder']->pref_name.$data['lastOrder']->address1.$data['lastOrder']->address2)?><br>
                        <?php assign($data['lastOrder']->address3) ?><br>
                        電話番号：<?php assign($data['lastOrder']->tel_no1)?>-<?php assign($data['lastOrder']->tel_no2)?>-<?php assign($data['lastOrder']->tel_no3)?></p>
                </section>

                <?php if($data['lastOrder']->pay_type === (string)Order::payType_Credit):?>
                <section>
                    <h2 class="hd2">お支払い方法</h2>
                    <p>クレジットカード決済</p>
                    <p class="ChoiceCrad">下4桁 <?php assign($data['lastOrder']->payment_credit)?></p>
                    <div class="conveniOrderWrap">
                        <dl class="conveniOrderList">
                            <dt>注文ID</dt>
                            <dd><?php assign($data['lastOrder']->gmo_payment_order_id)?></dd>
                        </dl>
                        <h3 class="hd3">注意事項</h3>
                        <p class="supplement1">・お申込み情報に関する情報をメールでもお送りしております。必ずご確認をお願いします。</p>
                        <!-- /.conveniInfoWrap --></div>
                </section>
                <?php elseif($data['lastOrder']->pay_type === (string)Order::payType_Convenience):?>
                    <section>
                        <h2 class="hd2">お支払い方法</h2>
                        <p><?php assign($data['lastOrder']->convenience_name)?>で決済（<a target="_blank" href="https://www.gmo-pg.com/service/convenience_store/various_user3/">コンビニでのお支払い方法</a>）</p>
                        <p>お支払いを確認後、発送手続き開始となります。</p>
                        <div class="conveniOrderWrap">
                            <dl class="conveniOrderList">
                                <dt>支払状況</dt>
                                <?php if($data['lastOrder']->payment_status == 'REQSUCCESS'):?>
                                    <dd>入金前</dd>
                                <?php elseif($data['lastOrder']->payment_status == 'PAYSUCCESS'):?>
                                    <dd>入金済み</dd>
                                <?php elseif($data['lastOrder']->payment_status == 'CANCEL'):?>
                                    <dd><span class="error">キャンセル（再購入より購入いただけます）</span></dd>
                                <?php endif;?>
                                <dt>支払期限</dt>
                                <dd><em><?php assign(date("Y年m月d日", strtotime($data['lastOrder']->payment_term_date)))?></em></dd>
                                <dt>注文ID</dt>
                                <dd><?php assign($data['lastOrder']->gmo_payment_order_id)?></dd>
                                <?php if(Order::isNeedConfirmNumber($data['lastOrder']->convenience_code)):?>
                                <dt>確認番号</dt>
                                <dd><?php assign($data['lastOrder']->payment_conf_no)?></dd>
                                <?php endif;?>
                                <dt><?php assign(Order::paymentNumberName($data['lastOrder']->convenience_code))?></dt>
                                <dd><?php assign($data['lastOrder']->payment_receipt_no)?></dd>
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
            <!-- /.messageOrderContent --></div>

        <p class="settlementHistory"><a href="<?php assign($data['mypage_url'])?>">本サイトの購入履歴一覧へ</a></p>
        <section class="messageText">
            <p><?php echo $data['cp_payment_action']->finish_html_content?></p>
            <!-- /.messageText --></section>
        <!-- /.messageInner --></div>
    <p class="messageDate"><small><?php assign(date("Y/m/d H:i", strtotime($data['created_at']))); ?></small></p>
    <!-- /.message_thanks --></section>
<?php endif;?>
    <section class="message_repurchase">
    <?php foreach ($data['product']->product_items as $productItem):?>
    <h1 class="messageHd1 jsModuleContTile close">「<?php assign($productItem->title)?>」の再購入</h1>
    <div class="jsModuleContTarget" style="display: none;">
        <div class="messageOrder">
            <div class="settlementInfo">
                <p class="productImg"><img src="<?php assign($productItem->image_url)?>" alt="<?php assign($productItem->title)?>"></p>
                <div class="settlementInfoInner">
                    <h2 class="title"><?php assign($productItem->title)?></h2>
                    <p class="price"><strong><?php assign(number_format($productItem->unit_price))?></strong>円（税込）</p>
                    <!-- /.settlementInfoInner --></div>
                <!-- /.settlementInfo --></div>
        </div>
        <p class="epurchaseBtn"">
        <?php if($productItem->stock > 0):?>
             <a href="<?php assign(Util::rewriteUrl('products','detail',array($data['product']->id)))?>">再購入する</a>
        <?php else:?>
            <span>販売終了しました</span>
        <?php endif;?>
        </p>
    </div>
    <?php endforeach;?>
    <!-- /.message_repurchase --></section>
<?php endif;?>
<?php write_html($this->scriptTag('user/UserActionPaymentService')); ?>
