<section class="inquiryEdit1 jsRoom">
    <form action="<?php assign(Util::rewriteUrl(InquiryRoom::getDir($data['operator_type']), 'api_save_inquiry_room.json')); ?>" method="POST">
        <?php write_html($this->csrf_tag()); ?>
        <?php write_html($this->formHidden('inquiry_room_id', $data['inquiry_room']->id)); ?>
        <section class="inquiryCont1">
            <h1 class="jsModuleContTile">カテゴリー</h1>
            <div class="inquirySettingWrap jsModuleContTarget">
                <p><?php assign(Inquiry::getCategory($data['inquiry']->category)); ?></p>
                <!-- /.inquirySettingWrap --></div>
            <!-- /.inquiryCont1 --></section>

        <?php if (InquiryRoom::isManager($data['operator_type'])): ?>
            <section class="inquiryCont1">
                <h1 class="jsModuleContTile">直前のURL</h1>
                <div class="inquirySettingWrap jsModuleContTarget">
                    <p><a href="<?php assign($data['inquiry']->referer); ?>" target="_blank"><?php assign($data['inquiry']->referer); ?></a></p>
                    <!-- /.inquirySettingWrap --></div>
                <!-- /.inquiryCont1 --></section>
        <?php endif; ?>

        <section class="inquiryCont1">
            <h1 class="jsModuleContTile">担当<small class="textLimit">（最大50文字）</small></h1>
            <div class="inquirySettingWrap jsModuleContTarget">
                <span class="jsOperatorNameError"></span>
                <p><input name="operator_name" type="text" autocomplete="on" value="<?php assign($data['inquiry_room']->operator_name); ?>"></p>
                <input name="dummy" type="text" style="display: none;">
                <!-- /.inquirySettingWrap --></div>
            <!-- /.inquiryCont1 --></section>

        <?php if (InquiryRoom::isManager($data['operator_type'])): ?>
        <section class="inquiryCont1">
            <h1 class="jsModuleContTile">セクション</h1>
            <div class="inquirySettingWrap jsModuleContTarget jsSection">
                <?php write_html($this->parseTemplate('InquirySection.php', array(
                    'inquiry_sections' => $data['inquiry_sections'],
                    'inquiry_section_id_1' => $data['inquiry_room']->inquiry_section_id_1,
                    'inquiry_section_id_2' => $data['inquiry_room']->inquiry_section_id_2,
                    'inquiry_section_id_3' => $data['inquiry_room']->inquiry_section_id_3,
                ))); ?>
                <!-- /.inquirySettingWrap --></div>
            <!-- /.inquiryCont1 --></section>
        <?php endif; ?>

        <section class="inquiryCont1">
            <h1 class="jsModuleContTile">対応状況</h1>
            <div class="inquirySettingWrap jsModuleContTarget">
                <p>
                    <select name="status">
                        <?php foreach(InquiryRoom::$status_options as $key => $val): ?>
                            <option value="<?php assign($key); ?>" <?php if ($data['inquiry_room']->status == $key) { assign('selected'); } ?>><?php assign($val); ?></option>
                        <?php endforeach; ?>
                    </select>
                </p>
                <!-- /.inquirySettingWrap --></div>
            <!-- /.inquiryCont1 --></section>

        <section class="inquiryCont1">
            <h1 class="jsModuleContTile">備考</h1>
            <div class="inquirySettingWrap jsModuleContTarget">
                <p><textarea name="remarks" id="" cols="25" rows="10"><?php assign($data['inquiry_room']->remarks); ?></textarea></p>
                <!-- /.inquirySettingWrap --></div>
            <!-- /.inquiryCont1 --></section>

        <p class="btnSet"><span class="btn3"><a href="javascript:void(0)" class="jsRoomSave">設定項目を更新する</a></span></p>

    </form>
    <!-- /.inquiryEdit1 --></section>