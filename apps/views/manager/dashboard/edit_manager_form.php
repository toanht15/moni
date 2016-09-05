<?php write_html($this->parseTemplate('BrandcoManagerHeader.php', array(
    'title' => '管理者編集',
    'managerAccount' => $this->managerAccount,
))) ?>

    <div class="container-fluid">
        <div class="row">
            <?php write_html($this->parseTemplate('BrandcoManagerMenu.php')); ?>

            <form id="frmAddManager" name="add_manager" action="<?php assign(Util::rewriteUrl('dashboard', 'edit_manager', array(), array(), '', true)); ?>" method="POST">
                <?php write_html($this->csrf_tag()); ?>
                <?php write_html($this->formHidden('id', $data['manager']->id))?>
                <div class="col-md-10 col-md-offset-2 main">
                    <h1 class="page-header">管理者編集</h1>
                        <?php if ( $this->mode == ManagerService::ADD_FINISH ): ?>
                            <div class="alert alert-success">
                                変更が完了しました
                            </div>
                        <?php elseif ( $this->mode == ManagerService::ADD_ERROR ): ?>
                            <div class="alert alert-danger">
                                変更できませんでした
                            </div>
                        <?php endif; ?>
                            <div class="col-md-5 col-md-offset-0">
                                <div class="form-group">
                                    <?php write_html( $this->formText(
                                        'username',
                                PHPParser::ACTION_FORM,
                                array('class' =>'form-control', 'maxlength'=>'255', 'placeholder'=> '管理者名(漢字入力、スペース無し)')
                            )); ?>
                            <?php if ( $this->ActionError && !$this->ActionError->isValid('username') ): ?>
                                <td colspan="2"><p class="attention1"><?php assign ( $this->ActionError->getMessage('username') )?></p></td>
                            <?php endif; ?>
                        </div>
                        <a href="javascript:void(0);" onclick="document.editManager.submit();return false;"><button class="btn btn-primary btn-large registrator">　更新　</button></a>
                    </div>
                </div>
            </form>
        </div><!-- row -->
    </div><!-- container-fluid -->

<?php write_html($this->parseTemplate('BrandcoManagerFooter.php')); ?>
