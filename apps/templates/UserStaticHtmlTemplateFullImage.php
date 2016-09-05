<section class="pagePartsTemplateFullImage">
    <div class="pagePartsTemplateFullImageCont">
        <figure>
            <?php if($data['link']):?>
            <a href="<?php assign($data['link']);?>">
            <?php endif;?>
                <img src="<?php assign($data['image_url']);?>">
            <?php if($data['link']):?>
            </a>
            <?php endif;?>
        <figcaption><?php assign($data['caption']);?></figcaption></figure>
        <!-- /.pagePartsTemplateFullImageCont --></div>
    <!-- /.pagePartsTemplateFullImage --></section>

