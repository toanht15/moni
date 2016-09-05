<footer class="moniplaFooter">
    <div class="copyright">
        <ul class="links">
            <li><a href="<?php assign(Util::rewriteUrl('inquiry', 'index', array(), array('cp_id' => 0))); ?>">お問い合わせ</a></li>
            <?php if (BrandInfoContainer::getInstance()->getBrandPageSetting()->agreement): ?>
                <li><a href="<?php assign(Util::rewriteUrl('', 'agreement')); ?>"><?php assign($data['brand']->name); ?>公式ファンサイト利用規約</a></li>
            <?php endif; ?>
        </ul>
        <small>Copyright &copy; <?php assign(date('Y'));?> <?php assign($data['brand']->enterprise_name ?: $data['brand']->name); ?>. ALL RIGHTS RESERVED</small>
    </div>

    <ul class="links">
        <li><a href="//allied-id.com/agreement" target="_blank">アライドID利用規約</a></li>
        <li><a href="http://www.aainc.co.jp/privacy/" target="_blank">個人情報保護方針</a></li>
        <li><a href="http://allied-id.com/maintenance" target="_blank">メンテナンス情報</a></li>
    </ul>
    <p class="poweredBy"><small>Powered by monipla</small></p>
</footer>

<?php write_html($this->parseTemplate('GoogleAnalytics.php', $data)); ?>
