<?php write_html($this->parseTemplate('BrandcoManagerHeader.php', array(
    'title' => 'KPI',
    'managerAccount' => $this->managerAccount,
))) ?>

<div class="container-fluid">
    <div class="row">
        <?php write_html($this->parseTemplate('BrandcoManagerMenu.php')); ?>

        <div class="col-md-10 col-md-offset-2 main">
            <h1 class="sub-header">KPI</h1>
            <form id="csv_campaign_list" action="<?php assign(Util::rewriteUrl('dashboard', 'csv_kpi', array(), array(), '', true)); ?>" method="GET">
                <h4>
                    <div style="text-align:left">
                        <?php write_html($this->formRadio( 'csv', PHPParser::ACTION_FORM, array('checked'=>"checked"),array('0' => '全てダウンロード'))); ?><br>
                        <?php write_html($this->formRadio( 'csv', PHPParser::ACTION_FORM, array(),array('1' => '日付指定'))); ?><br>
                        <?php write_html($this->formText(
                            'from_date',
                            PHPParser::ACTION_FORM,
                            array('maxlength' => '10', 'class' => 'jsDate inputDate', 'placeholder' => '年-月-日'))); ?>
                            ～
                            <?php write_html($this->formText(
                            'to_date',
                            PHPParser::ACTION_FORM,
                            array('maxlength' => '10', 'class' => 'jsDate inputDate', 'placeholder' => '年-月-日'))); ?><br>
                        <br>
                        <a href="javascript:void(0);" id="btn_csv_campaign_list" class="btn btn-primary btn-large">CSVダウンロード</a>&nbsp;&nbsp;<br>
                    </div>
                </h4>
            </form>

            <div class="table-responsive">

                <?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoManagerPager')->render(array(
                    'TotalCount' => $data['totalEntriesCount'],
                    'CurrentPage' => $this->params['p'],
                    'Count' => $data['limit'],
                ))) ?>

                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>Date</th>
                        <?php foreach($this->manager_kpi_columns as $manager_kpi_column):?>
                            <th><?php assign($manager_kpi_column->name);?></th>
                        <?php endforeach;?>
                    </tr>
                    </thead>
                    <tbody>
                        <tr class="danger">
                            <td style="color: #d9534f; font-weight: bold">Guinness</td>
                            <?php foreach($this->guinnesses as $guinness):?>
                                <td style="color: #d9534f; font-weight: bold"><?php assign($guinness);?></td>
                            <?php endforeach;?>
                        </tr>
                    <?php foreach($this->dates as $date):?>
                        <tr>
                            <td><?php assign($date->summed_date);?></td>
                            <?php foreach($this->manager_kpi_columns as $manager_kpi_column):?>
                                <?php $value = $manager_kpi_column->getFormattedValueByDate($date->summed_date, $manager_kpi_column,$manager_kpi_column, $manager_kpi_column);?>
                                <td <?php assign($value == $this->guinnesses[$manager_kpi_column->id] ? "style=color:#d9534f;font-weight:bold;" : '') ?>>
                                    <?php if ($value && $manager_kpi_column->import == ManagerKpiColumns::USER_PANEL_CLICK_NUM): ?>
                                        <a href="<?php write_html(Util::rewriteUrl('dashboard', 'panel_clicks', array(), array('date' => $date->summed_date, 'search_mode' => 'date'), '', true)); ?>"><?php assign($value);?></a>
                                    <?php else: ?>
                                        <?php assign($value);?>
                                    <?php endif; ?>
                                </td>
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

                <div class="col-xs-5">
                    <form name="kpi" id="kpi" action="<?php assign(Util::rewriteUrl( 'dashboard', 'kpi_pager', array(), array(), '', true )); ?>" method="POST">
                        <div class="range">
                            <?php if ($this->params['limit']): ?>
                                <input type="range" name="range" min="1" max="<?php assign($data['totalEntriesCount']); ?>" value="<?php assign($this->params['limit']); ?>" onchange="$('#range').val($(this).val())">
                                <span>表示件数 : <input name="limit" id="range" value="<?php assign($this->params['limit']); ?>"></span>
                            <?php else : ?>
                                <input type="range" name="range" min="1" max="<?php assign($data['totalEntriesCount']);?>" value="20" onchange="$('#range').val($(this).val())">
                                <span>表示件数 : <input name="limit" id="range" value="20"></span>
                            <?php endif ?>
                            <a href="javascript:void(0);" onclick="document.kpi.submit();return false;" class="btn btn-primary btn-large registrator">　変更　</a>
                        </div>
                    </form>
                </div>
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
    <script type="text/javascript" src="<?php assign($this->setVersion('/manager/js/services/ManagerKpiService.js')) ?>"></script>
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/i18n/jquery.ui.datepicker-ja.min.js"></script>


<?php write_html($this->parseTemplate('BrandcoManagerFooter.php')); ?>