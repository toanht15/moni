<?php if ($data['top_categories']): ?>
<nav class="sideNavi">
    <?php if ( Util::isSmartPhone()): ?>
        <ul>
    <?php endif; ?>
    <?php
    try{
        AAFW::import('jp.aainc.classes.cms_tree.CmsTreeCreator');
        $cms_tree_creator = CmsTreeCreator::getInstance($data['top_categories']);
        $cms_tree_creator->writeHtml($cms_tree_creator->create());
    }catch(Exception $e){
        aafwLog4phpLogger::getDefaultLogger()->error('SideColCategories tree display error.' . $e);
    }
    ?>
    <?php if ( Util::isSmartPhone()): ?>
        </ul>
    <?php endif; ?>
<!-- /.sideNavi --></nav>
<?php endif; ?>
