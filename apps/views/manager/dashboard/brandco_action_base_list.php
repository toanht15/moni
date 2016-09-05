<?php write_html($this->parseTemplate('BrandcoManagerHeader.php', array(
    'title' => 'ファイルリスト',
    'managerAccount' => $this->managerAccount,
))) ?>

<div class="container-fluid">
    <div class="row">
        <?php write_html($this->parseTemplate('BrandcoManagerMenu.php')); ?>

        <div class="col-md-10 col-md-offset-2 main">
            <ol class="breadcrumb">
                <li class="active">ファイルリスト</a></li>
            </ol>
            <h1 class="sub-header">ファイルリスト</h1>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                    <tr>
                    <?php foreach(array_keys($this->data[0]) as $key => $value):?>
                        <th><?php assign($value);?></th>
                    <?php endforeach;?>
                    </tr>
                    </thead>
                    <tbody>
                    <?php for($i = ($this->p - 1) * $this->limit; $i < min(($this->limit * $this->p), $this->totalEntriesCount); $i++):?>
                        <tr>
                        <?php foreach($this->data[$i] as $key => $value):?>
                            <td><?php assign($value);?></td>
                        <?php endforeach;?>
                        </tr>
                    <?php endfor;?>
                    </tbody>
                </table>
                <?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoManagerPager')->render(array(
                    'TotalCount' => $data['totalEntriesCount'],
                    'CurrentPage' => $this->p,
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