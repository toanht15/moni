<dt class="moduleSettingTitle jsModuleContTile jsPopularVoteSettingTitle" data-file_type="<?php assign($data['vote_file_type']); ?>"><?php
    if ($data['vote_file_type'] == CpPopularVoteAction::FILE_TYPE_IMAGE) {
        assign(画像候補設定);
    } else {
        assign(動画候補設定);
    }
    ?></dt>
<dd class="moduleSettingDetail jsModuleContTarget jsPopularVoteSettingDetail" data-file_type="<?php assign($data['vote_file_type']); ?>">
    <p>
        投票テーマ
        <?php if ( $this->ActionError && !$this->ActionError->isValid('text') && $data['vote_file_type'] == $data['file_type']): ?>
            <span class="iconError1"><?php assign ( $this->ActionError->getMessage('text') )?></span>
        <?php endif; ?>
        <?php write_html($this->formTextArea('text', ($data['vote_file_type'] == $data['file_type']) ? $data['text'] : '', array('maxlength'=>CpValidator::MAX_TEXT_LENGTH, 'class'=>'jsCandidateText', 'cols' => 30, 'rows' => 10, 'data-file_type' => $data['vote_file_type'], $data['disable']=>$data['disable']))); ?>
        <a href="javascript:void(0);"
           data-link="<?php assign(Util::rewriteUrl('admin-blog', 'file_list', array(), array('f_id' => BrandUploadFile::POPUP_FROM_POPULAR_VOTE_MODULE, 'stt' => ($data['disable'] ? 1 : 2)))) ?>"
           class="openNewWindow1 jsFileUploaderPopup"
           onclick="return false;">ファイル管理から本文に画像URL挿入</a>
        <br>
        <a href="javascript:void(0);"
           class="openNewWindow1"
           id="markdown_rule_popup"
           data-link="<?php assign(Util::rewriteUrl('admin-cp', 'markdown_rule')); ?>"
           onclick="return false;">
            文字や画像の装飾について</a>
    </p>

    <ol class="moduleRankingItem jsCandidateList" data-file_type="<?php assign($data['vote_file_type']); ?>" style="width: auto;">
        <?php if ($data['vote_file_type'] == $data['file_type']): ?>
            <?php foreach($data['candidate_list'] as $key => $candidate): ?>
                <li data-order_no="<?php assign($key + 1) ?>" data-file_type="<?php assign($data['vote_file_type']) ?>" class="jsCandidate">
                    <input name="candidate_thumbnail_url[]" type="hidden" class="jsCandidateThumbnailUrl" value="<?php assign($candidate['thumbnail_url']) ?>">
                    <input name="candidate_original_url[]" type="hidden" class="jsCandidateOriginalUrl" value="<?php assign($candidate['original_url']) ?>">
                    <input name="candidate_id[]" type="hidden" class="jsCandidateId" value="<?php assign($candidate['id']) ?>">

                    <span class="itemLabel jsCandidateNo">候補<?php assign($key + 1); ?></span>

                    <?php
                        $is_errored = false;
                        if ($this->ActionError) {
                           $is_errored = !$this->ActionError->isValid('candidate_title_' . $key);
                            if ($data['vote_file_type'] == CpPopularVoteAction::FILE_TYPE_IMAGE) {
                                $is_errored = $is_errored || !$this->ActionError->isValid('candidate_image_' . $key);
                            } else {
                                $is_errored = $is_errored || !$this->ActionError->isValid('candidate_movie' . $key);
                            }
                        }
                    ?>
                    <?php if ($data['disable'] || ($data['is_cp_action_fixed'] && !$is_errored)): ?>
                        <?php // タイトル ?>
                        <input type="text" class="jsCandidateTitle" value="<?php assign($candidate['title']) ?>" disabled="disabled">
                        <input name="candidate_title[]" type="hidden" value="<?php assign($candidate['title']) ?>" maxlength="33">

                        <?php // 画像 / 動画 ?>
                        <?php if ($data['vote_file_type'] == CpPopularVoteAction::FILE_TYPE_IMAGE): ?>
                            <input name="candidate_image[]" type="file" class="jsCandidateImage" disabled="disabled">
                            <input name="candidate_image[]" type="file" style="display: none;">
                        <?php else: ?>
                            <span class="youtubeUrl jsCandidateMovie"><span>https://www.youtube.com/watch?v=</span><input type="text" value="<?php assign($candidate['movie']) ?>" disabled="disabled"></span>
                            <input name="candidate_movie[]" type="hidden" value="<?php assign($candidate['movie']) ?>">
                        <?php endif; ?>

                        <?php // 詳細 ?>
                        <textarea cols="30" rows="10" placeholder="任意の説明 (任意入力)" class="jsCandidateDescription" disabled="disabled"><?php assign($candidate['description']) ?></textarea>
                        <input name="candidate_description[]" type="hidden" value="<?php assign($candidate['description']) ?>">

                        <?php // 削除ボタン ?>
                    <?php else: ?>
                        <?php // タイトル ?>
                        <?php if ( $this->ActionError && !$this->ActionError->isValid('candidate_title_' . $key) && $data['vote_file_type'] == $data['file_type']): ?>
                            <span class="iconError1"><?php assign ( $this->ActionError->getMessage('candidate_title_' . $key) )?></span>
                        <?php endif; ?>
                        <input name="candidate_title[]" type="text" placeholder="タイトル" class="jsCandidateTitle" value="<?php assign($candidate['title']) ?>" <?php assign($data['disable']); ?> maxlength="33">
                        <br>
                        <small class="textLimit jsTextLimit">（0文字/33文字）</small>

                        <?php // 画像 / 動画 ?>
                        <?php if ($data['vote_file_type'] == CpPopularVoteAction::FILE_TYPE_IMAGE): ?>
                            <?php if ( $this->ActionError && !$this->ActionError->isValid('candidate_image_' . $key) && $data['vote_file_type'] == $data['file_type']): ?>
                                <span class="iconError1"><?php assign ( $this->ActionError->getMessage('candidate_image_' . $key) )?></span>
                            <?php endif; ?>
                            <input name="candidate_image[]" type="file" class="jsCandidateImage" <?php assign($data['disable']); ?>>
                            <small>（推奨サイズ: 横1000px × 縦524px）</small>
                        <?php else: ?>
                            <?php if ( $this->ActionError && !$this->ActionError->isValid('candidate_movie_' . $key) && $data['vote_file_type'] == $data['file_type']): ?>
                                <span class="iconError1"><?php assign ( $this->ActionError->getMessage('candidate_movie_' . $key) )?></span>
                            <?php endif; ?>
                            <span class="youtubeUrl jsCandidateMovie"><span>https://www.youtube.com/watch?v=</span><input name="candidate_movie[]" type="text" value="<?php assign($candidate['movie']) ?>" <?php assign($data['disable']); ?>></span>
                        <?php endif; ?>

                        <?php // 詳細 ?>
                        <textarea name="candidate_description[]" cols="30" rows="10" placeholder="任意の説明 (任意入力)" class="jsCandidateDescription" <?php assign($data['disable']); ?>><?php assign($candidate['description']) ?></textarea>

                        <?php // 削除ボタン ?>
                        <?php if (!$data['disable']): ?>
                            <a href="javascript:void(0)" class="iconBtnDelete jsOpenModal" data-open_modal_type="Confirm"></a>
                        <?php endif; ?>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        <?php endif; ?>
        <!-- /.moduleRankingItem --></ol>

    <?php if (!$data['disable']): ?>
        <?php if ( $this->ActionError && !$this->ActionError->isValid('n_candidates') && $data['vote_file_type'] == $data['file_type']): ?>
            <p class="iconError1"><?php assign ( $this->ActionError->getMessage('n_candidates') )?></p>
        <?php endif; ?>
        <p><a href="javascript:void(0)" class="linkAdd jsAddCandidate" data-file_type="<?php assign($data['vote_file_type']); ?>" onclick="return false;">候補を追加する</a></p>
    <?php endif; ?>

    <dl class="moduleSeelctItemSetting">
        <dt>選択肢をランダムに表示する</dt>
        <dd>
            <a href="javascript:void(0)" class="switch jsRandomFlg" data-file_type="<?php assign($data['vote_file_type']); ?>" <?php if ($data['disable']) write_html('data-disabled="disabled"'); ?>>
                <?php write_html($this->formHidden('random_flg', $data['random_flg'])) ?>
                <span class="switchInner">
                    <span class="selectON">ON</span>
                    <span class="selectOFF">OFF</span>
                </span>
            </a>
        </dd>
    </dl>

    <!-- /.moduleSettingDetail --></dd>
