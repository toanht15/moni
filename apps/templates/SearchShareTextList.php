<li>
    <?php write_html($this->formCheckbox(
        'search_share_text/'.CpShareUserLog::SEARCH_EXISTS.'/'.$data["search_no"],
        $this->POST ? PHPParser::ACTION_FORM : $data['search_share']['search_share_text/'.CpShareUserLog::SEARCH_EXISTS],
        array('checked' => $data['search_share']['search_share_text/'.CpShareUserLog::SEARCH_EXISTS] ? "checked" : ""),
        array('1' => 'あり')
    ))?>
</li>
<li>
    <?php write_html($this->formCheckbox(
        'search_share_text/'.CpShareUserLog::SEARCH_NOT_EXISTS.'/'.$data["search_no"],
        $this->POST ? PHPParser::ACTION_FORM : $data['search_share']['search_share_text/'.CpShareUserLog::SEARCH_NOT_EXISTS],
        array('checked' => $data['search_share']['search_share_text/'.CpShareUserLog::SEARCH_NOT_EXISTS] ? "checked" : ""),
        array('1' => 'なし')
    ))?>
</li>