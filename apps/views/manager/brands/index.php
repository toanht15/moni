<?php write_html($this->parseTemplate('BrandcoManagerHeader.php', array(
    'title' => 'ブランド一覧',
    'managerAccount' => $this->managerAccount,
))) ?>

<div class="container-fluid">
    <div class="row">

        <?php write_html($this->parseTemplate('BrandcoManagerMenu.php')); ?>

        <div class="col-md-10 col-md-offset-2 main">
            <h1 class="page-header">ブランド一覧</h1>
            <?php if ( $this->params["mode"] == ManagerService::ADD_FINISH ): ?>
            <div class="alert alert-success">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <p>ブランド名: <b><?php assign($this->brand_name)?></b><br>
                    登録が完了しました。</p>
            </div>
            <?php endif; ?>
            <h4><span class="edit"><a href="#" class="jsMessageSetting">検索</a></span></h4>
            <div class="jsMessageSettingTarget" style="display:none">
                <div class="container">
                    <!-- Brand and toggle get grouped for better mobile display -->
                    <div class="container">
                        <!-- Row start -->
                        <div class="row">
                            <div class="col-md-11 col-sm-6 col-xs-12">
                                <div class="panel panel-default">
                                    <div class="panel-heading clearfix">
                                        <h3 class="panel-title">詳細検索</h3>
                                    </div>
                                    <div class="panel-body">
                                        <form name="brand_list" action="<?php assign(Util::rewriteUrl('brands', 'index', array(), array(), '', true)); ?>" method="GET" class="form-horizontal row-border">
                                            <div class="form-group">
                                                <label class="col-md-2 control-label">ブランド名検索</label>
                                                <div class="col-md-10">
                                                    <p class="form-group">
                                                        <?php write_html($this->formText(
                                                            'search_brand_name',
                                                            PHPParser::ACTION_FORM,
                                                            array('maxlength' => 20)
                                                        ));?>
                                                    </p>
                                                </div>
                                                <label class="col-md-2 control-label">公開状態</label>
                                                <div class="col-md-10">
                                                    <p class="form-group">
                                                        <?php write_html($this->formSelect(
                                                            'access',
                                                            PHPParser::ACTION_FORM,
                                                            array(),
                                                            BrandPageSetting::$select_list_access));?>
                                                    </p>
                                                </div>
                                                <label class="col-md-2 control-label">アカウント</label>
                                                <div class="col-md-10">
                                                    <p class="form-group">
                                                        <?php write_html($this->formSelect(
                                                            'account',
                                                            PHPParser::ACTION_FORM,
                                                            array(),
                                                            Brand::$select_list_account));?>
                                                    </p>
                                                </div>
                                                <label class="col-md-2 control-label">セールス</label>
                                                <div class="col-md-10">
                                                    <p class="form-group">
                                                        <?php write_html($this->formSelect(
                                                            'sales',
                                                            PHPParser::ACTION_FORM,
                                                            array(),
                                                            $this->select_list_manager));?>
                                                    </p>
                                                </div>
                                                <label class="col-md-2 control-label">コンサルタント</label>
                                                <div class="col-md-10">
                                                    <p class="form-group">
                                                        <?php write_html($this->formSelect(
                                                            'consultant',
                                                            PHPParser::ACTION_FORM,
                                                            array(),
                                                            $this->select_list_manager));?>
                                                    </p>
                                                </div>
                                                <label class="col-md-2 control-label">クローズ</label>
                                                <div class="col-md-10">
                                                    <p class="form-group">
                                                        <?php write_html($this->formSelect(
                                                            'delete_status',
                                                            PHPParser::ACTION_FORM,
                                                            array(),
                                                            $this->delete_status_list));?>
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <a href="<?php assign(Util::rewriteUrl('brands', 'index', array(), array(), '', true)); ?>" class="btn btn-primary btn-lg " role="button">条件クリア</a>
                                            </div>
                                            <div class="col-md-2">
                                                <a href="javascript:void(0);" onclick="document.brand_list.submit();return false;" class="btn btn-primary btn-lg btn-block">検索</a>
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
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>ID</th>
                    <th class="col-xs-2">ブランド名</th>
                    <th class="col-xs-1">企業名</th>
                    <th class="col-xs-2">サービスプラン</th>
                    <th>ファン数<br>(前日比)</th>
                    <th class="col-xs-1">担当者</th>
                    <th>公開状態</th>
                    <th>アカウント</th>
                    <th>PR許可</th>
                    <th>作成日</th>
                    <th class="col-xs-1">詳細</th>
                </tr>
                </thead>
                <tbody>

                <?php foreach ($data['list'] as $brand_data):?>
                    <tr>
                        <td>
                            <?php assign($brand_data->id) ?>
                        </td>
                        <td  class="col-xs-2">
                            <?php if($brand_data->hasOption(BrandOptions::OPTION_TOP)):?>
                                <a href="<?php assign(Util::rewriteUrl('dashboard', 'redirect_manager_sso', array(), array('redirect_uri' => $brand_data->getUrl()), '', true))?>" target="_blank"><?php assign($brand_data->name)?></a>
                            <?php else:?>
                                <?php assign($brand_data->name)?>
                            <?php endif;?>
                            <a href="<?php assign(Util::rewriteUrl('dashboard', 'redirect_manager_sso', array(), array('redirect_uri' => $brand_data->getUrl()), '', true))?>my/login" target="_blank"><span class="glyphicon glyphicon-log-in"></span></a><br>
                            <?php assign($brand_data->directory_name)?>
                        </td>
                        <td  class="col-xs-1"><?php assign($brand_data->enterprise_name)?></td>
                        <td  class="col-xs-2"><?php assign(BrandContract::$PLAN_LIST[$brand_data->plan]); ?></td>
                        <td>
                            <?php $fun_count_difference = $brand_data->latest_fun_count - $brand_data->second_fun_count; ?>
                            <?php assign($brand_data->latest_fun_count ? $brand_data->latest_fun_count : 0); ?>(<?php assign($fun_count_difference ? $fun_count_difference : 'ー')?>)
                        </td>
                        <td class="col-xs-1"><?php assign($brand_data->sales_name ? : '指定なし'); ?><br><?php assign($brand_data->consultant_name ? : '指定なし'); ?></td>
                        <td><?php assign( $brand_data->getBrandContracts()->toArray()[0]->getCloseStatus() ? 'クローズ' : BrandPageSetting::$select_list_access[$brand_data->public_flg] ); ?></td>
                        <td><?php assign(Brand::$select_list_account[$brand_data->test_page]); ?></td>

                        <td><?php assign(Brand::$monipla_pr_allow_type_list[$brand_data->monipla_pr_allow_type]); ?></td>

                        <td><?php assign(date('Y-m-d', strtotime($brand_data->created_at)))?></td>
                        <td class="col-xs-1"><a href="<?php assign(Util::rewriteUrl('brands', 'edit_form', array($brand_data->id), array(), '', true)); ?>">編集 </a><br>
                            <a href="<?php assign(Util::rewriteUrl('dashboard', 'brand_contract', array($brand_data->id), array(), '', true)); ?>"> クローズ</a><br>
                            <a href="<?php assign(Util::rewriteUrl('dashboard', 'brand_kpi', array($brand_data->id) ,array(), '', true)); ?>">KPI </a>/
                            <a href="<?php assign(Util::rewriteUrl('dashboard', 'brand_csv', array($brand_data->id), array(), '', true)); ?>"> CSV</a><br>
                            <a href="<?php assign(Util::rewriteUrl('dashboard', 'brand_login_history', array($brand_data->id), array(), '', true)); ?>">ログイン</a></td>
                    </tr>
                <?php endforeach;?>
                </tbody>
            </table>
            <?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoManagerPager')->render(array(
                'TotalCount' => $data['allBrandCount'],
                'CurrentPage' => $this->params['p'],
                'Count' => $data['limit'],
            ))) ?>
            <div class="row">
                <div class="col-xs-5">
                    <form name="brandlist" id="brand_list" action="<?php assign(Util::rewriteUrl('brands', 'index', array(), array(), '', true)); ?>" method="GET">
                        <div class="range">
                            <?php if ($this->params['limit']): ?>
                                <input type="range" name="range" min="1" max="<?php assign($data['allBrandCount']); ?>" value="<?php assign($this->params['limit']); ?>" onchange="$('#range').val($(this).val())">
                                <span>表示件数 : <input name="limit" id="range" value="<?php assign($this->params['limit']); ?>"></span>
                            <?php else : ?>
                                <input type="range" name="range" min="1" max="<?php assign($data['allBrandCount']); ?>" value="20" onchange="$('#range').val($(this).val())">
                                <span>表示件数 : <input name="limit" id="range" value="20"></span>
                            <?php endif ?>
                            <a href="javascript:void(0);" onclick="document.brandlist.submit();return false;" class="btn btn-primary btn-large registrator">　変更　</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div><!-- row -->
</div><!-- container-fluid -->
<?php write_html($this->parseTemplate('BrandcoManagerFooter.php')); ?>
