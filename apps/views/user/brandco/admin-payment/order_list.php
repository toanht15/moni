<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoHeader')->render($data['pageStatus'])) ?>
<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoAccountHeader')->render($data['pageStatus'])) ?>
<?php $timeHH=array() ?>
<?php for($i=0;$i<24;$i++): ?>
    <?php if($i<10): ?>
        <?php $j='0'.$i; ?>
    <?php else: ?>
        <?php $j = $i; ?>
    <?php endif; ?>
    <?php $timeHH[$j] = $j; ?>
<?php endfor; ?>

<?php $timeMM=array(); ?>
<?php for($i=0;$i<60;$i++): ?>
    <?php if($i<10): ?>
        <?php $j='0'.$i; ?>
    <?php else: ?>
        <?php $j = $i; ?>
    <?php endif; ?>
    <?php $timeMM[$j] = $j; ?>
<?php endfor; ?>
    <article>

        <?php if($data['error_message']):?>
            <br>
            <p class="attention1"><?php assign($data['error_message']);?></p>
            <br>
        <?php endif;?>
        <?php if(isset($_GET['updated'])):?>
            <p>インポート結果 </p>
            <p>・更新完了　<?php assign($_GET['updated'])?>件</p>
            <p>・更新失敗　<?php assign($_GET['failed'])?>件</p>
            <?php if($_GET['failed_ids']):?>
                <dl>
                <?php foreach (explode(',',$_GET['failed_ids']) as $failedId):?>
                    <li><?php assign($failedId)?></li>
                <?php endforeach;?>
                </dl>
            <?php endif;?>
            <br/>
        <?php endif;?>

        <h1 class="hd1">配送管理</h1>

        <section class="inquirySearch jsSearchForm">
            <form method="GET" name="orderSearchForm">
                <table class="inquiryTable1">
                    <tbody>
                    <tr class="jsCheckToggleWrap">
                        <th class="title1">注文完了日</span></th>
                        <td class="edit1">
                            <?php write_html($this->formText('from_order_completion_date', $_GET['from_order_completion_date'], array('class' => 'jsDate inputDate', 'placeholder' => '年/月/日'))) ?>
                            <?php write_html( $this->formSelect( 'from_order_completion_date_HH', array($_GET['from_order_completion_date_HH']), array('class'=>'inputTime'), $timeHH)); ?>：
                            <?php write_html( $this->formSelect( 'from_order_completion_date_MM', array($_GET['from_order_completion_date_MM']), array('class'=>'inputTime'), $timeMM)); ?>：
                            <?php write_html( $this->formSelect( 'from_order_completion_date_SS', array($_GET['from_order_completion_date_SS']), array('class'=>'inputTime'), $timeMM)); ?>

                            <br><span class="dash">～</span><br>
                            <?php write_html($this->formText('to_order_completion_date', $_GET['to_order_completion_date'], array('class' => 'jsDate inputDate', 'placeholder' => '年/月/日'))) ?>
                            <?php write_html( $this->formSelect( 'to_order_completion_date_HH', array($_GET['to_order_completion_date_HH'] ? $_GET['to_order_completion_date_HH'] : 23), array('class'=>'inputTime' ), $timeHH)); ?>；
                            <?php write_html( $this->formSelect( 'to_order_completion_date_MM', array($_GET['to_order_completion_date_MM'] ? $_GET['to_order_completion_date_MM'] : 59), array('class'=>'inputTime',), $timeMM)); ?>：
                            <?php write_html( $this->formSelect( 'to_order_completion_date_SS', array($_GET['to_order_completion_date_SS'] ? $_GET['to_order_completion_date_SS'] : 59), array('class'=>'inputTime',), $timeMM)); ?>

                        </td>
                        <th class="title2">決済完了日</th>
                        <td>
                            <?php write_html($this->formText('from_payment_completion_date', $_GET['from_payment_completion_date'], array('class' => 'jsDate inputDate', 'placeholder' => '年/月/日'))) ?>
                            <?php write_html( $this->formSelect( 'from_payment_completion_date_HH', array($_GET['from_payment_completion_date_HH']), array('class'=>'inputTime'), $timeHH)); ?>：
                            <?php write_html( $this->formSelect( 'from_payment_completion_date_MM', array($_GET['from_payment_completion_date_MM']), array('class'=>'inputTime'), $timeMM)); ?>：
                            <?php write_html( $this->formSelect( 'from_payment_completion_date_SS', array($_GET['from_payment_completion_date_SS']), array('class'=>'inputTime'), $timeMM)); ?>
                            <br><span class="dash">～</span><br>
                            <?php write_html($this->formText('to_payment_completion_date', $_GET['to_payment_completion_date'], array('class' => 'jsDate inputDate', 'placeholder' => '年/月/日'))) ?>
                            <?php write_html( $this->formSelect( 'to_payment_completion_date_HH', array($_GET['to_payment_completion_date_HH'] ? $_GET['to_payment_completion_date_HH'] : 23), array('class'=>'inputTime'), $timeHH)); ?>；
                            <?php write_html( $this->formSelect( 'to_payment_completion_date_MM', array($_GET['to_payment_completion_date_MM'] ? $_GET['to_payment_completion_date_MM'] : 59), array('class'=>'inputTime'), $timeMM)); ?>：
                            <?php write_html( $this->formSelect( 'to_payment_completion_date_SS', array($_GET['to_payment_completion_date_SS'] ? $_GET['to_payment_completion_date_SS'] : 59), array('class'=>'inputTime'), $timeMM)); ?>

                        </td>
                    </tr>
                    <tr class="jsCheckToggleWrap">
                        <th class="title1">会員No</span></th>
                        <td class="edit1">
                            <?php write_html($this->formText('user_no', $_GET['user_no'], array('class' => 'widthFull', 'placeholder' => ''))) ?>
                        </td>
                        <th class="title2">注文ID</th>
                        <td>
                            <?php write_html($this->formText('gmo_payment_order_id', $_GET['gmo_payment_order_id'], array('class' => 'widthFull', 'placeholder' => ''))) ?>
                        </td>
                    </tr>
                    <tr class="jsCheckToggleWrap">
                        <th class="title1">発送完了日</span></th>
                        <td class="edit1">
                            <?php write_html($this->formText('from_delivery_completion_date', $_GET['from_delivery_completion_date'], array('class' => 'jsDate inputDate', 'placeholder' => '年/月/日'))) ?>
                            <?php write_html( $this->formSelect( 'from_delivery_completion_date_HH', array($_GET['from_delivery_completion_date_HH']), array('class'=>'inputTime'), $timeHH)); ?>：
                            <?php write_html( $this->formSelect( 'from_delivery_completion_date_MM', array($_GET['from_delivery_completion_date_MM']), array('class'=>'inputTime'), $timeMM)); ?>：
                            <?php write_html( $this->formSelect( 'from_delivery_completion_date_SS', array($_GET['from_delivery_completion_date_SS']), array('class'=>'inputTime'), $timeMM)); ?>

                            <br><span class="dash">～</span><br>
                            <?php write_html($this->formText('to_delivery_completion_date', $_GET['to_delivery_completion_date'], array('class' => 'jsDate inputDate', 'placeholder' => '年/月/日'))) ?>
                            <?php write_html( $this->formSelect( 'to_delivery_completion_date_HH', array($_GET['to_delivery_completion_date_HH'] ? $_GET['to_delivery_completion_date_HH'] : 23), array('class'=>'inputTime' ), $timeHH)); ?>；
                            <?php write_html( $this->formSelect( 'to_delivery_completion_date_MM', array($_GET['to_delivery_completion_date_MM'] ? $_GET['to_delivery_completion_date_MM'] : 59), array('class'=>'inputTime',), $timeMM)); ?>：
                            <?php write_html( $this->formSelect( 'to_delivery_completion_date_SS', array($_GET['to_delivery_completion_date_SS'] ? $_GET['to_delivery_completion_date_SS'] : 59), array('class'=>'inputTime',), $timeMM)); ?>

                        </td>
                        <th class="title2">発送状態</th>
                        <td>
                            <input type="radio" name="delivery_flg" value="99"<?php assign($_GET['delivery_flg'] === '99' || !isset($_GET['delivery_flg'] ) ? 'checked="checked"' : "")?> >指定なし
                            <input type="radio" name="delivery_flg" value="0" <?php assign($_GET['delivery_flg'] === '0' ? 'checked="checked"' : "")?>> 未発送
                            <input type="radio" name="delivery_flg" value="1" <?php assign($_GET['delivery_flg'] === '1' ? 'checked="checked"' : "")?>> 発送済み
                        </td>
                    </tr>

                    <tr>
                        <td colspan="4">
                        <span class="btnSet">
                            <span class="btn3"><a href="javascript:void(0)" class="jsOrderSearch" data-url="<?php assign(Util::rewriteUrl('admin-payment','order_list',array($data['cp_action_id'])));?>">検索する</a></span>
                            <span class="btn3"><a href="javascript:void(0)" class="jsOrderDownLoad" data-url="<?php assign(Util::rewriteUrl('admin-payment','csv_order_list_download',array($data['cp_action_id'])))?>">CSV出力</a></span>
                            <!-- /.btnSet --></span>
                        </td>
                    </tr>
                    </tbody>
                    <!-- /.inquiryTable1 --></table>

            </form>
                <table class="inquiryTable1">
                    <tbody>
                    <tr class="jsCheckToggleWrap">
                        <td style="text-align: center">
                            <p><span class="btn3"><a href="#modal2" class="jsOpenModal">発送情報インポート</a></span></p>
                        </td>
                    </tr>
                    </tbody>
                </table>

            <!-- /.inquirySearch --></section>

        <section class="inquiryUserList jsInquiryList">
            <section class="userListWrap">
                <?php write_html(aafwWidgets::getInstance()->loadWidget('OrderListPager')->render(array(
                    'TotalCount' => $data['total_count'],
                    'CurrentPage' => $data['page'],
                    'Count' => $data['count'],
                ))) ?>
                <table class="itemTable">
                    <thead>
                    <tr>
                        <th>注文ID</th>
                        <th>会員NO</th>
                        <th>伝票番号</th>
                        <th>購入商品名</th>
                        <th>購入個数</th>
                        <th>郵便番号</th>
                        <th>住所</th>
                        <th>氏</th>
                        <th>名</th>
                        <th>氏(かな)</th>
                        <th>名(かな)</th>
                        <th>電話番号</th>
                        <th>注文日時</th>
                        <th>決済完了日時</th>
                        <th>発送日</th>
                        <th>発送ステータス</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($data['orders'] as $order):?>
                    <tr>
                        <td><?php assign($order['gmo_payment_order_id'])?></td>
                        <td><?php assign($order['no'])?></td>
                        <td><?php assign($order['delivery_id'])?></td>
                        <td><?php assign($order['product_title'])?></td>
                        <td><?php assign($order['sales_count'])?></td>
                        <td><?php assign($order['zip_code1']."-".$order['zip_code2'])?></td>
                        <td><?php assign($order['pref_name'].$order['address1'].$order['address2'].$order['address3'])?></td>
                        <td><?php assign($order['last_name'])?></td>
                        <td><?php assign($order['first_name'])?></td>
                        <td><?php assign($order['last_name_kana'])?></td>
                        <td><?php assign($order['first_name_kana'])?></td>
                        <td><?php assign($order['tel_no1'].'-'.$order['tel_no2'].'-'.$order['tel_no3'])?></td>
                        <td><?php assign($order['order_completion_date'])?></td>
                        <td><?php assign($order['payment_completion_date'])?></td>
                        <td><?php assign($order['delivery_date'])?></td>
                        <td><?php assign($order['delivery_flg'])?></td>
                    </tr>
                    <?php endforeach;?>
                    </tbody>
                    <!-- /.itemTable --></table>

                <?php write_html(aafwWidgets::getInstance()->loadWidget('OrderListPager')->render(array(
                    'TotalCount' => $data['total_count'],
                    'CurrentPage' => $data['page'],
                    'Count' => $data['count'],
                ))) ?>
            </section>

            <!-- /.inquiryUserList --></section>
    </article>

    <div class="modal1 jsModal" id="modal2">
        <section class="modalCont-medium jsModalCont">
            <h1>発送情報インポート</h1>
            <div class="modalAdminCoupon">
                <form name="importDeliveryInfoForm" action="<?php assign(Util::rewriteUrl('admin-payment','import_delivery_info',array($data['cp_action_id'])))?>" method="POST" enctype="multipart/form-data">
                    <?php write_html($this->csrf_tag()); ?>
                    <p><input type="file" accept="text/csv"  name="deliveryInfo">
                    <br><small>※CSVファイル（SJIS形式）で作成してください。（<a href="<?php assign($this->setVersion('/csv/delivery_sample.csv'))?>">CSVファイルサンプル</a>）</small>
                    </p>
                </form>
                <h2 class="hd2">注意点</h2>
                <ul class="listUnordered1">
                    <li>各項目をカンマ(,)で区切ってください。</li>
                    <li>１行目にカラム名は入れないでください。１行目よりインポートしたいデータを入れてください。</li>
                    <li>１行で１発送情報になります。空行は無視されます。</li>
                    <li>１列目に注文ID、２列目に伝票番号を入れてください</li>
                </ul>
                <!-- /.modalAdminCoupon --></div>
            <p class="btnSet">
                <span class="btn2"><a href="#closeModal">キャンセル</a></span>
                <span class="btn3"><a href="javascript: void(0);" onclick="document.importDeliveryInfoForm.submit(); return false;">インポート</a></span>
            </p>
        </section>
        <!-- /.modal1 --></div>

    <link rel="stylesheet" href="<?php assign($this->setVersion('/css/jqueryUI.css')) ?>">
    <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
    <script type="text/javascript"
            src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/i18n/jquery.ui.datepicker-ja.min.js"></script>
<?php write_html($this->scriptTag('OrderListService')) ?>
<?php write_html($this->parseTemplate('BrandcoFooter.php', array_merge($data['pageStatus'], array('script' => array())))); ?>