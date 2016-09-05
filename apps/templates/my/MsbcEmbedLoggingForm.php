<ul class="signupSns">
    <?php if(in_array(SocialAccountService::SOCIAL_MEDIA_FACEBOOK,$data['login_types'])): ?>
        <li class="btnSnsFb1"><a data-href="<?php assign(Util::rewriteUrl( 'auth', 'service_login', '', array('platform' => 'fb','redirect_url' => ''))) ?>" href="javascript:void(0)" class="jLogin login1"><span>Facebook</span></a></li>
    <?php endif; ?>
    <?php if(in_array(SocialAccountService::SOCIAL_MEDIA_TWITTER,$data['login_types'])): ?>
        <li class="btnSnsTw1"><a data-href="<?php assign(Util::rewriteUrl( 'auth', 'service_login', '', array('platform' => 'tw','redirect_url' => ''))) ?>" href="javascript:void(0)" class="jLogin login1"><span>Twitter</span></a></li>
    <?php endif; ?>
    <?php if(in_array(SocialAccountService::SOCIAL_MEDIA_LINKEDIN,$data['login_types'])): ?>
        <li class="btnSnsIn1"><a data-href="<?php assign(Util::rewriteUrl( 'auth', 'service_login', '', array('platform' => 'linkedin','redirect_url' => ''))) ?>" href="javascript:void(0)" class="jLogin login1"><span>LinkedIn</span></a></li>
    <?php endif; ?>
    <?php if(in_array(SocialAccountService::SOCIAL_MEDIA_YAHOO,$data['login_types'])): ?>
        <li class="btnSnsYh1"><a data-href="<?php assign(Util::rewriteUrl( 'auth', 'service_login', '', array('platform' => 'yh','redirect_url' => ''))) ?>" href="javascript:void(0)" class="jLogin login1"><span>Yahoo! JAPAN ID</span></a></li>
    <?php endif; ?>
    <?php if(in_array(SocialAccountService::SOCIAL_MEDIA_INSTAGRAM,$data['login_types'])): ?>
        <li class="btnSnsIg1"><a data-href="<?php assign(Util::rewriteUrl( 'auth', 'service_login', '', array('platform' => 'insta','redirect_url' => ''))) ?>" href="javascript:void(0)" class="jLogin login1"><span>Instagram</span></a></li>
    <?php endif; ?>
    <?php if(in_array(SocialAccountService::SOCIAL_MEDIA_GOOGLE,$data['login_types'])): ?>
        <li class="btnSnsGp1"><a data-href="<?php assign(Util::rewriteUrl( 'auth', 'service_login', '', array('platform' => 'ggl','redirect_url' => ''))) ?>" href="javascript:void(0)" class="jLogin login1"><span>Google</span></a></li>
    <?php endif; ?>
    <?php if(in_array(SocialAccountService::SOCIAL_MEDIA_LINE,$data['login_types'])): ?>
        <li class="btnSnsLn1"><a data-href="<?php assign(Util::rewriteUrl( 'auth', 'service_login', '', array('platform' => 'line','redirect_url' => ''))) ?>" href="javascript:void(0)" class="jLogin login1"><span>LINE</span></a></li>
    <?php endif; ?>
</ul>