<section class="pagePartsTemplatePostImgList">
    <form>
        <ul class="instaOnlyList">
        </ul>
        <div class="pager4" style="display: none;" id="pagination">
            <ul>
                <li class="prev"><a href="javascript:void(0)">前ヘ</a></li>
                <li class="next"><a href="javascript:void(0)">次へ</a></li>
            </ul>
        </div>
        <?php write_html($this->formHidden('api_url',$data['api_url'])) ?>
        <?php write_html($this->formHidden('number_image_per_page', Util::isSmartPhone() ? StaticHtmlInstagrams::NUMBER_IMAGE_PER_PAGE_SP : StaticHtmlInstagrams::$number_image_per_pages[$data['layout_type']])) ?>
        <?php write_html($this->formHidden('next_id',0)) ?>
        <?php write_html($this->formHidden('previous_id',0)) ?>
        <?php write_html($this->csrf_tag()); ?>
        <?php if(Util::isSmartPhone()): ?>
            <?php write_html($this->formHidden('isSP',1)) ?>
        <?php endif; ?>
    </form>
    <!-- /.pagePartsTemplatePostImgList --></section>
<?php write_html($this->scriptTag('BrandcoInstagramService'))?>