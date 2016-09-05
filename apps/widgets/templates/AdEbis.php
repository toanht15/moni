<?php if($data['cid']): ?>
    <!-- EBiS tag version2.10 start -->
    <img src="<?php assign(Util::getHttpProtocol()); ?>://<?php assign(config('AdEbis.Domain')) ?>/log.php?argument=<?php assign($data['cid']); ?>&ebisPageID=<?php assign($data['page_type']); ?>&ebisMember=<?php assign($data['token']); ?>&ebisAmount=&ebisOther1=<?php assign($data['cp_user']->from_id); ?>&ebisOther2=&ebisOther3=<?php assign($data['cp_id']);?>&ebisOther4=&ebisOther5=" width="0" height="0">
    <!-- EBiS tag end-->
<?php endif; ?>