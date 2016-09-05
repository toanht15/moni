<section class="campaignEditCont">
    <section class="userListWrap">
        <section class="userList">
            <div class="pager1">
                <p><strong>件数計算中...</strong></p>
            </div>

            <?php $this->search_no = 1; ?>
            <div class="userListCont">
                <ul class="userName">
                    <li class="allUser"><label></label></li>
                    <?php foreach($data['fan_list_users'] as $fan_list_user): ?>
                        <?php $user_info = $fan_list_user->getBrandcoUser(); ?>
                        <li>
                            <label title="<?php assign(!empty($data['is_hide_personal_info']) ? '' : $user_info->name) ?>">
                                <img src="<?php assign($user_info->profile_image_url ? $user_info->profile_image_url : config('Static.Url').'/img/base/imgUser1.jpg') ?>" width="20" height="20" alt="<?php assign(!empty($data['is_hide_personal_info']) ? '' : $user_info->name) ?>" onerror="this.src='<?php assign($this->setVersion('/img/base/imgUser1.jpg'));?>';">
                                <?php assign(!empty($data['is_hide_personal_info']) ? '' : Util::cutTextByWidth($user_info->name, 102)) ?>
                            </label>
                        </li>
                    <?php endforeach; ?>
                    <?php for($i = 1; $i <= CpCreateSqlService::DISPLAY_20_ITEMS-count($data['fan_list_users']); $i++): ?>
                        <li></li>
                    <?php endfor; ?>
                </ul>

                <?php
                    $service_factory = new aafwServiceFactory();
                    /** @var SocialLikeService $social_like_service */
                    $social_like_service = $service_factory->create('SocialLikeService');
                    $twitter_follow_service = $service_factory->create('TwitterFollowService');
                ?>

                <form id="frmSearchFan" name="frmSearchFan">
                    <?php write_html($this->formHidden('page_info', $data['list_page']['page_no'].'/'.$data['list_page']['tab_no']))?>
                    <?php if($data['list_page']['tab_no'] == CpCreateSqlService::TAB_PAGE_PROFILE): ?>
                        <?php write_html(aafwWidgets::getInstance()->loadWidget('CpUserProfile')->render(array(
                            'brand'                 => $data['brand'],
                            'fan_list_users'        => $data['fan_list_users'],
                            'search_condition'      => $data['search_condition'],
                            'fan_limit'             => $data['list_page']['limit'],
                            'search_no'             => $this->search_no,
                            'isSocialLikesEmpty'    => $social_like_service->isEmptyTable() ? 1 : 0,
                            'isTwitterFollowsEmpty' => $twitter_follow_service->isEmptyTable(),
                            'duplicateAddressShowType' => CpCreateSqlService::SHIPPING_ADDRESS_DUPLICATE,
                            'is_hide_personal_info' => $data['is_hide_personal_info']
                        ))) ?>
                    <?php endif; ?>
                    <?php write_html($this->csrf_tag()); ?>
                </form>
            <!-- /.userListCont --></div>

            <div class="pager1">
                <p><strong>件数計算中...</strong></p>
            </div>

            <?php write_html($this->parseTemplate('UserListItemCount.php', array(
                'limit'         => $data['list_page']['limit'],
            ))) ?>
        <!-- /.userList --></section>
    <!-- /.userListWrap --></section>
<!-- /.campaignEditCont --></section>