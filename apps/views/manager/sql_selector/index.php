<?php write_html($this->parseTemplate('BrandcoManagerHeader.php', array(
    'title' => 'データ抽出',
    'managerAccount' => $this->managerAccount,
))) ?>

<div class="container-fluid">
    <div class="row">
        <?php write_html($this->parseTemplate('BrandcoManagerMenu.php')); ?>
        <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
            <h1 class="sub-header">データ抽出</h1>
            <div class="panel-group" id="accordion">
                <?php foreach ($data['sql_selectors'] as $key => $category): ?>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href=<?php assign("#".$data['sql_selectors'][$key]['name']) ?>>
                                <?php assign($data['sql_selectors'][$key]['name']) ?>
                                (<?php assign(count($data['sql_selectors'][$key]['selectors'])) ?>)
                            </a>
                        </h4>
                    </div>
                    <div id=<?php assign($data['sql_selectors'][$key]['name']) ?> class="panel-collapse collapse">
                    <div class="panel-body"><?php write_html($this->parseTemplate('SqlSelectorList.php', array('sql_selector' => $data['sql_selectors'][$key]))) ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div><!-- row -->
</div><!-- container-fluid -->

<?php write_html($this->parseTemplate('BrandcoManagerFooter.php')); ?>
