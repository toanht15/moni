<?php write_html($this->parseTemplate('BrandcoManagerHeader.php', array(
	'title' => 'パスワード変更',
	'managerAccount' => $this->managerAccount,
))) ?>

<div class="container-fluid">
	<div class="row">
		<?php write_html($this->parseTemplate('BrandcoManagerMenu.php')); ?>

		<form id="frmChangeManager" name="change_password" action="<?php assign(Util::rewriteUrl('dashboard', 'change_password', array(), array(), '', true)); ?>" method="POST">
			<?php write_html($this->csrf_tag()); ?>

			<div class="col-md-10 col-md-offset-2 main">
				<h1 class="page-header">パスワード変更</h1>
				<?php if ( $this->mode == ManagerService::CHANGE_FINISH ): ?>
					<div class="alert alert-success">
						パスワードを変更しました。
					</div>
				<?php elseif ( $this->mode == ManagerService::CHANGE_REQUIRED ): ?>
					<div class="alert alert-success">
						<?php preg_match( '[\d+]' , ManagerService::PASSWORD_CHANGE_INTERVAL , $matches ); ?>
						最終パスワード変更から<?php assign($matches[0]) ?>ヶ月が経過しました。新しいパスワードへ変更をお願いいたします。
					</div>
				<?php elseif ($this->mode == ManagerService::CHANGE_ERROR ): ?>
					<div class="alert alert-danger">
						入力内容に誤りがあります。確認して下さい。
					</div>
				<?php endif; ?>
				<div class="col-md-5 col-md-offset-0">
					<div class="form-group">
						<?php write_html( $this->formPassword(
							'old_password',
							$this->POST ? $this->POST['old_password'] : '',
							array('class' =>'form-control', 'maxlength'=>'32', 'placeholder'=> '旧パスワード')
						)); ?>
						<?php if ( $this->ActionError && !$this->ActionError->isValid('old_password')): ?>
							<p class="attention1"><?php assign ( $this->ActionError->getMessage('old_password') )?></p>
						<?php endif; ?>
					</div>
					<div class="form-group">
						<?php write_html( $this->formPassword(
							'new_password',
							$this->POST ? $this->POST['new_password'] : '',
							array('class' =>'form-control', 'maxlength'=>'32', 'placeholder'=> '新パスワード')
						)); ?>
						<?php if ( $this->ActionError && !$this->ActionError->isValid('new_password')): ?>
							<p class="attention1"><?php assign ( $this->ActionError->getMessage('new_password') )?></p>
						<?php endif; ?>
					</div>
					<div class="form-group">
						<?php write_html( $this->formPassword(
							'new_password_confirm',
							$this->POST ? $this->POST['new_password_confirm'] : '',
							array('class' =>'form-control', 'maxlength'=>'32', 'placeholder'=> '新パスワード(確認用再入力)')
						)); ?>
						<?php if ( $this->ActionError && !$this->ActionError->isValid('new_password_confirm')): ?>
							<p class="attention1"><?php assign ( $this->ActionError->getMessage('new_password_confirm') )?></p>
						<?php endif; ?>
					</div>
					<a href="" onclick="document.frmChangeManager.submit();return false;"><button class="btn btn-primary btn-large registrator">　変更　</button></a>
				</div>
			</div>
		</form>
	</div><!-- row -->
</div><!-- container-fluid -->

<?php write_html($this->parseTemplate('BrandcoManagerFooter.php')); ?>
