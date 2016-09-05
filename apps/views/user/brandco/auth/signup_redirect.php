<form name="campaignJoinForm" action="<?php assign(Util::rewriteUrl('messages', 'join', '', '', '', true)); ?>" method="POST" enctype="multipart/form-data" >
    <?php write_html($this->csrf_tag()); ?>
    <?php write_html($this->formHidden('cp_id', $data["cp_id"])); ?>
    <?php write_html($this->formHidden('beginner_flg', $data['beginner_flg'])); ?>
</form>

<script>
    document.campaignJoinForm.submit();
</script>