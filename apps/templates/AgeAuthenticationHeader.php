<!-- site base setting -->
<style>
    /* site background */
    body {
        background-color: <?php assign($data['brand']->getColorBackground())?>;
    <?php if($data['brand']->background_img_url):?>
        background-image: url(<?php assign($data['brand']->background_img_url)?>);
    <?php endif;?>
    <?php if($data['brand']->getBackgroundImageRepeatType() == Brand::BACKGROUND_IMAGE_REPEAT_TYPE_NO):?>
        background-repeat: no-repeat;
    <?php elseif($data['brand']->getBackgroundImageRepeatType() == Brand::BACKGROUND_IMAGE_REPEAT_TYPE_X):?>
        background-repeat: repeat-x;
    <?php elseif($data['brand']->getBackgroundImageRepeatType() == Brand::BACKGROUND_IMAGE_REPEAT_TYPE_Y):?>
        background-repeat: repeat-y;
    <?php elseif($data['brand']->getBackgroundImageRepeatType() == Brand::BACKGROUND_IMAGE_REPEAT_TYPE_REPEAT):?>
        background-repeat: repeat;
    <?php endif;?>
    }

    /* panel title color */
    .contBoxMain h1 {
        background-color: <?php assign($data['brand']->getColorMain())?>;
        color: <?php assign($data['brand']->getColorText())?>;
    }
    /* message box for smartphone*/
    <?php if(Util::isSmartPhone()):?>

    .hd2 {
        color: <?php assign($data['brand']->getColorText())?>;
    }
    .pageTop a{
        color: <?php assign($data['brand']->getColorText())?>;
    }
    <?php endif; ?>

    /* site title color */
    .companyName h1 {
        color: <?php assign($data['brand']->getColorText())?>;
    }

    /* nav link color */
    .gnavi li, .gnavi li a {
        color: <?php assign($data['brand']->getColorText())?>;
    }
    body>footer, body>footer a {
        color: <?php assign($data['brand']->getColorText())?>;
    }

    <?php if ($data['brand']->isLimitedBrandPage(BrandInfoContainer::getInstance()->getBrandGlobalSettings())): ?>
    .newLabel {position:absolute; top:24px ; left:3px; z-index:500;}
    .newLabel span {padding:0 6px; font-size:11px; color:#FFF; line-height:11px; vertical-align:middle; text-align:center; border-radius:3px; border:1px solid #FFF; background:rgba(0,0,0,0.25);}
    <?php endif ?>
</style>
<header>
    <section class="account">
        <ul>
            <li class="btn3"></li>
            <li class="login"></li>
        </ul>
    </section>
    <section class="company">
        <div class="companyName">
            <p class="logo jsEditAreaWrap">
                <img src="<?php assign($data['brand']->getProfileImage())?>" width="130" height="130" alt="" style="position: absolute;left: 0;top: 0;">
            </p>
            <h1 class="jsEditAreaWrap">
                <?php assign($data['brand']->name) ?>
            </h1>
        </div>
    </section>
</header>