<?php if($data['is_fan_list_page']): ?>
    <?php $disable = '' ?>
<?php else: ?>
    <?php $disable = ($data['action']->status == CpAction::STATUS_FIX)?'disabled':'' ?>
<?php endif; ?>
    <section class="moduleEdit1">

    <?php write_html($this->parseTemplate('CpActionModuleTitle.php', array('disable' => $disable))); ?>
    <?php write_html($this->parseTemplate('CpActionModuleImage.php', array('disable' => $disable))); ?>
    <?php write_html($this->parseTemplate('CpActionModuleText.php', array('disable' => $disable))); ?>
    <?php write_html($this->parseTemplate('CpActionModuleDesign.php', array('disable' => $disable, 'design_type_arr' => CpAnnounceAction::$design_type))); ?>
    <!-- /.moduleEdit1 --></section>

    <section class="modulePreview1">
        <header class="modulePreviewHeader">
            <p>スマートフォン<a href="#" class="toggle_switch left jsModulePreviewSwitch">toggle_switch</a>PC</p>
            <!-- /.modulePreviewHeader --></header>

        <div class="displaySP jsModulePreviewArea">
            <section class="messageWrap" id="message_type">
                <section class="message_win" <?php if ($data['ActionForm']['design_type'] != CpAnnounceAction::DEFAULT_DESIGN_TYPE): ?>style="display: none"<?php endif; ?> id="message_type_<?php assign(CpAnnounceAction::DEFAULT_DESIGN_TYPE); ?>">
                    <h1 class="messageHd1">Congratulations!<span id="titlePreview"></span></h1>
                    <div class="messageInner">
                        <p class="messageImg"><img src="" width="600" height="300" id="imagePreview"></p>
                        <section class="messageText" id="textPreview"></section>
                    </div>
                <!-- /.message_win --></section>

                <section class="message" <?php if ($data['ActionForm']['design_type'] != CpAnnounceAction::NORMAL_DESIGN_TYPE): ?>style="display: none"<?php endif; ?> id="message_type_<?php assign(CpAnnounceAction::NORMAL_DESIGN_TYPE); ?>">
                    <p class="messageImg"><img src="" width="600" height="300" id="imagePreview_normal"></p>

                    <section class="messageText" id="textPreview_normal"></section>
                    
                    <!-- /.message --></section>
            </section>
        </div>

        <!-- /.modulePreview --></section>