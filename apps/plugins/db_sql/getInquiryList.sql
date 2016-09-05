SELECT
    *
FROM (
    SELECT
        *
    FROM (
        SELECT
            inquiry_rooms.id id,
            inquiries.user_name user_name,
            inquiries.category category,
            inquiry_messages.content content,
            inquiry_messages.created_at created_at,
            inquiry_rooms.operator_name operator_name,
            inquiry_rooms.status status,
            brands.name brand_name
        FROM
            inquiry_rooms IR
        INNER JOIN
            inquiry_rooms_messages_relations IRMR ON IRMR.inquiry_room_id = IR.id AND IRMR.del_flg = 0
        INNER JOIN
            inquiry_messages IM ON IM.id = IRMR.inquiry_message_id AND IM.del_flg = 0
        INNER JOIN
            inquiries I ON I.id = IM.inquiry_id AND I.del_flg = 0
        INNER JOIN
            inquiry_users IU ON IU.id = I.inquiry_user_id AND IU.del_flg = 0
        INNER JOIN
            inquiry_brands IB ON IB.id = IR.inquiry_brand_id AND IB.del_flg = 0
        INNER JOIN
            brands B ON B.id = IB.brand_id AND B.del_flg = 0
        WHERE
            IR.del_flg = 0
            AND IR.operator_type = ?operator_type?
        ORDER BY inquiry_messages.created_at DESC
    ) D
    GROUP BY D.id
) E
ORDER BY E.created_at DESC
