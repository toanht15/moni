<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoHeader')->render($data['pageStatus'])) ?>
<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoTopHeader')->render($data['pageStatus'])) ?>
<article>
    <section class="mypageUserWrap">
        <h1 class="mypageUser">
            <figure>
                <img src="<?php assign($data['user']->socialAccounts[0]->profileImageUrl ? $data['user']->socialAccounts[0]->profileImageUrl : $this->setVersion('/img/base/imgUser1.jpg')) ?>" height="40" width="40" alt="user img" onerror="this.src='<?php assign($this->setVersion('/img/base/imgUser1.jpg'));?>';">
            </figure>
            <span class="nametext"><?php assign($data['user']->name) ?>さん</span>
        </h1>
        <div class="mypageSetting" style="display:block;">
            <form action="<?php assign(Util::rewriteUrl( 'my', 'api_change_optin_status_without_login.json')); ?>" method="POST">
                <?php write_html($this->csrf_tag()); ?>
                <?php write_html($this->formHidden('optin_token', $data['optin_token'])); ?>
                <dl>
                    <dt>「<?php assign($data['brand']->name); ?>」からのメッセージをメールで受け取る</dt>
                    <dd><label><?php write_html($this->formRadio("radio_optin", $data['brands_users_relation']->optin_flg, array('class' => 'cmd_execute_change_optin_action'), array(BrandsUsersRelationService::STATUS_OPTIN => '受け取る')));?></label></dd>
                    <dd><label><?php write_html($this->formRadio("radio_optin", $data['brands_users_relation']->optin_flg, array('class' => 'cmd_execute_change_optin_action'), array(BrandsUsersRelationService::STATUS_OPTOUT => '受け取らない')));?></label></dd>
                </dl>
            </form>

            <p class="supplement1">当選通知メールについては受け取り可否に関わらずお送りさせていただきます</p>
            <!-- /.mypageSetting --></div>
        <!-- /.mypageUserWrap --></section>
</article>

<?php write_html($this->scriptTag('OptinStatusService')); ?>

<?php write_html( $this->parseTemplate('BrandcoFooter.php', $data['pageStatus']) ); ?>
