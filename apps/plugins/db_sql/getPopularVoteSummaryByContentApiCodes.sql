SELECT      CPV.*, IFNULL(PVU.popular_vote_num, 0) popular_vote_num
FROM 
(
	SELECT    cpvc.id 				      popular_candidate_id,
			      cpvc.title 				    popular_candidate_title,
			      cpvc.description 		  popular_candidate_description,
			      cpvc.thumbnail_url 		popular_candidate_thumbnail_url,
			      cpvc.original_url 		popular_candidate_original_url,
			      cpvc.order_no 			  popular_candidate_order_no,
			      cpva.id 				      popular_vote_id,
			      cpva.title 				    popular_vote_title,
			      cpva.image_url 			  popular_vote_image_url
	FROM      cp_popular_vote_candidates cpvc
	LEFT JOIN cp_popular_vote_actions cpva ON cpva.id = cpvc.cp_popular_vote_action_id
  LEFT JOIN content_api_codes cac ON cac.cp_action_id = cpva.cp_action_id
	WHERE     cac.cp_action_type = ?cp_action_type?
	AND       cac.code           = ?code?
	AND       cac.del_flg        = 0

) CPV
LEFT JOIN
(
	SELECT	  pvu.cp_popular_vote_candidate_id	popular_vote_candidate_id,
			      count(*)							            popular_vote_num
	FROM 	    popular_vote_users pvu
  LEFT JOIN content_api_codes cac ON cac.cp_action_id = pvu.cp_action_id
	WHERE     pvu.del_flg        = 0
	AND       cac.del_flg        = 0
	AND       cac.cp_action_type = ?cp_action_type?
	AND       cac.code           = ?code?
	GROUP BY  pvu.cp_popular_vote_candidate_id
) PVU
ON CPV.popular_candidate_id = PVU.popular_vote_candidate_id
ORDER BY CPV.popular_candidate_order_no