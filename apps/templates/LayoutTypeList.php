<?php
$layout_sizes = StaticHtmlEntries::$layout_sizes;
$layout_src = StaticHtmlEntries::$layout_src;

$label = array(
    StaticHtmlEntries::LAYOUT_NORMAL => '<img src="' . $this->setVersion($layout_src[StaticHtmlEntries::LAYOUT_NORMAL]) .'" width="20" height="13" alt="' . $layout_sizes[StaticHtmlEntries::LAYOUT_NORMAL] .'">' . $layout_sizes[StaticHtmlEntries::LAYOUT_NORMAL] .'',
    StaticHtmlEntries::LAYOUT_LP => '<img src="' . $this->setVersion($layout_src[StaticHtmlEntries::LAYOUT_LP]) .'" width="20" height="13" alt="' . $layout_sizes[StaticHtmlEntries::LAYOUT_LP] .'">' . $layout_sizes[StaticHtmlEntries::LAYOUT_LP] .'',
    StaticHtmlEntries::LAYOUT_FULL => '<img src="' . $this->setVersion($layout_src[StaticHtmlEntries::LAYOUT_FULL]) .'" width="20" height="13" alt="' . $layout_sizes[StaticHtmlEntries::LAYOUT_FULL] .'">' . $layout_sizes[StaticHtmlEntries::LAYOUT_FULL] .''
);
?>

<?php write_html($this->formRadio('layout_type', PHPParser::ACTION_FORM, array('id' => 'layoutTypeInput'), $label, array(), '', false)); ?>
<?php if ($data['pageStatus']['isLoginManager']): ?>
    <?php write_html($this->formRadio('layout_type', PHPParser::ACTION_FORM, array('id' => 'layoutTypeInput', 'class' => 'jsPlainRadio'), array(StaticHtmlEntries::LAYOUT_PLAIN => 'プレーン(制作限定)'), array('class' => 'labelModeAllied'), '', false)); ?>
<?php endif; ?>
