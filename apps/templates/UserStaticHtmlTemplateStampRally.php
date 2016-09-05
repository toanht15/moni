<style>
    .pagePartsWrap .stampRally .stampRallyList li.stampStatusFinished figure a:after{
        background: url(<?php assign($data['stamp_status_finished_image']) ?>) 0 0 no-repeat;
        background-size: cover;
    }
    .pagePartsWrap .stampRally .stampRallyList li.stampStatusJoined figure a:after{
        background: url(<?php assign($data['stamp_status_joined_image']) ?>) 0 0 no-repeat;
        background-size: cover;
    }
</style>

<div class="pagePartsWrap">
    <div class="stampRally">
        <p class="jsCurActiveCp btnSet" style="display: none"><span class="btn3"><a href="">キャンペーン参加する </a></span></p>
        <?php if(!$data['is_login']): ?>
            <section class="stampRallyLogin">
                <h1>ログインをして、キャンペーンの参加状況をチェック！</h1>
                <p class="btnSet"><span class="btn3"><a href="<?php assign(Util::rewriteUrl('my', 'login', array(), array('redirect_url'=>Util::getCurrentUrl()))) ?>">ログイン</a></span></p>
                <!-- /.stampRallyLogin --></section>
        <?php endif; ?>
        <ul class="stampRallyList">
        <!-- /.stampRallyList --></ul>
        <?php write_html($this->formHidden('cp_count',$data['campaign_count'])) ?>
        <?php write_html($this->formHidden('cp_ids',json_encode($data['cp_ids']))) ?>
        <?php write_html($this->formHidden('stamp_status_coming_soon_image',$data['stamp_status_coming_soon_image'])) ?>
        <?php write_html($this->csrf_tag()); ?>
        <!-- ./stampRally --></div>
<!-- /.pagePartsWrap --></div>
<?php write_html($this->scriptTag('admin-blog/StaticHtmlStampRallyService'))?>