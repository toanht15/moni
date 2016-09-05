<?php write_html($this->parseTemplate('BrandcoManagerHeader.php', array(
    'title' => 'お知らせ詳細',
    'managerAccount' => $this->managerAccount,
))) ?>

    <div class="container-fluid">
        <div class="row">
            <?php write_html($this->parseTemplate('BrandcoManagerMenu.php')); ?>
            <?php write_html($this->csrf_tag()); ?>
            <?php write_html($this->formHidden('id', $data['id']))?>
            <div class="col-md-10 col-md-offset-2 main">

                <ol class="breadcrumb">
                    <li><a href="<?php assign(Util::rewriteUrl('dashboard', 'brand_notification_list', array(), array(), '', true)); ?>">お知らせ一覧</a></li>
                    <li class="active">お知らせ詳細</a></li>
                </ol>

                <h1 class="page-header">お知らせ詳細</h1>
                <?php if ( $this->mode == BrandNotificationService::ADD_FINISH ): ?>
                    <div class="alert alert-success">
                        更新しました。
                    </div>
                <?php elseif ($this->mode == BrandNotificationService::ADD_ERROR ): ?>
                    <div class="alert alert-danger">
                        入力内容に誤りがあります。確認して下さい。
                    </div>
                <?php endif; ?>

                <div class="col-md-11 col-md-offset-0">
                    <section class="infomationDetail">
                        <p>件名:</p>
                            <div class="well well-lg"><?php assign($this->brand_notification_info->subject);?></div>
                        <p>本文:</p>
                            <div class="well well-lg"><?php write_html($this->brand_notification_info->contents);?></div>
                            <p>公開状況</p>
                            <div class="well well-lg"><?php write_html($this->conditions);?></div>
                            <p>メッセージの種類:</p>
                            <div class="well well-lg">
                            <img src="<?php assign($this->setVersion($this->notification_icon_info['icon']))?>" width="30" height="30" alt="attention">
                                <?php write_html($this->notification_icon_info['message_type']);?></div>
                            <p>公開日:</p>
                            <div class="well well-lg"><?php assign($this->brand_notification_info->publish_at);?></div>
                    </section>
                    <div class="container">
                        <form action="<?php assign(Util::rewriteUrl('dashboard', 'edit_notification_detail_form', array($this->notification_id), array(), '', true)); ?>">
                            <button class="btn btn-primary btn-large">　編集　</button></a>
                            <a href="javascript:void(0)">
                                <button class="btn btn-primary btn-large registrator" data-message="本当に削除しますか？">　削除　</button>
                            </a>
                        </form>
                        <form action="<?php assign(Util::rewriteUrl('dashboard', 'delete_brand_notification_details', array(), array(), '', true)); ?>" method="POST" id="brand_notification_form">
                            <?php write_html($this->formHidden('notification_id',$this->notification_id))?>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-10 col-md-offset-2">
                <h1 class="sub-header">既読管理</h1>
                    <div class="table-responsive">
                    <h3>お知らせ一覧</h3>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Brand Name</th>
                                <th>Admin</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($this->brandMessageReadmark as $brand):?>
                                <tr>
                                    <td>
                                        <?php if($brand['name'] == null): ?>
                                            -
                                        <?php else:?>
                                            <?php assign($brand['name']);?>
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <?php if($brand['admin_user_name'] == null): ?>
                                            -
                                        <?php else:?>
                                            <?php assign($brand['admin_user_name']);?>
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <?php if($brand['readMark'] == null): ?>
                                            未読
                                        <?php else:?>
                                            既読
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach;?>
                        </tbody>
                    </table>

                    <?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoManagerPager')->render(array(
                        'TotalCount' => $data['allManagerCount'],
                        'CurrentPage' => $this->params['p'],
                        'Count' => $data['pageLimited'],
                    ))) ?>
                </div>
            </div>
        </div><!-- row -->
    </div><!-- container-fluid -->
    <script>
        $('.registrator').click(function(event) {
            event.preventDefault();
            if (confirm($(this).data('message'))) {
                $('#brand_notification_form').submit();
            }
        });
    </script>
    <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
    <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/i18n/jquery.ui.datepicker-ja.min.js"></script>
    <script type="text/javascript" src="<?php assign($this->setVersion('/ckeditor/ckeditor.js')) ?>"></script>

<?php write_html($this->parseTemplate('BrandcoManagerFooter.php')); ?>
