<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoLoginOuterHeader')->render($data['pageStatus'])) ?>

<article>
<h1 class="hd1">Twitter連携</h1>
<h2 class="hd2">以下のTwitterアカウントと、連携が完了しました</h2>
<section class="editContList">
<ul>
    <li>
        <p class="fbpageList">
            <img src="<?php assign($data['account']->picture_url); ?>" alt="" width="40" height="40">
            <span><?php assign($data['account']->getName()); ?> </span>
        </p>
    </li>
</ul>
</section>
</article>
