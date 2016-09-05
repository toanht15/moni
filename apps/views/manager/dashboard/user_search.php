<?php write_html($this->parseTemplate('BrandcoManagerHeader.php', array(
    'title' => 'ユーザー検索',
    'managerAccount' => $this->managerAccount,
))) ?>
<script type="text/javascript" src="<?php assign($this->setVersion('/manager/js/typeahead.min.js'))?>"></script>
<div class="container-fluid">
    <div class="row">
        <?php write_html($this->parseTemplate('BrandcoManagerMenu.php')); ?>
        <form name="frmUserSearch" action="<?php assign(Util::rewriteUrl( 'dashboard', 'user_search', array(), array(), '', true )); ?>" method="GET" class="form-horizontal row-border">
            <div class="col-md-15 col-md-offset-2 main">
                <h1 class="page-header">ユーザー検索</h1>
                <div class="col-md-3 col-md-offset-0">
                    <p>検索方法</p>
                    <select name="search_type" id="search_type" onchange="this.form.submit()">
                        <option value="0"><?php assign("-")?></option>
                        <option value="<?php assign(UserSearchService::USER_SEARCH_PLATFORM_ID) ?>" <?php if($this->search == UserSearchService::USER_SEARCH_PLATFORM_ID) assign("selected") ?>>Platform ID検索</option>
                        <option value="<?php assign(UserSearchService::USER_SEARCH_BRANDCO_ID); ?>"<?php if($this->search == UserSearchService::USER_SEARCH_BRANDCO_ID) assign("selected") ?>>BRANDCo UID 検索</option>
                        <option value="<?php assign(UserSearchService::USER_SEARCH_SNS); ?>"<?php if($this->search == UserSearchService::USER_SEARCH_SNS) assign("selected") ?>>SNS 検索</option>
                        <option value="<?php assign(UserSearchService::USER_SEARCH_AA_MAIL); ?>"<?php if($this->search == UserSearchService::USER_SEARCH_AA_MAIL) assign("selected") ?>>Allied IDメールアドレス検索</option>
                        <option value="<?php assign(UserSearchService::USER_SEARCH_BRAND_MAIL); ?>"<?php if($this->search == UserSearchService::USER_SEARCH_BRAND_MAIL) assign("selected") ?>>BRANDCo メールアドレス検索</option>
                        <option value="<?php assign(UserSearchService::USER_SEARCH_BRAND); ?>"<?php if($this->search == UserSearchService::USER_SEARCH_BRAND) assign("selected") ?>>ブランド会員番号検索</option>
                    </select>
                    </div>

                <div class="col-md-5 col-md-offset-0">
                    <?php if ($this->search == UserSearchService::USER_SEARCH_PLATFORM_ID):?>
                        <div class="form-group">
                            <p>Platform ID 検索:</p>
                            <?php write_html( $this->formText(
                                'platform_id',PHPParser::ACTION_FORM,
                                array('class' =>'form-control', 'maxlength'=>'255', 'placeholder'=> 'Platform ID 検索')
                            )); ?>
                        </div>
                        <a href="" onclick="document.frmUserSearch.submit();return false;"><button class="btn btn-primary btn-large">Search</button></a>
                        <?php endif ?>
                </div>

                <div class="col-md-5 col-md-offset-0">
                    <?php if ($this->search == UserSearchService::USER_SEARCH_BRANDCO_ID):?>
                            <div class="form-group">
                                <p>BRANDCo UID 検索:</p>
                                <?php write_html( $this->formText(
                                    'brandco_uid',PHPParser::ACTION_FORM,
                                    array('class' =>'form-control', 'maxlength'=>'255', 'placeholder'=> 'BRANDCo UID 検索')
                                )); ?>
                                </div>
                        <a href="" onclick="document.frmUserSearch.submit();return false;"><button class="btn btn-primary btn-large">Search</button></a>
                    <?php endif ?>
                </div>
                </div>

                <div class="col-md-5 col-md-offset-0">
                    <?php if ($this->search == UserSearchService::USER_SEARCH_SNS):?>
                                <p>SNS 検索:  <?php write_html($this->formSelect( 'sns', PHPParser::ACTION_FORM, array(),$this->sns_type)); ?>
                                    <?php write_html( $this->formText(
                                        'sns_id',
                                        PHPParser::ACTION_FORM,
                                        array('maxlength'=>'255', 'placeholder'=> 'SNS 検索')
                                    )); ?>
                                </p>
                        <a href="" onclick="document.frmUserSearch.submit();return false;"><button class="btn btn-primary btn-large">Search</button></a>
                    <?php endif ?>
                </div>

                    <div class="col-md-5 col-md-offset-0">
                        <?php if ($this->search == UserSearchService::USER_SEARCH_AA_MAIL):?>
                        <div class="form-group">
                            <p>Allied IDメールアドレス検索 :</p>
                            <?php write_html( $this->formText(
                                'allied_mail_address',PHPParser::ACTION_FORM,
                                array('class' =>'form-control', 'maxlength'=>'255', 'placeholder'=> 'メールアドレス検索')
                            )); ?>
                        </div>
                    <a href="" onclick="document.frmUserSearch.submit();return false;"><button class="btn btn-primary btn-large">Search</button></a>
                <?php endif ?>
                    </div>

                    <div class="col-md-5 col-md-offset-0">
                <?php if ($this->search == UserSearchService::USER_SEARCH_BRAND_MAIL):?>
                        <div class="form-group">
                            <p>BRANDCo メールアドレス検索 :</p>
                            <?php write_html( $this->formText(
                                'brandco_mail_address',PHPParser::ACTION_FORM,
                                array('class' =>'form-control', 'maxlength'=>'255', 'placeholder'=> 'メールアドレス検索')
                            )); ?>
                        </div>

                    <a href="" onclick="document.frmUserSearch.submit();return false;"><button class="btn btn-primary btn-large">Search</button></a>
                    <?php endif ?>
                    </div>
            <div class="col-md-5 col-md-offset-0">
                    <?php if ($this->search == UserSearchService::USER_SEARCH_BRAND):?>
                            <?php if ($this->message != null):?>
                            <h4 style="color: #d9534f; font-weight: bold"><?php assign(($this->message))?></h4>
                            <?php endif ?>
                        <div class="form-group" >
                            <p>ブランドID :</p>
                            <label class="checkbox-inline">
                                <?php write_html( $this->formText(
                                    'brand_id',
                                    PHPParser::ACTION_FORM,
                                    array('maxlength'=>'255', 'placeholder'=> 'Brand ID')
                                )); ?>
                            </label>
                        </div>
                        <div class="form-group ">
                            <p>会員番号 :</p>
                            <?php write_html( $this->formText(
                                'member_no',PHPParser::ACTION_FORM,
                                array('class' =>'form-control', 'maxlength'=>'255', 'placeholder'=> '*会員番号')
                            )); ?>
                        </div>
                            <a href="" onclick="document.frmUserSearch.submit();return false;"><button class="btn btn-primary btn-large">Search</button></a>

                        <?php endif ?>
                    </div>
        </form>
    </div><!-- row -->

</div><!-- container-fluid -->
<div class="col-md-10 col-md-offset-2 main">
    <h4>Allied IDアカウント情報</h4>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Platform ID</th>
                    <th>名前</th>
                    <th>連携SNS</th>
                    <th>メールアドレス</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($this->platform_info->id != UserSearchService::PLATFORM_USER_NULL):?>
                <tr>
                    <td><?php assign($this->platform_info->id)?></td>
                    <td><?php assign($this->platform_info->name)?></td>
                    <?php if($this->platform_info->socialAccounts != null):?>
                        <td>
                            <?php foreach($this->platform_info->socialAccounts as $social_account_info):?>
                            <?php if ($social_account_info->socialMediaType == UserSearchService::SNS_TYPE_FACEBOOK):?>
                                <img src="<?php assign($this->setVersion('/img/sns/iconSnsFB3.png'))?>" width="15" height="15" alt="">
                            <?php endif ?>
                            <?php if ($social_account_info->socialMediaType == UserSearchService::SNS_TYPE_TWITTER):?>
                                <img src="<?php assign($this->setVersion('/img/sns/iconSnsTW3.png'))?>" width="15" height="15" alt="">
                            <?php endif ?>
                            <?php if ($social_account_info->socialMediaType == UserSearchService::SNS_TYPE_YAHOO):?>
                                <img src="<?php assign($this->setVersion('/img/sns/iconSnsYH3.png'))?>" width="15" height="15" alt="">
                            <?php endif ?>
                            <?php if ($social_account_info->socialMediaType == UserSearchService::SNS_TYPE_GOOGLE):?>
                                <img src="<?php assign($this->setVersion('/img/sns/iconSnsGP3.png'))?>" width="15" height="15" alt="">
                            <?php endif ?>
                            <?php if ($social_account_info->socialMediaType == UserSearchService::SNS_TYPE_INSTAGRAM):?>
                                <img src="<?php assign($this->setVersion('/img/sns/iconSnsIG3.png'))?>" width="15" height="15" alt="">
                            <?php endif ?>
                            <?php endforeach;?>
                        </td>
                    <?php else: ?>
                        <td><?php assign("-")?></td>
                    <?php endif ?>
                    <td><?php assign($this->platform_info->mailAddress)?></td>
                </tr>
            <?php else: ?>
                <tr>
                    <td><?php assign("データがありません")?></td>
                </tr>
            <?php endif ?>
            </tbody>

        </table>
    </div>
</div>

<div class="col-md-10 col-md-offset-2 main">
    <h4>連携アカウント情報</h4>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
            <tr>
                <th>SNS</th>
                <th>SNS UID</th>
                <th>ニックネーム</th>
                <th>メールアドレス</th>
                <th>連携状態</th>
            </tr>
            </thead>
            <tbody>
            <?php if($this->platform_info->socialAccounts):?>
            <?php foreach($this->platform_info->socialAccounts as $social_account_info):?>
            <tr>
                <td><?php assign($social_account_info->socialMediaType)?></td>
                <td><?php assign($social_account_info->socialMediaAccountID)?></td>
                <td><?php assign($social_account_info->name)?></td>
                <?php if($social_account_info->mailAddress != null):?>
                <td><?php assign($social_account_info->mailAddress)?></td>
                <?php else: ?>
                <td><?php assign("-")?></td>
                <?php endif ?>
                <?php if($social_account_info->validated == BrandsUsersRelationService::WITHDRAW):?>
                <td><?php assign("連携中")?></td>
                <?php else: ?>
                <td><?php assign("-")?></td>
                <?php endif ?>
                <td></td>
                <td></td>
            </tr>
            <?php endforeach;?>
            <?php else: ?>
            <tr>
                <td><?php assign("データがありません")?></td>
            </tr>
            <?php endif ?>
            </tbody>
        </table>
        <?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoManagerPager')->render(array(
            'TotalCount' => $data['allBrandCount'],
            'CurrentPage' => $this->params['p'],
            'Count' => $data['limit'],
        ))) ?>
    </div>
</div>

<div class="col-md-10 col-md-offset-2 main">
    <h4>BRANDCoアカウント情報</h4>
    <?php if($this->in_use_brandco):?>
    <td>
        <form id="with_draw_all_flg" name="with_draw_all_flg" action="<?php assign(Util::rewriteUrl( 'dashboard', 'user_search_delete', array(), array(), '', true)); ?>" method="POST">
        <?php write_html($this->formHidden('user_id',$this->brandco_user_info->id))?>
        <?php write_html($this->formHidden('platform_id',  $this->params['platform_id']))?>
        <?php write_html($this->formHidden('search_type',  $this->params['search_type']))?>
        <?php write_html($this->formHidden('brandco_uid',  $this->params['brandco_uid']))?>
        <?php write_html($this->formHidden('sns',  $this->params['sns']))?>
        <?php write_html($this->formHidden('sns_id',  $this->params['sns_id']))?>
        <?php write_html($this->formHidden('allied_mail_address',  $this->params['allied_mail_address']))?>
        <?php write_html($this->formHidden('brandco_mail_address',  $this->params['brandco_mail_address']))?>
        <?php write_html($this->formHidden('member_no',  $this->params['member_no']))?>
        <?php write_html($this->formHidden('search_brand_id',  $this->params['brand_id']))?>
        <a href="javascript:void(0)"><button class="btn btn-primary btn-large registrator" data-message="本当にBRANDCoから退会しますか？">BRANDCoから退会する</button></a>
    </form>
    </td>
    <?php endif ?>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
            <tr>
                <th>BRANDCo UID</th>
                <th>名前</th>
                <th>メールアドレス</th>
                <th>登録日</th>
            </tr>
            </thead>
            <tbody>
                <?php if($this->brandco_user_info != null):?>
                <tr>
                    <td><?php assign($this->brandco_user_info->id)?></td>
                    <td><?php assign($this->brandco_user_info->name)?></td>
                    <td><?php assign($this->brandco_user_info->mail_address)?> </td>
                    <td><?php assign($this->brandco_user_info->created_at)?></td>
                </tr>
                <?php else: ?>
                 <tr>
                    <td><?php assign("データがありません")?></td>
                 </tr>
                <?php endif ?>
            </tbody>
        </table>
    </div>
</div>

<div class="col-md-10 col-md-offset-2 main">
    <h4>ファン登録情報</h4>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
            <tr>
                <th>Brand ID</th>
                <th>ブランド名</th>
                <th>BRANDCo UID</th>
                <th>登録日</th>
                <th>退会日</th>
                <th>代理ログイン</th>
                <th>メール配信</th>
                <th>退会</th>
            </tr>
            </thead>
            <tbody>
            <?php if($this->brands_user_relations_info != null):?>
                <?php foreach($this->brands_user_relations_info as $fan_details):?>
                    <tr>
                        <td><?php assign($fan_details['brand_id'])?></td>
                        <td><?php assign($fan_details['brand_name'])?></td>
                        <td><?php assign($fan_details['user_id'])?> </td>
                        <td><?php assign($fan_details['created_at'])?></td>
                        <td><?php assign($fan_details['withdraw_date'])?></td>
                        <td>
                            <?php if($fan_details['withdraw_flg'] == BrandsUsersRelationService::WITHDRAW):?>
                                <?php assign("退会済み")?>
                            <?php else: ?>
                                <form id="backdoor_login" name="login" action="<?php assign(Util::rewriteUrl('dashboard', 'backdoor_login', array(), array(), '', true)); ?>">
                                    <?php write_html($this->formHidden('user_id', $fan_details['user_id']))?>
                                    <?php write_html($this->formHidden('brand_id', $fan_details['brand_id']))?>
                                    <?php write_html($this->formHidden('token', $fan_details['token']))?>
                                    <?php write_html($this->formHidden('platform_id',  $this->params['platform_id']))?>
                                    <?php write_html($this->formHidden('search_type',  $this->params['search_type']))?>
                                    <?php write_html($this->formHidden('brandco_uid',  $this->params['brandco_uid']))?>
                                    <?php write_html($this->formHidden('sns',  $this->params['sns']))?>
                                    <?php write_html($this->formHidden('sns_id',  $this->params['sns_id']))?>
                                    <?php write_html($this->formHidden('allied_mail_address',  $this->params['allied_mail_address']))?>
                                    <?php write_html($this->formHidden('brandco_mail_address',  $this->params['brandco_mail_address']))?>
                                    <?php write_html($this->formHidden('brand_name',  $this->params['brand_name']))?>
                                    <?php write_html($this->formHidden('member_no',  $this->params['member_no']))?>
                                    <?php write_html($this->formHidden('search_brand_id',  $this->params['brand_id']))?>
                                    <a href="javascript:void(0);" onclick="document.backdoor_login.submit();return false;"><button class="btn btn-primary btn-large">代理ログイン</button></a>
                                </form>
                        </td>
                        <?php endif ?>
                        <td>
                            <form id="" name="" action="<?php assign(Util::rewriteUrl( 'dashboard', 'user_search_update', array(), array(), '', true )); ?>" method="POST">
                                <?php write_html($this->formHidden('user_id', $fan_details['user_id']))?>
                                <?php write_html($this->formHidden('brand_id', $fan_details['brand_id']))?>
                                <?php write_html($this->formHidden('platform_id',  $this->params['platform_id']))?>
                                <?php write_html($this->formHidden('search_type',  $this->params['search_type']))?>
                                <?php write_html($this->formHidden('brandco_uid',  $this->params['brandco_uid']))?>
                                <?php write_html($this->formHidden('sns',  $this->params['sns']))?>
                                <?php write_html($this->formHidden('sns_id',  $this->params['sns_id']))?>
                                <?php write_html($this->formHidden('allied_mail_address',  $this->params['allied_mail_address']))?>
                                <?php write_html($this->formHidden('brandco_mail_address',  $this->params['brandco_mail_address']))?>
                                <?php write_html($this->formHidden('brand_name',  $this->params['brand_name']))?>
                                <?php write_html($this->formHidden('member_no',  $this->params['member_no']))?>
                                <?php write_html($this->formHidden('search_brand_id',  $this->params['brand_id']))?>
                                <?php if($fan_details['optin_flg'] == BrandsUsersRelationService::STATUS_OPTIN):?>
                                    <?php write_html($this->formHidden('mail_delivery', BrandsUsersRelationService::STATUS_OPTOUT))?>
                                    <a href="javascript:void(0)"><button class="btn btn-primary btn-large registrator" data-message="非配信に変更しますか？">配信中</button></a>
                                <?php else: ?>
                                <?php write_html($this->formHidden('mail_delivery', BrandsUsersRelationService::STATUS_OPTIN))?>
                                <a href="javascript:void(0)"><button class="btn btn-primary btn-large registrator" data-message="配信に変更しますか？">非配信中</button></a></td>
                                <?php endif ?>
                            </form>
                        </td>
                        <?php if($fan_details['withdraw_flg'] == BrandsUsersRelationService::WITHDRAW):?>
                            <td><?php assign("退会済み")?></td>
                        <?php else: ?>
                            <td>
                                <form id="with_draw_flg" name="with_draw_flg" action="<?php assign(Util::rewriteUrl( 'dashboard', 'user_search_update', array(), array(), '', true )); ?>" method="POST">
                                    <?php write_html($this->formHidden('user_id', $fan_details['user_id']))?>
                                    <?php write_html($this->formHidden('brand_id', $fan_details['brand_id']))?>
                                    <?php write_html($this->formHidden('platform_id',  $this->params['platform_id']))?>
                                    <?php write_html($this->formHidden('search_type',  $this->params['search_type']))?>
                                    <?php write_html($this->formHidden('brandco_uid',  $this->params['brandco_uid']))?>
                                    <?php write_html($this->formHidden('sns',  $this->params['sns']))?>
                                    <?php write_html($this->formHidden('sns_id',  $this->params['sns_id']))?>
                                    <?php write_html($this->formHidden('allied_mail_address',  $this->params['allied_mail_address']))?>
                                    <?php write_html($this->formHidden('brandco_mail_address',  $this->params['brandco_mail_address']))?>
                                    <?php write_html($this->formHidden('brand_name',  $this->params['brand_name']))?>
                                    <?php write_html($this->formHidden('member_no',  $this->params['member_no']))?>
                                    <?php write_html($this->formHidden('search_brand_id',  $this->params['brand_id']))?>
                                    <a href="javascript:void(0)"><button class="btn btn-primary btn-large registrator" data-message="退会しますか？">退会する</button></a>
                                </form>
                            </td>
                        <?php endif ?>
                    </tr>
                <?php endforeach;?>
            <?php else: ?>

                <tr>
                    <td><?php assign("データがありません")?></td>
                </tr>
            <?php endif ?>
            </tbody>
        </table>
    </div>
</div>

<div class="col-md-10 col-md-offset-2 main">
    <h4>キャンペーン参加情報</h4>
    <h5>全参加数: <?php assign(count($this->joined_cps))?></h5>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
            <tr>
                <th>Cp User ID</th>
                <th>Cp ID</th>
                <th>キャンペーン名</th>
                <th>Brand ID</th>
                <th>会員番号</th>
                <th>ブランド名</th>
                <th>開始日</th>
                <th>終了日</th>
                <th>当選発表日</th>
                <th>参加日時</th>
                <th>参加ステップ</th>
            </tr>
            </thead>
            <tbody>
            <?php if($this->joined_cps):?>
            <?php foreach($this->joined_cps as $joined_cp):?>
                <tr>
                    <td><?php assign($joined_cp['cp_user_id'])?></td>
                    <td><?php assign($joined_cp['id'])?></td>
                    <td><?php assign($joined_cp['title'])?></td>
                    <td><?php assign($joined_cp['brand_id'])?></td>
                    <td><?php assign($joined_cp['participated_no'])?></td>
                    <td><a href="<?php assign(Util::rewriteUrl('dashboard', 'redirect_manager_sso', array(), array('redirect_uri' =>  $joined_cp['brand'] !== null ? $joined_cp['brand']->getUrl() : "" ), '', true))?>my/login"" target="_blank"><?php assign($joined_cp['brand']->name)?></a></td>
                    <td><?php assign($joined_cp['start_date'])?></td>
                    <td><?php assign($joined_cp['end_date'])?></td>
                    <td><?php assign($joined_cp['announce_date'])?></td>
                    <td><?php assign($joined_cp['participated_date'])?></td>
                    <td>STEP<?php assign($joined_cp['joined_steps'])?>/<?php assign($joined_cp['total_steps']) ?><br>
                        <a href="<?php assign('https://'.config('Domain.brandco').'/'. $joined_cp['brand']->directory_name .'/'.'admin-cp'.'/'.'show_user_list'.'/'.$joined_cp['id'].'/'.$joined_cp['first_cp_action_id'])?>" target="_blank"><?php assign($joined_cp['participated_condition'])?></a>まで完了
                    </td>
                </tr>
            <?php endforeach;?>
            <?php else: ?>
                <tr>
                    <td><?php assign("データがありません")?></td>
                </tr>
            <?php endif ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    $('.registrator').click(function(event) {
        event.preventDefault();
        if (confirm($(this).data('message'))) {
            $(this).closest("form").submit();
        }
    });
</script>
<link rel="stylesheet" href="<?php assign($this->setVersion('/manager/userAutocomplete.css'))?>">
<?php write_html($this->parseTemplate('BrandcoManagerFooter.php')); ?>
