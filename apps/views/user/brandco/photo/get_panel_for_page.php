<?php if ($data['page_data']['photo_entries'] && $data['page_data']['photo_entries']->total() > 0): ?>
    <?php $li_count = 0; ?>
    <?php foreach($data['page_data']['photo_entries'] as $photo_entry): ?>
        <?php if ($li_count != 0) write_html('-->'); ?><li class="jsPhotoPanel">
        <?php $photo_user = $photo_entry->getPhotoUser(); ?>
        <a href="<?php assign(Util::rewriteUrl('photo', 'detail', array($photo_entry->id))); ?>">
            <img src="<?php assign($photo_user->getCroppedPhoto()); ?>" alt="<?php assign($photo_user->photo_title); ?>" onerror="this.src='<?php assign($photo_user->photo_url); ?>';">
        </a>
        </li><?php if (++$li_count != $data['page_data']['photo_entries']->total()) write_html('<!--'); ?>
    <?php endforeach; ?>
<?php endif; ?>