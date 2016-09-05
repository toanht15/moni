<?php write_html($this->parseTemplate('BrandcoManagerHeader.php', array(
    'title' => 'お知らせ追加',
    'managerAccount' => $this->managerAccount,
))) ?>

    <div class="container-fluid">
        <div class="row">
            <?php write_html($this->parseTemplate('BrandcoManagerMenu.php')); ?>

            <form id="frmAddNotification" name="add_brand_notification" action="<?php assign(Util::rewriteUrl('dashboard', 'add_brand_notifications', array(), array(), '', true)); ?>" method="POST">
                <?php write_html($this->formHidden('author',$this->managerAccount->name))?>
                <?php write_html($this->csrf_tag()); ?>
                <div class="col-md-10 col-md-offset-2 main">

                    <ol class="breadcrumb">
                        <li><a href="<?php assign(Util::rewriteUrl('dashboard', 'brand_notification_list', array(), array(), '', true)); ?>">お知らせ一覧</a></li>
                        <li class="active">お知らせ追加</a></li>
                    </ol>

                    <h1 class="page-header">お知らせ追加</h1>
                    <?php if ($this->mode == BrandNotificationService::ADD_FINISH ): ?>
                        <div class="alert alert-success">
                            登録が完了しました。
                        </div>
                    <?php elseif ($this->mode == BrandNotificationService::ADD_ERROR ): ?>
                        <div class="alert alert-danger">
                            入力内容に誤りがあります。確認して下さい。
                        </div>
                    <?php endif; ?>
                    <p class="pagePreview">
                        <span class="btn2"><a href="javascript:void(0)" id="previewButton" class="small1">プレビュー</a></span>
                    </p>
                    <div class="col-md-11 col-md-offset-0">
                        <div class="form-group">
                            <p>件名:</p>
                            <?php write_html( $this->formText(
                                'subject',
                                PHPParser::ACTION_FORM,
                                array('class' =>'form-control', 'maxlength'=>'255', 'placeholder'=> '件名を入力してください*')
                            )); ?>
                            <?php if ($this->ActionError && !$this->ActionError->isValid('subject')): ?>
                                <p class="attention1"><?php assign ( $this->ActionError->getMessage('subject') )?></p>
                            <?php endif; ?>
                            <p>本文:</p>
                            <div class="pageContEdit">
                                <?php write_html($this->formTextarea('contents', PHPParser::ACTION_FORM, array('cols' => '40', 'rows' => '4', 'width' => '960', 'height' => '580'))); ?>
                                <?php if ($this->ActionError && !$this->ActionError->isValid('contents')): ?>
                                    <p class="attention1"><?php assign($this->ActionError->getMessage('contents')) ?></p>
                                <?php endif; ?>
                                <!-- /.pageContEdit --></div>
                            <div name="display" id="display"
                                 data-uploadurl='<?php assign(Util::rewriteUrl('dashboard', 'manager_ckeditor_upload_file', array(), array(), '', true)) ?>'
                                 data-listurl='<?php assign(Util::rewriteUrl('dashboard', 'manager_list_file_upload', array(), array(), '', true)) ?>'>
                            </div>
                        </div>
                        <div class="form-group">
                            <p>メッセージの種類:</p>
                            <label>
                                <?php write_html($this->formSelect( 'test_page', PHPParser::ACTION_FORM, array(),$this->message_type)); ?>
                            </label>
                            <p>公開状況</p>
                            <label>
                                <?php write_html($this->formSelect( 'conditions', PHPParser::ACTION_FORM, array(),$this->conditions)); ?>
                            </label>
                            <p>公開日:</p>
                            <?php write_html($this->formText(
                                'public_date',
                                PHPParser::ACTION_FORM,
                                array('maxlength' => '10', 'class' => 'jsDate inputDate', 'placeholder' => '年-月-日'))); ?>
                            <?php if ( $this->ActionError && !$this->ActionError->isValid('public_date')): ?>
                                <p class="attention1"><?php assign ( $this->ActionError->getMessage('public_date') )?></p>
                            <?php endif; ?>
                        </div>

                        <a href="" onclick="document.frmAddNotification.submit();return false;"><button class="btn btn-primary btn-large registrator">　追加　</button></a>
                    </div>
                </div>
            </form>
        </div><!-- row -->
    </div><!-- container-fluid -->
    <script type="text/javascript" src="<?php assign($this->setVersion('/manager/js/services/AddNotificationService.js')) ?>"></script>
    <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/i18n/jquery.ui.datepicker-ja.min.js"></script>
    <script type="text/javascript" src="<?php assign($this->setVersion('/ckeditor/ckeditor.js')) ?>"></script>

<?php write_html($this->parseTemplate('BrandcoManagerFooter.php')); ?>