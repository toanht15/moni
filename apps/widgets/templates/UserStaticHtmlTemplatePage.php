<div class="pagePartsWrap">
<?php
foreach($data['publicContents'] as $parts):
    switch ($parts['type']):
        case StaticHtmlTemplate::TEMPLATE_TYPE_IMAGE_SLIDER:
            write_html($this->parseTemplate('UserStaticHtmlTemplateImageSlider.php', $parts['template']));
            break;
        case StaticHtmlTemplate::TEMPLATE_TYPE_FLOAT_IMAGE:
            write_html($this->parseTemplate('UserStaticHtmlTemplateFloatImage.php', $parts['template']));
            break;
        case StaticHtmlTemplate::TEMPLATE_TYPE_FULL_IMAGE:
            write_html($this->parseTemplate('UserStaticHtmlTemplateFullImage.php', $parts['template']));
            break;
        case StaticHtmlTemplate::TEMPLATE_TYPE_TEXT:
            write_html($this->parseTemplate('UserStaticHtmlTemplateText.php', $parts['template']));
            break;
        case StaticHtmlTemplate::TEMPLATE_TYPE_INSTAGRAM:
            write_html($this->parseTemplate('UserStaticHtmlTemplateInstagram.php', $parts['template']));
            break;
        case StaticHtmlTemplate::TEMPLATE_TYPE_STAMP_RALLY:
            write_html($this->parseTemplate('UserStaticHtmlTemplateStampRally.php', $parts['template']));
            break;
        default:
            break;
    endswitch;
endforeach;?>
<?php if ($data['limitedContents'] && !$data['pageStatus']['isLogin']): ?>
    <?php write_html($this->parseTemplate('UserStaticHtmlLoginLimitButton.php')); ?>
<?php else: ?>
    <?php
    foreach($data['limitedContents'] as $parts):
        switch ($parts['type']):
            case StaticHtmlTemplate::TEMPLATE_TYPE_IMAGE_SLIDER:
                write_html($this->parseTemplate('UserStaticHtmlTemplateImageSlider.php', $parts['template']));
                break;
            case StaticHtmlTemplate::TEMPLATE_TYPE_FULL_IMAGE:
                write_html($this->parseTemplate('UserStaticHtmlTemplateFullImage.php', $parts['template']));
                break;
            case StaticHtmlTemplate::TEMPLATE_TYPE_FLOAT_IMAGE:
                write_html($this->parseTemplate('UserStaticHtmlTemplateFloatImage.php', $parts['template']));
                break;
            case StaticHtmlTemplate::TEMPLATE_TYPE_TEXT:
                write_html($this->parseTemplate('UserStaticHtmlTemplateText.php', $parts['template']));
                break;
            case StaticHtmlTemplate::TEMPLATE_TYPE_INSTAGRAM:
                write_html($this->parseTemplate('UserStaticHtmlTemplateInstagram.php', $parts['template']));
                break;
            case StaticHtmlTemplate::TEMPLATE_TYPE_STAMP_RALLY:
                write_html($this->parseTemplate('UserStaticHtmlTemplateStampRally.php', $parts['template']));
                break;
            default:
                break;
        endswitch;
    endforeach;?>
<?php endif; ?>
</div>
<link rel="stylesheet" href="<?php assign_js($this->setVersion('/css/bxslider/jquery.bxslider.css'))?>">
<script src="<?php assign($this->setVersion('/top/js/jquery.bxslider.min.js')) ?>"></script>
<?php write_html($this->scriptTag('admin-blog/StaticHtmlInstagramService'))?>
<script type="text/javascript">
    $(function(){
        var winWid = $(window).width();

        //data-slide-num
        $('.pagePartsTemplatePhotoSliderCont').each(function(){
            var self = $(this);
            var slideNum = winWid > 481 ? parseInt(self.data('slide-num'),10) : parseInt(self.data('slide-num-sp'),10);
            var slideOptions = {
                slideWidth: self.width() / slideNum,
                minSlides: slideNum,
                maxSlides: slideNum,
                slideMargin: slideNum > 1 ? 10 : 0,
                pager: false,
                auto: false,
                speed: 500,
                controls: false
            }
            if( $('.pagePartsTemplateSlide',self).size() > slideNum ){
                slideOptions.controls = true;
                slideOptions.pager = true;
                slideOptions.auto = true;
                slideOptions.pause = 3000;
                slideOptions.speed = 500;
                slideOptions.infiniteLoop = true;
            }
            else {
                slideOptions.touchEnabled = false;
            }
            self.bxSlider(slideOptions);
        })
    });
</script>