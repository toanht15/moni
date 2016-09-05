<section class="pagePartsTemplatePhotoSlider">
    <div class="pagePartsTemplatePhotoSliderCont" data-slide-num="<?php assign($data['slider_pc_image_count'] ? $data['slider_pc_image_count'] : 5);?>" data-slide-num-sp="<?php assign($data['slider_sp_image_count'] ? $data['slider_sp_image_count'] :2);?>" >
        <?php foreach($data['item_list'] as $item):?>
            <div class="pagePartsTemplateSlide">
                <div class="pagePartsTemplateSlideImage">
                    <?php if($item['link']):?>
                    <a href="<?php assign($item['link']);?>">
                        <?php endif;?>
                        <img src="<?php assign($item['image_url']);?>">
                        <?php if($item['link']):?>
                    </a>
                <?php endif;?>
                </div>
                <div class="pagePartsTemplateSlideText">
                    <?php if($item['link']):?>
                    <a href="<?php assign($item['link']);?>">
                        <?php endif;?>
                        <?php assign($item['caption']);?>
                        <?php if($item['link']):?>
                    </a>
                <?php endif;?>
                </div>
                <!-- /.pagePartsTemplatePhotoSlide --></div>
        <?php endforeach;?>
        <!-- /.pagePartsTemplatePhotoListCont --></div>
        <!-- /.pagePartsTemplatePhotoList --></section>
