<?php write_html($this->parseTemplate('EmbedIframeHeader.php', array(
    'brand' => $data['pageStatus']['brand'],
))) ?>
<div class="OwnedWrap">
    <div class="entryOwned">
        <?php if($data['msbc_custom_login_page']): ?>
            <?php write_html($this->parseTemplate('my/MsbcEmbedLoggingForm.php', $data)); ?>
        <?php else: ?>
            <ul class="signupSns">
                <?php if(in_array(SocialAccountService::SOCIAL_MEDIA_FACEBOOK,$data['login_types'])): ?>
                    <li class="btnSnsFb1"><a data-href="<?php assign(Util::rewriteUrl( 'auth', 'service_login', '', array('platform' => 'fb','redirect_url' => ''))) ?>" href="javascript:void(0)" class="jLogin login1"><span>Facebook</span></a></li>
                <?php endif; ?>
                <?php if(in_array(SocialAccountService::SOCIAL_MEDIA_TWITTER,$data['login_types'])): ?>
                    <li class="btnSnsTw1"><a data-href="<?php assign(Util::rewriteUrl( 'auth', 'service_login', '', array('platform' => 'tw','redirect_url' => ''))) ?>" href="javascript:void(0)" class="jLogin login1"><span>Twitter</span></a></li>
                <?php endif; ?>
                <?php if(in_array(SocialAccountService::SOCIAL_MEDIA_LINE,$data['login_types'])): ?>
                    <li class="btnSnsLn1"><a data-href="<?php assign(Util::rewriteUrl( 'auth', 'service_login', '', array('platform' => 'line','redirect_url' => ''))) ?>" href="javascript:void(0)" class="jLogin login1"><span>LINE</span></a></li>
                <?php endif; ?>
                <?php if(in_array(SocialAccountService::SOCIAL_MEDIA_INSTAGRAM,$data['login_types'])): ?>
                    <li class="btnSnsIg1"><a data-href="<?php assign(Util::rewriteUrl( 'auth', 'service_login', '', array('platform' => 'insta','redirect_url' => ''))) ?>" href="javascript:void(0)" class="jLogin login1"><span>Instagram</span></a></li>
                <?php endif; ?>
                <?php if(in_array(SocialAccountService::SOCIAL_MEDIA_GOOGLE,$data['login_types'])): ?>
                    <li class="btnSnsGp1"><a data-href="<?php assign(Util::rewriteUrl( 'auth', 'service_login', '', array('platform' => 'ggl','redirect_url' => ''))) ?>" href="javascript:void(0)" class="jLogin login1"><span>Google</span></a></li>
                <?php endif; ?>
                <?php if(in_array(SocialAccountService::SOCIAL_MEDIA_YAHOO,$data['login_types'])): ?>
                    <li class="btnSnsYh1"><a data-href="<?php assign(Util::rewriteUrl( 'auth', 'service_login', '', array('platform' => 'yh','redirect_url' => ''))) ?>" href="javascript:void(0)" class="jLogin login1"><span>Yahoo! JAPAN ID</span></a></li>
                <?php endif; ?>
                <?php if(in_array(SocialAccountService::SOCIAL_MEDIA_LINKEDIN,$data['login_types'])): ?>
                    <li class="btnSnsIn1"><a data-href="<?php assign(Util::rewriteUrl( 'auth', 'service_login', '', array('platform' => 'linkedin','redirect_url' => ''))) ?>" href="javascript:void(0)" class="jLogin login1"><span>LinkedIn</span></a></li>
                <?php endif; ?>
            </ul>
        <?php endif; ?>

        <?php if($data['brand']->id == 479): ?>
            <p class="loginAttention">本サービスは、モニプラ・<a href="<?php assign('//'.config('Domain.aaid'))?>" target="_top"><img src="<?php assign($this->setVersion('/img/icon/iconAlliedID2.png')); ?>" alt="アライドID">アライドID</a>に登録された情報を利用いたします。<a href="<?php assign('//'.config('Domain.aaid'))?>/agreement" class="openNewWindow1" target="_top">アライドID利用規約</a>、<a href="<?php assign(Util::rewriteUrl('page', 'privacy')); ?>" class="openNewWindow1" target="_blank">一般社団法人 日本健康生活推進協会 個人情報保護方針</a>に同意の上、ご登録ください。</p>
        <?php else: ?>
            <p class="loginAttention">本サービスは、モニプラ・<a href="<?php assign('//'.config('Domain.aaid'))?>" target="_top"><img src="<?php assign($this->setVersion('/img/icon/iconAlliedID2.png')); ?>" alt="アライドID">アライドID</a>に登録された情報を利用いたします。<a href="<?php assign('//'.config('Domain.aaid'))?>/agreement" class="openNewWindow1" target="_top">アライドID利用規約</a>に同意の上、ご登録ください。</p>
        <?php endif; ?>
    <!-- /.entryOwned --></div>
<!-- /.OwnedWrap --></div>
<?php write_html($this->formHidden('base_url',Util::getBaseUrl()))?>
<?php write_html($this->formHidden('page_url',$data['pageUrl']))?>
<?php write_html($this->parseTemplate('EmbedIframeFooter.php', array('script'=>array('admin-blog/EmbedIframeControllService')))) ?>