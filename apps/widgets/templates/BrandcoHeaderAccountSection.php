<section class="account">
<ul>
    <?php if($data['isLoginAdmin'] && $data['brand']->hasOption(BrandOptions::OPTION_CMS, BrandInfoContainer::getInstance()->getBrandOptions())):?>
        <li class="preview"><a href="<?php assign(Util::rewriteUrl('', 'preview', array(), array('preview_url' => base64_encode(Util::getPreviewUrl(StaticHtmlEntries::DEFAULT_PREVIEW_MODE))))); ?>" target="_blank">プレビュー</a></li>
    <?php endif;?>
    <?php if(!$data['isLogin']):?>
        <?php if (!$data['is_closed_brand']): ?>
            <li class="btn3"><a href="<?php assign(Util::rewriteUrl('my', 'login')) ?>" class="loginBtn">無料登録・ログイン</a></li>
        <?php endif; ?>
    <?php else:?>
        <?php if (!$data['is_closed_brand']): ?>
            <li class="accountName">
                <?php if($data['brand']->hasOption(BrandOptions::OPTION_MYPAGE, BrandInfoContainer::getInstance()->getBrandOptions()) || $data['brand']->hasOption(BrandOptions::OPTION_CRM, BrandInfoContainer::getInstance()->getBrandOptions())):?>
                <a href="<?php assign(Util::rewriteUrl('mypage', 'inbox')); ?>">
                    <img src="<?php assign($data['userInfo']->socialAccounts[0]->profileImageUrl ? $data['userInfo']->socialAccounts[0]->profileImageUrl : $this->setVersion('/img/base/imgUser1.jpg')) ?>" width="30" height="30" alt="" onerror="this.src='<?php assign($this->setVersion('/img/base/imgUser1.jpg'));?>';"><?php assign(Util::isSmartPhone()? $this->cutLongText($data['userInfo']->name, 20) : $data['userInfo']->name);?>さん
                    <?php if($data['notifications_count'] && $data['notifications_count'] != 0): ?>
                        <span class="badge1"><?php assign($data['notifications_count']) ?></span>
                    <?php endif; ?>
                </a>
                <?php else:?>
                    <img src="<?php assign($data['userInfo']->socialAccounts[0]->profileImageUrl ? $data['userInfo']->socialAccounts[0]->profileImageUrl : $this->setVersion('/img/base/imgUser1.jpg')) ?>" width="30" height="30" alt="" onerror="this.src='<?php assign($this->setVersion('/img/base/imgUser1.jpg'));?>';"><?php assign(Util::isSmartPhone()? $this->cutLongText($data['userInfo']->name, 20) : $data['userInfo']->name);?>さん
                <?php endif;?>
            </li>
            <li class="logout"><a href="<?php assign(Util::rewriteUrl( 'my', 'logout' )); ?>" class="logout"><span class="textBalloon1"><span>ログアウト</span></span></a></li>
        <?php endif;?>
    <?php endif;?>
</ul>
<!-- /.account --></section>
