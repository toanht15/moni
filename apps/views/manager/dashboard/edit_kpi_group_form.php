<?php write_html($this->parseTemplate('BrandcoManagerHeader.php', array(
    'title' => 'KPIグループ編集',
    'managerAccount' => $this->managerAccount,
))) ?>

<div class="container-fluid">
    <div class="row">
        <?php write_html($this->parseTemplate('BrandcoManagerMenu.php')); ?>

        <form id="edit_groups" name="edit_kpi_groups" action="<?php assign(Util::rewriteUrl('dashboard', 'edit_kpi_groups', array(), array(), '', true)); ?>" method="POST">
            <?php write_html($this->csrf_tag()); ?>
            <?php write_html($this->formHidden('id', $data['group_id']))?>
            <div class="col-md-10 col-md-offset-2 main">

                <ol class="breadcrumb">
                    <li><a href="<?php assign(Util::rewriteUrl('dashboard', 'list_kpi_groups', array(), array(), '', true)); ?>">KPIグループ</a></li>
                    <li><a href="<?php assign(Util::rewriteUrl('dashboard', 'customise_kpi_groups', array($data['group_id']),array('p'=>$params['p']), '', true))?>"><?php assign($data['ActionForm']['name']);?></a></li>
                    <li class="active">KPIグループ編集</a></li>
                </ol>

                <h1 class="page-header">KPIグループ編集</h1>
                <?php if ( $this->mode == ManagerKpiGroupService::ADD_FINISH ): ?>
                    <div class="alert alert-success">
                        更新しました。
                    </div>
                <?php elseif ($this->mode == ManagerKpiGroupService::ADD_ERROR ): ?>
                    <div class="alert alert-danger">
                        入力内容に誤りがあります。確認して下さい。
                    </div>
                <?php endif; ?>

                <div class="col-md-5 col-md-offset-0">
                    <div class="form-group">
                        <?php write_html( $this->formText(
                            'name',
                            PHPParser::ACTION_FORM,
                            array('class' =>'form-control', 'maxlength'=>'255', 'placeholder'=> 'Group Name*')
                        )); ?>
                        <?php if ( $this->ActionError && !$this->ActionError->isValid('name')): ?>
                            <p class="attention1"><?php assign ( $this->ActionError->getMessage('name') )?></p>
                        <?php endif; ?>

                        <h3>KPI's*</h3>
                        <select multiple="multiple" id="groupColumnIds" name="groupColumnIds[]">

                            <?php $columnIds = array();?>
                            <?php foreach($data['kpi_group_columns'] as $kpi_group_column):
                                $kpi_column = $kpi_group_column->getManagerKpiColumns()->current();
                                $columnIds[$kpi_column->id] = true;?>
                                <option value='<?php assign($kpi_column->id);?>' selected><?php assign($kpi_column->name);?></option>
                            <?php endforeach;?>

                            <?php foreach($this->columns as $column):?>
                                <?php if(!$columnIds[$column->id]): ?>
                                    <option value='<?php assign($column->id);?>'><?php assign($column->name);?></option>
                                <?php endif; ?>
                            <?php endforeach;?>

                        </select>
                    </div>
                    <a href="javascript:void(0)" onclick="document.edit_groups.submit();return false;"><button class="btn btn-primary btn-large registrator">　更新　</button>
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
