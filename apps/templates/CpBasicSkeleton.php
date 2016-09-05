<div class="makeStepTypeCont" style="display: block;">
    <section class="makeNewStepList">
        <dl class="newStepDetail">
            <dt>
                公開設定
                <span class="iconHelp">
                <span class="text">ヘルプ</span>
                <span class="textBalloon1">
                  <span>
                    「限定」を選択すると、招待ユーザーのみ<br>参加できるキャンペーンとなります
                  </span>
                <!-- /.textBalloon1 --></span>
                <!-- /.iconHelp --></span>
            </dt>
            <?php
            $joinLimitArray = Cp::$join_limit_array;
            if (!$data['brand']->hasOption(BrandOptions::OPTION_CRM)) {
                unset($joinLimitArray[cp::JOIN_LIMIT_ON]);
            }
            ?>
            <dd><?php write_html($this->formRadio('join_limit_flg', Cp::JOIN_LIMIT_OFF, array('class' => 'customRadioBtn'), $joinLimitArray)); ?></dd>
            <dt>当選方法</dt>
            <dd><?php write_html($this->formRadio('announce_type', CpNewSkeletonCreator::ANNOUNCE_SELECTION, array('class' => 'customRadioBtn'), CpNewSkeletonCreator::$announce_type)); ?></dd>
            <dt>住所取得</dt>
            <dd><?php write_html($this->formRadio('shipping_address', CpCreator::SHIPPING_ADDRESS_ALL, array('class' => 'customRadioBtn'), CpPresentCreator::$shipping_address_type)); ?></dd>
            <dt>種別</dt>
            <dd class="moduleSetType">
                <input type="radio" name="basic_type" value="<?php assign(Cp::BASIC_SKELETON_PRESENT) ?>"
                       class="customRadioModule" id="present" checked="checked"><label for="present"><img
                        src="<?php assign($this->setVersion('/img/module/present1.png')) ?>" width="55" height="55"
                        alt="プレゼント">プレゼント</label>
                <input type="radio" name="basic_type" value="<?php assign(Cp::BASIC_SKELETON_PHOTO) ?>"
                       class="customRadioModule" id="photo"><label for="photo"><img
                        src="<?php assign($this->setVersion('/img/module/photo1.png')) ?>" width="55" height="55"
                        alt="写真投稿">写真投稿</label>
                <input type="radio" name="basic_type" value="<?php assign(Cp::BASIC_SKELETON_MOVIE) ?>"
                       class="customRadioModule" id="movie"><label for="movie"><img
                        src="<?php assign($this->setVersion('/img/module/movie1.png')) ?>" width="55" height="55"
                        alt="動画視聴">動画視聴</label>
                <input type="radio" name="basic_type" value="<?php assign(Cp::BASIC_SKELETON_QUESTIONNAIRE) ?>"
                       class="customRadioModule" id="questionnaire"><label for="questionnaire"><img
                        src="<?php assign($this->setVersion('/img/module/enquete1.png')) ?>" width="55" height="55"
                        alt="アンケート">アンケート</label>
                <input type="radio" name="basic_type" value="<?php assign(Cp::BASIC_SKELETON_GIFT) ?>"
                       class="customRadioModule" id="gift" disabled="disabled"><label for="gift"><img
                        src="<?php assign($this->setVersion('/img/module/gift1.png')) ?>" width="55" height="55"
                        alt="ギフト">ギフト
                    <span class="textBalloon1" id="text_gift"><span>この条件では開催できません</span></span></label>
                <input type="radio" name="basic_type" value="<?php assign(Cp::BASIC_SKELETON_COUPON) ?>"
                       class="customRadioModule" id="coupon" disabled="disabled"><label for="coupon"><img
                        src="<?php assign($this->setVersion('/img/module/coupon1.png')) ?>" width="55" height="55"
                        alt="クーポン">クーポン
                    <span class="textBalloon1" id="text_coupon"><span>この条件では開催できません</span></span></label>
                <input type="radio" name="basic_type" value="<?php assign(Cp::BASIC_SKELETON_INSTANT_WIN) ?>"
                       class="customRadioModule" id="instantWin" disabled="disabled"><label for="instantWin"><img
                        src="<?php assign($this->setVersion('/img/module/speedwin1.png')) ?>" width="55" height="55"
                        alt="スピードくじ">スピードくじ
                    <span class="textBalloon1" id="text_instantWin"><span>この条件では開催できません</span></span></label></dd>
            <!-- /.newStepDetail --></dl>

        <p class="actionWrap"><span class="btn3"><a
                    href="<?php assign(Util::rewriteUrl('admin-cp', 'edit_customize_skeleton', array(), array(
                        'type' => Cp::TYPE_CAMPAIGN,
                        'basic_type' => Cp::BASIC_SKELETON_PRESENT,
                        'shipping' => CpCreator::SHIPPING_ADDRESS_ALL,
                        'announce' => CpNewSkeletonCreator::ANNOUNCE_SELECTION,
                        'join_limit_flg' => Cp::JOIN_LIMIT_OFF))) ?>"
                    class="middle1" id="skeleton_url">次へ</a></span></p>

        <!-- /.makeNewStepList --></section>
    <!-- /.makeStepTypeCont --></div>
