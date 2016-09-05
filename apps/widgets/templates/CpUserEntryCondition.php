    <div class="itemTableWrap">
        <table class="itemTable">
            <thead>
                <tr>
                    <th rowspan="2" class="jsAreaToggleWrap">
                        評価<a href="javascript:void(0)" class="<?php assign($data['search_condition'][CpCreateSqlService::SEARCH_PROFILE_RATE] ? 'iconBtnSort' : 'btnArrowB1')?> jsAreaToggle">絞り込む</a>
                        <?php write_html($this->parseTemplate('SearchMemberRate.php', array(
                            'search_rate' => $data['search_condition'][CpCreateSqlService::SEARCH_PROFILE_RATE]
                        ))) ?>
                    </th>
                    <?php $item_no = 1; $group_delivery_reservation_count = 0; ?>
                    <?php $sent_time_col = 0 ?>
                    <?php foreach($data['cp_action_groups'] as $group): ?>
                        <?php $actions = $data['cp_actions'][$group->id] ?>
                        <?php if ($data['show_sent_time'] && $data['target_count'][$group->id] && $actions[0]->type != CpAction::TYPE_ANNOUNCE_DELIVERY): ?>
                            <?php if($data['target_count'][$group->id]): ?>
                                <?php $sent_time_col = 1;?>
                            <?php endif; ?>
                        <?php endif; ?>
                        <th class="jsAreaToggleWrap" colspan="<?php assign($data['action_col'][$group->id] + $sent_time_col) ?>">
                            <?php if($data['action_col'][$group->id] >= 2): ?>
                                STEP<?php assign($item_no) ?>-<?php assign($item_no + $data['action_col'][$group->id] - 1) ?>
                            <?php else: ?>
                                STEP<?php assign($item_no) ?>
                            <?php endif; ?>
                        </th>
                        <?php $item_no += $data['action_col'][$group->id]?>
                        <?php $sent_time_col = 0 ?>
                    <?php endforeach; ?>
                </tr>
                <tr>
                    <?php $item_no = 1;?>
                    <?php foreach($data['cp_action_groups'] as $group): ?>
                        <?php $actions = $data['cp_actions'][$group->id] ?>
                        <?php if ($data['show_sent_time'] && $data['target_count'][$group->id] && $actions[0]->type != CpAction::TYPE_ANNOUNCE_DELIVERY): ?>
                            <?php $group_delivery_reservation_count++ ?>
                            <th class="jsAreaToggleWrap snsAccount" title="送信日時">
                                <?php assign($group->getStepName()) ?>送信日時
                                <a href="javascript:void(0)" class="<?php assign($data['search_condition'][CpCreateSqlService::SEARCH_DELIVERY_TIME.'/'.$actions[0]->id] ? 'iconBtnSort' : 'btnArrowB1')?> jsAreaToggle">絞り込む</a>
                                <?php write_html($this->parseTemplate('SearchDeliveryTime.php', array(
                                    'search_delivery_time' => $data['search_condition'][CpCreateSqlService::SEARCH_DELIVERY_TIME.'/'.$actions[0]->id],
                                    'action'               => $actions[0]
                                ))) ?>
                            </th>
                        <?php endif; ?>

                        <?php foreach($actions as $action): ?>
                            <?php $cp_action_data = $action->getCpActionData(); ?>
                            <th class="jsAreaToggleWrap snsAccount" title="<?php assign($item_no . '.' . $cp_action_data->title) ?>">
                                <?php assign(Util::cutTextByWidth($item_no . '.' . $cp_action_data->title, 190)) ?>
                                <a href="javascript:void(0)" class="<?php assign($data['search_condition'][CpCreateSqlService::SEARCH_PARTICIPATE_CONDITION.'/'.$action->id] ? 'iconBtnSort' : 'btnArrowB1')?> jsAreaToggle">絞り込む</a>
                                <?php write_html($this->parseTemplate('SearchParticipateCondition.php', array(
                                    'search_participate_condition' => $data['search_condition'][CpCreateSqlService::SEARCH_PARTICIPATE_CONDITION.'/'.$action->id],
                                    'action'                       => $action
                                ))) ?>
                            </th>
                            <?php $item_no++; ?>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach($data['fan_list_users'] as $fan_list_user): ?>
                    <?php if($data['action']->id): ?>
                        <?php $page_user_message = $data['page_user_message'][$fan_list_user->user_id]; ?>
                        <?php if($page_user_message[0]): ?>
                            <tr class="sendUser">
                        <?php elseif($page_user_message[1]): ?>
                            <tr class="checkedUser">
                        <?php else: ?>
                            <tr>
                        <?php endif; ?>
                    <?php else: ?>
                        <tr>
                    <?php endif; ?>

                    <td class="userRating">
                        <?php  $rate_info = $data['brand_user_relation_service']->getMemberRate($fan_list_user->rate);  ?>
                        <img src="<?php assign($this->setVersion($rate_info['image_url'])) ?>" width="<?php assign($fan_list_user->rate == BrandsUsersRelationService::BLOCK ? 18 : 24 )?>" height="<?php assign($fan_list_user->rate == BrandsUsersRelationService::BLOCK ? 18 : 24 )?>" alt="<?php assign($fan_list_user->rate == BrandsUsersRelationService::BLOCK ? 'block user' : 'rating '.$fan_list_user->rate )?>"><?php assign($rate_info['rate']) ?>
                        <p class="ratingBox"><a class="ratingBlock" id="<?php assign($fan_list_user->brands_users_relations_id) ?>">ブロック</a><span class="starRating" id="<?php assign($fan_list_user->brands_users_relations_id) ?>" data-score="<?php assign($fan_list_user->rate)?>"></span></p>
                    </td>

                    <?php foreach($data['cp_action_groups'] as $group): ?>
                        <?php $actions = $data['cp_actions'][$group->id] ?>
                        <?php if($data['show_sent_time'] && $data['target_count'][$group->id] && $actions[0]->type != CpAction::TYPE_ANNOUNCE_DELIVERY): ?>
                            <?php if($data['fan_list_send_time_array'][$group->id][$fan_list_user->user_id]): ?>
                                <td><?php assign(date("Y/m/d H:i", strtotime($data['fan_list_send_time_array'][$group->id][$fan_list_user->user_id]))) ?></td>
                            <?php else: ?>
                                <td></td>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php foreach($actions as $action): ?>
                            <?php $user_status = $data['fan_list_statuses'][$action->id][$fan_list_user->cp_user_id]['status'] ?>
                            <?php if($action->type == CpAction::TYPE_PHOTO): ?>
                                <?php if(is_array($user_status)): ?>
                                    <td>
                                        <a href="#photo_edit_modal" class="jsOpenPhotoModal" data-photo_user_id="<?php assign($user_status[1]) ?>" data-page_type=<?php assign('show_user_list') ?>>
                                            <img src="<?php assign($user_status[0]) ?>" width="20" height="20">
                                        </a>
                                    </td>
                                    <?php write_html($this->formHidden('photo_edit_modal_url', Util::rewriteUrl('admin-cp', 'api_get_photo_edit_modal.json'))) ?>
                                <?php else: ?>
                                    <td title="<?php assign($user_status); ?>"><?php assign($user_status); ?></td>
                                <?php endif; ?>
                            <?php elseif($action->type == CpAction::TYPE_FREE_ANSWER || $action->type == CpAction::TYPE_COUPON): ?>
                                <td title="<?php assign($user_status); ?>"><?php assign(Util::cutTextByWidth($user_status, 190, '...')); ?></td>
                            <?php elseif($action->isOpeningCpAction()): ?>
                                <?php if(is_array($user_status)): ?>
                                    <td><span title="<?php assign($user_status[1]); ?>"><?php assign($user_status[0]); ?></span></td>
                                <?php else: ?>
                                    <td title="<?php assign($user_status); ?>"><?php assign($user_status); ?></td>
                                <?php endif; ?>
                            <?php else: ?>
                                <td title="<?php assign($user_status); ?>"><?php assign($user_status); ?></td>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>

                <?php for($i = 1; $i <= CpCreateSqlService::DISPLAY_20_ITEMS-count($data['fan_list_users']); $i++): ?>
                    <tr>
                        <td></td>
                        <?php if ($data['show_sent_time']): ?>
                            <?php for ($j = 0; $j < $group_delivery_reservation_count; $j ++): ?>
                                <td></td>
                            <?php endfor; ?>
                        <?php endif; ?>
                        <?php for($td = 1; $td < $item_no; $td++): ?>
                            <td></td>
                        <?php endfor; ?>
                    </tr>
                <?php endfor; ?>
            </tbody>
        <!-- /.itemTable --></table>
    <!-- /.itemTableWrap --></div>
