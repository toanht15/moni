<?php if(!$data['brand']->isPlan(BrandContract::PLAN_PROMOTION_MONIPLA, BrandInfoContainer::getInstance()->getBrandContract())): ?>
    <p class="loginAttention">本サービスは、モニプラ・<a href="<?php assign('//'.config('Domain.aaid'))?>" target="_blank"><img src="<?php assign($this->setVersion('/img/icon/iconAlliedID2.png')); ?>" alt="アライドID">アライドID</a>に登録された情報を利用いたします。</p>
<?php endif; ?>
<?php // TODO ▼▼text-align:center;を入れました ?>
<p class="supplement1" style="text-align:center;">
    <a href="<?php assign('//'.config('Domain.aaid'))?>/agreement" class="openNewWindow1" target="_blank">アライドID利用規約</a>
    <?php if($data['brand']->id == 479): // TODO ハードコーディング ?>
        、<a href="<?php assign(Util::rewriteUrl('page', 'privacy')); ?>" class="openNewWindow1" target="_blank">一般社団法人 日本健康生活推進協会 個人情報保護方針</a>
    <?php else: ?>
        <?php if (BrandInfoContainer::getInstance()->getBrandPageSetting()->agreement): ?>
            、<a href="<?php assign(Util::rewriteUrl('', 'agreement')); ?>" target="_blank"><?php assign($data['brand']->name); ?>公式ファンサイト利用規約</a>
        <?php endif; ?>
    <?php endif; ?>
    に同意の上、ご<?php assign($data['action']); ?>ください。
</p>
<?php // TODO ▲▲text-align:center;を入れました ?>
