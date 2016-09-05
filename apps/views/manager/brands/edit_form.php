<?php write_html($this->parseTemplate('BrandcoManagerHeader.php', array( 'title' => 'Brand detail', 'managerAccount' => $this->managerAccount, ))) ?>

<style xmlns="http://www.w3.org/1999/html">
    .required:after {
        content: "*";
        color: red;
        font-weight: 900;
    }
</style>
<?php $is_super_user = $this->managerAccount->isSuperUser() ?>
<?php $invalidInputClass = $this->invalidInputData ? 'invalid-input' : ""; ?>

<div class="container-fluid">
    <div class="row">
        <?php write_html($this->parseTemplate('BrandcoManagerMenu.php')); ?>
        <div class="col-md-10 col-md-offset-2 main">
            <ol class="breadcrumb">
                <li><a href="<?php assign(Util::rewriteUrl( 'brands', 'index', array(), array(), '', true)); ?>">ブランド一覧</a></li>
                <li class="active"><?php assign($data['brand']->name);?></a></li>
            </ol>

            <h1 class="page-header"><?php assign($data['brand']->name . ' / ' . $data['brand']->enterprise_name);?></h1>
            <?php if ( $this->mode == ManagerService::ADD_FINISH ): ?>
                <div class="alert alert-success">
                    更新が完了しました。
                </div>
            <?php elseif ($this->mode == ManagerService::ADD_ERROR ): ?>
                <div class="alert alert-danger">
                    更新に失敗しました。再度お試しください。
                </div>
            <?php endif; ?>

            <div class="well col-md-10 col-md-offset-0">
                <ul class="nav nav-tabs">
                    <li role="presentation" class="active"><a href="#">基本設定</a></li>
                    <li role="presentation">
                        <a href="<?php assign(Util::rewriteUrl('brands', 'edit_sns_form', array($data['brand']->id, array(), '', true) )); ?>">
                        SNSアカウント連携
                        </a>
                    </li>
                </ul>

                <form name="edit" action="<?php assign(Util::rewriteUrl( 'brands', 'edit', array(), array(), '', true )); ?>" method="POST">
                    <?php write_html($this->formHidden('id', $data['brand']->id))?>
                    <?php write_html($this->csrf_tag()); ?>

                    </br>
                    <p class="required">契約プラン:</p>
                    <p class="form-group">
                        <?php write_html($this->formRadio(
                            'plan',
                            PHPParser::ACTION_FORM,
                            array(),
                            $this->plan_list
                        )); ?>
                        <?php if ( $this->ActionError && !$this->ActionError->isValid('plan') ): ?>
                            <p class="attention1"><?php assign ( $this->ActionError->getMessage('plan') )?></p>
                        <?php endif; ?>
                    </p></br>

                    <p>ブランド情報：</p>
                    <table class="table-basic">
                        <tbody>
                        <tr>
                            <th class="required">URL</th>
                            <td class="td-basic">
                                <a href="<?php assign(Util::constructBaseURL($data['brand']->id, $this->brand->directory_name, true)); ?>" target="_blank"><?php assign(Util::constructBaseURL($data['brand']->id, $this->brand->directory_name, true))?></a>
                            </td>
                        </tr>
                        <tr>
                            <th class="required">ブランド名</th>
                            <td>
                                <?php write_html( $this->formText(
                                    'name',
                                    PHPParser::ACTION_FORM,
                                    array('class' =>'form-control', 'maxlength'=>'255', 'placeholder'=> 'ブランド名 (ex. MONIPLAファンサイト)')
                                ) ); ?>
                            </td>
                        </tr>
                        <?php if ( $this->ActionError && !$this->ActionError->isValid('name')): ?>
                            <tr>
                                <td colspan="2"><p class="attention1">ブランド名は<?php assign ( $this->ActionError->getMessage('name') )?></p></td>
                            </tr>
                        <?php endif; ?>
                        <tr>
                            <th class="required">メールFrom名</th>
                            <td>
                                <?php write_html( $this->formText(
                                    'mail_name',
                                    PHPParser::ACTION_FORM,
                                    array('class' =>'form-control', 'maxlength'=>'255', 'placeholder'=> 'メールFrom名 (※通常はブランド名と同じ設定にしてください。)')
                                ) ); ?>
                            </td>
                        </tr>
                        <?php if ( $this->ActionError && !$this->ActionError->isValid('mail_name')): ?>
                            <tr>
                                <td colspan="2"><p class="attention1">メールFrom名は<?php assign ( $this->ActionError->getMessage('mail_name') )?></p></td>
                            </tr>
                        <?php endif; ?>
                        <tr>
                            <th class="required">企業名</th>
                            <td>
                                <?php write_html( $this->formText(
                                    'enterprise_name',
                                    PHPParser::ACTION_FORM,
                                    array('class' =>'form-control', 'maxlength'=>'255', 'placeholder'=> '企業名 (ex. アライドアーキテクツ株式会社)')
                                ) ); ?>
                            </td>
                        </tr>
                        <?php if ( $this->ActionError && !$this->ActionError->isValid('enterprise_name')): ?>
                            <tr>
                                <td colspan="2"><p class="attention1">企業名は<?php assign ( $this->ActionError->getMessage('enterprise_name') )?></p></td>
                            </tr>
                        <?php endif; ?>

                        </tbody>
                    </table>

                    <p class="required">業種:</p>
                    <p class="form-group">
                        <?php write_html($this->formSelect(
                            'business_category',
                            PHPParser::ACTION_FORM,
                            array(),
                            $this->brand_business_category_list
                        ));?>
                        <?php if ( $this->ActionError && !$this->ActionError->isValid('business_category')): ?>
                            <p class="attention1"><?php assign ( $this->ActionError->getMessage('business_category') )?></p>
                        <?php endif; ?>
                    </p>

                    <p class="required">企業規模:</p>
                    <p class="form-group">
                        <?php write_html($this->formRadio(
                            'business_size',
                            PHPParser::ACTION_FORM,
                            array(),
                            $this->brand_business_size_list
                        )); ?>
                        <?php if ( $this->ActionError && !$this->ActionError->isValid('business_size')): ?>
                            <p class="attention1"><?php assign ( $this->ActionError->getMessage('business_size') )?></p>
                        <?php endif; ?>
                    </p>
                    <?php if($data['managerAccount']->authority === '1'): ?>
                    <p class="required">PR許可:</p>
                    <p class="form-group">
                        <?php write_html($this->formSelect(
                            'monipla_pr_allow_type',
                            PHPParser::ACTION_FORM,
                            array(), $this->monipla_pr_allow_type_list));?>
                    </p>
                    <?php else: ?>
                        <?php write_html($this->formHidden('monipla_pr_allow_type', PHPParser::ACTION_FORM));?>
                    <?php endif; ?>
                    <p class="required">営業担当:</p>
                    <p class="form-group">
                        <?php write_html($this->formSelect(
                            'sales_manager_id',
                            PHPParser::ACTION_FORM,
                            array(),$this->manager_list));?>
                    </p>
                    <p class="required">運用担当:</p>
                    <p class="form-group">
                        <?php write_html($this->formSelect(
                            'consultants_manager_id',
                            PHPParser::ACTION_FORM,
                            array(), $this->manager_list));?>
                    </p>

                    <p class="required">運用主体:</p>
                    <p class="form-group">
                        <?php write_html($this->formRadio(
                            'operation',
                            PHPParser::ACTION_FORM,
                            array(),
                            $this->operation_list
                        ));?>
                        <?php if ( $this->ActionError && !$this->ActionError->isValid('operation')): ?>
                            <p class="attention1"><?php assign ( $this->ActionError->getMessage('operation') )?></p>
                        <?php endif; ?>
                    </p>

                    <p>管理情報：</p>
                    <table class="table table-bordered">
                        <thead>
                        <tr class="jumbotron">
                            <th class="required">SFID</th>
                            <th class="required">利用期間</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if ($this->salesforceCount): ?>
                            <?php foreach($this->salesforces as $salesforce): ?>
                                <?php write_html($this->formHidden('salesforce_id_'.$salesforce->id, $salesforce->id));?>
                                <tr>
                                    <td>
                                        <?php write_html( $this->formText(
                                            'salesforce_url_'.$salesforce->id,
                                            PHPParser::ACTION_FORM,
                                            array('class' =>'form-control', 'maxlength'=>'255', 'placeholder'=> 'https://ap.saledforce.com/xxxxxxx')
                                        )); ?>
                                        <?php if ( $this->ActionError && !$this->ActionError->isValid('salesforce_url_'.$salesforce->id)): ?>
                                            <p class="attention1"><?php assign ( $this->ActionError->getMessage('salesforce_url_'.$salesforce->id) )?></p>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php write_html($this->formText(
                                            'start_date_'.$salesforce->id,
                                            PHPParser::ACTION_FORM,
                                            array('style' => 'width:100px;', 'maxlength' => '10', 'class' => 'jsDate inputDate', 'placeholder' => '年-月-日')
                                        )); ?>
                                        ～
                                        <?php write_html($this->formText(
                                            'end_date_'.$salesforce->id,
                                            PHPParser::ACTION_FORM,
                                            array('style' => 'width:100px;', 'maxlength' => '10', 'class' => 'jsDate inputDate', 'placeholder' => '年-月-日')
                                        )); ?><br>
                                        <?php if ( $this->ActionError && !( $this->ActionError->isValid('start_date_'.$salesforce->id) && $this->ActionError->isValid('end_date_'.$salesforce->id) ) ): ?>
                                            <p class="attention1"><?php assign ( $this->ActionError->getMessage('start_date_'.$salesforce->id) ?: $this->ActionError->getMessage('end_date_'.$salesforce->id))?></p>
                                        <?php endif; ?>
                                        <?php if ( $this->ActionError && !$this->ActionError->isValid('date_range_'.$salesforce->id)): ?>
                                            <p class="attention1"><?php assign ( str_replace(array('<%time1>','<%time2>'),array('利用終了日時','利用開始日時'),$this->ActionError->getMessage('date_range_'.$salesforce->id)) )?></p>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <?php write_html($this->formHidden('salesforce_id_new', 'new'))?>
                            <tr>
                                <td>
                                    <?php write_html($this->formText(
                                        'salesforce_url_new',
                                        PHPParser::ACTION_FORM,
                                        array('class' =>'form-control', 'maxlength'=>'255', 'placeholder'=> 'https://ap.saledforce.com/xxxxxxx')
                                    )); ?>
                                    <?php if ( $this->ActionError && !$this->ActionError->isValid('salesforce_url_new') ): ?>
                                        <p class="attention1"><?php assign ( $this->ActionError->getMessage('salesforce_url_new') )?></p>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php write_html($this->formText(
                                        'start_date_new',
                                        PHPParser::ACTION_FORM,
                                        array('style' => 'width:100px;', 'maxlength' => '10', 'class' => 'jsDate inputDate', 'placeholder' => '年-月-日')
                                    )); ?>
                                    〜
                                    <?php write_html($this->formText(
                                        'end_date_new',
                                        PHPParser::ACTION_FORM,
                                        array('style' => 'width:100px;', 'maxlength' => '10', 'class' => 'jsDate inputDate', 'placeholder' => '年-月-日')
                                    )); ?><br>
                                    <?php if ( $this->ActionError && !( $this->ActionError->isValid('start_date_new') && $this->ActionError->isValid('end_date_new') ) ): ?>
                                        <p class="attention1"><?php assign ( $this->ActionError->getMessage('start_date_new') ?: $this->ActionError->getMessage('end_date_new') )?></p>
                                    <?php endif; ?>
                                    <?php if ( $this->ActionError && !$this->ActionError->isValid('date_range_new')): ?>
                                        <p class="attention1"><?php assign ( str_replace(array('<%time1>','<%time2>'),array('利用終了日時','利用開始日時'),$this->ActionError->getMessage('date_range_new')) )?></p>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                    <?php if ($this->salesforceCount): ?>
                        <p class="text-right"><a onclick="addNewForm('salesforce_id_', 'new');">[+]追加</a></p>
                        <div id="salesforce_id_new" <?php if (!$this->haveErrorsForNewSalesforceForm) assign('style=display:none');?>>
                            <table class="table table-striped table-bordered table-hover">
                                <?php write_html($this->formHidden('salesforce_id_new', 'new'))?>
                                <tr>
                                    <td>
                                        <?php write_html($this->formText(
                                            'salesforce_url_new',
                                            PHPParser::ACTION_FORM,
                                            array('class' =>'form-control', 'maxlength'=>'255', 'placeholder'=> 'https://ap.saledforce.com/xxxxxxx')
                                        )); ?>
                                        <?php if ( $this->ActionError && !$this->ActionError->isValid('salesforce_url_new') ): ?>
                                            <p class="attention1"><?php assign ( $this->ActionError->getMessage('salesforce_url_new') )?></p>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php write_html($this->formText(
                                            'start_date_new',
                                            PHPParser::ACTION_FORM,
                                            array('style' => 'width:100px;', 'maxlength' => '10', 'class' => 'jsDate inputDate', 'placeholder' => '年-月-日')
                                        )); ?>
                                        〜
                                        <?php write_html($this->formText(
                                            'end_date_new',
                                            PHPParser::ACTION_FORM,
                                            array('style' => 'width:100px;', 'maxlength' => '10', 'class' => 'jsDate inputDate', 'placeholder' => '年-月-日')
                                        )); ?><br>
                                        <?php if ( $this->ActionError && !( $this->ActionError->isValid('start_date_new') && $this->ActionError->isValid('end_date_new') ) ): ?>
                                            <p class="attention1"><?php assign ( $this->ActionError->getMessage('start_date_new') ?: $this->ActionError->getMessage('end_date_new') )?></p>
                                        <?php endif; ?>
                                        <?php if ( $this->ActionError && !$this->ActionError->isValid('date_range_new')): ?>
                                            <p class="attention1"><?php assign ( str_replace(array('<%time1>','<%time2>'),array('利用終了日時','利用開始日時'),$this->ActionError->getMessage('date_range_new')) )?></p>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    <?php endif; ?>

                    <p class="required">問い合わせ対応：</p>
                    <p>
                        <?php write_html($this->formRadio( 'aa_alert_flg', $this->aa_alert_flg[0], array(),array('0' => 'クライアントが行う', '1' => 'アライドが行う'))); ?>
                    </p>

                    <p class="required">ブランド利用目的：</p>
                    <p>
                        <?php write_html($this->formRadio( 'test_page', PHPParser::ACTION_FORM, array('class' => 'testPage'),array('0' => '企業用', '1' => 'テストページ (BASIC認証)'))); ?>
                    </p>

                    <div class="panel panel-default" id="basicAuthDiv" style="margin-bottom: 15px; <?php if (!$this->ActionForm['test_page']): ?>display: none;<?php endif; ?>">
                        <div class="panel-heading clearfix">
                            <h4 class="panel-title required">BASIC認証</h4>
                        </div>
                        <div class="panel-body">
                            <div id="custom-toolbar">
                                <div class="form-inline" role="form">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <div class="input-group-addon">ID</div>
                                            <?php write_html( $this->formText(
                                                'test_id',
                                                PHPParser::ACTION_FORM,
                                                array('class' =>'form-control ' . $invalidInputClass, 'maxlength'=>'30', 'placeholder'=> 'Id')
                                            )); ?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="input-group">
                                            <div class="input-group-addon">Password</div>
                                            <?php write_html( $this->formText(
                                                'test_pass',
                                                PHPParser::ACTION_FORM,
                                                array('class' =>'form-control ' . $invalidInputClass, 'maxlength'=>'30', 'placeholder'=> 'Password')
                                            )); ?>
                                        </div>
                                    </div>

                                    <?php if ($this->ActionError && !$this->ActionError->isValid('invalid_test_id')): ?>
                                        <p class="attention1" style="margin: 0px"><?php assign($this->ActionError->getMessage('invalid_test_id')); ?></p>
                                    <?php endif; ?>
                                    <?php if ($this->ActionError && !$this->ActionError->isValid('invalid_test_pass')): ?>
                                        <p class="attention1" style="margin: 0px"><?php assign($this->ActionError->getMessage('invalid_test_pass')); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if ($this->managerAccount->authority != Manager::AGENT): ?>
                        <p>権限:</p>

                        <table class="table table-striped table-bordered table-hover">
                            <thead>
                            <tr class="jumbotron">
                                <th width="15%"></th>
                                <?php foreach(BrandOptions::$OPTION_LIST as $optionKey => $optionLabel):?>
                                    <th width="8%"><?php assign($optionLabel);?></th>
                                <?php endforeach;?>
                            </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>現在の権限</td>
                                    <?php foreach(BrandOptions::$OPTION_LIST as $optionStatusKey => $optionStatusValue): ?>
                                        <?php if($optionStatusKey == BrandOptions::OPTION_CP): ?>
                                            <?php write_html($this->formHidden('brand_options[]', BrandOptions::OPTION_CP)); ?>
                                            <td>
                                                <?php write_html('<span class="glyphicon glyphicon-ok"></span>'); ?>
                                            </td>
                                            <?php continue; ?>
                                        <?php endif; ?>
                                        <td>
                                            <input type="checkbox" name="brand_options[]" value="<?php assign($optionStatusKey); ?>"
                                                <?php if ($optionStatusKey == BrandOptions::OPTION_SEGMENT): ?>class="jsSegmentLimitCheckBox"<?php endif ?>
                                                <?php if($this->brand->hasOption($optionStatusKey)) assign('checked="checked"'); ?>>
                                        </td>
                                    <?php endforeach; ?>
                                </tr>
                            </tbody>
                        </table>

                        <div class="panel panel-default" id="segment_limit_form"
                             style="margin-bottom: 15px; <?php if (!$this->brand->hasOption(BrandOptions::OPTION_SEGMENT)): ?>display: none;<?php endif; ?>">
                            <div class="panel-heading clearfix">
                                <h4 class="panel-title required">セグメント上限設定</h4>
                            </div>
                            <?php $formText_attr = array('class' => 'form-control ' . $invalidInputClass, 'maxlength' => '30'); ?>
                            <?php if(!$is_super_user) $formText_attr['disabled'] = '1'; ?>
                            <?php write_html($this->formHidden('is_super_user', $is_super_user ? '1' : '')); ?>

                            <div class="panel-body">
                                <div id="custom-toolbar">
                                    <div class="form-inline" role="form">
                                        <div class="form-group">
                                            <div class="input-group">
                                                <div class="input-group-addon">セグメントグループ</div>
                                                <?php write_html($this->formText(
                                                    'segment_group_limit',
                                                    PHPParser::ACTION_FORM,
                                                    $formText_attr
                                                )); ?>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="input-group">
                                                <div class="input-group-addon">条件セグメント</div>
                                                <?php write_html($this->formText(
                                                    'conditional_segment_limit',
                                                    PHPParser::ACTION_FORM,
                                                    $formText_attr
                                                )); ?>
                                            </div>
                                        </div>

                                        <?php if ($this->ActionError && !$this->ActionError->isValid('segment_group_limit')): ?>
                                            <p class="attention1"
                                               style="margin: 0px"><?php assign($this->ActionError->getMessage('segment_group_limit')); ?></p>
                                        <?php endif; ?>
                                        <?php if ($this->ActionError && !$this->ActionError->isValid('conditional_segment_limit')): ?>
                                            <p class="attention1"
                                               style="margin: 0px"><?php assign($this->ActionError->getMessage('conditional_segment_limit')); ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <p>
                            <small>(参考) 各プランで初期に設定される権限一覧: </small>
                            <small>権限を開放したい場合は、↑のフォームから選択してください。</small>
                        </p>

                        <table class="table table-striped table-bordered table-hover">
                            <tbody>
                            <?php foreach($this->plan_list as $key => $serviceLabel): ?>
                                <tr>
                                    <td width="15%">
                                        <?php assign($serviceLabel);?>
                                    </td>
                                    <?php foreach(BrandOptions::$SERVICE_OPTIONS[$key] as $optionStatusKey => $optionStatusValue):?>
                                        <td width="8%">
                                            <?php write_html($optionStatusValue == BrandOptions::ON ? '<span class="glyphicon glyphicon-ok"></span>' : '');?>
                                        </td>
                                    <?php endforeach;?>
                                </tr>
                            <?php endforeach;?>
                            </tbody>
                        </table>
                    <?php endif; ?>

                    <p>備考:</p>
                    <p>
                        <?php write_html($this->formTextArea(
                            'memo',
                            PHPParser::ACTION_FORM,
                            array('cols'=>50, 'rows'=>10, 'maxlength'=>2000)
                        )); ?>
                        <?php if ( $this->ActionError && !$this->ActionError->isValid('memo')): ?>
                            <p class="attention1"><?php assign ( $this->ActionError->getMessage('memo') )?></p>
                        <?php endif; ?>
                    </p>

                    <p class="required">本番用として使う予定のアカウントか：<br><small>※データ管理用で、この設定はシステム的な動作に影響しません。</small></p>
                    <p class="form-group">
                        <?php write_html($this->formRadio(
                            'for_production_flg',
                            PHPParser::ACTION_FORM,
                            array(),
                            $this->for_production_flg_list
                        )); ?>
                        <?php if ( $this->ActionError && !$this->ActionError->isValid('for_production_flg')): ?>
                            <p class="attention1"><?php assign ( $this->ActionError->getMessage('for_production_flg') )?></p>
                        <?php endif; ?>
                    </p>

                </form>

                <p><a href="javascript:void(0);" onclick="document.edit.submit();return false;" class="btn btn-primary">更新</a></p>
            </div>
        </div>
    </div><!-- row -->
</div><!-- container-fluid -->

<script src="<?php assign($this->setVersion('/manager/js/services/CampaignListService.js'))?>"></script>
<script>
    function addNewForm(id, no) {
        document.getElementById(id + no).style.display = 'block';
    }
</script>
<?php write_html($this->parseTemplate('BrandcoManagerFooter.php')); ?>
