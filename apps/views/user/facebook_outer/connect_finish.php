<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoLoginOuterHeader')->render($data['pageStatus'])) ?>

<article>
<h1 class="hd1">Facebook連携</h1>
<h2 class="hd2">以下のFacebookページと連携が完了しました</h2>
<section class="editContList">
<ul>
    <?php foreach ($data['accounts'] as $account): ?>
        <li>
                <p class="fbpageList">
                    <img src="<?php assign($account->picture_url); ?>" alt="" width="40" height="40">
                    <span><?php assign($account->getName()); ?></span>
                </p>
        </li>
    <?php endforeach ?>
</ul>
</section>
</article>
