<?php write_html($this->parseTemplate('BrandcoManagerHeader.php', array(
    'title' => 'KPIグループ追加',
    'managerAccount' => $this->managerAccount,
))) ?>

<div class="container-fluid">
    <div class="row">
        <?php write_html($this->parseTemplate('BrandcoManagerMenu.php')); ?>

        <form id="add_groups" name="add_kpi_groups" action="<?php assign(Util::rewriteUrl('dashboard', 'add_kpi_groups', array(), array(), '', true)); ?>" method="POST">
            <?php write_html($this->csrf_tag()); ?>
            <div class="col-md-10 col-md-offset-2 main">

                <ol class="breadcrumb">
                    <li><a href="<?php assign(Util::rewriteUrl('dashboard', 'list_kpi_groups', array(), array(), '', true)); ?>">KPIグループ</a></li>
                    <li class="active">KPIグループ追加</a></li>
                </ol>

                <h1 class="page-header">KPIグループ追加</h1>
                <?php if ($this->mode == ManagerKpiGroupService::ADD_ERROR ): ?>
                    <div class="alert alert-danger">
                        入力内容に誤りがあります。確認して下さい。
                    </div>
                <?php endif; ?>

                <div class="col-md-5 col-md-offset-0">
                    <div class="form-group">
                        <?php write_html( $this->formText(
                            'name',
                            PHPParser::ACTION_FORM,
                            array('class' =>'form-control', 'maxlength'=>'255', 'placeholder'=> 'グループ名')
                        )); ?>
                        <?php if ( $this->ActionError && !$this->ActionError->isValid('name')): ?>
                            <p class="attention1"><?php assign ( $this->ActionError->getMessage('name') )?></p>
                        <?php endif; ?>

                        <h3>KPI群</h3>

                        <select multiple="multiple" id="groupColumnIds" name="groupColumnIds[]">

                            <?php foreach($this->columns as $column):?>
                                <option value='<?php assign($column->id);?>'><?php assign($column->name);?></option>
                            <?php endforeach;?>

                        </select>
                    </div>
                    <a href="" onclick="document.add_groups.submit();return false;"><button class="btn btn-primary btn-large registrator">　追加　</button></a>
                </div>
            </div>
        </form>
    </div><!-- row -->
</div><!-- container-fluid -->

<link rel="stylesheet" href="<?php assign($this->setVersion('/manager/multiselect/css/multi-select.css'))?>">
<script src="<?php assign($this->setVersion('/manager/multiselect/js/jquery.multi-select.js'))?>" type="text/javascript"></script>
<script>
    $('#groupColumnIds').multiSelect();
</script>
<?php write_html($this->parseTemplate('BrandcoManagerFooter.php')); ?>


