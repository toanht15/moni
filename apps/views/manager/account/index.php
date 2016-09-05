<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="">
	<meta name="author" content="">

	<title>Sign in to モニプラ</title>

	<!-- Bootstrap core CSS -->
        <link rel="stylesheet" href="<?php assign($this->setVersion('/manager/css/bootstrap.min.css'))?>">

	<!-- Custom styles for this template -->
        <link rel="stylesheet" href="<?php assign($this->setVersion('/manager/signin.css')) ?>">
	<link rel="stylesheet" href="<?php assign($this->setVersion('/manager/dashboard.css'))?>">

    <link rel="icon" href="<?php assign($this->setVersion('/img/base/favicon.ico'))?>">

</head>
<body>
	<div class="container">

            <form class="form-signin" role="form" id=frmSignin action="<?php assign(Util::rewriteUrl('account', 'login', array(), array(), '', true)); ?>" method="POST">
			<?php write_html($this->csrf_tag()); ?>
			<h2 class="form-signin-heading">Please sign in</h2>
				<div class="form-group">
					<?php write_html( $this->formEmail(
						'email',
						$this->ActionError ? PHPParser::ACTION_FORM : '',
						array('class' =>'form-control', 'maxlength'=>'255', 'placeholder'=> 'Email address', 'autofocus'=> true)
					)); ?>
					<?php if ($this->ActionError): ?>
						<?php if (!$this->ActionError->isValid('email')): ?>
							<p class="attention1"><?php assign ( $this->ActionError->getMessage('email') )?></p>
						<?php endif; ?>
					<?php endif; ?>
				</div>
				<div class="form-group">
					<?php write_html( $this->formPassword(
						'password',
						$this->POST ? $this->POST['password'] : '',
						array('class' =>'form-control', 'maxlength'=>'32', 'placeholder'=> 'Password')
					)); ?>
					<?php if ($this->ActionError): ?>
						<?php if (!$this->ActionError->isValid('password')): ?>
							<p class="attention1"><?php assign ( $this->ActionError->getMessage('password') )?></p>
						<?php endif; ?>
					<?php endif; ?>
				</div>
				<?php if ($this->login_err == ManagerService::LOGIN_INVALID): ?>
                    <p class="attention1">メールアドレスまたはパスワードが違います。<br>	複数回ログインに失敗するとアカウントがロックされますのでご注意下さい。</p>
                <?php elseif ($this->login_err == ManagerService::LOGIN_INVALID_MAX): ?>
                    <p class="attention1">お使いのアカウントはすでにロックされています。<br>	登録メールアドレスにパスワード再設定のご案内を通知しましたので、ご確認をお願いいたします。</p>
                <?php endif; ?>

			<a href="" onclick="document.frmSignin.submit();return false;"><button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button></a>
		</form>

	</div> <!-- /container -->

	<!-- Bootstrap core JavaScript
	================================================== -->
	<!-- Placed at the end of the document so the pages load faster -->

</body>
</html>