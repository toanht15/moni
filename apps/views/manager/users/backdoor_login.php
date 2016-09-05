<?php write_html($this->parseTemplate('BrandcoManagerHeader.php', array(
    'title' => 'User Search',
    'managerAccount' => $this->managerAccount
))) ?>

<div class="container-fluid">
    <?php write_html($this->parseTemplate('BrandcoManagerMenu.php')); ?>
    <div class="col-md-15 col-md-offset-2 main">
        <h1 class="page-header">ユーザー管理</h1>
        <div class="row">
            <div class="col-md-6 col-md-offset-0">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title">代理ログイン</h3>
                    </div>
                    <div class="panel-body">
                        <p>下のリンクからアクセスください</p>
                        <a href="<?php assign($this->oneTimeBrandUrl) ?>" target="_blank"><p>代理ログインURL</p></a>
                        <a href="<?php assign($this->return_url) ?>" class="btn btn-primary" role="button">戻る</a>
                    </div>
                </div>
                </div>
        </div>
    </div>
</div>

<?php write_html($this->parseTemplate('BrandcoManagerFooter.php')) ?>
