<?php foreach (CpTwitterFollowLog::$tw_follow_statuses as $key => $value): ?>
    <li>
        <?php write_html($this->formCheckbox(
            'search_tw_follow_type/' . $data['action_id'] . '/' . $key . '/' . $data["search_no"],
            $this->POST ? PHPParser::ACTION_FORM : $data['search_tw_follow']['search_tw_follow_type/' . $data['action_id'] . '/' . $key],
            array('checked' => $data['search_tw_follow']['search_tw_follow_type/' . $data['action_id'] . '/' . $key] ? "checked" : ""),
            array('1' => $value)
        ))?>
    </li>
<?php endforeach; ?>