<?php write_html($this->parseTemplate('BrandcoManagerHeader.php', array( 'title' => 'ブランド詳細', 'managerAccount' => $this->managerAccount, ))) ?>

<div class="container-fluid">
    <div class="row">
        <?php write_html($this->parseTemplate('BrandcoManagerMenu.php')); ?>

        <form method="POST" action="<?php assign(Util::rewriteUrl('dashboard', 'edit_brand_contract', array(), array(), '', true)) ?>" name="edit_brand_contract">
            <?php write_html($this->formHidden('brand_id', $this->brand->id)) ?>
            <?php write_html($this->csrf_tag()); ?>

            <div class="col-md-10 col-md-offset-2 main">
                <ol class="breadcrumb">
                    <li><a href="<?php assign(Util::rewriteUrl('dashboard', 'brand_list', array(), array(), '', true)); ?>">ブランド一覧</a></li>
                    <li class="active"><?php assign($this->brand->name);?></a></li>
                </ol>
                <h1 class="page-header">
                    クローズ設定
                    <?php if ($this->brand_contract->delete_status == BrandContracts::MODE_DATA_DELETED): ?>
                        <small>[データ削除済み]</small>
                    <?php endif; ?>
                </h1>

                <div class="col-md-10 col-md-offset-0">
                    <div class="form-inline">
                        <div class="form-group">
                            <label>クローズページ表示切替期間</label>
                            <?php write_html($this->formText('contract_end_date', PHPParser::ACTION_FORM, array('maxlength'=>'10', 'class'=>'jsDate inputDate form-control ', 'placeholder'=>'年/月/日'))); ?>
                            <?php write_html($this->formSelect('contract_end_time_hh', PHPParser::ACTION_FORM, array('class' => 'form-control'), $this->getHours())); ?>
                            <span>:</span>
                            <?php write_html($this->formSelect('contract_end_time_mm', PHPParser::ACTION_FORM, array('class'=> 'form-control'), $this->getMinutes())); ?>
                            ~
                            <?php write_html($this->formText('display_end_date', PHPParser::ACTION_FORM, array('maxlength'=>'10', 'class'=>'jsDate inputDate form-control', 'placeholder'=>'年/月/日'))); ?>
                            <?php write_html($this->formSelect('display_end_time_hh', PHPParser::ACTION_FORM, array('class' => 'form-control'), $this->getHours())); ?>
                            <span>:</span>
                            <?php write_html($this->formSelect('display_end_time_mm', PHPParser::ACTION_FORM, array('class'=>'form-control'), $this->getMinutes())); ?>
                        </div>
                        <?php if ($this->ActionError && !$this->ActionError->isValid('contract_end_date')): ?>
                            <p class="attention1" style="margin: 0px"><?php assign($this->ActionError->getMessage('contract_end_date')); ?></p>
                        <?php endif; ?>
                    </div>
                    <small>※クローズページ表示期間が過ぎたらサイトは表示されなくなり、表示期間が過ぎて3ヶ月後にユーザデータは削除されます。</small>

                    <h3>クローズページ</h3>
                    <div class="form-group">
                        <p><?php write_html($this->formText('closed_title', PHPParser::ACTION_FORM, array('maxlength' => '255', 'class'=>'form-control'))); ?></p>
                        <?php if ($this->ActionError && !$this->ActionError->isValid('closed_title')): ?>
                            <p class="attention1"><?php assign ( $this->ActionError->getMessage('closed_title') )?></p>
                        <?php endif; ?>
                        <div>
                            <?php write_html($this->formTextarea('closed_description', PHPParser::ACTION_FORM, array('cols' => '40', 'rows' => '4', 'width' => '960', 'height' => '580'))); ?>
                            <?php if ($this->ActionError && !$this->ActionError->isValid('closed_description')): ?>
                                <p class="attention1"><?php assign($this->ActionError->getMessage('closed_description')) ?></p>
                            <?php endif; ?>
                            <!-- /.pageContEdit --></div>
                    </div>
                    <small><div class="alert alert-info">
                        <p>置換タグ</p>
                        <p>BRAND名 <#BRAND_NAME></p>
                        <p>クローズページ表示開始日 <#CLOSE_START></p>
                        <p>クローズページ表示終了日 <#CLOSE_END_DATE></p>
                        <p>クローズページ表示終了日時 <#CLOSE_END_DATETIME></p>
                    </div></small>
                    <p style="margin-top:10px"><a href="javascript:void(0);" onclick="document.edit_brand_contract.submit();return false;" class="btn btn-primary">　更新　</a>&nbsp;&nbsp;<a href="javascript:void(0);" class="jsClosedBrandPreview">プレビュー</a></p>
                </div>
            </div>
        </form>
    </div>
</div>

<script type="text/javascript" src="<?php assign($this->setVersion('/ckeditor/ckeditor.js')) ?>"></script>
<?php write_html($this->scriptTag('BrandContractService')) ?>
<?php write_html($this->parseTemplate('BrandcoManagerFooter.php')); ?>
