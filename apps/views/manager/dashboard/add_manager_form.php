<?php write_html($this->parseTemplate('BrandcoManagerHeader.php', array(
	'title' => '管理者追加',
    'managerAccount' => $this->managerAccount,
))) ?>

	<div class="container-fluid">
		<div class="row">
            <?php write_html($this->parseTemplate('BrandcoManagerMenu.php')); ?>
	
                    <form id="frmAddManager" name="add_manager" action="<?php assign(Util::rewriteUrl('dashboard', 'add_manager', array(), array(), '', true)); ?>" method="POST">
			<?php write_html($this->csrf_tag()); ?>

				<div class="col-md-10 col-md-offset-2 main">
					<h1 class="page-header">管理者追加</h1>
						<?php if ( $this->mode == ManagerService::ADD_FINISH ): ?>
							<div class="alert alert-success">
								登録が完了しました。
							</div>
						<?php elseif ($this->mode == ManagerService::ADD_ERROR ): ?>
							<div class="alert alert-danger">
								入力内容に誤りがあります。確認して下さい。
							</div>
						<?php endif; ?>
					<div class="col-md-5 col-md-offset-0">
						<div class="form-group">
							<?php write_html( $this->formText(
								'username', 
								$this->mode == ManagerService::ADD_FINISH || !$this->mode ? null : PHPParser::ACTION_FORM,
								array('class' =>'form-control', 'maxlength'=>'255', 'placeholder'=> '管理者名(漢字入力、スペース無し)')
							)); ?>
							<?php if ( $this->ActionError && !$this->ActionError->isValid('username')): ?>
								<p class="attention1"><?php assign ( $this->ActionError->getMessage('username') )?></p>
							<?php endif; ?>
						</div>
						<div class="form-group">
							<?php write_html( $this->formEmail(
								'email', 
								$this->mode == ManagerService::ADD_FINISH || !$this->mode ? null : PHPParser::ACTION_FORM,
								array('class' =>'form-control', 'maxlength'=>'255', 'placeholder'=> 'メールアドレス')
							)); ?>
							<?php if ( $this->ActionError && !$this->ActionError->isValid('email')): ?>
								<p class="attention1"><?php assign ( $this->ActionError->getMessage('email') )?></p>
							<?php endif; ?>
						</div>
						<div class="form-group">
							<?php write_html( $this->formPassword(
								'password', 
								$this->POST ? $this->POST['password'] : '',
								array('class' =>'form-control', 'maxlength'=>'32', 'placeholder'=> 'パスワード')
							)); ?>
							<?php if ( $this->ActionError && !$this->ActionError->isValid('password')): ?>
								<p class="attention1"><?php assign ( $this->ActionError->getMessage('password') )?></p>
							<?php endif; ?>
						</div>
						<a href="" onclick="document.frmAddManager.submit();return false;"><button class="btn btn-primary btn-large registrator">　追加　</button></a>
					</div>
				</div>
			</form>
		</div><!-- row -->
	</div><!-- container-fluid -->

<?php write_html($this->parseTemplate('BrandcoManagerFooter.php')); ?>
