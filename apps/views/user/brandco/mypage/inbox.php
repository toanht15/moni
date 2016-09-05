<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoHeader')->render($data['pageStatus'])) ?>
<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoTopHeader')->render($data['pageStatus'])) ?>
    <article>
        <?php write_html($this->csrf_tag()); ?>

        <section class="mypageUserWrap">
                <h1 class="mypageUser">
                    <figure>
                        <img src="<?php assign($data['pageStatus']['userInfo']->socialAccounts[0]->profileImageUrl ? $data['pageStatus']['userInfo']->socialAccounts[0]->profileImageUrl : $this->setVersion('/img/base/imgUser1.jpg')) ?>" height="40" width="40" alt="user img" onerror="this.src='<?php assign($this->setVersion('/img/base/imgUser1.jpg'));?>';">
                    </figure>
                    <span class="nametext"><?php assign($data['pageStatus']['userInfo']->name) ?>さん</span><span class="edit"><a href="javascript:void(0)" class="jsMypageSetteing">設定変更</a></span>
                </h1>
            <div class="mypageSetting jsMypageSetteingTarget"<?php if($data['ActionForm']['optout']):?> style="display: block;"<?php endif;?>>
              <div class="btnSet"><p class="btn3"><a href="<?php assign(Util::rewriteUrl( 'my', 'redirect_platform',null,array('page'=>'my_account'))); ?>" class="large2">アカウントの設定</a></p></div>
              <form action="<?php assign(Util::rewriteUrl( 'my', 'api_change_optin_status.json')); ?>" class="executeChangeOptinAction" method="POST">
              <dl>
                <dt>メッセージをメールで受け取る</dt>
                <dd><label><?php write_html($this->formRadio("radio_optin", $this->brandUserInfo->optin_flg, array('class' => 'cmd_execute_change_optin_action'), array(BrandsUsersRelationService::STATUS_OPTIN => '受け取る')));?></label></dd>
                <dd><label><?php write_html($this->formRadio("radio_optin", $this->brandUserInfo->optin_flg, array('class' => 'cmd_execute_change_optin_action'), array(BrandsUsersRelationService::STATUS_OPTOUT => '受け取らない')));?></label></dd>
              </dl>
              </form>

                <p class="supplement1">当選通知メールについては受け取り可否に関わらずお送りさせていただきます</p>
                <p class="withdraw"><a href="<?php assign(Util::rewriteUrl('mypage', 'withdraw_form')) ?>" class="small1">退会手続き</a></p>

            <!-- /.mypageSetting --></div>
            <!-- /.mypageUserWrap --></section>

        <section class="messageWrap">
            <h2 class="hd2">メッセージ</h2>
            <?php if (!$data['message_info_list']): ?>

            <p class="messageListNone">
                <img src="<?php assign($this->setVersion('/img/base/imgMessageNone1.jpg'))?>" alt="メーッセージ無し">メッセージはまだ届いておりません。
                <!-- /.messageListNone --></p>
            <?php else: ?>
            <ul class="messageList">
                <?php foreach($data['message_info_list'] as $message_info): ?>
                    <?php foreach($message_info[inbox::MSG_INFO_PRODUCTS] as $order):?>
                        <li>
                            <ul class="messageListInner">
                                <li class="">
                                    <a href="<?php assign(Util::rewriteUrl('mypage','order_detail',array($order['order_id'])))?>">
                                        <figure><img src="<?php assign($order['product_image_url'])?>" class="book" alt=""></figure>
                                        <span class="title"><?php assign($order['product_title'])?>を購入しました</span>
                                        <small class="timestamp"><?php assign(Inbox::toInboxDateTime($order['order_completion_date']))?></small>
                                    </a>
                                </li>
                                <!-- /.messageListInner --></ul>
                        </li>
                    <?php endforeach;?>
                <?php $url = Util::rewriteUrl('messages', 'thread', array("cp_id" => $message_info[inbox::MSG_INFO_CP]->id)); ?>
                <li>
                    <ul class="messageListInner">
                        <li <?php if (inbox::canEmphasize($message_info)): ?>class="notRead"<?php endif; ?>>
                            <a href="<?php assign($url); ?>">
                                <figure>
                                    <img src="<?php assign($message_info[inbox::MSG_INFO_CP]->getIcon()); ?>" height="40" width="40">
                                </figure>
                                <span class="title"><?php assign($message_info[inbox::MSG_INFO_NEWEST]->title); ?></span>
                                <small class="timestamp"><?php assign(inbox::toInboxDateTime($message_info[inbox::MSG_INFO_NEWEST]->created_at)); ?></small>
                            </a>
                        </li>

                        <?php foreach($message_info[inbox::MSG_INFO_READ] as $read): ?>
                            <li class="readed">
                                <a href="<?php assign($url . "?scroll=message_" . $read->id); ?>">
                                    <span class="title"><?php assign($read->title); ?></span>
                                    <small class="timestamp"><?php assign(inbox::toInboxDateTime($read->created_at)); ?></small>
                                </a>
                            </li>
                        <?php endforeach; ?>
                        <?php if ($message_info[inbox::MSG_INFO_READ_COUNT] > 1): ?>
                            <li class="showAll">
                                <a href="javascript:void(0)" class="jsMessageToggle">
                                    <small>これ以前のメッセージ<?php assign($message_info[inbox::MSG_INFO_READ_COUNT]); ?>件</small>
                                </a>
                            </li>
                        <?php endif; ?>
                        <?php if (inbox::canShowUnreadMessages($message_info)): ?>
                            <?php foreach($message_info[inbox::MSG_INFO_UNREAD] as $unread): ?>
                                <li class="notRead" >
                                    <a href="<?php assign($url . "?scroll=message_" . $unread->id); ?>">
                                        <span><?php assign($unread->title); ?></span>
                                        <small class="timestamp"><?php assign(inbox::toInboxDateTime($unread->created_at)); ?></small>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    <!-- /.messageListInner --></ul>
                </li>
                <?php endforeach; ?>

            <!-- /.messageList --></ul>
            <?php endif; ?>
            <!-- /.messageWrap --></section>
        <p class="pageTop"><a href="#top"><span>ページTOPへ</span></a></p>
    </article>

<?php write_html($this->scriptTag('user/UserActionAccountSettingService')); ?>
<?php write_html($this->parseTemplate('BrandcoFooter.php', $data['pageStatus'])); ?>
