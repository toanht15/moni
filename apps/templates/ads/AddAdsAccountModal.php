<div class="modal1 jsModal" id="selectAdsSnSType">
    <section class="modalCont-large jsModalCont">
        <iframe data-src="<?php assign(Util::rewriteUrl('admin-fan', 'select_ads_sns_type', array(), array('callback_url' => $data['callback_url']))) ?>"
                frameborder="0"></iframe>
    </section>
</div>

<div class="modal1 jsModal" id="AddFbAccountModal">
    <section class="modalCont-large jsModalCont">
        <iframe
            data-src="<?php assign(Util::rewriteUrl('facebook', 'connect_marketing_account', array(), array('callback_url' => $data['callback_url'] ? $data['callback_url']. '?showModal='.SocialApps::PROVIDER_FACEBOOK : '', 'code' => $_GET['code'], 'state' => $_GET['state'], 'error_reason' => $_GET['error_reason']))) ?>"
            frameborder="0"></iframe>
    </section>
</div>

<div class="modal1 jsModal" id="AddTwitterAccountModal">
    <section class="modalCont-large jsModalCont">
        <iframe
            data-src="<?php assign(Util::rewriteUrl('twitter', 'connect_ads_account', array(), array('callback_url' => $data['callback_url'] ? $data['callback_url']. '?showModal='.SocialApps::PROVIDER_TWITTER : '', 'oauth_token' => $_GET['oauth_token'], 'oauth_verifier' => $_GET['oauth_verifier']))) ?>"
            frameborder="0"></iframe>
    </section>
</div>

<?php if($this->params['showModal'] == 'select_sns'): ?>
    <script>
        jQuery(function($){
            Brandco.unit.openModal('#selectAdsSnSType');
        });
    </script>
<?php endif;?>

<!-- SNS連携からの戻りの時はモーダルを開く -->
<?php if($this->params['showModal'] == SocialApps::PROVIDER_FACEBOOK): ?>
    <script>
        jQuery(function($){
            Brandco.unit.openModal('#AddFbAccountModal');
        });
    </script>
<?php endif;?>

<?php if($this->params['showModal'] == SocialApps::PROVIDER_TWITTER): ?>
    <script>
        jQuery(function($){
            Brandco.unit.openModal('#AddTwitterAccountModal');
        });
    </script>
<?php endif;?>