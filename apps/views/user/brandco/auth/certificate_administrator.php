<?php write_html($this->parseTemplate('BrandcoInviteHeader.php', $data['pageStatus'])); ?>

<article>
    <form name="frmCertificate" action="<?php assign(Util::rewriteUrl('auth', 'certificate_administrator_post')); ?>" method="POST">
        <ul class="loginPass">
            <?php write_html($this->csrf_tag()); ?>
            <?php write_html($this->formHidden('invite_token', $data['invite_token'])); ?>

            <?php if ( $this->ActionError && !$this->ActionError->isValid('password')): ?>
                <li class="pass"><span class="iconError1" style="color: #ff0000"><?php assign($this->ActionError->getMessage('password')); ?></span></li>
            <?php endif; ?>
            <?php if ( $this->ActionError && !$this->ActionError->isValid('invite_certificate_fail')): ?>
                <li class="pass"><span class="iconError1" style="color: #ff0000"><?php assign($this->ActionError->getMessage('invite_certificate_fail')); ?></span></li>
            <?php endif; ?>
            <li class="pass"><?php write_html($this->formText('password', PHPParser::ACTION_FORM, array('placeholder'=>'パスワード'))); ?></li>
            <li class="btn3"><a href="javascript:void();" id='submitButton' data-type='refresh'>認証</a></li>
            <!-- /.loginPass --></ul>
    </form>
</article>

<?php write_html($this->scriptTag('CertificateAdministratorService'))?>
<?php $param = array_merge($data['pageStatus'], array('script' => $data['script'])) ?>
<?php write_html($this->parseTemplate('BrandcoFooter.php', $param)); ?>
