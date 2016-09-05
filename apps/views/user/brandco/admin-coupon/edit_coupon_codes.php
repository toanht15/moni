<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoHeader')->render($data['pageStatus'])) ?>
<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoAccountHeader')->render($data['pageStatus'])) ?>

<article>
    <h1 class="hd1">クーポン設定</h1>
    <?php $coupon_action_manager = new CpCouponActionManager() ?>
    <section class="couponWrap">
        <dl class="couponDetail1">
            <form id="updateCouponCodeList" name="updateCouponCodeList" action="<?php  assign(Util::rewriteUrl( 'admin-coupon', 'update_coupon' )); ?>" method="POST">
                <?php write_html($this->csrf_tag()); ?>
                <?php write_html($this->formHidden('coupon_id', $data['coupon']->id)); ?>
                <?php write_html($this->formHidden('page', $this->params['p'] ? $this->params['p'] : 1)); ?>
                <?php write_html($this->formHidden('limit', $data['pageLimited'])); ?>
            <dt>クーポン名
              <span class="iconHelp">
                <span class="text">ヘルプ</span>
                <span class="textBalloon1">
                  <span>
                    クーポン名称として、一般公開されます
                  </span>
                <!-- /.textBalloon1 --></span>
              <!-- /.iconHelp --></span>
            </dt>
            <dd><?php write_html($this->formText('name', PHPParser::ACTION_FORM)) ?></dd>
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
            <dd><?php write_html($this->formTextArea('description', PHPParser::ACTION_FORM, array('cols'=>30, 'rows'=>2))) ?></dd>
            <dt>クーポン配布順序</dt>
            <dd><?php write_html($this->formRadio('distribution_type', PHPParser::ACTION_FORM, array(), Coupon::$distribution_type_label)) ?></dd>
            <dt>枚数</dt>
            <dd>
                <div class="couponCodeWrap">
                    <table class="couponCodeTable1">
                        <thead>
                        <tr>
                            <th>上限数</th>
                            <th>利用状況</th>
                            <th>クーポン種別数</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td><?php assign(number_format($data['coupon_limit'])) ?></td>
                            <td><?php assign($data['coupon_reserved']) ?></td>
                            <td><?php assign($data['coupon']->countReservedNum()) ?></td>
                        </tr>
                        </tbody>
                        <!-- /.couponCodeTable1 --></table>
                    <!-- /.couponCodeWrap --></div>
            </dd>
            <dt>コード</dt>
            <dd>
                <p><span class="btn3"><a href="#modal2" class="small1 jsOpenModal">コード追加</a></span></p>
                <?php if ($data['coupon_codes'] && $data['coupon_codes']->total() > 0): ?>
                <div class="couponCodeWrap">
                    <table class="couponCodeTable1">
                        <thead>
                        <tr>
                            <th>コード</th>
                            <th>期限</th>
                            <th class="upperlimitCode">上限数</th>
                            <th class="distributeCode">配布数</th>
                            <th class="addUpperlimit">上限を増やす</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($data['coupon_codes'] as $coupon_code): ?>
                            <tr>
                                <td><?php assign($coupon_code->code) ?></td>
                                <td>
                                    <?php write_html($this->formText('expire_date/'.$coupon_code->id, PHPParser::ACTION_FORM, array('class'=>"jsDate inputDate"))) ?>
                                    <label>
                                    <?php write_html($this->formCheckbox2('non_expire_date/'.$coupon_code->id, PHPParser::ACTION_FORM, array('class'=>'non_expire_date'), array('1' => 'なし'))) ?>
                                    </label>
                                    <?php if ( $this->ActionError && !$this->ActionError->isValid('expire_date/'.$coupon_code->id)): ?>
                                        <p class="attention1"><?php assign ( $this->ActionError->getMessage('expire_date/'.$coupon_code->id) )?></p>
                                    <?php endif; ?>
                                <td class="upperlimitCode"><?php assign(number_format($coupon_code->max_num)) ?></td>
                                <td class="distributeCode"><?php assign($coupon_action_manager->getCouponCodeReservedNum($coupon_code->id)) ?></td>
                                <td class="addUpperlimit">
                                    <?php write_html($this->formText('max_num_plus/'.$coupon_code->id, PHPParser::ACTION_FORM, array('class'=>'inputNum', 'placeholder'=>'1'))) ?>
                                    <?php if ( $this->ActionError && !$this->ActionError->isValid('max_num_plus/'.$coupon_code->id)): ?>
                                        <p class="attention1"><?php assign ( $this->ActionError->getMessage('max_num_plus/'.$coupon_code->id) )?></p>
                                    <?php endif; ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                        <!-- /.couponCodeTable1 --></table>
                    <!-- /.couponCodeWrap --></div>
                    <?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoModalPager')->render(array(
                        'TotalCount' => $data['totalCount'],
                        'CurrentPage' => $this->params['p'],
                        'Count' => $data['pageLimited'],
                    ))) ?>
                    <?php endif; ?>
                    </dd>
                </form>
                <!-- /.couponDetail1 --></dl>
                <p class="btnSet"><span class="btn3"><a href="javascript: void(0);" onclick="document.updateCouponCodeList.submit(); return false;">更新</a></span></p>
                <?php if ($data['can_delete']): ?>
                    <p class="couponDelete"><span class="btn4"><a href="#modal1" class="jsOpenModal">クーポンを削除</a></span></p>
                <?php endif; ?>
                <ul class="pager2">
                    <li class="prev"><a href="<?php write_html(Util::rewriteUrl('admin-coupon', 'coupon_codes', array($data['coupon']->id))) ?>" class="iconPrev1">コード一覧へ</a></li>
                    <!-- /.pager2 --></ul>
    </section>
</article>
    <?php if ($data['can_delete']): ?>
    <div class="modal1 jsModal" id="modal1">
        <section class="modalCont-small jsModalCont">
            <h1>本当に削除しますか？</h1>
            <p>
                このクーポンを削除しますか？
            </p>
            <p class="btnSet">
                <span class="btn2"><a href="#closeModal" class="small1">キャンセル</a></span>
                <span class="btn4"><a href="javascript:void(0)" class="small1" id="delete_coupon" data-coupon-id="<?php assign($data['coupon']->id) ?>">削除</a></span>
            </p>
        </section>
        <!-- /.modal1 --></div>
    <?php endif; ?>

    <div class="modal1 jsModal" id="modal2">
        <section class="modalCont-medium jsModalCont">
            <h1>コード追加</h1>
            <div class="modalAdminCoupon">
                <h2 class="hd2">クーポンコード</h2>
                <?php if ( $this->ActionError && !$this->ActionError->isValid('coupon_codes')): ?>
                    <p class="attention1"><?php assign ( $this->ActionError->getMessage('coupon_codes') )?></p>
                <?php endif; ?>
                <form id="createCouponCodeForm" name="createCouponCodeForm" action="<?php  assign(Util::rewriteUrl( 'admin-coupon', 'save_coupon_codes' )); ?>" method="POST" enctype="multipart/form-data">
                    <?php write_html($this->csrf_tag()); ?>
                    <?php write_html($this->formHidden('coupon_id', $data['coupon']->id)) ?>
                    <p><?php write_html($this->formTextArea('coupon_codes', PHPParser::ACTION_FORM, array('cols'=>30, 'rows'=>10, 'placeholder' => 'クーポンコード, 枚数, 期限日(YYYY/MM/DD)'))); ?>
                    <h2 class="hd2">クーポンコードインポート</h2>
                    <?php if ( $this->ActionError && !$this->ActionError->isValid('csv_coupon_codes')): ?>
                        <p class="attention1"><?php assign ( $this->ActionError->getMessage('csv_coupon_codes') )?></p>
                    <?php endif; ?>
                    <p><input type="file" name="csv_file">
                        <?php if ( $this->ActionError && !$this->ActionError->isValid('csv_file')): ?>
                            <p class="attention1"><?php assign ( $this->ActionError->getMessage('csv_file') )?></p>
                        <?php endif; ?>
                    <br><small>※CSVファイル（SJIS形式）で作成してください。（<a href="<?php assign($this->setVersion('/csv/sample.csv'))?>">CSVファイルサンプル</a>）</small>
                    <br><small>※１行目のヘッダーは「クーポンコード,枚数,期限日」に設定してください。１行目は読み込まれません。</small>
                    </p>
                </form>
                <h2 class="hd2">注意点</h2>
                <ul class="listUnordered1">
                    <li>各項目をカンマ(,)で区切ってください。</li>
                    <li>１行で１クーポンになります。空行は無視されます。</li>
                    <li>クーポンコードの長さは最大255文字です。</li>
                    <li>クーポンコードは入力必須です。</li>
                    <li>枚数の上限は「99,999,999」です。入力しない場合は「1」になります。</li>
                    <li>日付の形式は「2015/01/01」です。入力しない場合は「期限なし」になります。過去の日付は入力できません。</li>
                </ul>
                <!-- /.modalAdminCoupon --></div>
            <p class="btnSet">
                <span class="btn2"><a href="#closeModal">キャンセル</a></span>
                <span class="btn3"><a href="javascript: void(0);" onclick="document.createCouponCodeForm.submit(); return false;">追加</a></span>
            </p>
        </section>
        <!-- /.modal1 --></div>

<link rel="stylesheet" href="<?php assign($this->setVersion('/css/jqueryUI.css'))?>">
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/i18n/jquery.ui.datepicker-ja.min.js"></script>

<?php write_html($this->parseTemplate('BrandcoFooter.php', array_merge($data['pageStatus'], array('script' => array('admin-coupon/CouponCodeService'))))); ?>