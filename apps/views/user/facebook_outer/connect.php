<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoLoginOuterHeader')->render($data['pageStatus'])) ?>
<article>
    <h1 class="hd1">Facebook連携</h1>
    <h2 class="hd2">管理しているFacebookページ一覧</h2>
    <form id="frmFacebookAdd" name="frmFacebookAdd" action="<?php assign(Util::rewriteUrl('facebook_outer', 'connect_app' ))?>" method="post">
        <?php write_html($this->formHidden('callback_url' , $data['callback_url'])); ?>
        <?php write_html($this->formHidden('token' , $data['token'])); ?>
        <?php write_html($this->csrf_tag()); ?>
        <?php if($data['error']): ?>
            <p class="iconError1"><?php assign($data['error']) ?></p>
        <?php else: ?>
            <?php if($this->listPage):?>
                <section class="editContList">
                    <ul>
                        <?php write_html($this->csrf_tag()); ?>
                        <?php foreach ($this->listPage as $page): ?>
                        <?php $page = (array)$page; ?>
                        <li>
                            <?php if ($this->ActionError && !$this->ActionError->isValid('pageId')): ?>
                                <p class="iconError1"><?php assign($this->ActionError->getMessage('pageId')) ?></p>
                            <?php endif; ?>
                            <p class="fbpageList">
                                <label for="pageId_<?php assign($page['id']); ?>">
                                    <input type="checkbox" id="pageId_<?php assign($page['id']); ?>" value="<?php assign($page['id']) ?>" name="pageId[]">
                                    <img src="https://graph.facebook.com/<?php assign($page['id']) ?>/picture" alt="" width="40" height="40">
                                    <span>
                                        <?php assign($page['name']); ?>
                                        <?php if ($page['token_expired']): ?>
                                            <span class="iconError1">連携の有効期限が切れています。連携延長のため、再連携が必要です。</span>
                                        <?php endif; ?>
                                    </span>
                                </label>
                            </p>
                            <?php write_html($this->formHidden("token_" . $page['id'], $page['access_token'])); ?>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <div class="pager1">
                        <p><?php assign(count($this->listPage)); ?>件表示しています</p>
                    </div>
                </section>
            <?php else:?>
                <p class="iconError1">新しく連携するFacebookページがありません。Facebookページの登録をお願い致します。</p>
            <?php endif;?>
        <?php endif ?>
    <p class="btnSet">
        <?php if($data['error']): ?>
            <?php if($data['callback_url']):?>
                <span class="btn3"><a href="<?php assign(urldecode($data['callback_url']).'&callback_url='.urlencode($data['callback_url']))?>">再連携</a></span>
            <?php else:?>
                <span class="btn3"><a href="<?php assign(Util::rewriteUrl('', '', array(), array('connect'=>'fb')))?>" onclick="document.frmFacebookAdd.submit();return false;">再連携</a></span>
            <?php endif;?>
        <?php elseif($this->listPage):?>
            <span class="btn3"><a href="javascript:void(0)" onclick="document.frmFacebookAdd.submit();return false;">連携</a></span>
        <?php endif;?>
    </p>
    </form>
</article>
