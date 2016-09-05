<?php write_html($this->parseTemplate('BrandcoManagerHeader.php', array(
	'title' => 'ブランド追加',
    'managerAccount' => $this->managerAccount,
))) ?>
<style>
    .required:before {
        position: absolute;
        content: "*";
        color: red;
        font-weight: 900;
    }
</style>
	<div class="container-fluid">
		<div class="row">
            <?php write_html($this->parseTemplate('BrandcoManagerMenu.php')); ?>

            <form name="add_brand" action="<?php assign(Util::rewriteUrl('dashboard', 'add_brand', array(), array(), '', true)); ?>" method="POST">
			<?php write_html($this->csrf_tag()); ?>

				<div class="col-md-10 col-md-offset-2 main">
					<h1 class="page-header">ブランド追加</h1>
						<?php if ($this->mode == ManagerService::ADD_ERROR ): ?>
							<div class="alert alert-danger">
								登録に失敗しました。再度お試しください。
							</div>
						<?php endif; ?>
                        <?php if ( $this->ActionError && !$this->ActionError->isValid('plan')): ?>
                            <p class="attention1"><?php assign ( $this->ActionError->getMessage('plan') )?></p>
                        <?php endif; ?>

                    <p class="required">契約プラン：</p>
                    <p>※CRMなどの機能調整はブランド追加後に、編集から行ってください</p>
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
                    </p><br>

                    <div class="row">
                        <div class="col-md-5">
                            <p>ブランド情報：</p>※URLは後から変更できません
                            <p class="input-group required">
                                <span class="input-group-addon">http://<?php assign(config('Domain.brandco'))?>/</span>
                                <?php write_html( $this->formText(
                                    'directory_name',
                                    $this->mode == ManagerService::ADD_FINISH || !$this->mode ? null : PHPParser::ACTION_FORM,
                                    array('class' =>'form-control', 'maxlength'=>'255', 'placeholder'=> 'Directory Name')
                                )); ?>
                                <?php if ( $this->ActionError && !$this->ActionError->isValid('directory_name')): ?>
                                    <p class="attention1"><?php assign ( $this->ActionError->getMessage('directory_name') )?></p>
                                <?php endif; ?>
                            </p>
                            <p class="form-group required">
                                <?php write_html( $this->formText(
                                    'brand_name',
                                    $this->mode == ManagerService::ADD_FINISH || !$this->mode ? null : PHPParser::ACTION_FORM,
                                    array('class' =>'form-control', 'maxlength'=>'255', 'placeholder'=> 'ブランド名 (ex. MONIPLAファンサイト)')
                                )); ?>
                                <?php if ( $this->ActionError && !$this->ActionError->isValid('brand_name')): ?>
                                    <p class="attention1"><?php assign ( $this->ActionError->getMessage('brand_name') )?></p>
                                <?php endif; ?>
                            </p>
                            <p class="form-group required" >
                                <?php write_html( $this->formText(
                                    'mail_name',
                                    $this->mode == ManagerService::ADD_FINISH || !$this->mode ? null : PHPParser::ACTION_FORM,
                                    array('class' =>'form-control', 'maxlength'=>'255', 'placeholder'=> 'メールFrom名 (※通常はブランド名と同じ設定にしてください。)')
                                )); ?>
                                <?php if ( $this->ActionError && !$this->ActionError->isValid('mail_name')): ?>
                                    <p class="attention1"><?php assign ( $this->ActionError->getMessage('mail_name') )?></p>
                                <?php endif; ?>
                            </p>
                            <p class="form-group required">
                                <?php write_html( $this->formText(
                                    'enterprise_name',
                                    $this->mode == ManagerService::ADD_FINISH || !$this->mode ? null : PHPParser::ACTION_FORM,
                                    array('class' =>'form-control', 'maxlength'=>'255', 'placeholder'=> '企業名 (ex. アライドアーキテクツ株式会社)')
                                )); ?>
                                <?php if ( $this->ActionError && !$this->ActionError->isValid('enterprise_name')): ?>
                                    <p class="attention1"><?php assign ( $this->ActionError->getMessage('enterprise_name') )?></p>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>

<?php /*
                    <p class="form-group">
                        <?php write_html( $this->formText(
                            'enterprise_id',
                            $this->mode == ManagerService::ADD_FINISH || !$this->mode ? null : PHPParser::ACTION_FORM,
                            array('class' =>'form-control', 'maxlength'=>'255', 'placeholder'=> 'Enterprise Id')
                        )); ?>
                        <?php if ( $this->ActionError && !$this->ActionError->isValid('enterprise_id')): ?>
                            <p class="attention1"><?php assign ( $this->ActionError->getMessage('enterprise_id') )?></p>
                        <?php endif; ?>
                    </p>
                    <p class="form-group">
                        <?php write_html($this->formText(
                            'monipla_enterprise_token',
                            $this->mode == ManagerService::ADD_FINISH || !$this->mode ? null : PHPParser::ACTION_FORM,
                            array('class' => 'form-control', 'maxlength' => '255', 'placeholder' => 'Enterprise Relation Token')
                        )); ?>
                        <?php if ($this->ActionError && !$this->ActionError->isValid('enterprise_relation_token')): ?>
                            <p class="attention1"><?php assign($this->ActionError->getMessage('monipla_enterprise_token')); ?></p>
                        <?php endif; ?>
                    </p>
*/ ?>
                    <p class="required">業種：</p>
                    <p class="form-group">
                        <?php write_html($this->formSelect(
                            'business_category',
                            $this->mode == ManagerService::ADD_FINISH || !$this->mode ? null : PHPParser::ACTION_FORM,
                            array(),
                            $this->brand_business_category_list
                        ));?>
                        <?php if ( $this->ActionError && !$this->ActionError->isValid('business_category')): ?>
                            <p class="attention1"><?php assign ( $this->ActionError->getMessage('business_category') )?></p>
                        <?php endif; ?>
                    </p>

                    <p class="required">企業規模：</p>
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

                    <p class="required">営業担当：</p>
                    <p class="form-group">
                            <?php write_html($this->formSelect(
                            'sales_manager',
                            $this->mode == ManagerService::ADD_FINISH || !$this->mode ? null : PHPParser::ACTION_FORM,
                            array(),$this->manager_list));?>
                            <?php if ( $this->ActionError && !$this->ActionError->isValid('sales_manager')): ?>
                                <p class="attention1"><?php assign ( $this->ActionError->getMessage('sales_manager') )?></p>
                            <?php endif; ?>
                    </p>
                    <p class="required">運用担当：</p>
                    <p class="form-group">
                        <?php write_html($this->formSelect(
                            'consultants_manager',
                            $this->mode == ManagerService::ADD_FINISH || !$this->mode ? null : PHPParser::ACTION_FORM,
                            array(), $this->manager_list));?>
                        <?php if ( $this->ActionError && !$this->ActionError->isValid('consultants_manager')): ?>
                            <p class="attention1"><?php assign ( $this->ActionError->getMessage('consultants_manager') )?></p>
                        <?php endif; ?>
                    </p>

                    <p class="required">運用主体：</p>
                    <p class="form-group">
                        <?php write_html($this->formRadio(
                            'operation',
                            PHPParser::ACTION_FORM,
                            array(),
                            $this->operation_list
                        )); ?>
                        <?php if ( $this->ActionError && !$this->ActionError->isValid('operation')): ?>
                            <p class="attention1"><?php assign ( $this->ActionError->getMessage('operation') )?></p>
                        <?php endif; ?>
                    </p>

                    <div class="row">
                        <div class="col-md-7">
                            <p>管理情報：</p>
                            <table class="table table-bordered">
                                <thead>
                                <tr class="jumbotron">
                                    <th class="required">SFID</th>
                                    <th class="required">利用期間</th>
                                </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td width="300">
                                            <?php write_html( $this->formText(
                                                'salesforce_url',
                                                $this->mode == ManagerService::ADD_FINISH || !$this->mode ? null : PHPParser::ACTION_FORM,
                                                array('class' =>'form-control', 'maxlength'=>'255', 'placeholder'=> 'https://ap.saledforce.com/xxxxxxx')
                                            )); ?>
                                            <?php if ( $this->ActionError && !$this->ActionError->isValid('salesforce_url')): ?>
                                                <p class="attention1"><?php assign ( $this->ActionError->getMessage('salesforce_url') )?></p>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php write_html($this->formText(
                                                'start_date',
                                                PHPParser::ACTION_FORM,
                                                array('style' => 'width:100px;', 'maxlength' => '10', 'class' => 'jsDate inputDate', 'placeholder' => '年-月-日'))); ?>
                                            ～
                                            <?php write_html($this->formText(
                                                'end_date',
                                                PHPParser::ACTION_FORM,
                                                array('style' => 'width:100px;', 'maxlength' => '10', 'class' => 'jsDate inputDate', 'placeholder' => '年-月-日'))); ?><br>
                                            <?php if ( $this->ActionError && !( $this->ActionError->isValid('start_date') && $this->ActionError->isValid('end_date') ) ): ?>
                                                <p class="attention1"><?php assign ( $this->ActionError->getMessage('start_date') ?: $this->ActionError->getMessage('end_date'))?></p>
                                            <?php endif; ?>
                                            <?php if ( $this->ActionError && !$this->ActionError->isValid('date_range')): ?>
                                                <p class="attention1"><?php assign ( str_replace(array('<%time1>','<%time2>'),array('利用終了日時','利用開始日時'),$this->ActionError->getMessage('date_range')) )?></p>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <p class="required">問い合わせ対応：</p>
                    <p>
                        <?php write_html($this->formRadio( 'aa_alert_flg', $this->aa_alert_flg[0], array(), array('0' => 'クライアントが行う', '1' => 'アライドが行う'))); ?>
                    </p>

                    <p class="required">ブランド利用目的：</p>
                    <p>
                        <?php write_html($this->formRadio( 'test_page', PHPParser::ACTION_FORM, array(),array('0' => '企業用', '1' => 'テスト用(BASIC認証あり)'))); ?>
                    </p>

                    <p class="input-group">備考：</p>
                    <p class="form-group">
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

                    <p>
                        <a href="javascript:void(0);" onclick="document.add_brand.submit();return false;" class="btn btn-primary btn-large registrator">　　追加　　</a>
                    </p>
				</div>
			</form>
		</div><!-- row -->
	</div><!-- container-fluid -->

<script src="<?php assign($this->setVersion('/manager/js/services/CampaignListService.js'))?>"></script>

<?php write_html($this->parseTemplate('BrandcoManagerFooter.php')); ?>
