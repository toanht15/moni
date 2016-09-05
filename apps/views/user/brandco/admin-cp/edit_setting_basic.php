<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoHeader')->render($data['pageStatus'])) ?>
<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoAccountHeader')->render($data['pageStatus'])) ?>

<?php
    $disable = ($data['cp']->status != Cp::STATUS_DEMO) && ($data['status'] == Cp::SETTING_FIX) ? 'disabled':'';
    $isDisableWhenFixAction = ($data['cp']->status != Cp::STATUS_DEMO) && ($data['status'] == Cp::SETTING_FIX && !edit_setting_basic::canEditCp($data['CpStatus'])) ? 'disabled':'';
    $isDisableClientWhenFixAction = ($data['cp']->status != Cp::STATUS_DEMO) && ($data['status'] == Cp::SETTING_FIX && !edit_setting_basic::canShowByManager($data['CpStatus'], $data['isManager'])) ?'disabled':'';
    $isAlliedManagerFunction = !$isDisableClientWhenFixAction && $data['status'] == Cp::SETTING_FIX;
?>

<?php $timeHH=array() ?>
<?php for($i=0;$i<24;$i++): ?>
    <?php if($i<10): ?>
        <?php $j='0'.$i; ?>
    <?php else: ?>
        <?php $j = $i; ?>
    <?php endif; ?>
    <?php $timeHH[$j] = $j; ?>
<?php endfor; ?>

<?php $timeMM=array(); ?>
<?php for($i=0;$i<60;$i++): ?>
    <?php if($i<10): ?>
        <?php $j='0'.$i; ?>
    <?php else: ?>
        <?php $j = $i; ?>
    <?php endif; ?>
    <?php $timeMM[$j] = $j; ?>
<?php endfor; ?>

<?php write_html($this->parseTemplate('CpPublicConditions.php', array('cp_id' => $data['cp']->id))) ?>

<article>
    <p class="jsIsDisableWhenFixAction" style="display:none" data-disabled="<?php assign($isDisableWhenFixAction) ?>"></p>
    <?php if ($data['cp']->status != Cp::STATUS_DRAFT): ?>
        <?php write_html($this->parseTemplate('ActionHeader.php',array(
            'cp_id' => $data['cp']->id,
            'action_id' => null,
            'user_list_page' => true,
            'pageStatus' => $data['pageStatus'],
            'enable_archive' => false,
            'isHideDemoFunction' => false,
        ))); ?>
    <?php else: ?>
        <?php write_html(aafwWidgets::getInstance()->loadWidget('CreateCpActionHeader')->render(array('cp_id' => $data['cp']->id, 'setting_id'=>Cp::CP_SETTING_BASIC, 'mid'=>$this->params['mid']))) ?>
    <?php endif ?>
    <h1 class="hd1"><img src="<?php assign($this->setVersion('/img/module/setting1.png')) ?>" width="25" height="25" alt="基本設定" class="moduleIcon">基本設定</h1>

    <?php if ( $this->ActionError && !$this->ActionError->isValid('auth')): ?>
        <p class="attention1"><?php assign ( $this->ActionError->getMessage('auth') )?></p>
    <?php endif; ?>

<form id="actionForm" name="actionForm" action="<?php assign(Util::rewriteUrl( 'admin-cp', 'save_setting_basic' )); ?>" method="POST" enctype="multipart/form-data" >
    <?php write_html($this->csrf_tag()); ?>
    <?php write_html($this->formHidden('cp_id', $data['cp']->id)) ?>
    <?php write_html($this->formHidden('save_type', '', array('id'=>'save_type'))) ?>
    <section class="moduleEditWrap">
    <section class="moduleBasicSetting1">
    <dl><dt class="title">タイトル<br><small>※80文字以内</small></dt><dd>
    <?php write_html( $this->formText( 'title', PHPParser::ACTION_FORM, array('maxlength'=>'80', $disable=>$disable, 'class'=>'inputTitle'))); ?>
    <br><small class="textLimit"></small>
    <?php if ( $this->ActionError && !$this->ActionError->isValid('title')): ?>
        <p class="attention1"><?php assign ( $this->ActionError->getMessage('title') )?></p>
    <?php endif; ?>
    </dd><dt>サムネイル画像</dt><dd class="jsCheckToggleWrap">
            <label><?php write_html($this->formCheckBox('rectangle_flg', array($this->getActionFormValue('rectangle_flg')), array('class'=>'jsCheckToggle', $isDisableWhenFixAction=>$isDisableWhenFixAction), array(Cp::FLAG_SHOW_VALUE =>'横長サイズ画像も登録（moniplaのトップページ掲載に最適なサイズです）')))?></label>
            <p class="thumbImg">
                <img <?php write_html( $this->ActionForm['image_url'] ? "" : "style='display:none'" );?> src="<?php assign($this->ActionForm['image_url'] ? $this->ActionForm['image_url'] : '')?>" id="cpImage" width="160" height="160" alt="360*360">
                <input type="file" name="image_file" class="actionImage0" maxlength="512" <?php assign($isDisableWhenFixAction="$isDisableWhenFixAction") ?>>
                <small>※JPEG,GIF,PNGの360*360ピクセル</small>
                <?php if ( $this->ActionError && !$this->ActionError->isValid('image_file')): ?>
                    <span class="attention1"><?php assign ( $this->ActionError->getMessage('image_file') )?></span>
                <?php endif; ?>
            <?php write_html($this->formHidden('image_url', PHPParser::ACTION_FORM))?>
            </p>
            <p class="thumbImg jsCheckToggleTarget" style="display:none;">
                <img <?php write_html( $this->ActionForm['image_rectangle_url'] ? "" : "style='display:none'" ); ?> src="<?php assign($this->ActionForm['image_rectangle_url'] ? $this->ActionForm['image_rectangle_url'] : '')?>" id="cpRecImage" width="304" height="160" alt="1000*524">
                <input type="file" name="image_rectangle_file" class="actionImage1" maxlength="512" <?php assign($isDisableWhenFixAction="$isDisableWhenFixAction") ?>>
                <small>※JPEG,GIF,PNGの1000*524ピクセル</small>
                <?php if ( $this->ActionError && !$this->ActionError->isValid('image_rectangle_file')): ?>
                    <span class="attention1"><?php assign ( $this->ActionError->getMessage('image_rectangle_file') )?></span>
                <?php endif; ?>
            <?php write_html($this->formHidden('image_rectangle_url', PHPParser::ACTION_FORM))?>
            </p>
    </dd>
        <?php if ($data['cp']->selection_method == CpCreator::ANNOUNCE_NON_INCENTIVE): ?>
        <span class="jsPermanentToggleWrap">
            <dt>開催期間</dt><dd>
                <?php write_html($this->formRadio('permanent_flg', PHPParser::ACTION_FORM, array('class' => 'jsPermanentToggle', $disable => $disable), array('0' => '応募締切あり', '1' => '常時受付'))) ?>
            </dd>
        <?php endif ?>
            <dt><?php if ($isAlliedManagerFunction): ?>
                    <span class="labelModeAllied">応募日時</span>
                <?php else: ?>
                    応募日時
                <?php endif; ?>
            </dt><dd>
                <?php write_html( $this->formText( 'start_date', PHPParser::ACTION_FORM, array('maxlength'=>'10', 'class'=>'jsDate inputDate', 'placeholder'=>'年/月/日' ,$disable=>$disable))); ?><?php write_html( $this->formSelect( 'openTimeHH', PHPParser::ACTION_FORM, array('class'=>'inputTime' ,$disable=>$disable), $timeHH)); ?><span class="coron">:</span
                    ><?php write_html( $this->formSelect( 'openTimeMM', PHPParser::ACTION_FORM, array('class'=>'inputTime',$disable=>$disable), $timeMM)); ?>
                <span class="jsPermanentToggleTarget" <?php if ($this->getActionFormValue('permanent_flg')) write_html('style="display:none"') ?>>
                    <span class="dash">～</span>
                    <?php write_html( $this->formText( 'end_date', PHPParser::ACTION_FORM, array('maxlength'=>'10', 'class'=>'jsDate inputDate jsPublicDate', 'placeholder'=>'年/月/日',$isDisableClientWhenFixAction=>$isDisableClientWhenFixAction))); ?>
                    <span class="closeTimeDate" <?php if (!$this->getActionFormValue('closeTimeDate')) write_html('style="display:none"')?>>（終日）<label class="settingClose jsAreaToggle"><?php write_html($this->formCheckBox('closeTimeDate', array($this->getActionFormValue('closeTimeDate')),array($isDisableClientWhenFixAction=>$isDisableClientWhenFixAction), array('1'=>''), array("style"=>"display: none"), ' ')) ?><span class="textBalloon1"><span>時間の設定</span></span></label></span>
                    <?php if ($this->getActionFormValue('closeTimeDate')) $endTimeDisplay = 'display: none' ?>
                    <?php write_html( $this->formSelect( 'closeTimeHH', PHPParser::ACTION_FORM, array('class'=>'inputTime endTime',$isDisableClientWhenFixAction=>$isDisableClientWhenFixAction, 'style'=>$endTimeDisplay), $timeHH)); ?><span class="coron endTime" style="<?php write_html($endTimeDisplay) ?>">:</span
                        ><?php write_html( $this->formSelect( 'closeTimeMM', PHPParser::ACTION_FORM, array('class'=>'inputTime endTime',$isDisableClientWhenFixAction=>$isDisableClientWhenFixAction, 'style'=>$endTimeDisplay), $timeMM)); ?>
                    <?php if (!$disable):?>
                        <a href="javascript:void(0)" class="iconBtnDelete endTime" style="<?php write_html($endTimeDisplay) ?>">設定しない</a>
                    <?php endif;?>
                    </span>
                <?php if ( $this->ActionError && !$this->ActionError->isValid('start_date1')): ?>
                    <p class="attention1"><?php assign ( str_replace('<%time>','応募開始日時',$this->ActionError->getMessage('start_date1')) )?></p>
                <?php endif; ?>
                <?php if( $this->ActionError && !$this->ActionError->isValid('end_date1')): ?>
                    <p class="attention1"><?php assign ( str_replace('<%time>', '応募終了日時', $this->ActionError->getMessage('end_date1')) )?></p>
                <?php endif; ?>
                <?php if ( $this->ActionError && !$this->ActionError->isValid('start_date2')): ?>
                    <p class="attention1"><?php assign ( str_replace(array('<%time1>','<%time2>'),array('応募終了日時','応募開始日時'),$this->ActionError->getMessage('start_date2')) )?></p>
                <?php endif; ?>
            </dd>
            <dt>
            </dt><dd>
                <label><?php write_html($this->formCheckBox('use_public_date_flg', array($this->getActionFormValue('use_public_date_flg')), array('class' => 'jsToggleSettingPublicDate', $disable=>$disable), array(Cp::PUBLIC_DATE_ON =>'応募開始前にページを公開する')))?></label><br>
                <span class="jsSettingPublicDate" <?php if (!$this->getActionFormValue('use_public_date_flg')) { write_html('style="display:none"'); } ?>>
                    <?php write_html( $this->formText( 'public_date', PHPParser::ACTION_FORM, array('maxlength'=>'10', 'class'=>'jsDate inputDate', 'placeholder'=>'年/月/日',$disable=>$disable))); ?><?php write_html($this->formSelect( 'publicTimeHH', PHPParser::ACTION_FORM, array('class'=>'inputTime',$disable=>$disable), $timeHH));?><span class="coron">:</span
                        ><?php write_html( $this->formSelect( 'publicTimeMM', PHPParser::ACTION_FORM, array('class'=>'inputTime',$disable=>$disable), $timeMM)); ?><br>
                    <small>※設定した日時にキャンペーンページが事前公開されます (応募はできません)</small>
                    <?php if ( $this->ActionError && !$this->ActionError->isValid('public_date1')): ?>
                        <p class="attention1"><?php assign ( str_replace('<%time>','開始日時',$this->ActionError->getMessage('public_date1')) )?></p>
                    <?php endif; ?>
                    <?php if ( $this->ActionError && !$this->ActionError->isValid('public_date2')): ?>
                        <p class="attention1"><?php assign ( str_replace(array('<%time1>','<%time2>'),array('応募開始日時','開始日時'),$this->ActionError->getMessage('public_date2')) )?></p>
                    <?php endif; ?>
                </span>
            </dd>
            <span <?php if ($data['cp']->isNonIncentiveCp()) write_html('style="display: none"') ?>>
                <dt><?php if ($isAlliedManagerFunction): ?>
                        <span class="labelModeAllied">当選発表日</span>
                    <?php else: ?>
                        当選発表日
                    <?php endif; ?>
                </dt><dd>
                    <?php if ($data['cp']->selection_method == CpCreator::ANNOUNCE_LOTTERY): ?>
                        <?php write_html($this->formText( 'announce_date', PHPParser::ACTION_FORM, array('maxlength'=>'10', 'class'=>'jsDate inputDate jsAnnounceDate', 'placeholder'=>'年/月/日', 'disabled' => 'disabled'))); ?>
                    <?php else: ?>
                        <?php write_html($this->formText( 'announce_date', PHPParser::ACTION_FORM, array('maxlength'=>'10', 'class'=>'jsDate inputDate jsAnnounceDate', 'placeholder'=>'年/月/日',$isDisableClientWhenFixAction=>$isDisableClientWhenFixAction))); ?>
                    <?php endif; ?>
                        <?php write_html($this->formHidden( 'announceTimeHH', '23'));?>
                        <?php write_html($this->formHidden( 'announceTimeMM', '59')); ?>
                        <?php if ( $this->ActionError && !$this->ActionError->isValid('announce_date1')): ?>
                            <p class="attention1"><?php assign ( str_replace('<%time>', '当選発表日時',$this->ActionError->getMessage('announce_date1')) )?></p>
                        <?php endif; ?>
                        <?php if ( $this->ActionError && !$this->ActionError->isValid('announce_date2')): ?>
                            <p class="attention1"><?php assign ( str_replace(array('<%time1>','<%time2>'),array('当選発表日時','応募終了日時'),$this->ActionError->getMessage('announce_date2')) )?></p>
                        <?php endif; ?>
                </dd>
            </span>
                <dt>クローズ日時
                    <p class="iconHelp">
                        <span class="text">ヘルプ</span>
                          <span class="textBalloon1">
                            <span>
                              キャンペーン単位でページをクローズする時に使用します。<br> 画像の使用期限が決まっている時などにご利用ください。
                            </span>
                          <!-- /.textBalloon1 --></span>
                        <!-- /.iconHelp -->
                    </p>
                </dt><dd>
                    <?php write_html( $this->formText( 'cp_page_close_date', PHPParser::ACTION_FORM, array('maxlength' => '10', 'class' => 'jsDate inputDate', 'placeholder'=>'年/月/日', 'disabled'=>(!$this->getActionFormValue('use_cp_page_close_flg') || $isDisableClientWhenFixAction) ? 'disabled' : '') ) ); ?><?php write_html( $this->formSelect( 'cpPageCloseTimeHH', PHPParser::ACTION_FORM, array('class'=>'inputTime','disabled'=>(!$this->getActionFormValue('use_cp_page_close_flg') || $isDisableClientWhenFixAction) ? 'disabled' : ''), $timeHH ));?><span class="coron">:</span><?PHP write_html( $this->formSelect( 'cpPageCloseTimeMM', PHPParser::ACTION_FORM, array('class'=>'inputTime','disabled'=>(!$this->getActionFormValue('use_cp_page_close_flg') || $isDisableClientWhenFixAction) ? 'disabled' : ''), $timeMM ) ); ?>
                    <label><?php write_html($this->formCheckBox('use_cp_page_close_flg', array($this->getActionFormValue('use_cp_page_close_flg')), array('onclick' => 'useCloseMode()', $isDisableClientWhenFixAction=>$isDisableClientWhenFixAction), array(Cp::CLOSE_DATE_ON =>'設定する')))?></label>
                    <?php if ( $this->ActionError && !$this->ActionError->isValid('cp_page_close_date')): ?>
                        <p class="attention1"><?php assign ( $this->ActionError->getMessage('cp_page_close_date') )?></p>
                    <?php endif; ?>
                    <?php if ( $this->ActionError && !$this->ActionError->isValid('cp_page_close_date1')): ?>
                        <p class="attention1"><?php assign ( str_replace('<%time>','クローズ日時',$this->ActionError->getMessage('cp_page_close_date1')) )?></p>
                    <?php endif; ?>
                    <?php if ( $this->ActionError && !$this->ActionError->isValid('cp_page_close_date2')): ?>
                        <p class="attention1"><?php assign ( str_replace(array('<%time1>','<%time2>'),array('クローズ日時','当選発表日時'),$this->ActionError->getMessage('cp_page_close_date2')) )?></p>
                    <?php endif; ?>
                    <?php if ( $this->ActionError && !$this->ActionError->isValid('cp_page_close_date3')): ?>
                        <p class="attention1"><?php assign ( str_replace(array('<%time1>','<%time2>'),array('クローズ日時','応募日時'),$this->ActionError->getMessage('cp_page_close_date3')) )?></p>
                    <?php endif; ?>
                    <?php if ( $this->ActionError && !$this->ActionError->isValid('cp_page_close_date4')): ?>
                        <p class="attention1"><?php assign ( str_replace(array('<%time1>','<%time2>'),array('クローズ日時','終了日時'),$this->ActionError->getMessage('cp_page_close_date3')) )?></p>
                    <?php endif; ?>
                </dd>
                <span <?php if ($data['cp']->isNonIncentiveCp()) write_html('style="display: none"') ?>>
                    <dt>
                        発表表示
                    </dt><dd>
                        <?php if ($this->getActionFormValue('selection_method') == CpCreator::ANNOUNCE_LOTTERY): ?>
                            <?php $announce_display_label = 'スピードくじにより即時'; ?>
                        <?php elseif ($this->getActionFormValue('shipping_method') == Cp::SHIPPING_METHOD_MESSAGE): ?>
                            <?php $announce_display_label = '当選日時'; ?>
                        <?php elseif ($this->getActionFormValue('shipping_method') == Cp::SHIPPING_METHOD_PRESENT): ?>
                            <?php $announce_display_label = '発送をもって発表'; ?>
                        <?php endif; ?>
                        <p><?php write_html($this->formRadio('announce_display_label_use_flg', $this->getActionFormValue('announce_display_label_use_flg'), array('class' => 'jsAnnounceDisplayLabelUseFlg', $isDisableClientWhenFixAction => $isDisableClientWhenFixAction), array('0'=> $announce_display_label))) ?></p>

                        <p>
                            <?php write_html($this->formRadio('announce_display_label_use_flg', $this->getActionFormValue('announce_display_label_use_flg'), array('class' => 'jsAnnounceDisplayLabelUseFlg'), array('1' => 'その他の表示'))) ?>
                            <span><?php write_html($this->formText('announce_display_label', PHPParser::ACTION_FORM, array('class' => 'jsAnnounceDisplayLabel', 'maxlength' => 40, 'style' => 'width:270px', $isDisableClientWhenFixAction => $isDisableClientWhenFixAction))) ?></span>
                        </p>
                        <?php if ($this->ActionError && !$this->ActionError->isValid('announce_display_label_use_flg')): ?>
                            <p class="attention1"><?php assign($this->ActionError->getMessage('announce_display_label_use_flg')); ?></p>
                        <?php endif; ?>
                        <?php if ($this->ActionError && !$this->ActionError->isValid('announce_display_label')): ?>
                            <p class="attention1"><?php assign($this->ActionError->getMessage('announce_display_label')); ?></p>
                        <?php endif; ?>
                        </ul>
                    </dd>
                    <?php write_html($this->formHidden('shipping_method', PHPParser::ACTION_FORM, array(), array())); ?>
                    <dt>当選者数</dt><dd>
                    <ul class="prizeSetting">
                        <li class="prizeNum">
                            <?php write_html( $this->formNumber( 'winner_count', PHPParser::ACTION_FORM, array('class'=>'inputNum',$disable=>$disable))); ?>名
                            <?php if ( $this->ActionError && !$this->ActionError->isValid('winner_count')): ?>
                                <p class="attention1"><?php assign ( $this->ActionError->getMessage('winner_count') )?></p>
                            <?php endif; ?>
                        </li>
                        <?php if ($data['cp']->selection_method == CpCreator::ANNOUNCE_LOTTERY): ?>
                        <p><small>
                            ※応募者により良い体験を提供する為、開催日数より多くの当選者数の設定をお願いします（推奨）
                        </small></p>
                        <?php endif; ?>
                        <li class="showNum jsCheckToggleWrap">
                            <?php if (!$isDisableClientWhenFixAction && $data['status'] == Cp::SETTING_FIX): ?>
                                <span class="labelModeAllied"><?php write_html($this->formCheckBox('show_winner_label', array($this->getActionFormValue('show_winner_label')), array('class'=>'jsCheckToggle', $isDisableClientWhenFixAction=>$isDisableClientWhenFixAction), array(Cp::FLAG_SHOW_VALUE=>'当選者数表示の変更')))?></span>
                            <?php else: ?>
                                <?php write_html($this->formCheckBox('show_winner_label', array($this->getActionFormValue('show_winner_label')), array('class'=>'jsCheckToggle', $isDisableClientWhenFixAction=>$isDisableClientWhenFixAction), array(Cp::FLAG_SHOW_VALUE=>'当選者数表示の変更')))?>
                            <?php endif; ?>

                            <?php write_html($this->formText('winner_label', PHPParser::ACTION_FORM, array('placeholder'=>'（例）○組○名様', 'class'=>'jsCheckToggleTarget', $isDisableClientWhenFixAction=>$isDisableClientWhenFixAction)))?>
                            <?php if ( $this->ActionError && !$this->ActionError->isValid('winner_label')): ?>
                                <p class="attention1"><?php assign ( $this->ActionError->getMessage('winner_label') )?></p>
                            <?php endif; ?>
                        </li>
                    </ul>
                </dd>
            </span>
            <?php if ($data['cp']->selection_method == CpCreator::ANNOUNCE_NON_INCENTIVE): ?>
        </span>
    <?php endif ?>
        <dt><?php if ($isAlliedManagerFunction): ?>
                <span class="labelModeAllied">参加可能SNS
                    <span class="iconHelp">
                    <span class="text">ヘルプ</span>
                    <span class="textBalloon1">
                    <span>
                      限定した場合、選択したSNSアカウントのみで参加できます。<br>※メールアドレスを使用しての参加はできなくなります。
                    </span>
                    <!-- /.textBalloon1 --></span>
                    <!-- /.iconHelp --></span>
                </span>
            <?php else: ?>
                参加可能SNS
                <span class="iconHelp">
                <span class="text">ヘルプ</span>
                <span class="textBalloon1">
                    <span>
                      限定した場合、選択したSNSアカウントのみで参加できます。<br>※メールアドレスを使用しての参加はできなくなります。
                    </span>
                <!-- /.textBalloon1 --></span>
                <!-- /.iconHelp --></span>
            <?php endif; ?>

        </dt><dd class="jsCheckToggleWrap">
        <label><?php write_html($this->formCheckBox('join_limit_sns_flg', array($this->getActionFormValue('join_limit_sns_flg')), array('class'=>'jsCheckToggle', $isDisableClientWhenFixAction=>$isDisableClientWhenFixAction), array(Cp::FLAG_SHOW_VALUE=>'限定する')))?></label>
        <ul class="joinAccount jsCheckToggleTarget">
            <li><?php write_html( $this->formCheckBox('join_limit_sns[]',$this->getActionFormValue('join_limit_sns'),array($isDisableClientWhenFixAction=>$isDisableClientWhenFixAction),array(SocialAccountService::SOCIAL_MEDIA_FACEBOOK => 'Facebook'))); ?></li>
            <li><?php write_html( $this->formCheckBox('join_limit_sns[]',$this->getActionFormValue('join_limit_sns'),array($isDisableClientWhenFixAction=>$isDisableClientWhenFixAction),array(SocialAccountService::SOCIAL_MEDIA_TWITTER => 'Twitter'))); ?></li>
            <li><?php write_html( $this->formCheckBox('join_limit_sns[]',$this->getActionFormValue('join_limit_sns'),array($isDisableClientWhenFixAction=>$isDisableClientWhenFixAction),array(SocialAccountService::SOCIAL_MEDIA_LINE => 'LINE'))); ?></li>
            <li><?php write_html( $this->formCheckBox('join_limit_sns[]',$this->getActionFormValue('join_limit_sns'),array($isDisableClientWhenFixAction=>$isDisableClientWhenFixAction),array(SocialAccountService::SOCIAL_MEDIA_INSTAGRAM => 'Instagram'))); ?></li>
            <li><?php write_html( $this->formCheckBox('join_limit_sns[]',$this->getActionFormValue('join_limit_sns'),array($isDisableClientWhenFixAction=>$isDisableClientWhenFixAction),array(SocialAccountService::SOCIAL_MEDIA_YAHOO => 'Yahoo!'))); ?></li>
            <li><?php write_html( $this->formCheckBox('join_limit_sns[]',$this->getActionFormValue('join_limit_sns'),array($isDisableClientWhenFixAction=>$isDisableClientWhenFixAction),array(SocialAccountService::SOCIAL_MEDIA_GOOGLE => 'Google'))); ?></li>
            <?php if($data['can_login_by_linked_in']): ?>
                <li><?php write_html( $this->formCheckBox('join_limit_sns[]',$this->getActionFormValue('join_limit_sns'),array($isDisableClientWhenFixAction=>$isDisableClientWhenFixAction),array(SocialAccountService::SOCIAL_MEDIA_LINKEDIN => 'LinkedIN'))); ?></li>
            <?php endif; ?>
        </ul>
        <div class="attentionWrap jsCheckToggleTarget">
            <?php if ( $this->ActionError && !$this->ActionError->isValid('join_limit_sns')): ?>
                <p class="attention1"><?php assign ( $this->ActionError->getMessage('join_limit_sns') )?></p>
            <?php endif; ?>
        </div>
    </dd>
        <dt>参加条件
        </dt><dd>
            <ul class="prizeSetting">
                <li class="showNum jsCheckToggleWrap">
                    <label><?php write_html($this->formCheckBox('restricted_age_flg', array($this->getActionFormValue('restricted_age_flg')), array('class' => 'jsCheckToggle', $isDisableClientWhenFixAction => $isDisableClientWhenFixAction), array(Cp::CP_RESTRICTED_AGE_FLG_ON => '年齢'))) ?></label>
                    <?php if ( $this->ActionError && !$this->ActionError->isValid('restricted_age')): ?>
                        <div class="attentionWrap jsCheckToggleTarget">
                            <p class="attention1"><?php assign ( $this->ActionError->getMessage('restricted_age') )?></p>
                        </div>
                    <?php endif; ?>
                    <div class="attentionWrap jsCheckToggleTarget">
                        <?php write_html($this->formSelect('restricted_age', $this->getActionFormValue('restricted_age') ? $this->getActionFormValue('restricted_age') : 15, array($isDisableClientWhenFixAction => $isDisableClientWhenFixAction), range(0, 100))) ?>　歳以上
                    </div>
                </li>
                <li class="showNum jsCheckToggleWrap">
                    <label><?php write_html($this->formCheckBox('restricted_gender_flg', array($this->getActionFormValue('restricted_gender_flg')), array('class' => 'jsCheckToggle', $isDisableClientWhenFixAction => $isDisableClientWhenFixAction), array(Cp::CP_RESTRICTED_AGE_FLG_ON => '性別'))) ?></label>
                    <?php if ( $this->ActionError && !$this->ActionError->isValid('restricted_gender')): ?>
                        <div class="attentionWrap jsCheckToggleTarget">
                            <p class="attention1"><?php assign ( $this->ActionError->getMessage('restricted_gender') )?></p>
                        </div>
                    <?php endif; ?>
                    <div class="attentionWrap jsCheckToggleTarget">
                        <?php write_html($this->formRadio('restricted_gender', $this->getActionFormValue('restricted_gender'), array($isDisableClientWhenFixAction => $isDisableClientWhenFixAction), Cp::$cp_restricted_gender)) ?>
                    </div>
                </li>
                <li class="showNum jsCheckToggleWrap">
                    <label><?php write_html($this->formCheckBox('restricted_address_flg', array($this->getActionFormValue('restricted_address_flg')), array('class' => 'jsCheckToggle', $isDisableClientWhenFixAction => $isDisableClientWhenFixAction), array(Cp::CP_RESTRICTED_ADDRESS_FLG_ON => '住所'))) ?></label>
                    <?php if ( $this->ActionError && !$this->ActionError->isValid('restricted_addresses')): ?>
                        <div class="attentionWrap jsCheckToggleTarget">
                            <p class="attention1"><?php assign ( $this->ActionError->getMessage('restricted_addresses') )?></p>
                        </div>
                    <?php endif; ?>
                    <div class="attentionWrap jsCheckToggleTarget">
                        <?php foreach($data['prefectures'] as $region => $prefectures): ?>
                            <dl class="areaSelectList">
                                <dt><label><input type="checkbox" class="jsAllCheck" <?php if ($isDisableClientWhenFixAction): ?>disabled<?php endif ?>><?php assign($region) ?></label></dt>
                                <dd class="local">
                                    <?php write_html($this->formCheckBox('restricted_addresses', $this->getActionFormValue('restricted_addresses'), array($isDisableClientWhenFixAction => $isDisableClientWhenFixAction, 'class' => 'jsSingleCheck'), $prefectures)) ?>
                                </dd>
                            </dl>
                        <?php endforeach ?>
                    </div>
                </li>

            </ul>
        </dd>
        <dt>タグ設置</dt><dd class="jsCheckToggleWrap">
            <?php write_html($this->formCheckBox('use_extend_tag', array($this->getActionFormValue('use_extend_tag')), array('class'=>'jsCheckToggle', $isDisableWhenFixAction=>$isDisableWhenFixAction), array(Cp::FLAG_SHOW_VALUE =>'キャンペーンページトップに計測タグを設置する')))?>
            <div class="attentionWrap jsCheckToggleTarget">
                <?php write_html($this->formTextArea('extend_tag', PHPParser::ACTION_FORM, array('cols'=>30, 'rows'=>10, $isDisableWhenFixAction=>$isDisableWhenFixAction))) ?>
                <?php if ( $this->ActionError && !$this->ActionError->isValid('extend_tag')): ?>
                    <p class="attention1"><?php assign ( $this->ActionError->getMessage('extend_tag') )?></p>
                <?php endif; ?>
            </div>
            <p><small class="cautionList">
                    ※キャンペーンページトップにこちらで入力されたタグが設置されます。<br>
                    ※リターゲティングタグやコンバージョンタグ等の計測タグの設置が必要な場合はこちらに入力してください。
            </small></p>
        </dd>

        <dt><?php if ($isAlliedManagerFunction): ?>
                <span class="labelModeAllied">その他</span>
            <?php else: ?>
                注意事項
            <?php endif; ?>
        </dt><dd class="jsCheckToggleWrap">
        <?php write_html($this->formCheckBox('show_recruitment_note', array($this->getActionFormValue('show_recruitment_note')), array('class'=>'jsCheckToggle', $isDisableClientWhenFixAction=>$isDisableClientWhenFixAction), array(Cp::FLAG_SHOW_VALUE=>'表示する')))?>
        <div class="attentionWrap jsCheckToggleTarget">
            <?php write_html($this->formTextArea('recruitment_note', PHPParser::ACTION_FORM, array('cols'=>30, 'rows'=>10, $isDisableClientWhenFixAction=>$isDisableClientWhenFixAction))) ?>
            <?php if ( $this->ActionError && !$this->ActionError->isValid('recruitment_note')): ?>
                <p class="attention1"><?php assign ( $this->ActionError->getMessage('recruitment_note') )?></p>
            <?php endif; ?>
        </div>
    </dd>

    <?php if ($this->isManager): ?>
        <dt class="labelModeAllied">
            Salesforce契約ID
        </dt><dd class="jsCheckToggleWrap">
            <?php write_html($this->formText('salesforce_id', PHPParser::ACTION_FORM, array('class' => 'inputId'))); ?>
            <?php if ( $this->ActionError && !$this->ActionError->isValid('salesforce_id')): ?>
                <p class="attention1"><?php assign ( $this->ActionError->getMessage('salesforce_id') )?></p>
            <?php endif; ?>
        </dd>
        <dt class="labelModeAllied">
            シェア等の<br>URL差し替え
        </dt><dd class="jsRefUrlTypeToggleWrap">
            <?php $page_url_disable = ($isDisableWhenFixAction || $data['reference_url_type'] == Cp::REFERENCE_URL_TYPE_CP) ? 'disabled' : '' ?>
            <input type="radio" name="reference_url_type" class="jsRefUrlTypeToggle" id="reference_url_type_0" value="0" <?php if ($data['reference_url_type'] == Cp::REFERENCE_URL_TYPE_CP): ?>checked="checked"<?php endif ?> <?php if ($isDisableWhenFixAction): ?>disabled="disabled"<?php endif ?>>
                <label for="reference_url_type_0"> <?php assign($data['cp']->getUrlPath($data['brand'])) ?></label><br>
            <input type="radio" name="reference_url_type" class="jsRefUrlTypeToggle" id="reference_url_type_1" value="1" <?php if ($data['reference_url_type'] == Cp::REFERENCE_URL_TYPE_LP): ?>checked="checked"<?php endif ?> <?php if ($isDisableWhenFixAction): ?>disabled="disabled"<?php endif ?>>
                <label for="reference_url_type_1">
                    /<?php assign($data['brand']->directory_name) ?>/page/<?php write_html($this->formText('page_url', PHPParser::ACTION_FORM, array('class' => 'jsRefUrlTypeToggleTarget', 'maxlength' => '20', $page_url_disable => $page_url_disable))) ?>
                </label>
            <?php if ($this->ActionError && !$this->ActionError->isValid('page_url')): ?>
                <p class="attention1"><?php assign($this->ActionError->getMessage('page_url')) ?></p>
            <?php else: ?>
                <br>
            <?php endif; ?>
            <small class="cautionList">
                ※シェア、モニプラメディアの遷移先が変更になります。<br>
                ※写真投稿の個票ページ、ギフトページ、人気投票の個票ページにあるキャンペーン導線も変更になります。<br>
                ※シェアモジュール内のサムネイルは指定先のOGPに従います。
            </small>
        </dd>
    <?php endif; ?>
</dl>
<!-- /.basicSetting1 --></section>
    <!-- /.moduleEditWrap --></section>
</form>
    <?php if ($data['CpStatus'] == Cp::CAMPAIGN_STATUS_DRAFT): ?>
    <div class="moduleCheck">
        <ul>
            <?php if($data['status'] == Cp::SETTING_FIX): ?>
                <li class="btn1"><a href="javascript:void(0)" id="editButton" data-action="cp_id=<?php assign($data['cp']->id) ?>&setting=1"
                                    data-url= "<?php assign(Util::rewriteUrl('admin-cp','api_change_setting_status.json'))?>">確定解除</a></li>
            <?php else: ?>
                <li class="btn2"><a href="javascript:void(0)" class="small1" id="submitDraft">下書き保存</a></li>
                <li class="btn3"><a href="javascript:void(0)" id="submit">内容確定</a></li>
            <?php endif; ?>
        </ul>
    <!-- /.moduleCheck --></div>
    <?php elseif(edit_setting_basic::canEditCp($data['CpStatus']) || $data['cp']->status == Cp::STATUS_DEMO): ?>
        <?php if(!$data['pageStatus']['isAgent']): ?>
            <div class="moduleCheck">
                <ul>
                    <li class="btn3"><a href="javascript:void(0)" id="submit" data-action="<?php assign(Util::rewriteUrl('admin-cp', 'save_setting_basic', array(Cp::SETTING_FIX))); ?>">更新</a></li>
                </ul>
            </div>
        <?php endif ?>

    <?php elseif($data['cp']->join_limit_flg == Cp::JOIN_LIMIT_ON && ($data['CpStatus'] == Cp::CAMPAIGN_STATUS_SCHEDULE || $data['CpStatus'] == Cp::CAMPAIGN_STATUS_OPEN)): ?>
        <?php $service_factory = new aafwServiceFactory();
            /** @var CpMessageDeliveryService $cp_message_delivery_service */
            $cp_message_delivery_service = $service_factory->create('CpMessageDeliveryService');
            /** @var CpFlowService $cp_flow_service */
            $cp_flow_service = $service_factory->create('CpFlowService');
            $first_cp_action = $cp_flow_service->getFirstActionOfCp($data['cp']->id);
            $cp_message_delivery_reservations = $cp_message_delivery_service->getCpMessageDeliveryReservationsByCpActionId($first_cp_action->id);
            if ($cp_message_delivery_reservations) {
                $cp_message_delivery_targets = $cp_message_delivery_service->getCpMessageDeliveryTargetsCountByReservationId($cp_message_delivery_reservations->current()->id);
            }
        ?>
        <?php if (!$cp_message_delivery_targets): ?>
            <div class="moduleCheck">
                <ul>
                    <li class="btn3"><a href="javascript:void(0)" id="cancelSchedule" data-action="<?php assign(Util::rewriteUrl('admin-cp', 'cancel_schedule_cp')); ?>">限定公開解除</a></li>
                </ul>
            </div>
        <?php endif; ?>

    <?php elseif($data['CpStatus'] == Cp::CAMPAIGN_STATUS_SCHEDULE && $data['cp']->join_limit_flg == Cp::JOIN_LIMIT_OFF): ?>
        <div class="moduleCheck">
            <ul>
                <li class="btn3"><a href="javascript:void(0)" id="cancelSchedule" data-action="<?php assign(Util::rewriteUrl('admin-cp', 'cancel_schedule_cp')); ?>">公開予約解除</a></li>
            </ul>
        </div>
    <?php endif; ?>
<!-- /.wrap --></article>

<link rel="stylesheet" href="<?php assign($this->setVersion('/css/jqueryUI.css'))?>">
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/i18n/jquery.ui.datepicker-ja.min.js"></script>
<?php write_html($this->parseTemplate('MessageDeliveryConfirmBox.php', array(
    'reservation' => null,
    'cp_id' => $data['cp']->id,
    'pageStatus' => $data['pageStatus'],
))) ?>

<?php $script = array('admin-cp/EditSettingBasicService','admin-cp/CpMenuService'); ?>
<?php $param = array_merge($data['pageStatus'], array('script' => $script)) ?>

<?php write_html($this->parseTemplate('BrandcoFooter.php', $param)); ?>
