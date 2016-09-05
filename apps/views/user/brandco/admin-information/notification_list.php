<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoHeader')->render($data['pageStatus'])) ?>
<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoAccountHeader')->render($data['pageStatus'])) ?>

    <article>
        <h1 class="hd1">モニプラからのお知らせ</h1>

        <ul class="infomationList">
            <?php if(count($this->notifications) > 0):?>
                <?php foreach($this->notifications as $brand_notification):?>
                    <?php if ($brand_notification['brand_read_mark']):?>
                        <?php if ($brand_notification['id'] == $brand_notification['brand_read_mark']->brand_notification_id):?>
                            <li><a href="<?php assign(Util::rewriteUrl( 'admin-information', 'notification_list_details', array($brand_notification['id']) )); ?>"><figure><img src="<?php assign($this->setVersion($brand_notification['icon_information']['icon']))?>" width="30" height="30" alt="infomation"></figure><span class="title"><?php assign($brand_notification['subject']);?></span><small class="date"><?php assign($brand_notification['publish_at']);?></small></a></li>
                        <?php else:?>
                            <li class="notRead"><a href="<?php assign(Util::rewriteUrl( 'admin-information', 'notification_list_details', array($brand_notification['id']) )); ?>"><figure><img src="<?php assign($this->setVersion($brand_notification['icon_information']['icon']))?>" width="30" height="30" alt="infomation"></figure><span class="title"><?php assign($brand_notification['subject']);?></span><small class="date"><?php assign($brand_notification['publish_at']);?></small></a></li>
                        <?php endif; ?>
                    <?php else:?>
                        <li class="notRead"><a href="<?php assign(Util::rewriteUrl( 'admin-information', 'notification_list_details', array($brand_notification['id']) )); ?>"><figure><img src="<?php assign($this->setVersion($brand_notification['icon_information']['icon']))?>" width="30" height="30" alt="infomation"></figure><span class="title"><?php assign($brand_notification['subject']);?></span><small class="date"><?php assign($brand_notification['publish_at']);?></small></a></li>
                    <?php endif; ?>
                <?php endforeach;?>
            <?php else:?>
                表示できるお知らせはありません
            <?php endif; ?>
        <!-- /.infomationList --></ul>

        <div class="pager1">
            <?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoManagerPager')->render(array(
                'TotalCount' => $data['totalEntriesCount'],
                'CurrentPage' => $this->params['p'],
                'Count' => $data['pageLimited'],
            ))) ?>
        <!-- /.pager1 --></div>
    </article>

<?php write_html($this->parseTemplate('BrandcoFooter.php', $data['pageStatus'])); ?>