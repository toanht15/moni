SELECT
  c.id, c.status, c.public_date, c.start_date, c.end_date, c.announce_date, c.selection_method, c.join_limit_flg, c.brand_id,
  c.show_monipla_com_flg, c.show_winner_label, c.winner_label, c.winner_count, b.name, b.directory_name,c.created_at,c.type
FROM
  cps c
INNER JOIN
  brands b ON b.id = c.brand_id
INNER JOIN  cp_action_groups d ON d.cp_id = c.id AND d.del_flg = 0                     ?MODULE?
INNER JOIN  cp_actions e ON e.cp_action_group_id = d.id AND e.del_flg = 0              ?MODULE?

WHERE
  b.test_page = ?test_page? AND
  (                                                                 ?RANGE?
    (                                                               ?IS_NOT_OPENED?
    c.status IN(?status?) AND                                       ?IS_NOT_OPENED?
    c.join_limit_flg IN(?join_limit?) AND                           ?LIMIT_FLG?
    c.del_flg = 0 OR                                                ?IS_NOT_OPENED?
    (                                                               ?IS_NOT_OPENED?
      c.status = 3 AND c.join_limit_flg IN(?join_limit?)  AND       ?IS_STATUS?
      (                                                             ?IS_STATUS?
        (NOW() < c.start_date OR c.end_date <= NOW()) AND           ?NOT_STATUS_OPEN?
        (NOW() < c.end_date OR c.announce_date <= NOW()) AND        ?NOT_STATUS_WAITING_ANNOUNCE?
        NOW() < c.announce_date AND                                 ?NOT_STATUS_ANNOUNCE?
        c.del_flg = 0                                               ?IS_STATUS?
      )                                                             ?IS_STATUS?
    )                                                               ?IS_NOT_OPENED?
  )                                                                 ?IS_NOT_OPENED?
  OR                                                                ?OR?
  (c.status IN(?range?) and c.join_limit_flg IN(0,1)))              ?RANGE?
  AND c.type = ?cp_type?
  AND c.brand_id =?brand_id?                               ?BRAND?
  AND e.type IN(?module?)                                  ?MODULE?
  AND c.public_date >= ?public_date_from?                  ?PUBLIC_DATE?
  AND c.public_date <= ?public_date_to?                    ?PUBLIC_DATE?
  AND c.start_date >= ?start_date_from?                    ?START_DATE?
  AND c.start_date <= ?start_date_to?                      ?START_DATE?
  AND c.end_date >= ?end_date_from?                        ?END_DATE?
  AND c.end_date <= ?end_date_to?                          ?END_DATE?
  AND c.announce_date >= ?announce_date_from?              ?ANNOUNCE_DATE?
  AND c.announce_date <= ?announce_date_to?                ?ANNOUNCE_DATE?
  AND c.winner_count  >= ?winner_count_from?               ?WINNER_COUNT?
  AND c.winner_count <=  ?winner_count_to?                 ?WINNER_COUNT?
  AND c.id = ?cp_id?                                       ?CP?

GROUP BY
  c.id

ORDER BY
  c.id DESC                                                ?ORDER_ID?
  c.public_date DESC                                       ?ORDER_PUBLIC?
  c.winner_count DESC                                      ?ORDER_WINNER?
