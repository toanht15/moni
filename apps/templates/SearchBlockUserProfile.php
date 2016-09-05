<?php
$service_factory = new aafwServiceFactory();
/** @var BrandPageSettingService $brand_page_setting_service */
$brand_page_setting_service = $service_factory->create('BrandPageSettingService');
$page_settings = $brand_page_setting_service->getPageSettingsByBrandId($data['brand_id']);
?>
<div class="customaudienceRefinement jsModuleContWrap" xmlns="http://www.w3.org/1999/html">
    <div class="categoryLabel jsModuleContTile close">
        <p>ユーザー情報</p>
        <p class="iconHelp">
            <span class="text"></span>
              <span class="textBalloon1">
                <span>
                  数値入力例<br>
                  <span class="label">50回</span><span class="sample"><span type="text" class="inputNum">50</span><span class="dash">〜</span><span type="text" class="inputNum">50</span></span><br>
                  <span class="label">50回〜100回</span><span class="sample"><span type="text" class="inputNum">50</span><span class="dash">〜</span><span type="text" class="inputNum">100</span></span>
                </span>
              <!-- /.textBalloon1 --></span>
            <!-- /.iconHelp --></p>
        <!-- /.categoryLabel --></div>
    <div class="refinementWrap jsModuleContTarget">
        <div class="setting">
            <div class="refinementItem jsSearchInputBlock">
                <form>
                    <?php write_html($this->formHidden("search_type", CpCreateSqlService::SEARCH_PROFILE_RATE)) ?>
                    <p class="settingLabel">評価</p>
                    <p class="settingDetail">
                    <ul class="kind">
                        <li><label><input type="checkbox" name='search_rate/<?php assign(BrandsUsersRelationService::RATE_5.'/'.$data["search_no"])?>' <?php assign($data[CpCreateSqlService::SEARCH_PROFILE_RATE]['search_rate/'.BrandsUsersRelationService::RATE_5] ? 'checked' : '')?>><img src="<?php assign($this->setVersion('/img/raty/iconStar_5.png')) ?>" width="24" height="24" alt=""> 5</label></li>
                        <li><label><input type="checkbox" name='search_rate/<?php assign(BrandsUsersRelationService::RATE_4.'/'.$data["search_no"])?>' <?php assign($data[CpCreateSqlService::SEARCH_PROFILE_RATE]['search_rate/'.BrandsUsersRelationService::RATE_4] ? 'checked' : '')?>><img src="<?php assign($this->setVersion('/img/raty/iconStar_4.png')) ?>" width="24" height="24" alt=""> 4</label></li>
                        <li><label><input type="checkbox" name='search_rate/<?php assign(BrandsUsersRelationService::RATE_3.'/'.$data["search_no"])?>' <?php assign($data[CpCreateSqlService::SEARCH_PROFILE_RATE]['search_rate/'.BrandsUsersRelationService::RATE_3] ? 'checked' : '')?>><img src="<?php assign($this->setVersion('/img/raty/iconStar_3.png')) ?>" width="24" height="24" alt=""> 3</label></li>
                        <li><label><input type="checkbox" name='search_rate/<?php assign(BrandsUsersRelationService::RATE_2.'/'.$data["search_no"])?>' <?php assign($data[CpCreateSqlService::SEARCH_PROFILE_RATE]['search_rate/'.BrandsUsersRelationService::RATE_2] ? 'checked' : '')?>><img src="<?php assign($this->setVersion('/img/raty/iconStar_2.png')) ?>" width="24" height="24" alt=""> 2</label></li>
                        <li><label><input type="checkbox" name='search_rate/<?php assign(BrandsUsersRelationService::RATE_1.'/'.$data["search_no"])?>' <?php assign($data[CpCreateSqlService::SEARCH_PROFILE_RATE]['search_rate/'.BrandsUsersRelationService::RATE_1] ? 'checked' : '')?>><img src="<?php assign($this->setVersion('/img/raty/iconStar_1.png')) ?>" width="24" height="24" alt=""> 1</label></li>
                        <li><label><input type="checkbox" name='search_rate/<?php assign(BrandsUsersRelationService::NON_RATE.'/'.$data["search_no"])?>' <?php assign($data[CpCreateSqlService::SEARCH_PROFILE_RATE]['search_rate/'.BrandsUsersRelationService::NON_RATE] ? 'checked' : '')?>><img src="<?php assign($this->setVersion('/img/raty/iconStar_0.png')) ?>" width="24" height="24" alt=""> 未評価</label></li>
                        <li><label><input type="checkbox" name='search_rate/<?php assign(BrandsUsersRelationService::BLOCK.'/'.$data["search_no"])?>' <?php assign($data[CpCreateSqlService::SEARCH_PROFILE_RATE]['search_rate/'.BrandsUsersRelationService::BLOCK] ? 'checked' : '')?>><img src="<?php assign($this->setVersion('/img/raty/iconBlockOn.png')) ?>" width="24" height="24" alt=""></label></li>
                    </ul>
                    <!-- /.settingDetail --></p>
                </form>
                <!-- /.refinementItem --></div>
            <div class="refinementItem jsSearchInputBlock">
                <form>
                    <?php write_html($this->formHidden("search_type", CpCreateSqlService::SEARCH_PROFILE_MEMBER_NO)) ?>
                    <p class="settingLabel">会員No.</p>
                    <p class="settingDetail">
                        <?php write_html($this->formTextArea(
                            'search_profile_member_no_from',
                            $data[CpCreateSqlService::SEARCH_PROFILE_MEMBER_NO]['search_profile_member_no_from'],
                            array('placeholder'=>'No.', 'class'=>'pluralItems jsReplaceLbComma', 'flg' => $data['flg'])
                        )); ?>
                        <!-- /.settingDetail --></p>
                    <small class="supplement1">※カンマ/改行区切りで複数指定可</small>
                </form>
                <!-- /.refinementItem --></div>
            <div class="refinementItem jsSearchInputBlock">
                <form>
                    <?php write_html($this->formHidden("search_type", CpCreateSqlService::SEARCH_PROFILE_REGISTER_PERIOD)) ?>
                    <p class="settingLabel">登録期間</p>
                    <p class="settingDetail">
                        <?php write_html($this->formText(
                            'search_profile_register_period_from',
                            $data[CpCreateSqlService::SEARCH_PROFILE_REGISTER_PERIOD]['search_profile_register_period_from'],
                            array('class'=>'jsDate inputDate','placeholder'=>'年/月/日')
                        )); ?>
                        <span class="dash">〜</span>
                        <?php write_html($this->formText(
                            'search_profile_register_period_to',
                            $data[CpCreateSqlService::SEARCH_PROFILE_REGISTER_PERIOD]['search_profile_register_period_to'],
                            array('class'=>'jsDate inputDate','placeholder'=>'年/月/日')
                        )); ?>
                        <!-- /.settingDetail --></p>
                </form>
                <!-- /.refinementItem --></div>
            <div class="refinementItem jsSearchInputBlock">
                <form>
                    <?php write_html($this->formHidden("search_type", CpCreateSqlService::SEARCH_PROFILE_LAST_LOGIN)) ?>
                    <p class="settingLabel">最終ログイン</p>
                    <p class="settingDetail">
                        <?php
                        if($data[CpCreateSqlService::SEARCH_PROFILE_LAST_LOGIN]['search_profile_last_login_from']) {
                            $search_profile_last_login_from = date('Y/m/d', strtotime($data[CpCreateSqlService::SEARCH_PROFILE_LAST_LOGIN]['search_profile_last_login_from']));
                        }
                        if($data[CpCreateSqlService::SEARCH_PROFILE_LAST_LOGIN]['search_profile_last_login_to']) {
                            $search_profile_last_login_to = date('Y/m/d', strtotime($data[CpCreateSqlService::SEARCH_PROFILE_LAST_LOGIN]['search_profile_last_login_to']));
                        }
                        ?>
                        <?php write_html($this->formText(
                            'search_profile_last_login_from',
                            $search_profile_last_login_from,
                            array('class'=>'jsDate inputDate','placeholder'=>'年/月/日')
                        )); ?>
                        <span class="dash">〜</span>
                        <?php write_html($this->formText(
                            'search_profile_last_login_to',
                            $search_profile_last_login_to,
                            array('class'=>'jsDate inputDate','placeholder'=>'年/月/日')
                        )); ?>
                        <!-- /.settingDetail --></p>
                    </form>
                <!-- /.refinementItem --></div>
            <div class="refinementItem jsSearchInputBlock">
                <form>
                    <?php write_html($this->formHidden("search_type", CpCreateSqlService::SEARCH_PROFILE_LOGIN_COUNT)) ?>
                    <p class="settingLabel">ログイン回数</p>
                    <?php $data["search_type"] = CpCreateSqlService::SEARCH_PROFILE_LOGIN_COUNT;
                          $data["unit_label"] = "回";
                    ?>
                    <?php write_html($this->parseTemplate("SearchRangeInputNum.php", $data)) ?>
                </form>
                <!-- /.refinementItem --></div>

            <?php if($page_settings->privacy_required_sex): ?>
                <div class="refinementItem jsSearchInputBlock">
                    <p class="settingLabel">性別</p>
                    <form>
                        <?php write_html($this->formHidden("search_type", CpCreateSqlService::SEARCH_PROFILE_SEX)) ?>
                        <ul class="kind">
                            <li><label><input type="checkbox" name='search_profile_sex/<?php assign(UserAttributeService::ATTRIBUTE_SEX_MAN.'/'.$data["search_no"])?>' <?php assign($data[CpCreateSqlService::SEARCH_PROFILE_SEX]['search_profile_sex/'.UserAttributeService::ATTRIBUTE_SEX_MAN] ? 'checked' : '')?>><span class="iconSexM">男性</span>男性</label></li>
                            <li><label><input type="checkbox" name='search_profile_sex/<?php assign(UserAttributeService::ATTRIBUTE_SEX_WOMAN.'/'.$data["search_no"])?>' <?php assign($data[CpCreateSqlService::SEARCH_PROFILE_SEX]['search_profile_sex/'.UserAttributeService::ATTRIBUTE_SEX_WOMAN] ? 'checked' : '')?>><span class="iconSexF">女性</span>女性</label></li>
                            <li><label><input type="checkbox" name='search_profile_sex/<?php assign(UserAttributeService::ATTRIBUTE_SEX_UNKWOWN.'/'.$data["search_no"])?>' <?php assign($data[CpCreateSqlService::SEARCH_PROFILE_SEX]['search_profile_sex/'.UserAttributeService::ATTRIBUTE_SEX_UNKWOWN] ? 'checked' : '')?>><span class="iconSexN">未設定</span>未設定</label></li>
                            <!-- /.settingDetail --></ul>
                    </form>
                    <!-- /.refinementItem --></div>
            <?php endif; ?>

            <?php if($page_settings->privacy_required_birthday): ?>
                <div class="refinementItem jsSearchInputBlock">
                    <p class="settingLabel">年齢</p>
                    <form>
                        <?php write_html($this->formHidden("search_type", CpCreateSqlService::SEARCH_PROFILE_AGE)) ?>
                        <?php $data["search_type"] = CpCreateSqlService::SEARCH_PROFILE_AGE;
                        $data["unit_label"] = "歳";
                        ?>
                        <?php write_html($this->parseTemplate("SearchRangeInputNum.php", $data)) ?>
                    </form>
                    <!-- /.refinementItem --></div>
            <?php endif; ?>

            <?php if($page_settings->privacy_required_address): ?>
                <div class="refinementItem jsSearchInputBlock">
                    <p class="settingLabel">都道府県</p>
                    <form>
                        <?php write_html($this->formHidden("search_type", CpCreateSqlService::SEARCH_PROFILE_ADDRESS)) ?>
                        <ul class="prefectures">
                            <?php
                            $service_factory = new aafwServiceFactory();
                            $prefecture_service = $service_factory->create('PrefectureService');
                            $prefectures = $prefecture_service->getAllPrefectures();
                            ?>
                            <?php foreach($prefectures as $prefecture): ?>
                                <li>
                                    <?php write_html($this->formCheckbox(
                                        'search_profile_address/'.$prefecture->id.'/'.$this->search_no,
                                        $data[CpCreateSqlService::SEARCH_PROFILE_ADDRESS][$prefecture->id],
                                        array('checked' => $data[CpCreateSqlService::SEARCH_PROFILE_ADDRESS]['search_profile_address/'.$prefecture->id] ? 'checked' : ''),
                                        array('1' => $prefecture->name)
                                    ))?>
                                </li>
                            <?php endforeach;?>
                            <li>
                                <?php write_html($this->formCheckbox(
                                    'search_profile_address/'.CpCreateSqlService::NOT_SET_PREFECTURE.'/'.$this->search_no,
                                    $data[CpCreateSqlService::SEARCH_PROFILE_ADDRESS][CpCreateSqlService::NOT_SET_PREFECTURE],
                                    array('checked' => $data[CpCreateSqlService::SEARCH_PROFILE_ADDRESS]['search_profile_address/'.CpCreateSqlService::NOT_SET_PREFECTURE] ? 'checked' : ''),
                                    array('1' => '未設定')
                                ))?>
                            </li>
                        </ul>
                    </form>
                    <!-- /.refinementItem --></div>
            <?php endif; ?>

            <!-- /.setting --></div>
        <!-- /.refinementWrap --></div>
    <!-- /.customaudiencRefinement --></div>
