<?php write_html($this->parseTemplate('BrandcoManagerHeader.php', array(
    'title' => '月額ブランド管理',
    'managerAccount' => $this->managerAccount,
))) ?>

<div class="container-fluid">
    <div class="row">
        <?php write_html($this->parseTemplate('BrandcoManagerMenu.php')); ?>
        <div class="col-md-10 col-md-offset-2 main">
            <h1 class="page-header">月額ブランド管理</h1>
            <h4><span class="edit"><a href="#" class="jsMessageSetting">検索</a></span></h4>
            <div class="jsMessageSettingTarget" style="display: none">
                <div class="container">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-11 col-sm-6 col-xs-12">
                                <div class="panel panel-default">
                                    <div class="panel-heading clearfix">
                                        <h3 class="panel-title">検索</h3>
                                    </div>
                                    <div class="panel-body">
                                        <form name="brand_mgr"
                                              action="<?php assign(Util::rewriteUrl('dashboard', 'brand_mgr', array(), array(), '', true)); ?>"
                                              method="GET" class="form-horizontal row-border">
                                            <div class="form-group">
                                                <label class="col-md-2 control-label">営業担当</label>
                                                <div class="col-md-10">
                                                    <p class="form-group">
                                                        <?php write_html($this->formSelect(
                                                            'sales',
                                                            $data['sales'],
                                                            array(),
                                                            $this->select_list_manager)); ?>
                                                    </p>
                                                </div>
                                                <label class="col-md-2 control-label">運用担当</label>
                                                <div class="col-md-10">
                                                    <p class="form-group">
                                                        <?php write_html($this->formSelect(
                                                            'consultant',
                                                            $data['consultant'],
                                                            array(),
                                                            $this->select_list_manager)); ?>
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <a href="javascript:void(0);"
                                                   onclick="document.brand_mgr.submit();return false;"
                                                   class="btn btn-primary btn-lg btn-block">検索</a>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoManagerPager')->render(array(
                'TotalCount' => $data['allBrandCount'],
                'CurrentPage' => $this->params['p'],
                'Count' => $data['limit'],
            ))) ?>

            <table class="table table-bordered">
                <thead>
                <tr>
                    <th class="col-xs-1" rowspan="2" style="text-align:center">ID</th>
                    <th class="col-xs-2" rowspan="2" style="text-align:center">ブランド</th>
                    <th class="col-xs-1" rowspan="2" style="text-align:center">最終<br>ログイン</th>
                    <th class="col-xs-3" colspan="3" style="text-align:center">公開中(件)</th>
                    <th class="col-xs-5" colspan="7" style="text-align:center">キャンペーン状態</th>
                </tr>
                <tr>
                    <th class="col-xs-1" style="text-align:center"><?= $data['two_month_ago'] ?></th>
                    <th class="col-xs-1" style="text-align:center"><?= $data['last_month'] ?></th>
                    <th class="col-xs-1" style="text-align:center"><?= $data['current_month'] ?></th>
                    <th>下書き</th>
                    <th>公開予約</th>
                    <th>デモ公開</th>
                    <th>公開中</th>
                    <th>発表待ち</th>
                    <th>終了</th>
                    <th>クローズ</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($data['brand_list'] as $brand_info): ?>
                    <tr style="text-align: center">
                        <td><?= $brand_info['brand_id'] ?></td>
                        <td style="text-align: left">
                            <?php if($brand_info['has_option']):?>
                                <a href="<?php assign(Util::rewriteUrl('dashboard', 'redirect_manager_sso', array(), array('redirect_uri' => $brand_info['brand_url']), '', true))?>" target="_blank"><?php assign($brand_info['brand_name'])?></a>
                            <?php else:?>
                                <?php assign($brand_info['brand_name'])?>
                            <?php endif;?>
                            <a href="<?php assign(Util::rewriteUrl('dashboard', 'redirect_manager_sso', array(), array('redirect_uri' => $brand_info['brand_url']), '', true))?>my/login" target="_blank"><span class="glyphicon glyphicon-log-in"></span></a><br>
                            <?php assign($brand_info['directory_name'])?>
                        </td>
                        <td><?= $brand_info['last_login'] ?></td>
                        <td><?= $brand_info['two_month_ago_open_cp_count'] ?></td>
                        <td><?= $brand_info['last_month_open_cp_count'] ?></td>
                        <td><?= $brand_info['current_month_open_cp_count'] ?></td>
                        <td><?= $brand_info['current_month_draft_cp_count'] ?></td>
                        <td><?= $brand_info['cp_status_schedule'] ?></td>
                        <td><?= $brand_info['cp_status_demo'] ?></td>
                        <td><?= $brand_info['cp_status_open'] ?></td>
                        <td><?= $brand_info['cp_status_wait_announce'] ?></td>
                        <td><?= $brand_info['cp_status_close'] ?></td>
                        <td><?= $brand_info['cp_status_cp_page_close'] ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>

            <?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoManagerPager')->render(array(
                'TotalCount' => $data['allBrandCount'],
                'CurrentPage' => $this->params['p'],
                'Count' => $data['limit'],
            ))) ?>

            <div class="row">
                <div class="col-xs-5">
                    <form name="brandmgr" id="brand_list"
                          action="<?php assign(Util::rewriteUrl('dashboard', 'brand_mgr', array(), array(), '', true)); ?>"
                          method="GET">
                        <div class="range">
                            <?php if ($this->params['limit']): ?>
                                <input type="range" name="range" min="1"
                                       max="<?php assign($data['allBrandCount']); ?>"
                                       value="<?php assign($this->params['limit']); ?>"
                                       onchange="$('#range').val($(this).val())">
                                <span>表示件数 : <input name="limit" id="range"
                                                    value="<?php assign($this->params['limit']); ?>"></span>
                            <?php else : ?>
                                <input type="range" name="range" min="1"
                                       max="<?php assign($data['allBrandCount']); ?>" value="20"
                                       onchange="$('#range').val($(this).val())">
                                <span>表示件数 : <input name="limit" id="range" value="20"></span>
                            <?php endif ?>
                            <a href="javascript:void(0);" onclick="document.brandmgr.submit();return false;"
                               class="btn btn-primary btn-large registrator">　変更　</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php write_html($this->parseTemplate('BrandcoManagerFooter.php')); ?>