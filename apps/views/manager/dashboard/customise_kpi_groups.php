<?php write_html($this->parseTemplate('BrandcoManagerHeader.php', array(
    'title' => 'KPIグループ / ' . $data['kpi_group']->name,
    'managerAccount' => $this->managerAccount,
))) ?>

    <div class="container-fluid">
        <div class="row">
            <?php write_html($this->parseTemplate('BrandcoManagerMenu.php')); ?>

            <div class="col-md-10 col-md-offset-2 main">

                <ol class="breadcrumb">
                    <li><a href="<?php assign(Util::rewriteUrl('dashboard', 'list_kpi_groups', array(), array(), '', true)); ?>">KPIグループ</a></li>
                    <li class="active"><?php assign($data['kpi_group']->name);?></a></li>
                </ol>

                <h1 class="sub-header"><?php assign($data['kpi_group']->name);?></h1>
                <div class="table-responsive">
                    <ul class="nav nav-pills">
                        <li class="enable"><a href="<?php assign(Util::rewriteUrl('dashboard', 'edit_kpi_group_form', array($data['kpi_group']->id),array('p'=>$params['p']), '', true))?>">編集</a></li>
                    </ul>
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>Date</th>
                            <?php foreach($data['kpi_column_list'] as $group_column):?>
                                <th><?php assign($group_column->getManagerKpiColumns()->current()->name);?></th>
                            <?php endforeach;?>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if(count($data['kpi_column_list']) > 0):?>
                            <?php foreach($data['dates'] as $date):?>
                            <tr>
                                <td><?php assign($date->summed_date);?></td>
                                <?php foreach($data['kpi_column_list'] as $kpi_group_column):
                                    $kpi_column = $kpi_group_column->getManagerKpiColumns()->current();?>
                                    <td><?php assign($kpi_column->getFormattedValueByDate($date->summed_date, $kpi_column));?></td>
                                <?php endforeach;?>
                            </tr>
                            <?php endforeach;?>
                        <?php else :?>
                            <td>データコンテンツがありません</td>
                        <?php endif?>
                        </tbody>

                    </table>
                    <?php if(count($data['kpi_column_list']) > 0):?>
                        <?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoManagerPager')->render(array(
                            'TotalCount' => $data['totalEntriesCount'],
                            'CurrentPage' => $this->params['p'],
                            'Count' => $data['pageLimited'],
                        ))) ?>
                    <?php endif?>
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