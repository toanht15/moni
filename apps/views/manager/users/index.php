<?php write_html($this->parseTemplate('BrandcoManagerHeader.php',array('title' => 'ユーザー管理', 'managerAccount' => $this->managerAccount))) ?>

<div class="container-fluid">
    <?php write_html($this->parseTemplate('BrandcoManagerMenu.php')) ?>
    <div class="col-md-15 col-md-offset-2 main">
        <h1 class="page-header">ユーザー管理</h1>
        <div class="row">
            <form action="<?php assign(Util::rewriteUrl('users', 'index', array(), array(), '', true)) ?>" method="GET">
                <div class="col-md-6 col-md-offset-0">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h3 class="panel-title">検索</h3>
                        </div>
                        <div class="panel-body">
                            <?php write_html($this->parseTemplate('user_search/SearchTypeForm.php')) ?>
                            <?php write_html($this->parseTemplate('user_search/PlatformUserIdForm.php')) ?>
                            <?php write_html($this->parseTemplate('user_search/BrandcoUserIdForm.php')) ?>
                            <?php write_html($this->parseTemplate('user_search/SnsUserIdForm.php')) ?>
                            <?php write_html($this->parseTemplate('user_search/PlatformMailAddressForm.php')) ?>
                            <?php write_html($this->parseTemplate('user_search/BrandcoMailAddressForm.php')) ?>
                            <?php write_html($this->parseTemplate('user_search/BrandcoBrandNoForm.php')) ?>
                            <button class="btn btn-primary" id="userSearchButton">検索</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <?php if($data['error_messages']): ?>
            <div class="alert alert-danger col-md-6" role="alert">
                <?php foreach($data['error_messages'] as $message): ?>
                    <div>
                        <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                        <span class="sr-only"></span><?php assign($message)?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <?php if($data['search_error']): ?>
            <div class="alert alert-danger col-md-6" role="alert">
                <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                <span class="sr-only"></span><?php assign($data['search_error'])?>
            </div>
        <?php endif; ?>
        <?php if($data['show_user_list']): ?>
            <div class="row">
                <?php write_html($this->parseTemplate('user_search/UserList.php', array('users' => $data['users']))) ?>
            </div>
        <?php endif; ?>
        <?php foreach($data['users'] as $platform_user_id => $user): ?>
            <div id="userAccount<?php assign($platform_user_id) ?>">
                <div class="row">
                    <?php write_html($this->parseTemplate('user_search/PlatformUser.php', array('platform_user' => $user['platform_user']))) ?>
                    <?php write_html($this->parseTemplate('user_search/BrandcoUser.php', array('brandco_user' => $user['brandco_user']))) ?>
                </div>
                <div class="row">
                    <?php write_html($this->parseTemplate('user_search/SnsUser.php', array('social_accounts' => $user['platform_user']['social_accounts']))) ?>
                </div>
                <div class="row">
                    <?php write_html($this->parseTemplate('user_search/BrandcoBrandUsers.php', array('brand_users' => $user['brand_users'],'parameter_data' => $data['parameter_data']))) ?>
                </div>
                <div class="row">
                    <?php write_html($this->parseTemplate('user_search/BrandcoCpUsers.php', array('cp_users' => $user['cp_users'], 'cp_user_statuses' => $user['cp_user_statuses']))) ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script src="<?php assign($this->setVersion('/manager/js/services/UserSearchService.js'))?>"></script>
<?php write_html($this->parseTemplate('BrandcoManagerFooter.php')) ?>
