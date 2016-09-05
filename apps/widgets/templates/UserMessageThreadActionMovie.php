<section class="messageWrap jsMessage" id="message_<?php assign($data['message_info']["message"]->id); ?>" >
    <a id="<?php assign('ca_' . $data["message_info"]["cp_action"]->id) ?>"></a>

    <form class="executeMovieActionForm" action="<?php assign(Util::rewriteUrl('messages', "api_execute_movie_action.json")); ?>" method="POST">

        <?php write_html($this->csrf_tag()); ?>
        <?php write_html($this->formHidden('cp_action_id', $data['message_info']["cp_action"]->id)); ?>
        <?php write_html($this->formHidden('cp_user_id', $data['cp_user']->id)); ?>
        <?php write_html($this->formHidden('movie_object_id', $data["message_info"]["concrete_action"]->movie_object_id)); ?>
        <?php write_html($this->formHidden('view_status', $data["message_info"]["action_status"]->status)); ?>
        <?php write_html($this->formHidden('message_id', $data['message_info']["message"]->id)); ?>

            <section class="message">
                <?php if($data["message_info"]["concrete_action"]->image_url): ?>
                    <p class="messageImg"><img src="<?php assign($data["message_info"]["concrete_action"]->image_url); ?>"></p>
                <?php endif; ?>
                <?php if($data["message_info"]["concrete_action"]->text): ?>
                    <?php $message_text = $data['message_info']['concrete_action']->html_content ? $data['message_info']['concrete_action']->html_content : $this->toHalfContentDeeply($data['message_info']['concrete_action']->text); ?>
                    <section class="messageText"><?php write_html($message_text); ?></section>
                <?php endif; ?>
                    <?php if($data["message_info"]["concrete_action"]->isOriginalVideo()): ?>
                        <?php $not_join_text =$data["message_info"]["action_status"]->status == CpUserActionStatus::NOT_JOIN ? "<br/>視聴後に次のステップへ進みます。" : "" ?>
                        <?php if($data["message_info"]["concrete_action"]->popup_view_flg == 1): ?>
                        <div class="messageFooter">
                            <ul class="btnSet">
                            <li class="btn3"><a href="javascript:void(0);"data-link="<?php assign(Util::rewriteUrl('video', 'watch_video', array(), array('video_url' => urlencode($data['message_info']['concrete_action']->movie_url), 'msg_id' => $data['message_info']["message"]->id))) ?>"class="movie1 jsWatchVideoPopup"onclick="return false;">再生する</a></li>
                            <!-- /.btnSet --></ul>
                            <p class="info"><small class="supplement1">※別ウィンドウで再生します。<?php write_html($not_join_text) ?></small></p>
                            <!-- /.messageFooter -->
                        </div>
                        <?php else: ?>
                        <div class="messageMovie">
                            <p class="moveiInner"><video controls class="jsVideoStream" style="width: 100%;"><source src="<?php assign($data['message_info']['concrete_action']->movie_url) ?>" type="video/mp4"></video></p>
                            <p class="moveiAttention">動画を再生してください。<?php write_html($not_join_text) ?></p>
                        <!-- /.messageMovie -->
                        </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div id="yt_player"></div>
                        <div class="messageMovie">
                            <p class="moveiReload"><a href="javascript:void(0)" id="movieReload">再読み込み</a></p>
                            <?php if ($data["message_info"]["action_status"]->status == CpUserActionStatus::NOT_JOIN) : ?>
                                <p class="moveiAttention" id="state_playing">視聴後に次のステップへ進みます。</p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                <div class="messageFooter">

                </div>
                <!-- /.message --></section>
    </form>
    <!-- /.message --></section>
<?php write_html($this->scriptTag('user/UserActionMovieService')); ?>
