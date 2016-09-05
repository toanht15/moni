<?php write_html($this->parseTemplate('BrandcoManagerHeader.php', array(
    'title' => 'KPIグループ',
    'managerAccount' => $this->managerAccount,
))) ?>

    <div class="container-fluid">
        <div class="row">
            <?php write_html($this->parseTemplate('BrandcoManagerMenu.php')); ?>

            <div class="col-md-10 col-md-offset-2 main">

                <h1 class="sub-header">KPIグループ</h1>
                <?php if ( $this->params["mode"] == ManagerKpiGroupService::ADD_FINISH ): ?>
                    <div class="alert alert-success">
                        登録が完了しました。
                    </div>
                <?php endif; ?>

                <div class="table-responsive">
                    <ul class="nav nav-pills">
                        <li class="enable"><a href="<?php assign(Util::rewriteUrl('dashboard', 'add_kpi_group_form', array(), array(), '', true)); ?>">KPIグループ追加</a></li>
                    </ul>
                    <h3>グループ</h3>
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>グループ名</th>
                            <th>KPI数</th>
                            <th>作成日</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if(count($this->kpi_groups) > 0):?>
                            <?php foreach($this->kpi_groups as $kpi_group):?>
                                <tr>
                                    <td>
                                        <a href="<?php assign(Util::rewriteUrl('dashboard', 'customise_kpi_groups', array($kpi_group->id), array(), '', true))?>">
                                            <?php assign($kpi_group->name);?></a>
                                    </td>
                                    <?php if($kpi_group->getColumnCount() > 0):?>
                                        <td><?php assign($kpi_group->getColumnCount()) ?></td>
                                    <?php else :?>
                                        <td>-</td>
                                    <?php endif?>
                                    <td><?php assign($kpi_group->created_at);?></td>
                                </tr>
                            <?php endforeach;?>
                        <?php else :?>
                            <tr><td>データがありません</td></tr>
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

    <style type="text/css">
        .autoSave {
            border: 0;
            width: 100%;
            background-color: transparent;
        }
    </style>
    <script type="text/javascript" src="<?php assign($this->setVersion('/js/brandco/services/ManagerKpiService.js'))?>"></script>


<?php write_html($this->parseTemplate('BrandcoManagerFooter.php')); ?>