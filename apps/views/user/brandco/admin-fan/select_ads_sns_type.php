<?php write_html($this->parseTemplate('BrandcoModalHeader.php', array(
    'brand' => $data['brand'],
))) ?>
<article class="modalInner-large">
    <header>
        <h1>追加するソーシャルメディアを選んでください。</h1>
    </header>
    <section class="modalInner-cont">
        <section class="editAdSNS">
            <ul>
                <?php
                $fb_callback_url = $data['callback_url'] ? $data['callback_url']. '?showModal='.SocialApps::PROVIDER_FACEBOOK : '';
                $tw_callback_url = $data['callback_url'] ? $data['callback_url']. '?showModal='.SocialApps::PROVIDER_TWITTER : '';
                ?>

                <?php if($this->brand->hasOption(BrandOptions::OPTION_FACEBOOK_ADS, BrandInfoContainer::getInstance()->getBrandOptions())): ?>
                    <li class="fbAccount"><a href="<?php assign(Util::rewriteUrl('facebook', 'connect_marketing_account', array(), array('callback_url' => $fb_callback_url))) ?>"><span>Facebook</span></a></li>
                <?php endif;?>

                <?php if($this->brand->hasOption(BrandOptions::OPTION_TWITTER_ADS, BrandInfoContainer::getInstance()->getBrandOptions())): ?>
                    <li class="twAccount"><a href="<?php assign(Util::rewriteUrl('twitter', 'connect_ads_account', array(), array('callback_url' => $tw_callback_url))) ?>"><span>Twitter</span></a></li>
                <?php endif;?>

            </ul>
        </section>
    </section>
    <footer>
        <p class="btn2"><a href="#closeModalFrame">閉じる</a></p>
    </footer>
</article>
<?php write_html($this->parseTemplate('BrandcoModalFooter.php')) ?>
