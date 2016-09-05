<?php if ($data['is_olympus_header_footer']): ?>
    <?php write_html($this->parseTemplate('OlympusHeader.php', $data)) ?>
<?php elseif ($data['is_whitebelg_header_footer']):?>
    <?php write_html($this->parseTemplate('WhitebelgHeader.php', $data)) ?>
<?php elseif ($data['is_kenken_header_footer']):?>
    <?php write_html($this->parseTemplate('KenkenHeader.php', $data)) ?>
<?php elseif ($data['is_uq_header_footer']):?>
    <?php write_html($this->parseTemplate('UQHeader.php', $data)) ?>
<?php else: ?>
    <?php if(!$data['isOrderList']):?><?php //配送管理のページでは非表示にしたい?>
    <header>
        <section class="account">
            <ul>
                <li class="accountCompany"><img src="<?php assign($data['brand']->getProfileImage())?>" width="30" height="30" alt=""><?php assign($data['brand']->name)?></li>
                <?php if($data['brand']->hasOption(BrandOptions::OPTION_MYPAGE, BrandInfoContainer::getInstance()->getBrandOptions()) || $data['brand']->hasOption(BrandOptions::OPTION_CRM, BrandInfoContainer::getInstance()->getBrandOptions())):?>
                    <?php if(!$data['isLogin']):?>
                        <li class="btn3"><a href="<?php assign(Util::rewriteUrl('my', 'login')) ?>" class="loginBtn">無料登録・ログイン</a></li>
                    <?php else:?>
                        <li class="accountName">
                            <a href="<?php assign(Util::rewriteUrl('mypage', 'inbox')); ?>">
                                <img src="<?php assign($data['userInfo']->socialAccounts[0]->profileImageUrl ? $data['userInfo']->socialAccounts[0]->profileImageUrl :$this->setVersion('/img/base/imgUser1.jpg')) ?>" width="30" height="30" alt="" onerror="this.src='<?php assign($this->setVersion('/img/base/imgUser1.jpg'));?>';"><?php assign(Util::isSmartPhone()? $this->cutLongText($data['userInfo']->name, 20) : $data['userInfo']->name);?>さん
                                <?php if($data['notifications_count'] && $data['notifications_count'] != 0): ?>
                                    <span class="badge1"><?php assign($data['notifications_count']) ?></span>
                                <?php endif; ?>

                            </a>
                        </li>
                        <li class="logout"><a href="<?php assign(Util::rewriteUrl( 'my', 'logout' )); ?>" class="logout"><span class="textBalloon1"><span>ログアウト</span></span></a></li>
                    <?php endif;?>
                <?php else:?>
                    <li class="accountName">
                        <img src="<?php assign($data['userInfo']->socialAccounts[0]->profileImageUrl ? $data['userInfo']->socialAccounts[0]->profileImageUrl :$this->setVersion('/img/base/imgUser1.jpg')) ?>" width="30" height="30" alt="" onerror="this.src='<?php assign($this->setVersion('/img/base/imgUser1.jpg'));?>';"><?php assign(Util::isSmartPhone()? $this->cutLongText($data['userInfo']->name, 20) : $data['userInfo']->name);?>さん
                    </li>
                <?php endif; ?>
            </ul>
        <!-- /.account --></section>
    </header>
    <?php endif;?>
<?php endif ?>
