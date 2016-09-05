<?php
$share_types = [
    CpShareUserLog::TYPE_SHARE => CpShareUserLog::STATUS_SHARE,
    CpShareUserLog::TYPE_SKIP => CpShareUserLog::STATUS_SKIP,
    CpShareUserLog::TYPE_UNREAD => CpShareUserLog::STATUS_UNREAD
]
?>
<?php foreach ($share_types as $key => $value): ?>
    <li>
        <?php write_html($this->formCheckbox(
            'search_share_type/' . $key . '/' . $data["search_no"],
            $this->POST ? PHPParser::ACTION_FORM : $data['search_share']['search_share_type/' . $key],
            array('checked' => $data['search_share']['search_share_type/' . $key] ? "checked" : ""),
            array('1' => $value)
        ))?>
    </li>
<?php endforeach; ?>