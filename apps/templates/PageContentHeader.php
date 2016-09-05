<ul class="snsBtns-btn">
    <?php if (in_array(StaticHtmlEntries::SNS_PLUGIN_FACEBOOK, $data['sns_plugin_ids'])): ?>
        <li><div class="fb-like" data-href="<?php assign(Util::getCurrentUrl(true))?>" data-layout="button_count" data-action="like" data-show-faces="false" data-share="false"></div></li>
    <?php endif; ?>
    <?php if (in_array(StaticHtmlEntries::SNS_PLUGIN_TWITTER, $data['sns_plugin_ids'])): ?>
        <li><a href="https://twitter.com/share" class="twitter-share-button" data-lang="ja" data-count="none">ツイート</a></li>
    <?php endif; ?>
    <?php if (in_array(StaticHtmlEntries::SNS_PLUGIN_GOOGLE, $data['sns_plugin_ids'])): ?>
        <li><div class="g-plusone" data-size="medium" data-annotation="none"></div></li>
    <?php endif; ?>
    <?php if ($data['custom_plugin']): ?>
        <li><?php write_html($data['custom_plugin']) ?></li>
    <?php endif; ?>
<!-- /.snsBtns-btn --></ul>