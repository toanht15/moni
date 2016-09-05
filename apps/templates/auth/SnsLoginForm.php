<ul class="loginSns">
    <?php foreach ($data['available_sns_accounts'] as $available_sns_account): ?>
        <?php switch($available_sns_account):
            case SocialAccount::SOCIAL_MEDIA_FACEBOOK: ?>
                <li class="btnSnsFb1"><a href="<?php assign(Util::rewriteUrl( 'auth', 'service_login', '', $this->fetchSNSQuery('fb', $data))) ?>" title="Facebook" class="login1"><span class="inner">Facebook</span></a></li>
                <?php break; ?>
            <?php case SocialAccount::SOCIAL_MEDIA_TWITTER: ?>
                <li class="btnSnsTw1"><a href="<?php assign(Util::rewriteUrl( 'auth', 'service_login', '', $this->fetchSNSQuery('tw', $data))) ?>" title="Twitter" class="login1"><span class="inner">Twitter</span></a></li>
                <?php break; ?>
            <?php case SocialAccount::SOCIAL_MEDIA_LINE: ?>
                <li class="btnSnsLn1"><a href="<?php assign(Util::rewriteUrl( 'auth', 'service_login', '', $this->fetchSNSQuery('line', $data))) ?>" title="LINE" class="login1"><span class="inner">LINE</span></a></li>
                <?php break; ?>
            <?php case SocialAccount::SOCIAL_MEDIA_INSTAGRAM: ?>
                <li class="btnSnsIg1"><a href="<?php assign(Util::rewriteUrl( 'auth', 'service_login', '', $this->fetchSNSQuery('insta', $data))) ?>" title="Instagram" class="login1"><span class="inner">Instagram</span></a></li>
                <?php break; ?>
            <?php case SocialAccount::SOCIAL_MEDIA_GOOGLE: ?>
                <li class="btnSnsGp1"><a href="<?php assign(Util::rewriteUrl( 'auth', 'service_login', '', $this->fetchSNSQuery('ggl', $data))) ?>" title="Google" class="login1"><span class="inner">Google</span></a></li>
                <?php break; ?>
            <?php case SocialAccount::SOCIAL_MEDIA_YAHOO: ?>
                <li class="btnSnsYh1"><a href="<?php assign(Util::rewriteUrl( 'auth', 'service_login', '', $this->fetchSNSQuery('yh', $data))) ?>" title="Yahoo!" class="login1"><span class="inner">Yahoo! JAPAN ID</span></a></li>
                <?php break; ?>
            <?php case SocialAccount::SOCIAL_MEDIA_LINKEDIN: ?>
                <li class="btnSnsIn1"><a href="<?php assign(Util::rewriteUrl( 'auth', 'service_login', '', $this->fetchSNSQuery('linkedin', $data))) ?>" title="LinkedIn" class="login1"><span class="inner">LinkedIn</span></a></li>
                <?php break; ?>
            <?php endswitch; ?>
    <?php endforeach; ?>

    <?php // ▼▼ TODO NECレノボグループ対応 ?>
    <?php if ($data['pageStatus']['brand']->id == '452' || $data['pageStatus']['brand']->id == '453'): ?>
        <li><small>※利用者の同意無く勝手にシェアされることはありません。</small></li>
    <?php endif; ?>
    <?php // ▲▲ TODO NECレノボグループ対応 ?>
</ul>
