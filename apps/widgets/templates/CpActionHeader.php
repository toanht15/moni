<?php write_html(aafwWidgets::getInstance()->loadWidget('CpActionTitle')->render(array('enable_archive' => $data['enable_archive'], 'cp' => $data['cp'])));?>

<ul class="campaignAction">
    <?php if($data['cp_status'] == Cp::CAMPAIGN_STATUS_DEMO && !$data["isHideDemoFunction"]): ?>
        <p class="btnSet">
            <span class="btn1"><a href="javascript:void(0)" class="resetDemoButton" data-cp-id="<?php assign($data['cp']->id) ?>">参加情報の一括クリア</a></span>
            <span class="btn3"><a href="javascript:void(0)" class="cancelDemoButton" data-cp-id="<?php assign($data['cp']->id) ?>">デモ終了</a></span>
        </p>
    <?php endif ?>
    <ul class="data">
        <li class="campaignTag">期間：<?php assign($data['cp_entry_term']) ?></li>
        <?php if(!$data['cp']->isNonIncentiveCp()): ?>
            <?php if($data['cp']->shipping_method == Cp::SHIPPING_METHOD_MESSAGE):?>
                <li class="<?php assign($data['should_announce'] ? 'campaignTag_caution' : 'campaignTag'); ?>">発表：<?php assign($data['cp_announce_date']) ?></li>
            <?php endif ?>
            <li class="campaignTag">当選数：<?php assign($data['cp_winner_count'])?></li>
        <?php endif ?>
        <?php if($data['cp']->join_limit_flg == cp::JOIN_LIMIT_OFF && $data['cp_status'] != Cp::CAMPAIGN_STATUS_DRAFT && $data['cp_status'] != Cp::CAMPAIGN_STATUS_SCHEDULE ): ?>
            <li class="preview">
                <a class="font-open" href="<?php assign($data['cp']->status == Cp::STATUS_DEMO ? $data['cp']->getDemoUrl() : $data['cp']->getUrl());?>" target="_blank">キャンペーンページへ</a>
            </li>
        <?php endif; ?>
    </ul><!-- /.data -->

    <ul class="action">
        <li><a class="font-edit" href="<?php assign(Util::rewriteUrl('admin-cp', 'edit_action', array($data['cp']->id,$data['first_action_id']))) ?>">編集</a></li>

        <li><a class="font-fan" href="<?php assign(Util::rewriteUrl('admin-cp', 'show_user_list', array($data['cp']->id,$data['first_action_id']), array('join_user' => 1))) ?>">参加者一覧</a></li>

        <?php if(!empty($data['post_actions'])): ?>
            <?php if(count($data['post_actions']) > 1): ?>
                <li>
                    <span class="font-images">
                        <select name="<?php assign('post_manage_'.$data['cp']->id)?>">
                            <option value="" selected>投稿管理</option>
                            <?php foreach($data['post_actions'] as $action_id => $action): ?>
                                <?php if($action['type'] == CpAction::TYPE_PHOTO): ?>
                                    <option value="<?php assign(Util::rewriteUrl('admin-cp', 'photo_campaign', array($action_id))); ?>">写真投稿</option>
                                <?php elseif($action['type'] == CpAction::TYPE_INSTAGRAM_HASHTAG): ?>
                                    <option value="<?php assign(Util::rewriteUrl('admin-cp', 'instagram_hashtags', array($action_id))); ?>">Instagram投稿</option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </span>
                </li>
            <?php else: ?>
                <li>
                    <?php $action_id = array_keys($data['post_actions'])[0]; ?>
                    <?php if($data['post_actions'][$action_id]['type'] == CpAction::TYPE_PHOTO): ?>
                        <a href="<?php assign(Util::rewriteUrl('admin-cp', 'photo_campaign', array($action_id))); ?>" class="font-images">投稿管理</a>
                    <?php elseif($data['post_actions'][$action_id]['type'] == CpAction::TYPE_INSTAGRAM_HASHTAG): ?>
                        <a href="<?php assign(Util::rewriteUrl('admin-cp', 'instagram_hashtags', array($action_id))); ?>" class="font-images">投稿管理</a>
                    <?php endif; ?>
                </li>
            <?php endif; ?>
        <?php endif; ?>
        <?php if ($data['first_tweet_action']): ?>
            <li><a href="<?php assign(Util::rewriteUrl('admin-cp', 'tweet_posts', array($data['first_tweet_action']))); ?>" class="font-images labelModeAllied">ツイート管理</a></li>
        <?php elseif ($data['subway_tweet_action']): // ツイート管理がクライアントも利用できるハードコーディング（SUBWAY-14）?>
            <li><a href="<?php assign(Util::rewriteUrl('admin-cp', 'tweet_posts', array($data['subway_tweet_action']))); ?>" class="font-images">ツイート管理</a></li>
        <?php endif; ?>

        <?php if ($data['first_questionnaire_action']): ?>
            <li><a href="<?php assign(Util::rewriteUrl('admin-cp', 'questionnaires', array($data['first_questionnaire_action']))); ?>" class="font-images labelModeAllied">アンケート出力管理</a></li>
        <?php endif; ?>

        <?php if ($data['first_popular_vote_action']): ?>
            <li><a href="<?php assign(Util::rewriteUrl('admin-cp', 'popular_votes', array($data['first_popular_vote_action']))); ?>" class="font-images labelModeAllied">人気投票管理</a></li>
        <?php endif; ?>

        <?php if(!empty($data['announce_actions'])): ?>
            <?php if(count($data['announce_actions']) > 1): ?>
                <li>
                    <span class="font-win">
                        <select name="<?php assign('announce_'.$data['cp']->id)?>">
                            <option value="" selected>当選発表</option>
                            <?php foreach($data['announce_actions'] as $action_id => $action): ?>
                                <option value="<?php assign(Util::rewriteUrl('admin-cp', 'show_user_list', array($data['cp']->id, $action['first_action_id_in_group']))); ?>"><?php assign('STEP '.$action['order_no'])?></option>
                            <?php endforeach; ?>
                        </select>
                    </span>
                </li>
            <?php else: ?>
                <li<?php if ($data['should_announce']) write_html(' class="caution"'); ?>>
                    <?php $action = array_values($data['announce_actions'])[0]; ?>
                    <a href="<?php assign(Util::rewriteUrl('admin-cp', 'show_user_list', array($data['cp']->id, $action['first_action_id_in_group']))); ?>" class="font-win">当選発表</a>
                </li>
            <?php endif; ?>
        <?php endif; ?>
        <li><a href="javascript:CpMenuService.downLoadDataDownLoadModalTemplate(<?php assign($data['cp']->id)?>)" class="font-download">データダウンロード</a></li>

        <?php if($data['user_list_page']): ?>
            <?php if(!empty($data['popular_vote_actions'])): ?>
                <?php if(count($data['popular_vote_actions']) > 1): ?>
                    <li>
                        <span class="font-images">
                            <select name="<?php assign('popular_vote_manage_'.$data['cp']->id)?>">
                                <option value="" selected>候補一覧</option>
                                <?php foreach($data['popular_vote_actions'] as $action_id => $action): ?>
                                    <option value="<?php assign(Util::rewriteUrl('popular_vote', 'ranking', array($action_id))); ?>"><?php assign('STEP '.$action['order_no'])?></option>
                                <?php endforeach; ?>
                            </select>
                        </span>
                    </li>
                <?php else: ?>
                    <li>
                        <?php $action_id = array_keys($data['popular_vote_actions'])[0]; ?>
                        <a href="<?php assign(Util::rewriteUrl('popular_vote', 'ranking', array($action_id))); ?>" class="font-images">候補一覧</a>
                    </li>
                <?php endif; ?>
            <?php endif; ?>

            <?php if($data['first_photo_action']): ?>
                <li>
                    <span class="font-download">
                        <select name="<?php assign('download_photo_'.$data['cp']->id)?>">
                            <option value="" selected>写真ダウンロード</option>
                            <option value="<?php assign(Util::rewriteUrl('admin-cp', 'download_photo_image_zip', array($data['cp']->id, $data['first_photo_action'])))?>">CSV+画像</option>
                            <option value="<?php assign(Util::rewriteUrl('admin-cp', 'download_user_photo_data_zip', array($data['cp']->id, $data['first_photo_action'])))?>">エクセル</option>
                        </select>
                    </span>
                </li>
            <?php endif; ?>

            <?php if($data['first_instagram_hashtag_action']): ?>
                <li>
                    <span class="font-download">
                        <select name="<?php assign('download_instagram_image'.$data['cp']->id)?>">
                            <option value="" selected>Instagram投稿ダウンロード</option>
                            <option value="<?php assign(Util::rewriteUrl('admin-cp', 'download_instagram_hashtag_post_image_zip', array($data['cp']->id, $data['first_instagram_hashtag_action']),array('file_type' => '1')))?>">CSV+画像</option>
                            <option value="<?php assign(Util::rewriteUrl('admin-cp', 'download_instagram_hashtag_post_image_zip', array($data['cp']->id, $data['first_instagram_hashtag_action']),array('file_type' => '2')))?>">エクセル</option>
                        </select>
                    </span>
                </li>
            <?php endif; ?>

            <?php if($data['pageStatus']['isLoginManager'])?>

                <?php if($data['first_questionnaire_action']): ?>
                    <li><a class="font-download labelModeAllied" href="<?php assign(Util::rewriteUrl('admin-cp', 'download_question_answer_zip', array($data['cp']->id, $data['first_questionnaire_action']))); ?>">アンケートダウンロード</a></li>
                <?php endif; ?>

                <?php if($data['first_tweet_action']): ?>
                    <li><a class="font-download labelModeAllied" href="<?php assign(Util::rewriteUrl('admin-cp', 'download_twitter_tweet_zip', array($data['cp']->id, $data['first_tweet_action']))); ?>">ツイートデータダウンロード</a></li>
                <?php endif; ?>

            <?php endif; ?>
    </ul><!-- /.action -->
</ul>

<?php write_html(aafwWidgets::getInstance()->loadWidget('CpHeaderActionList')->render(
    array(
        'cp' => $data['cp'],
        'group_array' => $data['group_array'],
        'action' => $data['action'],
        'group' => $data['group'],
        'user_list_page' => $data['user_list_page'],
        'pageStatus' => $data['pageStatus'],
        'enable_archive' => $data['enable_archive'],
        'isHideDemoFunction' => $data['isHideDemoFunction'],
    )
)); ?>
