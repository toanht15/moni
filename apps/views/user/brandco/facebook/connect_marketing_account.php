<?php write_html($this->parseTemplate('BrandcoModalHeader.php', array(
    'brand' => $data['brand'],
))) ?>

<?php if($_GET['refreshTop']):?>
    <script>
        parent.Brandco.helper.brandcoBlockUI();
    </script>

    <!-- セグメント一覧戻る場合 -->
    <?php if (strpos($_GET['callback_url'], 'segment_list') !== false): ?>
        <script>
            window.top.location.replace(window.top.location.href.split('?', 1)+'?showModal=ads_action');
        </script>
    <?php else: ?>
        <script>
            window.top.location.replace(window.top.location.href.split('?', 1)+'?mid=updated');
        </script>
    <?php endif;?>
<?php endif;?>

<article class="modalInner-large">
    <header class="innerFB-small">
        <?php if($data['user_name']): ?>
            <h1><?php assign($data['user_name'])?>さんが管理しているFacebook広告アカウント</h1>
        <?php endif; ?>
    </header>
    <form id="frmFacebookAdd" name="frmFacebookAdd" action="<?php assign(Util::rewriteUrl( 'facebook', 'save_marketing_account'))?>" method="post">
        <?php write_html($this->formHidden('callback_url' , $params['callback_url'])); ?>
        <?php write_html($this->csrf_tag()); ?>
        <?php write_html($this->formHidden('ads_user_id' , $data['ads_user_id'])); ?>
        <span class="iconError1 jsFbAccountError" style="display: none;"></span>
        <section class="modalInner-cont">
<?php if($data['error']): ?>
    <?php assign($data['error']) ?>
<?php else: ?>
    <?php if($data["ads_accounts"]):?>
        Facebook広告アカウントを1つ以上選択してください。
        <section class="editContList">
            <ul>
                <?php foreach ($data["ads_accounts"] as $account): ?>
                    <?php
                        if ($ad_users = $account->getData()["users"]["data"]) {
                            foreach ($ad_users as $user) {
                                if ($user["id"] == $data["sns_account_id"]) {
                                    $role = AdsAccount::$fb_account_roles[$user["role"]];
                                    break;
                                }
                            }
                        }
                    ?>
                    <li>
                        <p class="fbpageList">
                            <label for="<?php assign($account->id); ?>">
                                <input type="checkbox" id="<?php assign($account->id); ?>" value="<?php assign($account->id) ?>" name="account_ids[]">
                                <span>
                                    <?php assign($account->name." [role: ".$role.", id: ".str_replace('act_', '', $account->id)."]"); ?>
                                </span>
                            </label>
                        </p>
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>
    <?php else:?>
        新しく連携するFacebook広告がありません。<br />
        Facebook広告の管理者アカウントでログインしてください。
    <?php endif;?>
<?php endif; ?>
    </section>
</form>
    <footer>
        <p class="btnSet">
            <span class="btn2"><a href="#closeModalFrame">キャンセル</a></span>
<?php if($data['error']): ?>
    <?php if($params['callback_url']):?>
        <span class="btn3"><a href="<?php assign(urldecode($params['callback_url']).'&callback_url='.urlencode($params['callback_url']))?>">再連携</a></span>
    <?php else:?>
        <span class="btn3"><a href="<?php assign(Util::rewriteUrl('', '', array(), array('connect'=>'fb')))?>" onclick="document.frmFacebookAdd.submit();return false;">再連携</a></span>
    <?php endif;?>
<?php elseif(count($data["ads_accounts"]) > 0):?>
    <span class="btn3"><a href="javascript:void(0)" class="jsConnectFbAccount">連携</a></span>
<?php endif;?>
        </p>
    </footer>
</article>
<?php $script = array('admin-fan/ConnectAdsAccountService') ?>
<?php write_html($this->parseTemplate('BrandcoModalFooter.php', array('script' => $script))) ?>