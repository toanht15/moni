<section class="message">
    <h1 class="messageHd1">「<?php assign($data['product_item']->title)?>」のご購入</h1>
    <div class="messageSettlement">
        <div class="settlementInfo">
            <p class="productImg">
                <img src="<?php assign($data['product_item']->image_url)?>" alt="<?php assign($data['product']->title)?>">
            </p>
            <form name="frmOrder" method="get" action="<?php assign(Util::rewriteUrl('products','detail',array($data['product']->id)))?>">
            <div class="settlementInfoInner">
                <h2 class="title"><?php assign($data['product_item']->title)?></h2>
                <p class="price"><strong><?php assign(number_format($data['product_item']->unit_price))?></strong>円（税込）</p>
                <?php if($data['product_item']->stock > 0):?>
                <p class="number">数量
                    <select name="order_count[<?php echo($data['product_item']->id)?>]" id="">
                        <?php for($i=1; $i <= ($data['product_item']->stock >= 10 ? 10 : $data['product_item']->stock); $i++):?>
                            <option value="<?php assign($i)?>"><?php assign($i)?></option>
                        <?php endfor;?>
                    </select>
                </p>
                <p class="btn4"><a href="javascript:document.frmOrder.submit()" class="large1"><span class="iconCart">購入する</span></a></p>
                <?php else:?>
                    <p class="btn4"><span class="large1"><span class="iconCart">購入する</span></span></p>
                    <p>販売を終了しました</p>
                <?php endif;?>
                <!-- /.settlementInfoInner --></div>
            <p class="text"><?php write_html($this->nl2brAndHtmlspecialchars($data['product_item']->description))?>
            </p>
            <!-- /.settlementInfo --></div>
        <!-- /.messageSettlement --></div>
    <!-- /.message --></section>
