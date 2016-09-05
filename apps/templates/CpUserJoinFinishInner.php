<?php $message_text = $data['message_info']['concrete_action']->html_content ? $data['message_info']['concrete_action']->html_content : $this->toHalfContentDeeply($data['message_info']['concrete_action']->text); ?>
<section class="messageText"><?php write_html($message_text); ?></section>

<ul class="btnSet">
    <?php if($data['cp_info']['cp']['back_monipla_flg'] && $data['pageStatus']['brand']->hasOption(BrandOptions::OPTION_TOP, BrandInfoContainer::getInstance()->getBrandOptions())): ?>
        <li class="btn3"><a href="<?php write_html(Util::rewriteUrl(null, null)) ?>" class="large1_arrow1">サイトトップへ</a></li>
    <?php endif; ?>
    <!-- /.btnSet --></ul>