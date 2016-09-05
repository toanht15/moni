<?php $service_factory = new aafwServiceFactory();
/** @var StaticHtmlCategoryService $static_html_category_service */
$static_html_category_service = $service_factory->create('StaticHtmlCategoryService');
?>
<nav class="bredlink1">
    <ul>
        <li class="home"><a href="<?php write_html(Util::rewriteUrl('','')) ?>">HOME</a></li>
        <?php if ($data['grandfather_category']): ?>
            <li><a href="<?php write_html($static_html_category_service->getUrlByCategory($data['grandfather_category'])) ?>"><?php assign($data['grandfather_category']->name) ?></a></li>
        <?php endif; ?>
        <?php if ($data['father_category']): ?>
            <li><a href="<?php write_html($static_html_category_service->getUrlByCategory($data['father_category'])) ?>"><?php assign($data['father_category']->name) ?></a></li>
        <?php endif; ?>
        <?php  if ($data['current_category_url']): ?>
            <li><a href="<?php assign($data['current_category_url']) ?>"><?php assign($data['current_category_name']) ?></a></li>
            <li class="current"><span><?php write_html($data['page_title']) ?></span></li>
        <?php elseif ($data['current_category_name']): ?>
            <li class="current"><span><?php write_html($data['current_category_name']) ?></span></li>
        <?php else: ?>
            <li class="current"><span><?php write_html($data['page_title']) ?></span></li>
        <?php endif; ?>
    </ul>
<!-- /.bredlink1 --></nav>