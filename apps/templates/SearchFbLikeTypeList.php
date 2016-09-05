<?php foreach (CpFacebookLikeLog::$fb_like_statuses as $key => $value): ?>
    <li>
        <?php write_html($this->formCheckbox(
            'search_fb_like_type/' . $data['action_id'] . '/' . $key . '/' . $data["search_no"],
            $this->POST ? PHPParser::ACTION_FORM : $data['search_fb_like']['search_fb_like_type/' . $data['action_id'] . '/' . $key],
            array('checked' => $data['search_fb_like']['search_fb_like_type/' . $data['action_id'] . '/' . $key] ? "checked" : ""),
            array('1' => $value)
        ))?>
    </li>
<?php endforeach; ?>