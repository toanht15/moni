<ul class="inquirySetting">
    <li>
        <span class="label">大：</span><select name="inquiry_section_id_1" data-section_level="<?php assign(InquirySection::TYPE_MAJOR); ?>">
            <option value="0">選択してください</option>
            <?php foreach($data['inquiry_sections'] as $inquiry_section): ?>
                <?php if ($inquiry_section->level == InquirySection::TYPE_MAJOR) : ?>
                    <option value="<?php assign($inquiry_section->id); ?>" <?php if ($data['inquiry_section_id_1'] == $inquiry_section->id) { assign('selected'); } ?>><?php assign($inquiry_section->name); ?></option>
                <?php endif; ?>
            <?php endforeach; ?>
        </select>
    </li>
    <li>
        <span class="label">中：</span><select name="inquiry_section_id_2" data-section_level="<?php assign(InquirySection::TYPE_MEDIUM); ?>">
            <option value="0">選択してください</option>
            <?php foreach($data['inquiry_sections'] as $inquiry_section): ?>
                <?php if ($inquiry_section->level == InquirySection::TYPE_MEDIUM) : ?>
                    <option value="<?php assign($inquiry_section->id); ?>" <?php if ($data['inquiry_section_id_2'] == $inquiry_section->id) { assign('selected'); } ?>><?php assign($inquiry_section->name); ?></option>
                <?php endif; ?>
            <?php endforeach; ?>
        </select>
    </li>
    <li>
        <span class="label">小：</span><select name="inquiry_section_id_3" data-section_level="<?php assign(InquirySection::TYPE_MINOR); ?>">
            <option value="0">選択してください</option>
            <?php foreach($data['inquiry_sections'] as $inquiry_section): ?>
                <?php if ($inquiry_section->level == InquirySection::TYPE_MINOR) : ?>
                    <option value="<?php assign($inquiry_section->id); ?>" <?php if ($data['inquiry_section_id_3'] == $inquiry_section->id) { assign('selected'); } ?>><?php assign($inquiry_section->name); ?></option>
                <?php endif; ?>
            <?php endforeach; ?>
        </select>
    </li>
    <li><a href="javascript:void(0)" class="linkAdd jsOpenSectionModal" data-open_modal_type="SectionAdd">新しいセクションを追加する</a></li>
    <li><a href="javascript:void(0)" class="linkDelete jsOpenSectionModal" data-open_modal_type="SectionDelete">セクションを削除する</a></li>
    <!-- /.inquirySetting --></ul>
