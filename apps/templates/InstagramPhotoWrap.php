<li>
    <div class="instaPhotoWrap">
        <a href="#instagram_modal" class="jsOpenIGModal"
           data-link="<?php assign($data['instagram_post']->link) ?>"
           data-entry="<?php assign(StreamService::STREAM_TYPE_INSTAGRAM) ?>"
        >
            <p class="instaPhoto"><img src="<?php assign($data['instagram_post']->images ? $data['instagram_post']->images->standard_resolution->url : '') ?>" alt=""></p>
            <p class="inText">
                <span class="userInfo"><span><img src="<?php assign($data['instagram_post']->caption->from->profile_picture) ?>" height="25" width="25" alt=""></span><span><?php assign($data['instagram_post']->caption->from->username) ?></span></span><?php write_html($this->nl2brAndHtmlspecialchars($data['instagram_post']->caption->text)) ?></p>
        </a>
        <!-- /.instaPhoto --></div>
</li>