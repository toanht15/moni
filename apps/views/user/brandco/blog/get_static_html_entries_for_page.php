<?php foreach ($data['static_entries'] as $static_entry): ?>
    <li class="jsCategoryPanel">
        <a href="<?php write_html(Util::rewriteUrl('', 'page', array($static_entry->page_url))) ?>">
            <figure class="pageImg"><img src="<?php assign($static_entry->getImageUrl($data['brand'])) ?>" alt="<?php assign($static_entry->title) ?>"></figure>
            <p class="pageData">
                <strong><?php assign($static_entry->title) ?></strong>
                <span class="description"><?php assign($this->cutLongText($static_entry->meta_description ? $static_entry->meta_description : str_replace('&nbsp;', '', $static_entry->body, $static_entry->body), 150)) ?></span>
                <?php if (!$this->getService('BrandGlobalSettingService')->getBrandGlobalSetting($data['brand']->id, BrandGlobalSettingService::CMS_CATEGORY_LIST_DATETIME_HIDDEN)->content): ?>
                    <span class="date"><?php assign(date('Y/m/d H:i', strtotime($static_entry->public_date))) ?></span>
                <?php endif; ?>
            </p>
        </a>
    </li>
<?php endforeach; ?>