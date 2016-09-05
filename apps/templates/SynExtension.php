<?php if (Util::isSmartPhone() && in_array($data['brand_id'], config('SynExtension'), true)): // TODO ハードコーディング?>
    <section class="jsSynExtension message" <?php if (!$data['visible']) write_html('style="display: none;"'); ?>>
        <h1 class="synExtensionHd1">あなたへのオススメ</h1>
        <div id="logly-lift-4088597"></div>
    </section>

    <script src="<?php write_html($this->setVersion('/js/syn/SynService.js', false)); ?>"></script>

    <?php if ($data['visible']): ?>
        <script>
            SynService.generateSynExtensionCode();
        </script>
    <?php endif; ?>
<?php endif; ?>