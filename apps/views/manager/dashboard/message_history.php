<?php write_html($this->parseTemplate('BrandcoManagerHeader.php', array(
    'title' => 'メッセージ送信履歴',
    'managerAccount' => $this->managerAccount,
))) ?>

    <div class="container-fluid">
        <div class="row">
            <?php write_html($this->parseTemplate('BrandcoManagerMenu.php')); ?>
            <form name="frmMessageHistorySearch" id="frmMessageHistorySearch" action="" method="GET" class="form-horizontal row-border">
                <div class="col-md-10 col-md-offset-2 main">
                    <h1 class="page-header">メッセージ送信履歴</h1>
                    <div class="col-md-0 col-md-offset-0">
                        <?php write_html($this->formCheckBox(
                            'test_page', PHPParser::ACTION_FORM,
                            array('checked' => $this->test_page),array('1' => 'テストブランドを含める'))); ?>
                        <div class="col-md-offset-0">
                            メッセージID :
                            <?php write_html( $this->formText(
                                'message_id',PHPParser::ACTION_FORM,
                                array('maxlength'=>'20')
                            )); ?>

                            アカウントID :
                            <?php write_html( $this->formText(
                                'client_id',PHPParser::ACTION_FORM,
                                array('maxlength'=>'20')
                            )); ?>

                            送信日 :
                            <?php write_html($this->formText(
                                'from_date',
                                PHPParser::ACTION_FORM,
                                array('maxlength' => '20', 'class' => 'jsDate inputDate'))); ?>
                            ～
                            <?php write_html($this->formText(
                                'to_date',
                                PHPParser::ACTION_FORM,
                                array('maxlength' => '20', 'class' => 'jsDate inputDate'))); ?>
                        </div>
                    </div>

                </div>
                <div class="col-md-0 col-md-offset-6">
                    <a href="javascript:void(0)" class="submitButton" data-action="<?php assign(Util::rewriteUrl( 'dashboard', 'message_history', array(), array(), '', true )); ?>"><button class="btn btn-primary btn-large">　検索　</button></a>
                </div>
            </form>
        </div><!-- row -->
    </div><!-- container-fluid -->

    <div class="col-md-10 col-md-offset-2 main">
        <h4>検索結果</h4>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                <tr>
                    <?php foreach($this->header as $title_name):?>
                        <th><?php assign($title_name)?></th>
                    <?php endforeach; ?>
                </tr>
                </thead>
                <tbody>
                <?php if ($this->message_history == null):?>
                    <tr>
                        <td><?php assign('データがありません')?></td>
                    </tr>
                <?php else: ?>
                    <?php foreach($this->message_history as $message):?>
                        <tr>
                            <td><?php assign($message['message_id'])?></td>
                            <td><?php assign($message['message_type'])?></td>
                            <td><?php assign($message['type'])?></td>
                            <td><?php assign($message['account_name'])?></td>
                            <td><?php assign($message['brand_id'])?></td>
                            <td><?php assign($message['delivery_date'])?></td>
                            <td><?php assign($message['delivery_sum'])?></td>
                            <td><?php assign($message['open_sum'])?></td>
                            <td><?php assign($message['brandco_access_rate'])?></td>
                            <td><?php assign($message['message_read_sum'])?></td>
                            <td><?php assign($message['message_been_read_sum'])?></td>
                            <td><?php assign($message['message_read_rate'])?></td>
                            <td><?php assign($message['message_delivery_rate'])?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="col-md-10 col-md-offset-2 main">
        <?php if($this->message_limit):?>
            <h4 style="color: #d9534f; font-weight: bold"><?php assign($this->message_limit)?></h4>
        <?php endif; ?>
    </div>

    <div class="col-md-10 col-md-offset-2 main">
        <a href="javascript:void(0)" class="submitButton" data-action="<?php assign(Util::rewriteUrl( 'dashboard', 'csv_message_history', array(), array(), '', true )); ?>"><button class="btn btn-primary btn-large">CSVダウンロード</button></a>
    </div>

<script src="<?php assign($this->setVersion('/manager/js/services/MessageHistoryService.js'))?>"></script>
<?php write_html($this->parseTemplate('BrandcoManagerFooter.php')); ?>