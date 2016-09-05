<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoHeader')->render($data['pageStatus'])) ?>
<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoAccountHeader')->render($data['pageStatus'])) ?>

    <article>
        <h1 class="hd1">モニプラからのお知らせ</h1>

        <section class="infomationDetail">
            <h1><img src="<?php assign($this->setVersion($this->icon_information['icon']))?>" width="30" height="30" alt="attention"><span class="title"><?php assign($this->brand_notification_info->subject);?></span></h1>
            <p class="date"><small><?php assign($this->brand_notification_info->publish_at);?></small></p>
            <div class = "ckeditorWrap">
                <p class="ingfomationBody"><?php write_html($this->brand_notification_info->contents);?></p>
            </div>
        </section>

        <section class="backPage">
            <p><a href="<?php write_html(Util::rewriteUrl('admin-information', 'notification_list')) ?>" class="iconPrev1">お知らせ一覧へ</a></p>
            <!-- /.backPage --></section>
    </article>

<script>
    function autolink(character) {
            pattern = /(^|[\s\n]|<br>)((?:https?|ftp):\/\/[\-A-Z0-9+\u0026\u2019@#\/%?=()~_|!:,.;]*[\-A-Z0-9+\u0026@#\/%=~()_|])/gi;
            return character.replace(pattern, "$1<a href='$2'>$2</a>");
    }
    $(".ingfomationBody").html(autolink($(".ingfomationBody").html()));
</script>

<?php write_html($this->parseTemplate('BrandcoFooter.php', $data['pageStatus'])); ?>