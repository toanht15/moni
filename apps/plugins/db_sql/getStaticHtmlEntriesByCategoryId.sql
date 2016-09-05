SELECT she.id, she.title, she.page_url, she.og_image_url, she.created_at
FROM static_html_entries she
	LEFT JOIN static_html_entry_categories shec ON shec.static_html_entry_id = she.id
WHERE shec.category_id = ?category_id?
AND she.del_flg = 0
AND she.id <= ?max_id?
AND she.hidden_flg = 0
AND she.public_date <> ""
AND she.public_date <= NOW()