<p class="batchAction">
    <span style="margin-right: 25px;border-right: 1px solid #9d9d9d"><?php write_html($this->formCheckbox2('tweet_check_all', null, array('class' => 'jsTweetCheckAll', 'data-tweet_check_class' => 'jsTweetCheck'), array('1' => '全選択'))) ?></span>
    <?php write_html($this->formRadio('multi_tweet_approval_status_' . $data['menu_order'],
        TweetMessage::APPROVAL_STATUS_APPROVE,
        array('class' => 'jsMultiTweetApprovalStatus'),
        array(TweetMessage::APPROVAL_STATUS_APPROVE => '出力', TweetMessage::APPROVAL_STATUS_REJECT => '非出力'))); ?>
    <span class="btn3"><a href="javascript:void(0);" class="small1 jsTweetActionFormSubmit<?php assign($data['menu_order']) ?>">適用</a></span>
    <!-- /.batchAction --></p>
