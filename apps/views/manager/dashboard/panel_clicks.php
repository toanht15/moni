
<?php write_html($this->parseTemplate('BrandcoManagerHeader.php', array(
    'title' => 'KPI',
    'managerAccount' => $this->managerAccount,
))) ?>

<div class="container-fluid">
    <div class="row">
        <?php write_html($this->parseTemplate('BrandcoManagerMenu.php')); ?>

        <div class="col-md-10 col-md-offset-2 main">
            <h1 class="sub-header">KPI</h1>

            <p><b>Panel Clicks Ranking</b></p>

            <form class="navbar-form navbar-left" role="search" action='<?php write_html(Util::rewriteUrl('dashboard', 'panel_clicks', array(), array(), '', true)) ?>' method="GET">
                <div class="form-group">
                    <?php write_html($this->formRadio(
                        'search_mode',
                        PHPParser::ACTION_FORM,
                        array(),
                        array('all' => 'All', 'date' => '日付')
                    )); ?>
                    <?php write_html($this->formText(
                        'date',
                        PHPParser::ACTION_FORM,
                        array('maxlength'=>'10', 'class'=>'form-control jsDate inputDate', 'placeholder'=>'年/月/日')));
                    ?>
                </div>
                <button type="submit" class="btn btn-default">Submit</button>
            </form>

            <div class="table-responsive">
                <table class="table table-striped">
                    <?php if (!$this->total_count): ?>
                        <tr><td>0件</td></tr>
                    <?php else: ?>
                        <tr>
                            <td><?php assign('Access Count'); ?></td>
                            <td><?php assign('Brand Name'); ?></td>
                            <td><?php assign('Panel Type'); ?></td>
                            <td><?php assign('Entry Type'); ?></td>
                            <td><?php assign('Panel Link'); ?></td>
                            <td><?php assign('Created'); ?></td>
                        </tr>
                        <?php foreach($this->user_panel_clicks as $user_panel_click): ?>
                            <tr>
                                <?php $brand = $user_panel_click->getBrand(); ?>
                                <td><?php assign($user_panel_click->access_count); ?></td>
                                <td><?php assign($brand->name); ?></td>
                                <td><?php assign(UserPanelClick::$panel_type[$user_panel_click->panel_type]); ?></td>
                                <td><?php assign($user_panel_click->entries); ?></td>
                                <td><?php write_html($this->toHalfContentDeeply($user_panel_click->getEntryUrl($brand->directory_name))); ?></td>
                                <td><?php assign($user_panel_click->created_at); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </table>
            </div>

            <?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoManagerPager')->render(array(
                'TotalCount' => $this->total_count,
                'CurrentPage' => $this->params['p'],
                'Count' => $this->limit,
            ))); ?>

            <p>クリック全件数: <?php assign(number_format($this->row_count)) ?>件</p>
        </div>
    </div>
</div>

<script type="text/javascript" src="<?php assign($this->setVersion('/js/brandco/services/ManagerKpiService.js'))?>"></script>
<?php write_html($this->parseTemplate('BrandcoManagerFooter.php')); ?>