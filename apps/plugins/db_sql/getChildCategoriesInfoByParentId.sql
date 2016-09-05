SELECT shc.id, shc.name, shc.directory, shc.description, shc.keyword, shc.created_at
FROM static_html_categories shc
	LEFT JOIN  static_html_category_relations shcr ON  shcr.children_id = shc.id
WHERE shcr.parent_id = ?parent_id?
AND shc.del_flg = 0
AND shcr.del_flg = 0