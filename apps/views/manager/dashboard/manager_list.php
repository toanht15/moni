<?php write_html($this->parseTemplate('BrandcoManagerHeader.php', array(
    'title' => '管理者一覧',
    'managerAccount' => $this->managerAccount,
))) ?>

<div class="container-fluid">
    <div class="row">
        <?php write_html($this->parseTemplate('BrandcoManagerMenu.php')); ?>
        <div class="col-md-14 col-md-offset-2 main">
            <form name="manager_search" action="<?php assign(Util::rewriteUrl('dashboard', 'manager_list', array(), array(), '', true)); ?>" method="GET" class="form-horizontal row-border">
                <h4><span class="edit"><a href="#" class="jsMessageSetting">詳細検索条件</a></span></h4>
                <div class="jsMessageSettingTarget" style="display:1">
                    <div class="container">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <label class="col-md-2 control-label">管理者ID</label>
                                <div class="col-md-10">
                                    <label class="checkbox-inline">
                                        <?php write_html( $this->formText(
                                            'manager_id',
                                            PHPParser::ACTION_FORM,
                                            array('maxlength'=>'255', 'placeholder'=> '管理者ID')
                                        )); ?>
                                    </label>
                                </div>
                                <label class="col-md-2 control-label">名前</label>
                                <div class="col-md-10">
                                    <label class="checkbox-inline">
                                        <?php write_html( $this->formText(
                                            'manager_name',
                                            PHPParser::ACTION_FORM,
                                            array('maxlength'=>'255', 'placeholder'=> '名前')
                                        )); ?>
                                    </label>
                                </div>
                                <label class="col-md-2 control-label">メールアドレス</label>
                                <div class="col-md-10">
                                    <label class="checkbox-inline">
                                        <?php write_html( $this->formText(
                                            'manager_mail',
                                            PHPParser::ACTION_FORM,
                                            array('maxlength'=>'255', 'placeholder'=> 'メールアドレス')
                                        )); ?>
                                    </label>
                                </div>
                                <label class="col-md-2 control-label">権限</label>
                                <div class="col-md-10">
                                    <label class="checkbox-inline">
                                        <?php write_html( $this->formSelect(
                                            'auth',
                                            PHPParser::ACTION_FORM,
                                            array(),$this->auth
                                        )); ?>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 col-md-offset-4">
                    <a href="javascript:void(0);" onclick="document.manager_search.submit();return false;" class="btn btn-primary btn-xs">検索</a><br>
            </form>
        </div>
        </div>
        <div class="col-md-10 col-md-offset-2 main">
            <h1 class="page-header">管理者一覧</h1>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>NO</th>
                        <th>名前</th>
                        <th>メールアドレス</th>
                        <th>権限</th>
                        <th>ログイン回数</th>
                        <th>最終ログイン日時</th>
                        <th>作成日</th>
                        <th>詳細</th>

                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($this->managerAccounts as $managerAccount):?>
                        <tr>
                            <td>
                                <?php assign($managerAccount['id'])?>
                            </td>
                            <td><?php assign($managerAccount['name'])?></td>
                            <td><?php assign($managerAccount['mail_address'])?></td>
                            <td><?php assign(Manager::$MANAGER_AUTHORITY_LIST[$managerAccount['authority']])?></td>
                            <td><?php assign($managerAccount['login_count'])?></td>
                            <td><?php assign($managerAccount['last_login_date'])?></td>
                            <td><?php assign($managerAccount['created_at'])?></td>
                            <td><a href="<?php assign(Util::rewriteUrl('dashboard', 'edit_manager_form', array($managerAccount['id']), array(), '', true)); ?>">編集</a></td>
                        </tr>
                    <?php endforeach;?>
                    </tbody>
                </table>
                <?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoManagerPager')->render(array(
                    'TotalCount' => $data['allManagerCount'],
                    'CurrentPage' => $this->params['p'],
                    'Count' => $data['limit'],
                ))) ?>
            </div>
        </div>

    </div><!-- row -->
</div><!-- container-fluid -->
<script src="<?php assign($this->setVersion('/manager/js/services/CampaignListService.js'))?>"></script>
<?php write_html($this->parseTemplate('BrandcoManagerFooter.php')); ?>
