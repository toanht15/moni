<?php if($data['is_fan_list_page']): ?>
    <?php $disable = '' ?>
<?php else: ?>
    <?php $disable = ($data['action']->status == CpAction::STATUS_FIX)?'disabled':'' ?>
<?php endif; ?>
    <section class="moduleEdit1">

    <?php write_html($this->parseTemplate('CpActionModuleTitle.php', array('disable' => $disable))); ?>
    <?php write_html($this->parseTemplate('CpActionModuleImage.php', array('disable' => $disable))); ?>
    <?php write_html($this->parseTemplate('CpActionModuleText.php', array('disable' => $disable))); ?>
    <?php write_html($this->parseTemplate('CpActionModuleCVTag.php', array('disable' => $disable))); ?>
    <?php write_html($this->parseTemplate('CpActionModuleDesign.php', array('disable' => $disable, 'design_type_arr' => CpJoinFinishAction::$design_type))); ?>
    <!-- /.moduleEdit1 --></section>

    <section class="modulePreview1">
        <header class="modulePreviewHeader">
            <p>スマートフォン<a href="#" class="toggle_switch left jsModulePreviewSwitch">toggle_switch</a>PC</p>
            <!-- /.modulePreviewHeader --></header>

        <div class="displaySP jsModulePreviewArea">
            <section class="messageWrap" id="message_type">
                <section class="message_thanks" <?php if ($data['ActionForm']['design_type'] != CpJoinFinishAction::DEFAULT_DESIGN_TYPE): ?>style="display: none"<?php endif; ?> id="message_type_<?php assign(CpJoinFinishAction::DEFAULT_DESIGN_TYPE); ?>">
                    <p class="messageImg"><img src="" width="600" height="300" id="imagePreview"></p>
                    <h1 class="messageHd1">Thank you!<span id="titlePreview"></span></h1>
                    <div class="messageInner">
                      <section class="messageText" id="textPreview"></section>
                    </div>

                    <div class="messageFooter">
                        <?php if($data['cp']->back_monipla_flg && $data['pageStatus']['brand']->hasOption(BrandOptions::OPTION_TOP, BrandInfoContainer::getInstance()->getBrandOptions())): ?>
                            <ul class="btnSet">
                                <li class="btn3"><a href="javascript:void(0)" class="large1_arrow1">サイトトップへ</a></li>
                            <!-- /.btnSet --></ul>
                        <?php endif; ?>
                    </div>
                    <!-- /.message_thanks --></section>

                <section class="message" <?php if ($data['ActionForm']['design_type'] != CpJoinFinishAction::NORMAL_DESIGN_TYPE): ?>style="display: none"<?php endif; ?> id="message_type_<?php assign(CpJoinFinishAction::NORMAL_DESIGN_TYPE); ?>">
                    <p class="messageImg"><img src="" width="600" height="300" id="imagePreview_normal"></p>
                    <p class="messageText" id="textPreview_normal"></p>

                    <div class="messageFooter">
                        <?php if($data['cp']->back_monipla_flg && $data['pageStatus']['brand']->hasOption(BrandOptions::OPTION_TOP, BrandInfoContainer::getInstance()->getBrandOptions())): ?>
                            <ul class="btnSet">
                                <li class="btn3"><a href="javascript:void(0)" class="large1_arrow1">サイトトップへ</a></li>
                            <!-- /.btnSet --></ul>
                        <?php endif; ?>
                    </div>
                    <!-- /.message --></section>
            </section>
        </div>

        <!-- /.modulePreview --></section>
