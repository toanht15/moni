<?php write_html($this->parseTemplate('BrandcoManagerHeader.php', array(
    'title' => 'ブランド管理権限設定',
    'managerAccount' => $this->managerAccount,
))) ?>

<div class="container-fluid">
    <div class="row">
        <?php write_html($this->parseTemplate('BrandcoManagerMenu.php')); ?>
        <?php write_html($this->formHidden('manager_id', $this->manager_id)); ?>
        <div class="col-md-8 col-md-offset-2 main">
            <h1 class="page-header">管理ブランド一覧</h1>
            <div class="container">
                <div class="panel-body">
                    <div class="brandSelectBox" style="display: none;">
                        <?php write_html($this->formSelect(
                            'brand_list',
                            PHPParser::ACTION_FORM,
                            array('id' => 'jsBrandSelectBox'), $this->available_brands
                        )); ?>
                    </div>
                    <div class="col-md-6 autoCompleteSelection" style="border:1px solid #D8D8D8; padding: 3px; margin-right: 20px;">
                        <input type="text" autocomplete="off" class="autoCompleteInput" placeholder="ブランドを選択してください" tabindex="200"
                               style="width: 520px; border: 0; padding: 5px 5px 5px 0px;">
                        <div class="glyphicon glyphicon-chevron-down jShowAllData" aria-hidden="true" tabindex="200" title="全ブランドを表示する"></div>
                    </div>
                    <div class="col-md-2 addBrand">
                        <input class="btn btn-primary jsAddBrand " type="button" value="追加" data-message="本当に追加しますか？">
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-10 col-md-offset-2 main">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>NO</th>
                        <th>ブランド名</th>
                        <th>編集</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($this->current_agent_brands as $brand):?>
                        <tr>
                            <td><?php assign($brand['id'])?></td>
                            <td><?php assign($brand['name'])?></td>
                            <td><input type="button" class="btn btn-danger btn-xs jsDeleteBrand" value="削除" data-message="本当に削除しますか？" data-action="<?php assign($brand['id'])?>"></td>
                        </tr>
                    <?php endforeach;?>
                    </tbody>
                </table>
            </div>
        </div>
    </div><!-- row -->
</div><!-- container-fluid -->

<?php write_html($this->csrf_tag()); ?>

<link href="https://code.jquery.com/ui/1.10.4/themes/ui-lightness/jquery-ui.css" rel="stylesheet">
<script type="text/javascript" src="https://code.jquery.com/jquery-1.10.2.js"></script>
<script type="text/javascript" src="https://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
<script src="<?php assign($this->setVersion('/manager/js/services/EditAgentService.js')) ?>"></script>

<?php write_html($this->parseTemplate('BrandcoManagerFooter.php')); ?>
