<?php write_html($this->parseTemplate('BrandcoManagerHeader.php', array(
    'title' => 'お知らせ編集',
    'managerAccount' => $this->managerAccount,
))) ?>

    <div class="container-fluid">
        <div class="row">
            <?php write_html($this->parseTemplate('BrandcoManagerMenu.php')); ?>

            <form id="edit_brand_notification" name="edit_kpi_groups" action="<?php assign(Util::rewriteUrl( 'dashboard', 'edit_notification_details',array($this->notification_id), array(), '', true)); ?>" method="POST">
                <?php write_html($this->formHidden('id', $data['id']->id))?>
                <?php write_html($this->csrf_tag()); ?>
                <div class="col-md-10 col-md-offset-2 main">

                    <ol class="breadcrumb">
                        <li><a href="<?php assign(Util::rewriteUrl('dashboard', 'brand_notification_list', array(), array(), '', true)); ?>">お知らせ一覧</a></li>
                        <li><a href="<?php assign(Util::rewriteUrl('dashboard', 'brand_notification_details', array($data['notification_id']), array(), '', true))?>"><?php assign($data['ActionForm']['subject']);?></a></li>
                        <li class="active">お知らせ編集</a></li>
                    </ol>

                    <h1 class="page-header">お知らせ編集</h1>
                    <?php if ( $this->mode == BrandNotificationService::ADD_FINISH ): ?>
                        <div class="alert alert-success">
                            更新しました。
                        </div>
                    <?php elseif ($this->mode == BrandNotificationService::ADD_ERROR ): ?>
                        <div class="alert alert-danger">
                            入力内容に誤りがあります。確認して下さい。
                        </div>
                    <?php endif; ?>
                    <div class="col-md-10 col-md-offset-0">
                        <p class="pagePreview">
                            <span class="btn2"><a href="javascript:void(0)" id="previewButton1" class="small1">プレビュー</a></span>
                        </p>
                        <p>件名:</p>
                            <div class="form-group">
                                <?php write_html( $this->formText(
                                    'subject',
                                    PHPParser::ACTION_FORM,
                                    array('class' =>'form-control', 'maxlength'=>'255', 'placeholder'=> 'subject*')
                                )); ?>
                                <?php if ( $this->ActionError && !$this->ActionError->isValid('subject')): ?>
                                    <p class="attention1"><?php assign ( $this->ActionError->getMessage('subject') )?></p>
                                <?php endif; ?>
                            </div>
                        <p>本文:</p>
                            <div class="pageContEdit">
                                <?php write_html($this->formTextarea('contents', PHPParser::ACTION_FORM, array('cols' => '40', 'rows' => '4', 'width' => '400', 'height' => '900'))); ?>
                                <?php if ($this->ActionError && !$this->ActionError->isValid('contents')): ?>
                                    <p class="attention1"><?php assign($this->ActionError->getMessage('contents')) ?></p>
                                <?php endif; ?>
                                <div name="display" id="display"
                                    data-uploadurl='<?php assign(Util::rewriteUrl('dashboard', 'manager_ckeditor_upload_file', array(), array(), '', true)) ?>'
                                    data-listurl='<?php assign(Util::rewriteUrl('dashboard', 'manager_list_file_upload', array(), array(), '', true)) ?>'>
                                </div>
                            <!-- /.pageContEdit --></div>
                        <div class="form-group">
                            <p>公開状況:</p>
                                <label>
                                    <?php write_html($this->formSelect(
                                        'conditions',
                                        PHPParser::ACTION_FORM,
                                        array(),$this->conditions)); ?>
                                </label>
                        <div class="form-group">
                            <p>メッセージの種類:</p>
                                <label>
                                <?php write_html($this->formSelect(
                                    'message_type',
                                    PHPParser::ACTION_FORM,
                                    array(),$this->message_type)); ?>
                                </label>
                            <p>公開日:</p>
                                <?php write_html($this->formText(
                                    'publish_at',
                                    PHPParser::ACTION_FORM,
                                    array('maxlength' => '10', 'class' => 'jsDate inputDate', 'placeholder' => '年/月/日'))); ?>
                                <?php if ( $this->ActionError && !$this->ActionError->isValid('publish_at')): ?>
                                    <p class="attention1"><?php assign ( $this->ActionError->getMessage('publish_at') )?></p>
                                <?php endif; ?>
                        </div>
                        <a href="javascript:void(0)" onclick="document.edit_brand_notification.submit();return false;"><button class="btn btn-primary btn-large registrator">更新</button>
                    </div>
                </div>
            </form>

        </div><!-- row -->
    </div><!-- container-fluid -->

    <script type="text/javascript" src="<?php assign($this->setVersion('/manager/js/services/EditNotificationService.js')) ?>"></script>
    <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/i18n/jquery.ui.datepicker-ja.min.js"></script>
    <script type="text/javascript" src="<?php assign($this->setVersion('/ckeditor/ckeditor.js')) ?>"></script>
<?php write_html($this->parseTemplate('BrandcoManagerFooter.php')); ?>