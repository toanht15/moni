<?php write_html($this->parseTemplate('BrandcoManagerHeader.php', array(
    'title' => 'ブランドKPI',
    'managerAccount' => $this->managerAccount,
))) ?>

<div class="container-fluid">
    <div class="row">
        <?php write_html($this->parseTemplate('BrandcoManagerMenu.php')); ?>

        <div class="col-md-10 col-md-offset-2 main">
            <ol class="breadcrumb">
                <li><a href="<?php assign(Util::rewriteUrl('dashboard', 'brand_list', array(), array(), '', true)); ?>">ブランド一覧</a></li>
                <li class="active"><?php assign($this->brand_name);?></li>
            </ol>
            <h1 class="sub-header">ブランドKPI-<?php assign($this->brand_name);?></h1>
            <h2><?php assign($this->brand_name);?></h2>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>Date</th>
                        <?php foreach($this->columns as $column):?>
                            <th><?php assign($column->name);?></th>
                        <?php endforeach;?>
                    </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="color: #d9534f; font-weight: bold">Guinness</td>
                            <?php foreach($this->records as $record):?>
                                <td style="color: #d9534f; font-weight: bold"><?php assign(number_format($record));?></td>
                            <?php endforeach;?>
                        </tr>
                    <?php foreach($this->dates as $date):?>
                        <tr>
                            <td><?php assign($date->summed_date);?></td>
                            <?php foreach($this->columns as $column):?>
                                <?php $value = $column->getValue($date->summed_date, $data['brandId']);?>
                                <?php if($column->import): // 自動計算?>
                                    <?php if($value == number_format($this->records[$column->id])):?>
                                        <td style="color: #d9534f; font-weight: bold"><?php assign($value);?></td>
                                    <?php else:?>
                                        <td><?php assign($value);?></td>
                                    <?php endif;?>
                                <?php else: // 手入力?>
                                    <td>
                                        <input type="number" class="wid120 autoSave"
                                               onkeyDown="return ManagerKpiService.numOnly()"
                                               name='<?php assign($column->id);?>__<?php assign($date->summed_date);?>'
                                               value='<?php assign($value);?>'>
                                    </td>
                                <?php endif;?>
                            <?php endforeach;?>
                        </tr>
                    <?php endforeach;?>
                    </tbody>
                </table>
                <?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoManagerPager')->render(array(
                    'TotalCount' => $data['totalEntriesCount'],
                    'CurrentPage' => $this->params['p'],
                    'Count' => $data['limit'],
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