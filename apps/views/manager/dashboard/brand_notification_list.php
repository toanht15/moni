<?php write_html($this->parseTemplate('BrandcoManagerHeader.php', array(
    'title' => 'お知らせ編集',
    'managerAccount' => $this->managerAccount,
))) ?>

    <div class="container-fluid">
        <div class="row">
            <?php write_html($this->parseTemplate('BrandcoManagerMenu.php')); ?>

            <div class="col-md-10 col-md-offset-2 main">
                <h1 class="sub-header">お知らせ編集</h1>
                <div class="table-responsive">
                    <ul class="nav nav-pills">
                        <li class="enable"><a href="<?php assign(Util::rewriteUrl('dashboard', 'add_brand_notification_form', array(), array(), '', true)); ?>">お知らせ追加</a></li>
                    </ul>
                    <h3>お知らせ一覧</h3>
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>登録日</th>
                            <th>公開日(予定)</th>
                            <th>件名</th>
                            <th>メッセージの種類</th>
                            <th>公開状況</th>
                            <th>作者</th>
                        </tr>
                        </thead>
                        <tbody>
                            <?php if(count($this->notifications) > 0):?>
                                <?php foreach($this->notifications as $brand_notification):?>
                                    <tr>
                                        <td><?php assign($brand_notification['created_at']);?></td>
                                        <td><?php assign($brand_notification['publish_at']) ?></td>
                                        <td><a href="<?php assign(Util::rewriteUrl( 'dashboard', 'brand_notification_details', array($brand_notification['id']), array(), '', true)); ?>"><?php assign($brand_notification['subject']) ?></a></td>
                                        <td><?php assign($brand_notification['icon_information']['message_type'])?></td>
                                        <td><?php assign($brand_notification['conditions'])?></td>
                                        <td><?php assign($brand_notification['author']);?></td>
                                    </tr>
                                <?php endforeach;?>
                            <?php else :?>
                                    <tr>
                                        <td>データがありません</td>
                                    </tr>
                            <?php endif?>
                        </tbody>
                    </table>

                    <?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoManagerPager')->render(array(
                        'TotalCount' => $data['totalEntriesCount'],
                        'CurrentPage' => $this->params['p'],
                        'Count' => $data['pageLimited'],
                    ))) ?>
                </div>
            </div>

        </div><!-- row -->
    </div><!-- container-fluid -->

<?php write_html($this->csrf_tag()); ?>

<?php write_html($this->parseTemplate('BrandcoManagerFooter.php')); ?>