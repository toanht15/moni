SELECT shc.id, shc.name, shc.directory, shc.order_no
FROM static_html_categories shc
	LEFT JOIN static_html_entry_categories shec ON shec.category_id = shc.id
		LEFT JOIN static_html_entries she ON she.id = shec.static_html_entry_id
WHERE shc.del_flg = 0
AND she.del_flg = 0
AND she.id = ?static_html_entry_id?