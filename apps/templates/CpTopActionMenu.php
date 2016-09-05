<?php $detail = $data['action']->getCpActionDetail();?>
<li class="moduleDetail1">
    <?php if($data['action']->id == $data['current_id'] || $data['action']->isAnnounceDelivery()): ?>
        <span class="current <?php assign($data['action']->status == CpAction::STATUS_FIX ? 'finished' : '')?>">
            <img src="<?php assign($this->setVersion('/img/module/'.$detail['icon']))?>" height="25" width="25" alt="<?php assign($detail['title'])?>"><span class="textBalloon1"><span><?php assign($detail['title'])?></span></span>
        </span>
    <?php else: ?>
        <a class="moduleIcon <?php assign($data['action']->status == CpAction::STATUS_FIX ? 'finished' : '')?>" href="<?php assign(Util::rewriteUrl('admin-cp', 'edit_action_base', array($data['action']->id), null))?>">
            <img src="<?php assign($this->setVersion('/img/module/'.$detail['icon']))?>" height="25" width="25" alt="<?php assign($detail['title'])?>"><span class="textBalloon1"><span><?php assign($detail['title'])?></span></span>
        </a>
    <?php endif; ?>
</li>