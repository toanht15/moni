<div class="col-sm-3 col-md-2 sidebar">
    <ul class="nav nav-sidebar">
        <?php foreach(Manager::$MANAGER_ALLOWED_LIST[$this->managerAccount->authority] as $menu): ?>
            <?php if(array_key_exists($menu, Manager::$MANAGER_MENU)): ?>
                <?php if(strstr(Manager::$MANAGER_MENU[$menu]['url'], $_SERVER['REQUEST_URI'])): ?>
                    <li<?php if(strstr(Manager::$MANAGER_MENU[$menu]['url'], $_SERVER['REQUEST_URI'])):?> class="active"<?php endif;?>><a><?php assign(Manager::$MANAGER_MENU[$menu]['name']) ?></a></li>
                <?php else: ?>
                    <li><a href="<?php assign(Manager::getManagerActionUrl(Manager::$MANAGER_MENU[$menu])); ?>" <?php if ($menu == Manager::MENU_INQUIRY) { write_html('target="_blank"'); } ?>><?php assign(Manager::$MANAGER_MENU[$menu]['name']) ?></a></li>
                <?php endif ?>
            <?php endif ?>
        <?php endforeach ?>
    </ul>
</div>
