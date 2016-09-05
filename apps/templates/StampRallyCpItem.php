<li class="<?php assign(StaticHtmlStampRallyService::$stamp_status_classes[$data['stamp_status']]); assign(!$data['start_date']? ' stampStatusComingsoonNoDate' : '') ?>">
    <?php if($data['start_date']): ?>
        <p class="startDate"><span class="day"><?php assign($data['start_date']->format('m/d')) ?></span><span class="time"><?php assign($data['start_date']->format('H:i')) ?></span><small>～</small></p>
    <?php endif; ?>
    <figure>
        <?php if($data['stamp_status'] == StaticHtmlStampRallyService::STAMP_STATUS_COMING_SOON): ?>
            <img src="<?php assign($data['cp_image']);?>">
        <?php elseif($data['stamp_status'] == StaticHtmlStampRallyService::STAMP_STATUS_CLOSED): ?>
            <span><img src="<?php assign($this->setVersion('/img/base/imgCpDummy1000.png'));?>"></span>
        <?php else: ?>
            <a href="<?php assign($data['cp_url'])?>" target="_blank"><img src="<?php assign($data['cp_image'] ? $data['cp_image'] : $this->setVersion('/img/base/imgCpDummy1000.png'));?>"></a>
        <?php endif; ?>
    </figure>
    <?php if($data['end_date']): ?>
        <p class="endDate"><span class="day"><?php assign($data['end_date']->format('m/d')) ?></span><span class="time"><?php assign($data['end_date']->format('H:i')) ?></span><small>まで</small></p>
    <?php endif; ?>
</li>

