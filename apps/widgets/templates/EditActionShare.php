<?php if($data['is_fan_list_page']): ?>
    <?php $disable = '' ?>
<?php else: ?>
    <?php $disable = ($data['action']->status == CpAction::STATUS_FIX) ? 'disabled' : '' ?>
<?php endif; ?>

<section class="moduleEdit1">
    <?php write_html($this->parseTemplate('CpActionModulePlaceholder.php', array('disable'=>$disable))); ?>
    <?php if($data['can_share_external_page']): ?>
        <?php write_html($this->parseTemplate('CpActionModuleShareUrl.php', array(
            'disable' => $disable,
            'cp_share_action' => $data['cp_share_action'],
            'error_share_url' => $data['error_share_url']
        ))); ?>
    <?php endif; ?>
    <?php write_html(aafwWidgets::getInstance()->loadWidget('CpActionModuleDeadLine')->render([
        'ActionForm'       => $data['ActionForm'],
        'ActionError'      => $data['ActionError'],
        'cp_action'        => $data['action'],
        'is_login_manager' => $data['pageStatus']['isLoginManager'],
        'disable'          => $disable,
    ])); ?>
    <!-- /.moduleEdit1 --></section>
<section class="modulePreview1">
    
    <header class="modulePreviewHeader">
        <p>スマートフォン<a href="#" class="toggle_switch left jsModulePreviewSwitch">toggle_switch</a>PC</p>
        <!-- /.modulePreviewHeader --></header>
    <ul class="tablink1" id="shareTab">
        <li class="current" id="fbShareTab"><span>Facebook</span></li>
        <!-- /.tablink1 --></ul>
    <div class="displaySP jsModulePreviewArea">
        <section class="messageWrap">
            <section class="message_share" id="fbSharePreview">
                <h1 class="messageHd1"><?php assign($data['ActionForm']['title']); ?></h1>
                <div class="shareInner">
                    <div class="targettPost" id="external_page_preview" <?php write_html(($data['ActionForm']['share_url'] || $data['error_share_url']) ? '' : "style='display: none'")?>>
                        <div class="figure">
                            <figure><img src="<?php assign($data['error_share_url'] ? '' : $data['meta_tags']->image); ?>" alt="" id="og_image" <?php write_html( ($data['meta_tags']->image && !$data['error_share_url']) ? '' : "style='display: none'")?>></figure>
                        </div>
                        <p class="title">
                            <strong id="og_title"><?php assign($data['error_share_url'] ? '' : $data['meta_tags']->title); ?></strong>
                        </p>
                        <!-- /.targettPost --></div>

                    <div class="targettPost" id="top_page_preview" <?php write_html(($data['cp_share_action']->meta_data || $data['error_share_url']) ? 'style="display: none;"' : '')?>>
                        <div class="figure">
                            <figure><img src="<?php assign($data['cp_og_info']['image']); ?>" alt=""></figure>
                        </div>
                        <p class="title">
                            <strong class="title"><?php assign($data['cp_og_info']['title']); ?></strong>
                        </p>
                        <!-- /.targettPost --></div>

                    <p>
                        <textarea class="fbSharePreviewArea" placeholder="<?php write_html($data['ActionForm']['placeholder']); ?>" readonly id="shareTextPreview"></textarea>
                    </p>

                    <!-- /.shareInner --></div>
                <div class="messageFooter">
                    <ul class="btnSet">
                        <li class="btnShareFb"><a class="large1"><?php assign($data['is_last_action'] ? 'シェアする' : 'シェアして次へ'); ?></a></li>
                    </ul>
                    <p class="skip"><a><small>シェアせず次へ</small></a></p>
                </div>
                <!-- /.message_share --></section>
        <!-- /.displayPC --></div>
    <!-- /.modulePreview --></section>
