<div class="makeStepTypeCont">
    <section class="makeNewStepList">
        <style>.newStepDetail li {display: inline-block;}</style>
        <form name="frm" action="<?php assign(Util::rewriteUrl('admin-cp', 'edit_customize_skeleton')); ?>">
            <?php write_html($this->formHidden('type', Cp::TYPE_CAMPAIGN)) ?>
            <?php write_html($this->formHidden('shipping', CpCreator::SHIPPING_ADDRESS_NONE)) ?>
            <?php write_html($this->formHidden('announce', CpNewSkeletonCreator::ANNOUNCE_NON_INCENTIVE)) ?>
            <?php write_html($this->formHidden('join_limit_flg', Cp::JOIN_LIMIT_OFF)) ?>
            <ul class="newStepDetail">
                <li class="moduleSetType">
                    <input type="radio" name="basic_type" value="<?php assign(Cp::PERMANENT_SKELETON_QUESTIONNAIRE) ?>"
                           class="customRadioModule" id="perm_questionnaire" checked="checked"><label for="perm_questionnaire"><img
                            src="<?php assign($this->setVersion('/img/module/enquete1.png')) ?>" width="55" height="55"
                            alt="アンケート">アンケート</label>
                </li>
                <?php if($data['canUsePaymentModule']):?>
                <li class="moduleSetType">
                    <input type="radio" name="basic_type" value="<?php assign(Cp::PERMANENT_SKELETON_PAYMENT) ?>"
                           class="customRadioModule" id="perm_payment"><label for="perm_payment"><img
                            src="<?php assign($this->setVersion('/img/module/enquete1.png')) ?>" width="55" height="55"
                            alt="決済">決済</label>
                </li>
                <?php endif;?>
            <!-- /.newStepDetail --></ul>
            <p class="actionWrap">
                <span class="btn3">
                    <a href="javascript:document.frm.submit();" class="middle1">次へ</a>
                </span>
            </p>
        </form>
        <!-- /.makeNewStepList --></section>

    <!-- /.makeStepTypeCont --></div>