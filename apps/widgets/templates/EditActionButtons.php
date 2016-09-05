<?php if($data['is_fan_list_page']): ?>
    <?php $disable = '' ?>
<?php else: ?>
    <?php $disable = ($data['action']->status == CpAction::STATUS_FIX)?'disabled':'' ?>
<?php endif; ?>

    <section class="moduleEdit1">

        <?php write_html($this->parseTemplate('CpActionModuleTitle.php', array('disable'=>$disable))); ?>
        <?php write_html($this->parseTemplate('CpActionModuleImage.php', array('disable'=>$disable))); ?>
        <?php write_html($this->parseTemplate('CpActionModuleText.php', array('disable'=>$disable))); ?>

        <?php if(count($data['after_actions']) > 0):?>
            <?php foreach($data['after_actions'] as $cp_action): ?>
                <?php $options[$cp_action->id] = $cp_action->getCpActionDetail()['title'] ?>
            <?php endforeach; ?>
        <?php endif; ?>

        <section class="moduleCont1 open">
            <h1 class="editBranch1 jsModuleContTile">分岐ボタン設定</h1>
            <?php write_html($this->formHidden('order', "", array("id" => "order"))); ?>
            <div class="moduleSettingWrap jsModuleContTarget">
                <ul class="moduleSetting" id="editButtonUl">
                    <?php if ($data['cp_next_actions_info']): ?>
                        <?php $next_actions_count =  count($data['cp_next_actions_info']); ?>

                        <?php $service_factory = new aafwServiceFactory();
                            /** @var CpFlowService $cp_flow_service */
                            $cp_flow_service = $service_factory->create('CpFlowService');
                        ?>
                        <?php $last_next_action = end($data['cp_next_actions_info']) ?>
                        <?php for($i=0; $i < (count($data['cp_next_actions_info'])); $i++): ?>
                             <?php $next_action = $cp_flow_service->getCpNextActionById($data['cp_next_actions_info'][$i]->next_action_table_id) ?>
                            <li id="newLi_<?php assign($data['cp_next_actions_info'][$i]->id) ?>">
                            <span class="btn1Edit">
                                <?php write_html( $this->formText( 'newTitle'.$data['cp_next_actions_info'][$i]->id, $data['cp_next_actions_info'][$i]->label, array('placeholder'=>'入力してください', 'maxlength'=>'80', 'id'=>'btn_text'.$data['cp_next_actions_info'][$i]->id,$disable=>$disable))); ?>
                            </span>
                            <span class="btnAction">
                              <?php write_html($this->formSelect( "newOption".$data['cp_next_actions_info'][$i]->id, $next_action->cp_next_action_id, $attr = array($disable=>$disable), $options)) ?>
                            <!-- /.btnAction --></span>
                                <?php if ($data['cp_next_actions_info'][$i]->id == $last_next_action->id && count($data['cp_next_actions_info'])>1): ?>
                                    <a href="javascript:void(0)" class="iconBtnDelete" <?php if ($disable) write_html('style="display: none"') ?>>削除する</a>
                                <?php endif; ?>
                                <?php if ( $this->ActionError && !$this->ActionError->isValid('newTitle'.$data['cp_next_actions_info'][$i]->id)): ?>
                                    <p class="attention1"><?php assign ( $this->ActionError->getMessage('newTitle'.$data['cp_next_actions_info'][$i]->id) )?></p>
                                <?php endif; ?>
                                <?php if ( $this->ActionError && !$this->ActionError->isValid('newOption'.$data['cp_next_actions_info'][$i]->id)): ?>
                                    <p class="attention1"><?php assign ( $this->ActionError->getMessage('newOption'.$data['cp_next_actions_info'][$i]->id) )?></p>
                                <?php endif; ?>
                                </li>
                        <?php endfor; ?>

                    <?php else: ?>
                        <li id="newLi_1">
                            <span class="btn1Edit">
                               <?php write_html( $this->formText( 'newTitle1', PHPParser::ACTION_FORM, array('placeholder'=>'入力してください','maxlength'=>'80', 'id'=>'button1',$disable=>$disable))); ?>
                            </span>
                <span class="btnAction" style="display: none">
                          <?php write_html($this->formSelect( "newOption1", '', $attr = array('style'=>'display: none'), $options)) ?>
                <!-- /.btnAction --></span>
                        </li>
                    <?php endif; ?>
                    <li id="addBranch1Li" <?php if ($disable) write_html('style="display: none"') ?>><a href="javascript:void(0)" class="linkAdd" id="addBranch1Button">追加する</a>
                        <a href="javascript:void(0)" class="btnBranch1"><span>分岐する</span></a></li>
                    <!-- /.moduleSetting --></ul>
                <!-- /.moduleSettingWrap --></div>
            <!-- /.moduleCont1 --></section>

        <!-- /.moduleEdit1 --></section>
    <section class="modulePreview1">
        <header class="modulePreviewHeader">
            <p>スマートフォン<a href="#" class="toggle_switch left jsModulePreviewSwitch">toggle_switch</a>PC</p>
            <!-- /.modulePreviewHeader --></header>

        <div class="displaySP jsModulePreviewArea">
            <section class="messageWrap">

                <section class="message">
                    <p class="messageImg"><img src="" id="imagePreview"></p>
                    <section class="messageText" id="textPreview"></section>
                    <div class="messageFooter">
                        <ul class="btnSet" id="buttonPreview">
                        </ul>
                        <p class="date"><small>2014/06/25 10:57</small></p>
                    </div>
                    <!-- /.message --></section>

                <!-- /.messageWrap --></section>

            <!-- /.displayPC --></div>

        <!-- /.modulePreview --></section>