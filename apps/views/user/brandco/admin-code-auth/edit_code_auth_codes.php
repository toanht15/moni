<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoHeader')->render($data['pageStatus'])) ?>
<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoAccountHeader')->render($data['pageStatus'])) ?>
<?php
$service_factory = new aafwServiceFactory();
$code_auth_action_manager = $service_factory->create('CpCodeAuthActionManager');
?>

<article>
    <h1 class="hd1">認証コード設定</h1>
    <section class="couponWrap">
        <dl class="couponDetail1">
            <form name="updateCodeAuthCodeList" action="<?php  assign(Util::rewriteUrl( 'admin-code-auth', 'update_code_auth' )); ?>" method="POST">
                <?php write_html($this->csrf_tag()); ?>
                <?php write_html($this->formHidden('code_auth_id', $data['code_auth']->id)); ?>
                <?php write_html($this->formHidden('page', $this->params['p'] ? $this->params['p'] : 1)); ?>
                <?php write_html($this->formHidden('limit', $data['page_limited'])); ?>
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
            <dd><?php write_html($this->formText('name', PHPParser::ACTION_FORM)) ?></dd>
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
            <dd><?php write_html($this->formTextArea('description', PHPParser::ACTION_FORM, array('cols' => 30, 'rows' => 2))) ?></dd>
            <dt></dt>
            <dd>
                <div class="couponCodeWrap">
                    <table class="couponCodeTable1">
                        <thead>
                        <tr>
                            <th>上限数</th>
                            <th>認証済み合計</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td><?php assign(number_format($data['code_auth_limit'])) ?></td>
                            <td><?php assign(number_format($data['code_auth_reserved'])) ?></td>
                        </tr>
                        </tbody>
                        <!-- /.couponCodeTable1 --></table>
                    <!-- /.couponCodeWrap --></div>
            </dd>
            <dt>コード</dt>
            <dd>
                <p><span class="btn3"><a href="#modal2" class="small1 jsOpenModal">コード追加</a></span></p>
                <?php if ($data['code_auth_codes'] && $data['code_auth_codes']->total() > 0): ?>
                <div class="couponCodeWrap">
                    <table class="couponCodeTable1">
                        <thead>
                        <tr>
                            <th>コード</th>
                            <th>期限</th>
                            <th class="upperlimitCode">上限数</th>
                            <th class="distributeCode">認証済みコード数</th>
                            <th class="addUpperlimit">上限を増やす</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($data['code_auth_codes'] as $code_auth_code): ?>
                            <tr>
                                <td><?php assign($code_auth_code->code) ?></td>
                                <td>
                                    <?php write_html($this->formText('expire_date/' . $code_auth_code->id, PHPParser::ACTION_FORM, array('class'=>"jsDate inputDate"))) ?>
                                    <label>
                                    <?php write_html($this->formCheckbox2('non_expire_date/' . $code_auth_code->id, PHPParser::ACTION_FORM, array('class'=>'non_expire_date'), array('1' => 'なし'))) ?>
                                    </label>
                                    <?php if ( $this->ActionError && !$this->ActionError->isValid('expire_date/' . $code_auth_code->id)): ?>
                                        <p class="attention1"><?php assign ( $this->ActionError->getMessage('expire_date/' . $code_auth_code->id) )?></p>
                                    <?php endif; ?>
                                <td class="upperlimitCode"><?php assign(number_format($code_auth_code->max_num)) ?></td>
                                <td class="distributeCode"><?php assign($code_auth_action_manager->countCodeAuthUsersByCodeAuthCodeId($code_auth_code->id)) ?></td>
                                <td class="addUpperlimit">
                                    <?php write_html($this->formText('max_num_plus/' . $code_auth_code->id, PHPParser::ACTION_FORM, array('class' => 'inputNum', 'placeholder' => '1'))) ?>
                                    <?php if ( $this->ActionError && !$this->ActionError->isValid('max_num_plus/' . $code_auth_code->id)): ?>
                                        <p class="attention1"><?php assign ( $this->ActionError->getMessage('max_num_plus/' . $code_auth_code->id) )?></p>
                                    <?php endif; ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                        <!-- /.couponCodeTable1 --></table>
                    <!-- /.couponCodeWrap --></div>
                    <?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoModalPager')->render(array(
                        'TotalCount' => $data['total_count'],
                        'CurrentPage' => $this->params['p'],
                        'Count' => $data['page_limited'],
                    ))) ?>
                    <?php endif; ?>
                    </dd>
                </form>
                <!-- /.couponDetail1 --></dl>
                <p class="btnSet"><span class="btn3"><a href="javascript: void(0);" onclick="document.updateCodeAuthCodeList.submit(); return false;">更新</a></span></p>
                <?php if ($data['can_delete']): ?>
                    <p class="couponDelete"><span class="btn4"><a href="#modal1" class="jsOpenModal">認証コードを削除</a></span></p>
                <?php endif; ?>
                <ul class="pager2">
                    <li class="prev"><a href="<?php write_html(Util::rewriteUrl('admin-code-auth', 'code_auth_codes', array($data['code_auth']->id))) ?>" class="iconPrev1">コード一覧へ</a></li>
                    <!-- /.pager2 --></ul>
    </section>
</article>
<?php if ($data['can_delete']): ?>
    <div class="modal1 jsModal" id="modal1">
        <section class="modalCont-small jsModalCont">
            <h1>本当に削除しますか？</h1>
            <p>
                この認証コードを削除しますか？
            </p>
            <p class="btnSet">
                <span class="btn2"><a href="#closeModal" class="small1">キャンセル</a></span>
                <span class="btn4"><a href="javascript:void(0)" class="small1" id="delete_code_auth" data-code_auth_id="<?php assign($data['code_auth']->id) ?>">削除</a></span>
            </p>
        </section>
        <!-- /.modal1 --></div>
<?php endif; ?>

    <div class="modal1 jsModal" id="modal2">
        <section class="modalCont-medium jsModalCont">
            <h1>認証コード追加</h1>
            <div class="modalAdminCoupon">
                <h2 class="hd2">認証コード</h2>
                <?php if ( $this->ActionError && !$this->ActionError->isValid('code_auth_codes')): ?>
                    <p class="attention1"><?php assign ( $this->ActionError->getMessage('code_auth_codes') )?></p>
                <?php endif; ?>
                <form name="createCodeAuthCodeForm" action="<?php  assign(Util::rewriteUrl( 'admin-code-auth', 'save_code_auth_codes' )); ?>" method="POST" enctype="multipart/form-data">
                    <?php write_html($this->csrf_tag()); ?>
                    <?php write_html($this->formHidden('code_auth_id', $data['code_auth']->id)) ?>
                    <p><?php write_html($this->formTextArea('code_auth_codes', PHPParser::ACTION_FORM, array('cols' => 30, 'rows' => 10, 'placeholder' => '認証コード, 使用回数, 期限日(YYYY/MM/DD)'))); ?>
                    <h2 class="hd2">認証コードインポート</h2>
                    <?php if ( $this->ActionError && !$this->ActionError->isValid('csv_code_auth_codes')): ?>
                        <p class="attention1"><?php assign ( $this->ActionError->getMessage('csv_code_auth_codes') )?></p>
                    <?php endif; ?>
                    <p><input type="file" name="csv_file">
                        <?php if ( $this->ActionError && !$this->ActionError->isValid('csv_file')): ?>
                            <p class="attention1"><?php assign ( $this->ActionError->getMessage('csv_file') )?></p>
                        <?php endif; ?>
                    <br><small>※CSVファイル（SJIS形式）で作成してください。（<a href="<?php assign($this->setVersion('/csv/sample.csv'))?>">CSVファイルサンプル</a>）</small>
                    <br><small>※１行目のヘッダーは「認証コード,使用回数,期限日」に設定してください。１行目は読み込まれません。</small>
                    </p>
                </form>
                <h2 class="hd2">注意点</h2>
                <ul class="listUnordered1">
                    <li>各項目をカンマ(,)で区切ってください。</li>
                    <li>１行で１認証コードになります。空行は無視されます。</li>
                    <li>認証コードの長さは最大255文字です。</li>
                    <li>認証コードは入力必須です。</li>
                    <li>使用回数の上限は「99,999,999」です。入力しない場合は「1」になります。</li>
                    <li>日付の形式は「2015/01/01」です。入力しない場合は「期限なし」になります。過去の日付は入力できません。</li>
                </ul>
                <!-- /.modalAdminCoupon --></div>
            <p class="btnSet">
                <span class="btn2"><a href="#closeModal">キャンセル</a></span>
                <span class="btn3"><a href="javascript: void(0);" onclick="document.createCodeAuthCodeForm.submit(); return false;">追加</a></span>
            </p>
        </section>
        <!-- /.modal1 --></div>

<link rel="stylesheet" href="<?php assign($this->setVersion('/css/jqueryUI.css'))?>">
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/i18n/jquery.ui.datepicker-ja.min.js"></script>

<?php write_html($this->parseTemplate('BrandcoFooter.php', array_merge($data['pageStatus'], array('script' => array('admin-code_auth/CodeAuthCodeService'))))); ?>