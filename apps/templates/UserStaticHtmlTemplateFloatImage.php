<section class="pagePartsTemplateFloatImage">
    <div class="pagePartsTemplateFloatImage<?php write_html($data['position_type'] == StaticHtmlFloatImage::IMAGE_POSITION_LEFT ? "Left":"Right");?>">
        <figure <?php if(Util::isSmartPhone() && $data['smartphone_float_off_flg']) write_html('class="pagePartsTemplateFloatNone"');?>>
            <?php if($data['link']):?>
            <a href="<?php assign($data['link']);?>">
            <?php endif;?>
                <img src="<?php assign($data['image_url']);?>">
            <?php if($data['link']):?>
            </a>
            <?php endif;?>
            <figcaption><?php assign($data['caption']);?></figcaption></figure>
        <?php write_html($this->nl2brAndHtmlspecialchars($data['text']));?>
    </div>
</section>
