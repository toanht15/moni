<?php write_html($this->parseTemplate('BrandcoManagerHeader.php', array(
    'title' => '代理店一覧',
    'managerAccount' => $this->managerAccount,
))) ?>

<div class="container-fluid">
    <div class="row">
        <?php write_html($this->parseTemplate('BrandcoManagerMenu.php')); ?>
        <div class="col-md-10 col-md-offset-2 main">
            <h1 class="page-header">代理店一覧</h1>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>NO</th>
                        <th>名前</th>
                        <th>メールアドレス</th>
                        <th>詳細</th>

                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($this->agent_list as $agent): ?>
                        <tr>
                            <td>
                                <?php assign($agent['id']) ?>
                            </td>
                            <td><?php assign($agent['name']) ?></td>
                            <td><?php assign($agent['mail_address']) ?></td>
                            <td>
                                <a href="<?php assign(Util::rewriteUrl('dashboard', 'edit_agent_form', array($agent['id']), array(), '', true)); ?>">管理権限の追加</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoManagerPager')->render(array(
                    'TotalCount' => $data['total_count'],
                    'CurrentPage' => $this->params['p'],
                    'Count' => $data['limit'],
                ))) ?>
            </div>
        </div>
    </div><!-- row -->
</div><!-- container-fluid -->
<?php write_html($this->parseTemplate('BrandcoManagerFooter.php')); ?>
