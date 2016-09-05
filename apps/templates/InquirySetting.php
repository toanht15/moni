<section class="inquirySetting">
    <dl class="moduleSettingList jsModuleContTarget">
        <dt class="moduleSettingTitle jsModuleContTile close"><?php if (InquiryRoom::isManager($data['operator_type'])) {assign('セクション/');} ?>テンプレート設定</dt>
        <dd class="moduleSettingDetail jsModuleContTarget" style="display: none;">
            <?php if (InquiryRoom::isManager($data['operator_type'])) : ?>
            <section class="jsSection">
                <h3 class="hd3">セクション</h3>
                <div>
                    <?php write_html($this->parseTemplate('InquirySection.php', array(
                        'inquiry_sections' => $data['inquiry_sections'],
                        'inquiry_section_id_1' => 0,
                        'inquiry_section_id_2' => 0,
                        'inquiry_section_id_3' => 0,
                    ))); ?>
                    </div>
                <!-- /.inquiryCont1 --></section>
            <?php endif; ?>
            <section class="jsTemplate">
                <h3 class="hd3">テンプレート</h3>
                <p class="tenmplateEdit"><a href="javascript:void(0)" class="jsOpenTemplateModal" data-open_modal_type="Template">テンプレートを編集</a></p>
                <!-- /.inquiryCont1 --></section>
            <!-- /.moduleSettingDetail --></dd>
        <!-- /.moduleSettingList --></dl>
</section>
