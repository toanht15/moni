<?php
$text_box_disabled = 'disabled';
if(!$data['disable'] && ($data['cp_share_action']->share_url || $data['error_share_url'])){
    $text_box_disabled = '';
}
?>
<section class="moduleCont1">
    <h1 class="editShare1 jsModuleContTile">シェア対象ページの設定</h1>
    <div class="moduleSettingWrap jsModuleContTarget">
        <?php if ($data['error_share_url']): ?>
            <p class="iconError1"><?php assign ( $this->ActionError->getMessage('share_url') )?></p>
        <?php endif; ?>
        <ul class="moduleSetting jsCheckToggleWrap">
            <li><label><?php write_html( $this->formRadio( 'share_url_type', PHPParser::ACTION_FORM, array($data['disable']=>$data['disable'],'checked'=> $data['cp_share_action']->share_url ? '' : 'checked'),array(CpShareActionService::TOP_PAGE_SHARE => 'このキャンペーンのトップページ'))); ?></label></li>
            <li>
                <label><?php write_html( $this->formRadio( 'share_url_type', PHPParser::ACTION_FORM, array($data['disable']=>$data['disable'],'checked'=> $data['cp_share_action']->share_url ? 'checked' : ''), array(CpShareActionService::EXTERNAL_SHARE => '外部キャンペーンページURL'))); ?>
                <p><?php write_html( $this->formText( 'share_url', PHPParser::ACTION_FORM, array('maxlength'=>'512', 'id'=>'share_url', $text_box_disabled => $text_box_disabled))); ?></p>
                <p class="jsCheckToggleTarget" <?php write_html($text_box_disabled ?  'style="display: none;"' : '')?>><a href="javascript:void(0)" id="preview">プレビューに反映</a></p>
            </li>
        </ul>
        <!-- /.moduleSettingWrap --></div>
        <?php write_html($this->formHidden('api_get_meta_data_url', Util::rewriteUrl('admin-cp', 'api_get_meta_data.json'))) ?>
    <!-- /.moduleCont1 --></section>