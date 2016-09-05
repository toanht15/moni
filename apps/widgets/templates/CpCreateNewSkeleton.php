<div class="makeStepTypeCont1">

    <section class="skeletonWrap">
        <form id="newSkeletonForm" name="newSkeletonForm" action="<?php assign(Util::rewriteUrl( 'admin-cp', 'save_setting_skeleton',array(Cp::SKELETON_NEW), array('cps_type' => $data['type']))); ?>" method="POST">
            <?php write_html($this->csrf_tag()); ?>
            <?php write_html($this->formHidden('cps_type', Cp::TYPE_CAMPAIGN)) ?>
            <?php write_html($this->formHidden('skeleton_type', Cp::SKELETON_NEW)) ?>
            <?php write_html($this->formHidden('join_limit_flg', $data['join_limit_flg'])) ?>
            <?php write_html($this->formHidden('announce_type', $data['announce_type'])) ?>
            <?php write_html($this->formHidden('groupCount', '', array("id" => "newSkeletonGroupCount"))) ?>
        </form>
        <p class="skeltonDetail">
            <span class="iconHelp">
            <span class="text">ヘルプ</span>
            <span class="textBalloon1">
                <span>
                  繋がっているSTEPでは、ユーザー自らの操作で次に進めます<br>
                  離れているSTEPでは、次に進むためには管理者の操作が必要です。
            </span>
            <!-- /.textBalloon1 --></span>
            <!-- /.iconHelp --></span>
            <span class="actionWrap">
                <span class="btn3"><a href="javascript:void(0)" class="middle1 newSkeletonSubmitButton">次へ</a></span>
            </span>
        </p>
        <?php
        $step_plus = 0;
        if ($data['basic_type'] == Cp::BASIC_SKELETON_QUESTIONNAIRE) {
            $step_plus += 1;
        }
        if ($data['basic_type'] == Cp::BASIC_SKELETON_PHOTO) {
            $step_plus += 1;
        }
        if ($data['basic_type'] == Cp::BASIC_SKELETON_MOVIE) {
            $step_plus += 1;
        }
        //キャンペーンテンプレート
        if ($data['basic_type'] == Cp::TEMPLATE_SKELETON_RETWEET) {
            $step_plus += 1;
        }
        if ($data['basic_type'] == Cp::TEMPLATE_SKELETON_TWEET) {
            $step_plus += 1;
        }
        if ($data['basic_type'] == Cp::TEMPLATE_SKELETON_POPULAR_VOTE) {
            $step_plus += 1;
        }
        if ($data['basic_type'] == Cp::TEMPLATE_SKELETON_QUESTIONNAIRE) {
            $step_plus += 1;
        }
        if ($data['basic_type'] == Cp::TEMPLATE_SKELETON_DOUBLE_QUESTIONNAIRE) {
            $step_plus += 1;
        }
        if ($data['basic_type'] == Cp::TEMPLATE_SKELETON_PHOTO_COLLECTION) {
            $step_plus += 1;
        }
        if ($data['basic_type'] == Cp::TEMPLATE_SKELETON_INSTAGRAM_HASHTAG) {
            $step_plus += 1;
        }
        if ($data['basic_type'] == Cp::TEMPLATE_SKELETON_TWITTER_FOLLOW) {
            $step_plus += 1;
        }
        if ($data['basic_type'] == Cp::TEMPLATE_SKELETON_MOVIE_YOUTUBE_CHANNEL) {
            $step_plus += 2;
        }
        if ($data['basic_type'] == Cp::TEMPLATE_SKELETON_CODE_AUTHENTICATION) {
            $step_plus += 1;
        }
        if ($data['basic_type'] == Cp::TEMPLATE_SKELETON_PHOTO_MUSTBUY) {
            $step_plus += 1;
        }

        if ($data['shipping_type'] == CpCreator::SHIPPING_ADDRESS_ELECTED) {
            $step_plus += 1;
        }
        if ($data['shipping_type'] == CpCreator::SHIPPING_ADDRESS_ALL) {
            $step_plus += 1;
        }
        if ($data['announce_type'] == CpCreator::ANNOUNCE_FIRST || $data['announce_type'] == CpCreator::ANNOUNCE_LOTTERY) {
            $step_plus += 1;
        }
        ?>
        <div class="stepListEdit">
            <div class="stepListWrap">
                <ul class="stepList newSkeletonTag">
                    <li class="stepDetail_require newSkeletonGroup">
                        <h1>STEP1-<?php assign(2+$step_plus) ?></h1>
                        <ul class="moduleList"
                            <?php
                                if ($data['announce_type'] != CpCreator::ANNOUNCE_FIRST) {
                                    if (!$data['can_set_coupon_for_non_incentive_cp']){
                                        write_html('data-disable-actions=' . CpAction::TYPE_COUPON . ',' . CpAction::TYPE_GIFT . ',' . CpAction::TYPE_ANNOUNCE_DELIVERY . ' ');
                                    } else {
                                        write_html('data-disable-actions='. CpAction::TYPE_GIFT . ',' . CpAction::TYPE_ANNOUNCE_DELIVERY . ' ');
                                    }
                                }
                                if ($data['announce_type'] == CpCreator::ANNOUNCE_LOTTERY) {
                                    write_html('data-disable-before=instant_win ');
                                }
                            ?>>
                            <?php $display = 'style=display:none' ?>
                            <?php //最初のモジュール。常設アンケートor決済かエントリーモジュールかの2つに別れる ?>
                            <?php if ($data['basic_type'] == Cp::PERMANENT_SKELETON_QUESTIONNAIRE): ?>
                                <li class="moduleDetail1 jsLockSortable jsLockShift" data-opening_flg="1" data-action-type="<?php assign(CpAction::TYPE_QUESTIONNAIRE) ?>">
                                    <span class="moduleIcon lock">
                                        <img src="<?php assign($this->setVersion('/img/module/enqueteAndCp1.png'))?>" width="33" height="33" alt="アンケート">
                                        <span class="textBalloon1"><span>アンケート</span></span>
                                    </span>
                                    <span class="addModuleR" <?php assign($display) ?>>追加する</span>
                                </li>
                            <?php elseif ($data['basic_type'] == Cp::PERMANENT_SKELETON_PAYMENT): ?>
                                <li class="moduleDetail1 jsLockSortable jsLockShift" data-opening_flg="1" data-action-type="<?php assign(CpAction::TYPE_PAYMENT) ?>">
                                    <span class="moduleIcon lock">
                                        <img src="<?php assign($this->setVersion('/img/module/enqueteAndCp1.png'))?>" width="33" height="33" alt="決済">
                                        <span class="textBalloon1"><span>決済</span></span>
                                    </span>
                                    <span class="addModuleR" <?php assign($display) ?>>追加する</span>
                                </li>
                            <?php else: ?>
                                <li class="moduleDetail1 jsLockSortable jsLockShift" data-opening_flg="1" data-action-type="<?php assign(CpAction::TYPE_ENTRY) ?>">
                                    <span class="moduleIcon lock">
                                        <img src="<?php assign($this->setVersion('/img/module/cpBase.png'))?>" height="33" width="33" alt="キャンペーン告知">
                                        <span class="textBalloon1"><span>キャンペーン告知</span></span>
                                    </span>
                                    <span class="addModuleR" <?php assign($display) ?>>追加する</span>
                                </li>
                            <?php endif ?>

                            <?php if ($data['basic_type'] == Cp::BASIC_SKELETON_QUESTIONNAIRE): ?>
                                <li class="moduleDetail1" data-action-type="<?php assign(CpAction::TYPE_QUESTIONNAIRE) ?>">
                                    <span class="addModuleL" <?php assign($display) ?>>追加する</span>
                                    <span class="moduleIcon">
                                        <img src="<?php assign($this->setVersion('/img/module/enquete1.png'))?>" width="33" height="33" alt="アンケート">
                                        <span class="textBalloon1"><span>アンケート</span></span>
                                    </span>
                                    <span class="addModuleR" <?php assign($display) ?>>追加する</span>
                                </li>
                            <?php endif; ?>
                            <?php if ($data['basic_type'] == Cp::BASIC_SKELETON_PHOTO): ?>
                                <li class="moduleDetail1" data-action-type="<?php assign(CpAction::TYPE_PHOTO) ?>">
                                    <span class="addModuleL" <?php assign($display) ?>>追加する</span>
                                    <span class="moduleIcon">
                                        <img src="<?php assign($this->setVersion('/img/module/photo1.png'))?>" width="33" height="33" alt="写真投稿">
                                        <span class="textBalloon1"><span>写真投稿</span></span>
                                    </span>
                                    <span class="addModuleR" <?php assign($display) ?>>追加する</span>
                                </li>
                            <?php endif; ?>
                            <?php if ($data['basic_type'] == Cp::BASIC_SKELETON_MOVIE): ?>
                                <li class="moduleDetail1" data-action-type="<?php assign(CpAction::TYPE_MOVIE) ?>">
                                    <span class="addModuleL" <?php assign($display) ?>>追加する</span>
                                    <span class="moduleIcon">
                                        <img src="<?php assign($this->setVersion('/img/module/movie1.png'))?>" width="33" height="33" alt="動画視聴">
                                        <span class="textBalloon1"><span>動画視聴</span></span>
                                    </span>
                                    <span class="addModuleR" <?php assign($display) ?>>追加する</span>
                                </li>
                            <?php endif; ?>
                            <?php if ($data['basic_type'] == Cp::TEMPLATE_SKELETON_RETWEET): ?>
                                <li class="moduleDetail1" data-action-type="<?php assign(CpAction::TYPE_RETWEET) ?>">
                                    <span class="addModuleL" <?php assign($display) ?>>追加する</span>
                                    <span class="moduleIcon">
                                        <img src="<?php assign($this->setVersion('/img/module/twitterRetweet1.png'))?>" width="33" height="33" alt="リツイート">
                                        <span class="textBalloon1"><span>リツイート</span></span>
                                    </span>
                                    <span class="addModuleR" <?php assign($display) ?>>追加する</span>
                                </li>
                            <?php endif; ?>
                            <?php if ($data['basic_type'] == Cp::TEMPLATE_SKELETON_TWEET): ?>
                                <li class="moduleDetail1" data-action-type="<?php assign(CpAction::TYPE_TWEET) ?>">
                                    <span class="addModuleL" <?php assign($display) ?>>追加する</span>
                                    <span class="moduleIcon">
                                        <img src="<?php assign($this->setVersion('/img/module/twitterTweet1.png'))?>" width="33" height="33" alt="ツイート">
                                        <span class="textBalloon1"><span>ツイート</span></span>
                                    </span>
                                    <span class="addModuleR" <?php assign($display) ?>>追加する</span>
                                </li>
                            <?php endif; ?>
                            <?php if ($data['basic_type'] == Cp::TEMPLATE_SKELETON_POPULAR_VOTE): ?>
                                <li class="moduleDetail1" data-action-type="<?php assign(CpAction::TYPE_POPULAR_VOTE) ?>">
                                    <span class="addModuleL" <?php assign($display) ?>>追加する</span>
                                    <span class="moduleIcon">
                                        <img src="<?php assign($this->setVersion('/img/module/ranking1.png'))?>" width="33" height="33" alt="人気投票">
                                        <span class="textBalloon1"><span>人気投票</span></span>
                                    </span>
                                    <span class="addModuleR" <?php assign($display) ?>>追加する</span>
                                </li>
                            <?php endif; ?>
                            <?php if ($data['basic_type'] == Cp::TEMPLATE_SKELETON_QUESTIONNAIRE): ?>
                                <li class="moduleDetail1" data-action-type="<?php assign(CpAction::TYPE_QUESTIONNAIRE) ?>">
                                    <span class="addModuleL" <?php assign($display) ?>>追加する</span>
                                    <span class="moduleIcon">
                                        <img src="<?php assign($this->setVersion('/img/module/enquete1.png'))?>" width="33" height="33" alt="アンケート">
                                        <span class="textBalloon1"><span>アンケート</span></span>
                                    </span>
                                    <span class="addModuleR" <?php assign($display) ?>>追加する</span>
                                </li>
                            <?php endif; ?>
                            <?php if ($data['basic_type'] == Cp::TEMPLATE_SKELETON_DOUBLE_QUESTIONNAIRE): ?>
                                <li class="moduleDetail1" data-action-type="<?php assign(CpAction::TYPE_QUESTIONNAIRE) ?>">
                                    <span class="addModuleL" <?php assign($display) ?>>追加する</span>
                                    <span class="moduleIcon">
                                        <img src="<?php assign($this->setVersion('/img/module/enquete1.png'))?>" width="33" height="33" alt="アンケート">
                                        <span class="textBalloon1"><span>アンケート</span></span>
                                    </span>
                                    <span class="addModuleR" <?php assign($display) ?>>追加する</span>
                                </li>
                            <?php endif; ?>
                            <?php if ($data['basic_type'] == Cp::TEMPLATE_SKELETON_PHOTO_COLLECTION): ?>
                                <li class="moduleDetail1" data-action-type="<?php assign(CpAction::TYPE_PHOTO) ?>">
                                    <span class="addModuleL" <?php assign($display) ?>>追加する</span>
                                    <span class="moduleIcon">
                                        <img src="<?php assign($this->setVersion('/img/module/photo1.png'))?>" width="33" height="33" alt="写真投稿">
                                        <span class="textBalloon1"><span>写真投稿</span></span>
                                    </span>
                                    <span class="addModuleR" <?php assign($display) ?>>追加する</span>
                                </li>
                            <?php endif; ?>
                            <?php if ($data['basic_type'] == Cp::TEMPLATE_SKELETON_TWITTER_FOLLOW): ?>
                                <li class="moduleDetail1" data-action-type="<?php assign(CpAction::TYPE_TWITTER_FOLLOW) ?>">
                                    <span class="addModuleL" <?php assign($display) ?>>追加する</span>
                                    <span class="moduleIcon">
                                        <img src="<?php assign($this->setVersion('/img/module/twitterFollow1.png'))?>" width="33" height="33" alt="Twitter フォロー">
                                        <span class="textBalloon1"><span>Twitter フォロー</span></span>
                                    </span>
                                    <span class="addModuleR" <?php assign($display) ?>>追加する</span>
                                </li>
                            <?php endif; ?>
                            <?php if ($data['basic_type'] == Cp::TEMPLATE_SKELETON_MOVIE_YOUTUBE_CHANNEL): ?>
                                <li class="moduleDetail1" data-action-type="<?php assign(CpAction::TYPE_MOVIE) ?>">
                                    <span class="addModuleL" <?php assign($display) ?>>追加する</span>
                                    <span class="moduleIcon">
                                        <img src="<?php assign($this->setVersion('/img/module/movie1.png'))?>" width="33" height="33" alt="動画視聴">
                                        <span class="textBalloon1"><span>動画視聴</span></span>
                                    </span>
                                    <span class="addModuleR" <?php assign($display) ?>>追加する</span>
                                </li>
                                <li class="moduleDetail1" data-action-type="<?php assign(CpAction::TYPE_YOUTUBE_CHANNEL) ?>">
                                    <span class="addModuleL" <?php assign($display) ?>>追加する</span>
                                    <span class="moduleIcon">
                                        <img src="<?php assign($this->setVersion('/img/module/ytchannel1.png'))?>" width="33" height="33" alt="YouTubeチャンネル登録">
                                        <span class="textBalloon1"><span>YouTubeチャンネル登録</span></span>
                                    </span>
                                    <span class="addModuleR" <?php assign($display) ?>>追加する</span>
                                </li>
                            <?php endif; ?>
                            <?php if ($data['basic_type'] == Cp::TEMPLATE_SKELETON_CODE_AUTHENTICATION): ?>
                                <li class="moduleDetail1" data-action-type="<?php assign(CpAction::TYPE_CODE_AUTHENTICATION) ?>">
                                    <span class="addModuleL" <?php assign($display) ?>>追加する</span>
                                    <span class="moduleIcon">
                                        <img src="<?php assign($this->setVersion('/img/module/code1.png'))?>" width="33" height="33" alt="コード認証">
                                        <span class="textBalloon1"><span>コード認証</span></span>
                                    </span>
                                    <span class="addModuleR" <?php assign($display) ?>>追加する</span>
                                </li>
                            <?php endif; ?>
                            <?php if ($data['basic_type'] == Cp::TEMPLATE_SKELETON_PHOTO_MUSTBUY): ?>
                                <li class="moduleDetail1" data-action-type="<?php assign(CpAction::TYPE_PHOTO) ?>">
                                    <span class="addModuleL" <?php assign($display) ?>>追加する</span>
                                    <span class="moduleIcon">
                                        <img src="<?php assign($this->setVersion('/img/module/photo1.png'))?>" width="33" height="33" alt="写真投稿">
                                        <span class="textBalloon1"><span>写真投稿</span></span>
                                    </span>
                                    <span class="addModuleR" <?php assign($display) ?>>追加する</span>
                                </li>
                            <?php endif; ?>
                            <?php if ($data['shipping_type'] == CpCreator::SHIPPING_ADDRESS_ALL): ?>
                                <li class="moduleDetail1" data-action-type="<?php assign(CpAction::TYPE_SHIPPING_ADDRESS) ?>">
                                    <span class="addModuleL" <?php assign($display) ?>>追加する</span>
                                    <span class="moduleIcon">
                                        <img src="<?php assign($this->setVersion('/img/module/address1.png'))?>" width="33" height="33" alt="配送先情報">
                                        <span class="textBalloon1"><span>配送先情報</span></span>
                                    </span>
                                    <span class="addModuleR" <?php assign($display) ?>>追加する</span>
                                </li>
                            <?php endif; ?>
                            <?php if ($data['basic_type'] == Cp::TEMPLATE_SKELETON_INSTAGRAM_HASHTAG): ?>
                                <li class="moduleDetail1" data-action-type="<?php assign(CpAction::TYPE_INSTAGRAM_HASHTAG) ?>">
                                    <span class="addModuleL" <?php assign($display) ?>>追加する</span>
                                    <span class="moduleIcon">
                                        <img src="<?php assign($this->setVersion('/img/module/hastag1.png'))?>" width="33" height="33" alt="Instagram 投稿">
                                        <span class="textBalloon1"><span>Instagram 投稿</span></span>
                                    </span>
                                    <span class="addModuleR" <?php assign($display) ?>>追加する</span>
                                </li>
                            <?php endif; ?>
                            <?php if ($data['announce_type'] == CpCreator::ANNOUNCE_LOTTERY): ?>
                                <li class="moduleDetail1 jsLockSortable instant_win" data-action-type="<?php assign(CpAction::TYPE_INSTANT_WIN) ?>">
                                    <span class="addModuleL" <?php assign($display) ?>>追加する</span>
                                    <span class="moduleIcon lock">
                                        <img src="<?php assign($this->setVersion('/img/module/speedwin1.png'))?>" width="33" height="33" alt="スピードくじ">
                                        <span class="textBalloon1"><span>スピードくじ</span></span>
                                    </span>
                                    <span class="addModuleR" <?php assign($display) ?>>追加する</span>
                                </li>
                            <?php endif; ?>
                            <?php if ($data['shipping_type'] == CpCreator::SHIPPING_ADDRESS_ELECTED && $data['announce_type'] == CpCreator::ANNOUNCE_LOTTERY): ?>
                                <li class="moduleDetail1" data-action-type="<?php assign(CpAction::TYPE_SHIPPING_ADDRESS) ?>">
                                    <span class="addModuleL" <?php assign($display) ?>>追加する</span>
                                    <span class="moduleIcon">
                                        <img src="<?php assign($this->setVersion('/img/module/address1.png'))?>" width="33" height="33" alt="配送先情報">
                                        <span class="textBalloon1"><span>配送先情報</span></span>
                                    </span>
                                    <span class="addModuleR" <?php assign($display) ?>>追加する</span>
                                </li>
                            <?php endif; ?>
                            <?php if ($data['announce_type'] == CpCreator::ANNOUNCE_SELECTION || $data['announce_type'] == CpCreator::ANNOUNCE_DELIVERY || $data['announce_type'] == CpCreator::ANNOUNCE_NON_INCENTIVE): ?>
                                <li class="moduleDetail1 jsLockSortable" data-action-type="<?php assign(CpAction::TYPE_JOIN_FINISH) ?>">
                                    <span class="addModuleL" <?php assign($display) ?>>追加する</span>
                                    <span class="moduleIcon lock">
                                        <img src="<?php assign($this->setVersion('/img/module/finish1.png'))?>" width="33" height="33" alt="参加完了">
                                        <span class="textBalloon1"><span>参加完了</span></span>
                                    </span>
                                    <span class="addModuleR" <?php assign($display) ?>>追加する</span>
                                </li>
                            <?php else: ?>
                                <?php if ($data['announce_type'] == CpCreator::ANNOUNCE_FIRST && $data['basic_type'] == Cp::BASIC_SKELETON_GIFT ): ?>
                                    <li class="moduleDetail1" data-action-type="<?php assign(CpAction::TYPE_GIFT) ?>">
                                        <span class="addModuleL" <?php assign($display) ?>>追加する</span>
                                        <span class="moduleIcon">
                                            <img src="<?php assign($this->setVersion('/img/module/gift1.png'))?>" width="33" height="33" alt="ギフト">
                                            <span class="textBalloon1"><span>ギフト</span></span>
                                        </span>
                                        <span class="addModuleR" <?php assign($display) ?>>追加する</span>
                                    </li>
                                    <li class="moduleDetail1 jsLockSortable" data-action-type="<?php assign(CpAction::TYPE_JOIN_FINISH) ?>">
                                        <span class="addModuleL" <?php assign($display) ?>>追加する</span class="moduleIcon">
                                        <span class="moduleIcon lock">
                                            <img src="<?php assign($this->setVersion('/img/module/finish1.png'))?>" width="33" height="33" alt="参加完了">
                                            <span class="textBalloon1"><span>参加完了</span></span>
                                        </span>
                                        <span class="addModuleR" <?php assign($display) ?>>追加する</span>
                                    </li>
                                <?php else: ?>
                                    <li class="moduleDetail1 jsLockSortable" data-action-type="<?php assign(CpAction::TYPE_ANNOUNCE) ?>">
                                        <span class="addModuleL" <?php assign($display) ?>>追加する</span class="moduleIcon">
                                        <span class="moduleIcon lock">
                                            <img src="<?php assign($this->setVersion('/img/module/win1.png'))?>" width="33" height="33" alt="当選通知">
                                            <span class="textBalloon1"><span>当選通知</span></span>
                                        </span>
                                        <span class="addModuleR" <?php assign($display) ?>>追加する</span>
                                    </li>
                                <?php endif; ?>
                            <?php endif; ?>
                            <?php if ($data['shipping_type'] == CpCreator::SHIPPING_ADDRESS_ELECTED && $data['announce_type'] == CpCreator::ANNOUNCE_FIRST): ?>
                                <li class="moduleDetail1" data-action-type="<?php assign(CpAction::TYPE_SHIPPING_ADDRESS) ?>">
                                    <span class="addModuleL" <?php assign($display) ?>>追加する</span>
                                    <span class="moduleIcon">
                                        <img src="<?php assign($this->setVersion('/img/module/address1.png'))?>" width="33" height="33" alt="配送先情報">
                                        <span class="textBalloon1"><span>配送先情報</span></span>
                                    </span>
                                    <span class="addModuleR" <?php assign($display) ?>>追加する</span>
                                </li>
                            <?php endif; ?>
                            <?php if ($data['announce_type'] == CpCreator::ANNOUNCE_FIRST && $data['basic_type'] == Cp::BASIC_SKELETON_COUPON): ?>
                                <li class="moduleDetail1" data-action-type="<?php assign(CpAction::TYPE_COUPON) ?>">
                                    <span class="addModuleL" <?php assign($display) ?>>追加する</span>
                                    <span class="moduleIcon">
                                        <img src="<?php assign($this->setVersion('/img/module/coupon1.png'))?>" width="33" height="33" alt="クーポン">
                                        <span class="textBalloon1"><span>クーポン</span></span>
                                    </span>
                                    <span class="addModuleR" <?php assign($display) ?>>追加する</span>
                                </li>
                            <?php endif; ?>
                            <?php if ($data['announce_type'] == CpCreator::ANNOUNCE_FIRST && $data['basic_type'] == Cp::TEMPLATE_SKELETON_COUPON): ?>
                                <li class="moduleDetail1" data-action-type="<?php assign(CpAction::TYPE_COUPON) ?>">
                                    <span class="addModuleL" <?php assign($display) ?>>追加する</span>
                                    <span class="moduleIcon">
                                        <img src="<?php assign($this->setVersion('/img/module/coupon1.png'))?>" width="33" height="33" alt="クーポン">
                                        <span class="textBalloon1"><span>クーポン</span></span>
                                    </span>
                                    <span class="addModuleR" <?php assign($display) ?>>追加する</span>
                                </li>
                            <?php endif; ?>
                        <!-- /.moduleList --></ul>
                    <!-- /.stepDetail_require --></li>

                    <?php if ($data['announce_type'] == CpCreator::ANNOUNCE_SELECTION): ?>
                        <li class="stepDetail_require newSkeletonGroup">
                            <h1>STEP<?php assign((3+$step_plus).(($data['shipping_type'] == CpCreator::SHIPPING_ADDRESS_ELECTED) ? '-'.(4+$step_plus) : (($data['basic_type'] == Cp::TEMPLATE_SKELETON_DOUBLE_QUESTIONNAIRE) ? '-'.(5+$step_plus) :''))) ?></h1>
                            <ul class="moduleList">
                                <li class="moduleDetail1 jsLockSortable" data-action-type="<?php assign(CpAction::TYPE_ANNOUNCE) ?>">
                                    <span class="addModuleL" <?php assign($display) ?>>追加する</span>
                                    <span class="moduleIcon lock">
                                        <img src="<?php assign($this->setVersion('/img/module/win1.png'))?>" width="33" height="33" alt="当選通知">
                                        <span class="textBalloon1"><span>当選通知</span></span>
                                    </span>
                                    <span class="addModuleR" <?php assign($display) ?>>追加する</span>
                                </li>
                                <?php if ($data['shipping_type'] == CpCreator::SHIPPING_ADDRESS_ELECTED): ?>
                                    <li class="moduleDetail1" data-action-type="<?php assign(CpAction::TYPE_SHIPPING_ADDRESS) ?>">
                                        <span class="addModuleL" <?php assign($display) ?>>追加する</span>
                                        <span class="moduleIcon">
                                            <img src="<?php assign($this->setVersion('/img/module/address1.png'))?>" width="33" height="33" alt="配送先情報">
                                            <span class="textBalloon1"><span>配送先情報</span></span>
                                        </span>
                                        <span class="addModuleR" <?php assign($display) ?>>追加する</span>
                                    </li>
                                <?php endif; ?>
                                <?php if ($data['basic_type'] == Cp::TEMPLATE_SKELETON_DOUBLE_QUESTIONNAIRE): ?>
                                    <li class="moduleDetail1" data-action-type="<?php assign(CpAction::TYPE_QUESTIONNAIRE) ?>">
                                        <span class="addModuleL" <?php assign($display) ?>>追加する</span>
                                        <span class="moduleIcon">
                                            <img src="<?php assign($this->setVersion('/img/module/enquete1.png'))?>" width="33" height="33" alt="アンケート">
                                            <span class="textBalloon1"><span>アンケート</span></span>
                                        </span>
                                        <span class="addModuleR" <?php assign($display) ?>>追加する</span>
                                    </li>
                                    <li class="moduleDetail1" data-action-type="<?php assign(CpAction::TYPE_MESSAGE) ?>">
                                        <span class="addModuleL" <?php assign($display) ?>>追加する</span>
                                        <span class="moduleIcon">
                                            <img src="<?php assign($this->setVersion('/img/module/mail1.png'))?>" width="33" height="33" alt="メッセージ">
                                            <span class="textBalloon1"><span>メッセージ</span></span>
                                        </span>
                                        <span class="addModuleR" <?php assign($display) ?>>追加する</span>
                                    </li>
                                <?php endif; ?>
                            <!-- /.moduleList --></ul>
                        <!-- /.stepDetail_require --></li>
                    <?php elseif ($data['announce_type'] == CpCreator::ANNOUNCE_DELIVERY): ?>
                        <li class="stepDetail_require newSkeletonGroup">
                            <h1>STEP<?php assign((3+$step_plus).(($data['shipping_type'] == CpCreator::SHIPPING_ADDRESS_ELECTED) ? '-'.(4+$step_plus) : '')) ?></h1>
                            <ul class="moduleList">
                                <li class="moduleDetail1 jsLockSortable jsLockShift" data-action-type="<?php assign(CpAction::TYPE_ANNOUNCE_DELIVERY) ?>">
                                    <span class="moduleIcon lock">
                                        <img src="<?php assign($this->setVersion('/img/module/shipping1.png'))?>" width="33" height="33" alt="賞品の発送をもって発表">
                                        <span class="textBalloon1"><span>賞品の発送をもって発表</span></span>
                                    </span>
                                </li>
                                <!-- /.moduleList --></ul>
                            <!-- /.stepDetail_require --></li>
                    <?php endif; ?>
                    <?php if ($data['announce_type'] != CpCreator::ANNOUNCE_NON_INCENTIVE): ?>
                        <li class="stepDetail_require">
                            <h1>STEP N</h1>
                            <ul class="moduleList">
                                <li class="addModuleDetail1"><span>追加する</span></li>
                            <!-- /.moduleList --></ul>
                        <!-- /.stepDetail_require --></li>
                    <?php endif ?>
                <!-- /.stepList --></ul>
            <!-- /.stepListWrap --></div>
            <div class="deleteModule">
                <p class="">削除する</p>
            <!-- /.deleteModule --></div>
            <p class="supplement1">アイコンをドラッグ&ドロップすることでフローをカスタマイズできます。</p>
        <!-- /.stepListEdit --></div>
    <!-- /.skeletonWrap --></section>

    <ul class="selectModuleList">
        <?php
        $invisible_types = array(CpAction::TYPE_ENTRY, CpAction::TYPE_JOIN_FINISH, CpAction::TYPE_INSTANT_WIN);
        if ($data['join_limit_flg'] == Cp::JOIN_LIMIT_ON){
            $invisible_types[] = CpAction::TYPE_SHARE;
        }
        if ($data['announce_type'] == CpCreator::ANNOUNCE_FIRST) {
            $invisible_types[] = CpAction::TYPE_INSTAGRAM_HASHTAG;
        }
        if ($data['announce_type'] == CpCreator::ANNOUNCE_NON_INCENTIVE) {
            $invisible_types[] = CpAction::TYPE_ANNOUNCE;
            $invisible_types[] = CpAction::TYPE_GIFT;
            if (!$data['can_set_shipping_address_for_non_incentive_cp']) {
                $invisible_types[] = CpAction::TYPE_SHIPPING_ADDRESS;
            }
            if (!$data['can_set_coupon_for_non_incentive_cp']) {
                $invisible_types[] =  CpAction::TYPE_COUPON;
            }
        }
        if ($data['announce_type'] != CpCreator::ANNOUNCE_DELIVERY) {
            $invisible_types[] = CpAction::TYPE_ANNOUNCE_DELIVERY;
        }
        if ($data['announce_type'] == CpCreator::ANNOUNCE_DELIVERY) {
            $invisible_types[] = CpAction::TYPE_ANNOUNCE;
        }
        ?>

        <?php foreach($data['CpActionDetail'] as $key=>$value): ?>
            <?php if(!in_array($key, $invisible_types)): ?>
                <li class="moduleDetail2" data-action-type="<?php assign($key) ?>">
                    <?php if($key == CpAction::TYPE_PAYMENT && !$data['can_use_payment_module']): ?>
                        <?php continue;?>
                    <?php endif;?>
                    <span class="hdModuleIcon">
                        <img src="<?php assign($this->setVersion('/img/module/'.$value['icon']))?>" width="55" height="55" alt="<?php assign($value['title']) ?>">
                    </span>
                    <span class="moduleName"><?php assign($value['title']) ?></span>
                </li>
            <?php endif; ?>
        <?php endforeach; ?>
    </ul>
    <!-- 分岐どうしよう -->

    <!-- /.makeStepTypeCont --></div>
