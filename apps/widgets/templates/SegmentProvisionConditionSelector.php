<?php
$header_text = $data['is_and_condition'] ? 'AND' : 'OR';
$selector_class = $data['is_fixed_condition'] ? 'segmentSelectContPankuzu' : 'segmentSelectCont';
?>
<div class="modal1 jsModal" id="segmentProvisionConditionSelector">
    <style>
        .pagePartsSettingCont label {
            display: inline-block;
        }
    </style>
    <form>
        <section class="metricSettingBox modalCont-large jsModalCont">
            <h1 class="jsSegmentConditionTitle"><?php assign($header_text) ?>条件</h1>

            <div class="pagePartsSetting">
                <div class="pagePartsSettingCont">
                    <p>追加する条件を「項目」より選び、「値」を決めてください。</p>

                    <h2 class="segmentSelectHd">項目</h2>

                    <div class="<?php assign($selector_class) ?>">
                        <div class="segmentTab">
                            <ul class="jsProvisionCategory">
                                <?php write_html($this->parseTemplate('segment/SegmentConditionList.php', array(
                                    'conditions' => $data['category_list'],
                                    'category_mode' => SegmentCreatorService::SEGMENT_PROVISION_CATEGORY_MODE
                                ))) ?>
                            </ul>
                            <!-- /.segmentTab --></div>
                        <div class="segmentData jsSegmentData">
                            <ul class="second jsProvisionCondition">
                                <?php write_html($this->parseTemplate('segment/SegmentConditionList.php', array(
                                    'conditions' => $data['condition_list'],
                                    'category_mode' => SegmentCreatorService::SEGMENT_PROVISION_CONDITION_MODE
                                ))) ?>
                                <!-- /.second --></ul>
                            <ul class="third jsProvisionSubCondition">
                                <?php write_html($this->parseTemplate('segment/SegmentConditionList.php', array(
                                    'conditions' => $data['sub_condition_list'],
                                    'category_mode' => SegmentCreatorService::SEGMENT_PROVISION_SUB_CONDITION_MODE
                                ))) ?>
                                <!-- /.third --></ul>
                            <!-- /.segmentData --></div>
                        <!-- /.segmentSelectCont --></div>
                    <div class="segmentValue jsProvisionConditionValue" <?php if (!$data['is_fixed_condition']): ?>style="display: none"<?php endif ?>>
                        <h2 class="segmentSelectHd">値</h2>

                        <div class="segmentValueInner">
                            <dl class="metricSettingList">
                                <dt class="jsSPConditionTitle"><?php if ($data['condition_view_title']) assign($data['condition_view_title']); ?></dt>
                                <span class="iconError1 jsSPCSelectorError" style="display: none"></span>
                                <dd><ul class="status jsProvisionConditionDetail">
                                        <?php if ($data['condition_view']) write_html($data['condition_view']); ?>
                                        <!-- /.status --></ul></dd>
                            </dl>
                            <!-- /.segmentValueInner --></div>
                        <!-- /.segmentValue --></div>
                        <p class="option"><label><input type="checkbox" name="not_condition_flg">除外条件（NOT）に設定</label></p>
                    <!-- /.pagePartsSettingCont --></div>
                <p class="btnSet">
                    <span class="btn2"><a href="javascript:void(0);" class="small1 jsCloseSPCComponent">キャンセル</a></span>
                    <span class="btn3"><a href="javascript:void(0);" class="small1 jsSaveSPCComponent">設定する</a></span>
                </p>
                <!-- /.pagePartsSetting --></div>

        </section>
    </form>
    <!-- /#segmentProvisionConditionSelector --></div>
