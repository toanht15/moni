<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Reset password</title>

    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="<?php assign($this->setVersion('/manager/css/bootstrap.min.css'))?>">

    <!-- Custom styles for this template -->
    <link rel="stylesheet" href="<?php assign($this->setVersion('/manager/signin.css')) ?>">
    <link rel="stylesheet" href="<?php assign($this->setVersion('/manager/dashboard.css'))?>">

    <link rel="icon" href="<?php assign($this->setVersion('/img/base/favicon.ico'))?>">

</head>
<body>
<div class="container">
    <form  class="form-signin" id="frmResetManager" name="reset_password" action="<?php assign(Util::rewriteUrl('account', 'reset_password', array(), array(), '', true)); ?>" method="POST">

        <?php if ($this->mode == ManagerService::ACCOUNT_ERROR ): ?>
            <p class="attention1">対象のアカウントがありません。URLを確認して下さい。</p>
        <?php else: ?>
            <?php write_html($this->csrf_tag()); ?>
            <h2 class="form-signin-heading">パスワード再設定</h2>

            <?php if ($this->mode == ManagerService::CHANGE_ERROR ): ?>
                <p class="attention1">入力内容に誤りがあります。確認して下さい。</p>
            <?php endif; ?>

            <input type="hidden" name="manager_token" value="<?php assign($params['token']); ?>">

            <div class="form-group">
                <?php write_html( $this->formPassword(
                    'new_password',
                    $this->POST ? $this->POST['new_password'] : '',
                    array('class' =>'form-control', 'maxlength'=>'32', 'placeholder'=> '新パスワード', 'autofocus'=> true)
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
            <a href="" onclick="document.frmResetManager.submit();return false;"><button class="btn btn-lg btn-primary btn-block" type="submit">送信</button></a>
        <?php endif; ?>
    </form>

</div>
</body>
</html>