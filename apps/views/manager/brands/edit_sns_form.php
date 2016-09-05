<?php write_html($this->parseTemplate('BrandcoManagerHeader.php', array(
    'title' => 'Brand detail',
    'managerAccount' => $this->managerAccount, )))
?>

<script>
$(document).ready(function() {
<?php foreach ($data['types'] as $type => $app_id): ?>
    $('#<?php assign($type); ?>_button').on('click', function(event) {
        event.preventDefault();
        var param = {
            data: {
            'brand_id': <?php assign($data['brand_id']); ?>,
            'social_app_id': <?php assign($app_id); ?>,
            'user_id': $("#<?php assign($type); ?>_admin_user_id").val()
            },
            url: '<?php assign($data['sns_outer_url']); ?>',
            beforeSend: function() {
                $("#<?php assign($type); ?>_loader").css('display', 'block');
                $("#<?php assign($type); ?>_button").css('display', 'none');
                $("#<?php assign($type); ?>_title").css('display', 'none');
            },
            success: function(json) {
                console.log(json);
                if (json.result === "ok") {
                    $('#<?php assign($type); ?>_url').html(json.data.url);
                    $('#<?php assign($type); ?>_url').attr("href", json.data.url);
                    $('#<?php assign($type); ?>_password').html(json.data.password);
                    $('#<?php assign($type); ?>_info').css('display', 'block');
                } else {
                    alert("エラーが発生しました(" + json.data + ')');
                }
            },
            complete: function() {
                $("#<?php assign($type); ?>_loader").css('display', 'none');
            }
        };
        Brandco.api.callAjaxWithParam(param, false, false);
    });
<?php endforeach ?>
});
</script>

<div class="container-fluid">
    <div class="row">
        <?php write_html($this->parseTemplate('BrandcoManagerMenu.php')); ?>
        <div class="col-md-10 col-md-offset-2 main">
            <ol class="breadcrumb">
                <li><a href="<?php assign(Util::rewriteUrl('brands', 'index', array(), array(), '', true )); ?>">ブランド一覧</a></li>
                <li class="active"><?php assign($data['brand']->name);?></a></li>
            </ol>
            <h1 class="page-header"><?php assign($data['brand']->name . ' / ' . $data['brand']->enterprise_name);?></h1>
            <div class="well col-md-10 col-md-offset-0">
                <ul class="nav nav-tabs">
                    <li role="presentation">
                        <a href="<?php assign(Util::rewriteUrl('brands', 'edit_form', array($data['brand']->id), array(), '', true)); ?>">
                        基本設定
                        </a>
                    </li>
                    <li role="presentation" class="active"><a href="#">SNSアカウント</a></li>
                </ul>
                <h3>SNS連携</h3>
                <?php foreach ($data['data_list'] as $key => $accounts): ?>
                    <h4><?php assign($data['titles'][$key]); ?>連携</h4>
                    <ul class="list-group">
                        <?php foreach ($accounts as $account): ?>
                            <li class="list-group-item">
                                <span class="snsIcon">
                                    <img src="<?php assign($this->setVersion("/img/sns/iconSns" . strtoupper($key) . "1.png")); ?>" width="22">
                                </span>
                                <a href="<?php assign($account->getUrl()); ?>" target="_blank"><?php assign($account->getName()); ?></a>
                            </li>
                        <?php endforeach; ?>
                        <?php if (count($data['admin_user_params'])): ?>
                            <li class="list-group-item">
                                <span id="<?php assign($key); ?>_title">
                                管理者を
                                <?php write_html($this->formSelect(
                                    "${key}_admin_user_id",
                                    PHPParser::ACTION_FORM,
                                    ['id' => "${key}_admin_user_id"],
                                    $data['admin_user_params']
                                ));
                                ?>
                                に設定して、
                                </span>
                                <a id="<?php assign($key); ?>_button" class="btn btn-primary btn-large" href="javascript:void();">連携URL発行</a>
                                <img id="<?php assign($key); ?>_loader" src="<?php assign($this->setVersion('/img/base/amimeLoading.gif')); ?>" width="25" height="25" style="display: none">
                                <span id="<?php assign($key); ?>_info" style="display:none">
                                <a id="<?php assign($key); ?>_url" href="" target="_brank"></a><br /><br />
                                    パスワード:&nbsp;<span id="<?php assign($key); ?>_password"></span><br /><br />
                                    ※このURLとパスワードから管理画面にログインせずに<?php assign($data['titles'][$key]); ?>連携できます。
                                    十分注意して扱ってください。
                                </span>
                            </li>
                        <?php else: ?>
                            <li class="list-group-item">連携URLを発行するためには、管理者登録を行ってください。</li>
                        <?php endif ?>
                    </ul>
                <?php endforeach ?>
            </div>
        </div>
    </div><!-- row -->
</div><!-- container-fluid -->

<?php write_html($this->parseTemplate('BrandcoManagerFooter.php')); ?>
