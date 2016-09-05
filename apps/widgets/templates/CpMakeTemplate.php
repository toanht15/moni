<?php
$default_key = key($data['templates']);
$default_temp = current($data['templates']);
?>
<div class="makeStepTypeCont">
    <div class="skeltonRecommendWrap">
        <section class="skeltonSetList">
            <ul>
                <?php foreach ($data['templates'] as $key => $value): ?>
                    <li class="jsCpTemplateToggle<?php if ($key == $default_key): ?> current<?php endif ?>" data-temp_type="<?php assign('cp_temp_' . $key) ?>"
                        data-temp_url="<?php assign(Util::rewriteUrl('admin-cp', 'edit_customize_skeleton', array(), $value['Param'])) ?>">
                        <?php if ($key == $default_key): ?>
                            <span><img src="<?php assign($this->setVersion('/img/module/' . $data['CpActionDetail'][$value['MainAction']]['icon'])) ?>" width="25" height="25" alt=""></span>
                            <p><?php assign(Util::cutTextByWidth($value['Title'], 740)) ?></p>
                        <?php else: ?>
                            <a href="javascript:void(0);"><span><img
                                        src="<?php assign($this->setVersion('/img/module/' . $data['CpActionDetail'][$value['MainAction']]['icon'])) ?>"
                                        width="25" height="25" alt=""></span>
                                <p><?php assign(Util::cutTextByWidth($value['Title'], 740)) ?></p></a>
                        <?php endif ?>
                    </li>
                <?php endforeach ?>
            </ul>
            <!-- /.skeltonSetList --></section>
        <section class="skeltonSetDetail">

            <div class="skeltonSetDetailWrap">
                <?php foreach ($data['templates'] as $key => $value): ?>
                    <ul class="stepList" id="<?php assign('cp_temp_' . $key) ?>"
                        <?php if ($key != $default_key): ?>style="display: none"<?php endif ?>>
                        <?php $last_order_no = 0 ?>
                        <?php foreach ($value['Group'] as $group_actions): ?>
                            <li class="stepDetail_require">
                                <?php if (count($group_actions) > 1): ?>
                                    <h1><?php assign('STEP' . ($last_order_no + 1) . '-' . ($last_order_no + count($group_actions))) ?></h1>
                                <?php else: ?>
                                    <h1><?php assign('STEP' . ($last_order_no + 1)) ?></h1>
                                <?php endif; ?>
                                <ul class="moduleList">
                                    <?php foreach ($group_actions as $action): ?>
                                        <li class="moduleDetail1"><span class="moduleIcon"><img
                                                    src="<?php assign($this->setVersion('/img/module/' . $data['CpActionDetail'][$action]['icon'])) ?>"
                                                    width="20" height="20" alt=""></span></li>
                                    <?php endforeach; ?>
                                    <!-- /.moduleList --></ul>
                                <!-- /.stepDetail_require --></li>
                            <?php $last_order_no += count($group_actions); ?>
                        <?php endforeach ?>
                        <!-- /.stepList --></ul>
                <?php endforeach ?>

                <div class="skeltonExplainWrap"><p><!-- Explanation of the Template --></p>
                    <!-- /.skeltonExplainWrap --></div>

                <p class="actionWrap"><span class="btn3"><a
                            href="<?php assign(Util::rewriteUrl('admin-cp', 'edit_customize_skeleton', array(), $default_temp['Param'])) ?>"
                            class="middle1 jsCpTempSubmitBtn">選択</a></span></p>

                <!-- /.skeltonSetDetailWrap --></div>
            <!-- /.skeltonSetDetail --></section>
        <!-- /.skeltonRecommendWrap --></div>
    <!-- /.makeStepTypeCont --></div>
