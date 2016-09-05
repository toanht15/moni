<?php if($data['sideMenus'] || $data['isLoginAdmin']):?>
    <nav class="sideNavi jsEditAreaWrap">
        <?php if($data['brand']->side_menu_title):?>
            <?php if($data['brand']->side_menu_title_type == Brand::menuSideTypeImage):?>
                <h1><img alt="sideMenu" src="<?php assign($data['brand']->side_menu_title)?>"></h1>
            <?php else:?>
                <h1><?php assign($data['brand']->side_menu_title)?></h1>
            <?php endif;?>
        <?php else:?>
            <h1><?php assign($data['brand']->name)?></h1>
        <?php endif;?>
        <?php if($data['sideMenus']):?>
            <ul>
                <?php foreach($data['sideMenus'] as $menu):?>
                    <li><a href="<?php assign($menu->link)?>"<?php if($menu->is_blank_flg):?> target="_blank"<?php endif;?>><?php assign($menu->name)?></a></li>
                <?php endforeach;?>
            </ul>
        <?php elseif($data['isLoginAdmin']):?>
            <ul><li><a>外部リンクを設定できます</a></li></ul>
        <?php endif;?>
        <?php if($data['isLoginAdmin']):?>
            <section class="editArea">
                <a href="#sideMenus" class="jsOpenModal"><span>編集する</span></a>
            </section>
        <?php endif;?>
    </nav>
<?php endif;?>