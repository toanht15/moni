<?php write_html($this->parseTemplate('BrandcoModalHeader.php', array(
    'brand' => $data['brand'],
))) ?>


<article class="modalInner-large">
    <header class="innerFB-small">
        <h1><?php assign($data['userInfo']->name)?>さんが管理しているFBページ</h1>
    </header>
    <form id="frmFacebookAdd" name="frmFacebookAdd" action="<?php assign(Util::rewriteUrl( 'facebook', 'connect_app' ))?>" method="post">
        <?php write_html($this->formHidden('callback_url' , $params['callback_url'])); ?>
        <section class="modalInner-cont">
<?php if($data['error']): ?>
    <?php assign($data['error']) ?>
<?php else: ?>
    <?php if($this->listPage):?>
        Facebookページを1つ以上選択してください。
        <section class="editContList">
            <ul>
                <?php write_html($this->csrf_tag()); ?>
                <?php foreach ($this->listPage as $page): ?>
                    <?php $page = (array)$page; ?>
                    <li>
                        <p class="fbpageList">
                            <label for="pageId_<?php assign($page['id']); ?>">
                                <input type="checkbox" id="pageId_<?php assign($page['id']); ?>" value="<?php assign($page['id']) ?>" name="pageId[]">
                                <img src="https://graph.facebook.com/<?php assign($page['id']) ?>/picture" alt="" width="40" height="40">
                                <span>
                                    <?php assign($page['name']); ?>
                                    <?php if ($page['token_expired']):?>
                                        <span class="iconError1">連携の有効期限が切れています。連携延長のため、再連携が必要です。</span>
                                    <?php endif;?>
                                </span>
                            </label>
                        </p>
                        <?php write_html($this->formHidden("token_" . $page['id'], $page['access_token'])); ?>
                        <?php if ($this->ActionError && !$this->ActionError->isValid('pageId')): ?>
                            <p class="text-error"><?php assign($this->ActionError->getMessage('pageId')) ?></p>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>
    <?php else:?>
        新しく連携するFacebookページがありません。<br />
        Facebookページの管理者アカウントでログインしてください。
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
<?php elseif($this->listPage):?>
    <span class="btn3"><a href="javascript:void(0)" onclick="document.frmFacebookAdd.submit();return false;">連携</a></span>
<?php endif;?>
        </p>
    </footer>
</article>

<?php write_html($this->parseTemplate('BrandcoModalFooter.php')) ?>
