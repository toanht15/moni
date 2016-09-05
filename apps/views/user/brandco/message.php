<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoHeader')->render($data['pageStatus'])) ?>
<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoTopHeader')->render($data['pageStatus'])) ?>

<article>
    <section class="messageWrap">
        <?php if($data['status'] === strval(CpUserActionStatus::JOIN) || $data['can_read_message']): ?>
        <section class="message">
            <?php if($data["concrete_action"]->image_url): ?>
                <p class="messageImg"><img src="<?php assign($data["concrete_action"]->image_url); ?>" alt="campaign img"></p>
            <?php endif; ?>
            <?php $message_text = $data['concrete_action']->html_content ? $data['concrete_action']->html_content : $this->toHalfContentDeeply($data['concrete_action']->text); ?>
            <section class="messageText"><?php write_html($message_text); ?></section>
            <div class="messageFooter">
                <?php if(!$data['is_last_action_in_group']): ?>
                    <ul class="btnSet">
                        <li class="btn3"><a class="large3" href="<?php write_html(Util::rewriteUrl('my', 'login', array(), array('cp_id' => $data['cp']->id, 'msg_token' => $data['msg_token']))); ?>"><small>ログインして</small>続きをみる</a></li>
                        <!-- /.btnSet --></ul>
                <?php endif; ?>

                <p class="date"><small><?php assign(date("Y/m/d H:i", strtotime($data['cp_action']->created_at))); ?></small></p>
            </div>
         <!-- /.message --></section>
        <?php elseif ($data['status'] === strval(CpUserActionStatus::NOT_JOIN)): ?>

            <?php write_html($this->parseTemplate('UserMessageThreadActionDeadLine.php', array(
                "message_info" => $data['message_info'],
            )))
            ?>
        <?php endif; ?>
     <!-- /.messageWrap --></section>
</article>

<?php write_html($this->parseTemplate('BrandcoFooter.php', $data['pageStatus'])); ?>
