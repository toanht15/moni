<?php
AAFW::import('jp.aainc.aafw.base.aafwServiceBase');
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');

class InquiryService extends aafwServiceBase {

    const MODEL_TYPE_INQUIRIES = 1;
    const MODEL_TYPE_INQUIRY_ROOMS = 2;
    const MODEL_TYPE_INQUIRY_ROOMS_MESSAGES_RELATIONS = 3;
    const MODEL_TYPE_INQUIRY_MESSAGES = 4;
    const MODEL_TYPE_INQUIRY_USERS = 5;

    const N_INQUIRIES_PER_PAGE = 20;

    /** @var aafwLog4phpLogger $logger */
    protected $logger;
    /** @var aafwLog4phpLogger $hipchat_logger */
    protected $hipchat_logger;

    private $models = array();

    public function __construct() {
        $this->models = array(
            self::MODEL_TYPE_INQUIRIES => $this->getModel('Inquiries'),
            self::MODEL_TYPE_INQUIRY_ROOMS => $this->getModel('InquiryRooms'),
            self::MODEL_TYPE_INQUIRY_ROOMS_MESSAGES_RELATIONS => $this->getModel('InquiryRoomsMessagesRelations'),
            self::MODEL_TYPE_INQUIRY_MESSAGES => $this->getModel('InquiryMessages'),
            self::MODEL_TYPE_INQUIRY_USERS => $this->getModel('InquiryUsers'),
        );

        $this->logger = aafwLog4phpLogger::getDefaultLogger();
        $this->hipchat_logger = aafwLog4phpLogger::getHipChatLogger();
    }

    /**
     * @param $model_type
     * @param $filter
     * @return mixed
     */
    public function getRecord($model_type, $filter = array()) {
        return $this->models[$model_type]->findOne(array_merge($filter, array('del_flg' => 0)));
    }

    /**
     * @param $model_type
     * @param $filter
     * @return mixed
     */
    public function getRecords($model_type, $filter = array()) {
        return $this->models[$model_type]->find(array_merge($filter, array('del_flg' => 0)));
    }

    /**
     * @param $model_type
     * @param $filter
     * @return mixed
     */
    public function countRecord($model_type, $filter = array()) {
        return $this->models[$model_type]->count(array_merge($filter, array('del_flg' => 0)));
    }

    /**
     * @param $inquiry_user_id
     * @param array $data
     * @return mixed
     * @throws aafwException
     */
    public function createInquiry($inquiry_user_id, $data = array()) {
        $inquiry = $this->models[self::MODEL_TYPE_INQUIRIES]->createEmptyObject();
        $inquiry->inquiry_user_id = $inquiry_user_id;
        $inquiry->user_name = ($data['user_name']) ?: '';
        $inquiry->user_agent = ($data['user_agent']) ?: '';
        $inquiry->referer = ($data['referer']) ?: '';
        $inquiry->cp_id = ($data['cp_id']) ?: 0;
        $inquiry->brand_id = ($data['brand_id']) ?: 0;
        $inquiry->category = ($data['category']) ?: Inquiry::TYPE_OTHERS;

        return $this->models[self::MODEL_TYPE_INQUIRIES]->save($inquiry);
    }

    /**
     * 更新時間を変更
     * @param $inquiry_id
     * @return mixed
     */
    public function updateInquiry($inquiry_id) {
        $inquiry = $this->getRecord(self::MODEL_TYPE_INQUIRIES, array('id' => $inquiry_id));

        return $this->models[self::MODEL_TYPE_INQUIRIES]->save($inquiry);
    }

    /**
     * @param $inquiry_brand_id
     * @param $inquiry_id
     * @param $operator_type
     * @param array $data
     * @return mixed
     */
    public function createInquiryRoom($inquiry_brand_id, $inquiry_id, $operator_type, $data = array()) {
        $inquiry_room = $this->models[self::MODEL_TYPE_INQUIRY_ROOMS]->createEmptyObject();
        $inquiry_room->inquiry_brand_id = $inquiry_brand_id;
        $inquiry_room->inquiry_id = $inquiry_id;
        $inquiry_room->operator_name = ($data['operator_name']) ?: '';
        $inquiry_room->operator_type = $operator_type;
        $inquiry_room->status = ($data['status']) ?: InquiryRoom::STATUS_OPEN;
        $inquiry_room->access_token = md5(uniqid(rand(), 1));
        $inquiry_room->inquiry_section_id_1 = ($data['inquiry_section_id_1']) ?: 0;
        $inquiry_room->inquiry_section_id_2 = ($data['inquiry_section_id_2']) ?: 0;
        $inquiry_room->inquiry_section_id_3 = ($data['inquiry_section_id_3']) ?: 0;
        $inquiry_room->remarks = ($data['remarks']) ?: '';
        $inquiry_room->forwarded_flg = ($data['forwarded_flg']) ?: 0;

        return $this->models[self::MODEL_TYPE_INQUIRY_ROOMS]->save($inquiry_room);
    }

    /**
     * @param $inquiry_room_id
     * @param array $data
     * @return mixed
     */
    public function updateInquiryRoom($inquiry_room_id, $data = array()) {
        $inquiry_room = $this->getRecord(self::MODEL_TYPE_INQUIRY_ROOMS, array('id' => $inquiry_room_id));
        $inquiry_room->operator_name = isset($data['operator_name']) ? $data['operator_name'] : $inquiry_room->operator_name;
        $inquiry_room->status = isset($data['status']) ? $data['status'] : $inquiry_room->status;
        $inquiry_room->inquiry_section_id_1 = isset($data['inquiry_section_id_1']) ? $data['inquiry_section_id_1'] : $inquiry_room->inquiry_section_id_1;
        $inquiry_room->inquiry_section_id_2 = isset($data['inquiry_section_id_2']) ? $data['inquiry_section_id_2'] : $inquiry_room->inquiry_section_id_2;
        $inquiry_room->inquiry_section_id_3 = isset($data['inquiry_section_id_3']) ? $data['inquiry_section_id_3'] : $inquiry_room->inquiry_section_id_3;
        $inquiry_room->remarks = ($data['remarks']) ?: $inquiry_room->remarks;
        $inquiry_room->forwarded_flg = isset($data['forwarded_flg']) ?: $inquiry_room->forwarded_flg;

        return $this->models[self::MODEL_TYPE_INQUIRY_ROOMS]->save($inquiry_room);
    }

    /**
     * @param $inquiry_brand_id
     * @param $inquiry_id
     * @param $operator_type
     * @param array $data
     * @return mixed
     */
    public function getOrCreateInquiryRoom($inquiry_brand_id, $inquiry_id, $operator_type, $data = array()) {
        if ($record = $this->getRecord(self::MODEL_TYPE_INQUIRY_ROOMS, array(
            'inquiry_brand_id' => $inquiry_brand_id,
            'inquiry_id' => $inquiry_id,
            'operator_type' => $operator_type
        ))
        ) {
            return $record;
        }

        return $this->createInquiryRoom($inquiry_brand_id, $inquiry_id, $operator_type, $data);
    }

    /**
     * @param $inquiry_room_id
     * @param $inquiry_message_id
     * @param array $data
     * @return mixed
     * @throws aafwException
     */
    public function createInquiryRoomsMessagesRelation($inquiry_room_id, $inquiry_message_id, $data = array()) {
        $inquiry_rooms_messages_relation = $this->models[self::MODEL_TYPE_INQUIRY_ROOMS_MESSAGES_RELATIONS]->createEmptyObject();
        $inquiry_rooms_messages_relation->inquiry_room_id = $inquiry_room_id;
        $inquiry_rooms_messages_relation->inquiry_message_id = $inquiry_message_id;
        $inquiry_rooms_messages_relation->forward_flg = ($data['forward_flg']) ?: 0;
        $inquiry_rooms_messages_relation->forwarded_flg = ($data['forwarded_flg']) ?: 0;

        return $this->models[self::MODEL_TYPE_INQUIRY_ROOMS_MESSAGES_RELATIONS]->save($inquiry_rooms_messages_relation);
    }

    /**
     * @param $inquiry_rooms_messages_relation_id
     * @param array $data
     * @return mixed
     */
    public function updateInquiryRoomsMessagesRelation($inquiry_rooms_messages_relation_id, $data = array()) {
        $inquiry_rooms_messages_relation = $this->getRecord(self::MODEL_TYPE_INQUIRY_ROOMS_MESSAGES_RELATIONS, array('id' => $inquiry_rooms_messages_relation_id));
        $inquiry_rooms_messages_relation->forward_flg = isset($data['forward_flg']) ?: $inquiry_rooms_messages_relation->forward_flg;
        $inquiry_rooms_messages_relation->forwarded_flg = isset($data['forwarded_flg']) ?: $inquiry_rooms_messages_relation->forwarded_flg;

        return $this->models[self::MODEL_TYPE_INQUIRY_ROOMS_MESSAGES_RELATIONS]->save($inquiry_rooms_messages_relation);
    }

    /**
     * @param $inquiry_room_id
     * @param $inquiry_message_id
     * @param array $data
     * @return mixed
     */
    public function getOrCreateInquiryRoomsMessagesRelation($inquiry_room_id, $inquiry_message_id, $data = array()) {
        if ($record = $this->getRecord(self::MODEL_TYPE_INQUIRY_ROOMS_MESSAGES_RELATIONS, array(
            'inquiry_room_id' => $inquiry_room_id,
            'inquiry_message_id' => $inquiry_message_id,
        ))
        ) {
            return $record;
        }

        return $this->createInquiryRoomsMessagesRelation($inquiry_room_id, $inquiry_message_id, $data);
    }

    /**
     * @param $inquiry_id
     * @param $sender
     * @param array $data
     * @return mixed
     * @throws aafwException
     */
    public function createInquiryMessage($inquiry_id, $sender, $data = array()) {
        $inquiry_message = $this->models[self::MODEL_TYPE_INQUIRY_MESSAGES]->createEmptyObject();
        $inquiry_message->inquiry_id = $inquiry_id;
        $inquiry_message->sender = $sender;
        $inquiry_message->content = ($data['content']) ?: '';
        $inquiry_message->draft_flg = ($data['draft_flg']) ?: 0;

        return $this->models[self::MODEL_TYPE_INQUIRY_MESSAGES]->save($inquiry_message);
    }

    /**
     * @param $inquiry_message_id
     * @param array $data
     * @return mixed
     */
    public function updateInquiryMessage($inquiry_message_id, $data = array()) {
        $inquiry_message = $this->getRecord(self::MODEL_TYPE_INQUIRY_MESSAGES, array('id' => $inquiry_message_id));
        $inquiry_message->sender = ($data['sender']) ?: $inquiry_message->sender;
        $inquiry_message->content = ($data['content']) ?: $inquiry_message->content;
        $inquiry_message->draft_flg = (isset($data['draft_flg'])) ? $data['draft_flg'] : $inquiry_message->draft_flg;

        return $this->models[self::MODEL_TYPE_INQUIRY_MESSAGES]->save($inquiry_message);
    }

    /**
     * @param $sender
     * @param $inquiry_id
     * @param $inquiry_room_id
     * @param array $data
     * @return mixed
     */
    public function createInquiryMessageAndRelation($sender, $inquiry_id, $inquiry_room_id, $data = array()) {
        $inquiry_message = $this->createInquiryMessage($inquiry_id, $sender, $data);
        $inquiry_rooms_messages_relation = $this->createInquiryRoomsMessagesRelation($inquiry_room_id, $inquiry_message->id, $data);

        return array($inquiry_message, $inquiry_rooms_messages_relation);
    }

    /**
     * @param array $data
     * @return mixed
     * @throws aafwException
     */
    public function createInquiryUser($data = array()) {
        $inquiry_user = $this->models[self::MODEL_TYPE_INQUIRY_USERS]->createEmptyObject();
        $inquiry_user->user_id = ($data['user_id']) ?: InquiryUser::USER_ID_ANONYMOUS;
        $inquiry_user->mail_address = ($data['mail_address']) ?: '';

        return $this->models[self::MODEL_TYPE_INQUIRY_USERS]->save($inquiry_user);
    }

    /**
     * @param $data
     * @return entity|mixed
     */
    public function getOrCreateInquiryUser($data) {
        if ($inquiry_user = $this->getRecord(self::MODEL_TYPE_INQUIRY_USERS, array(
            'user_id' => $data['user_id'],
            'mail_address' => $data['mail_address'],
        ))
        ) {
            return $inquiry_user;
        };

        return $this->createInquiryUser($data);
    }

    /***********************************************************
     * forwardInquiry
     ***********************************************************/
    /**
     * @param $forward_from
     * @param string $forward_memo
     * @param int $forwarded_rooms_messages_relation_id
     */
    public function forwardInquiry($forward_from, $forward_memo = '', $forwarded_rooms_messages_relation_id = 0) {
        // 転送元のInquiryRoomにforwarded_flgを立てる
        $this->updateInquiryRoom($forward_from->id, array('status' => InquiryRoom::STATUS_FORWARDED));

        // 転送先のInquiryRoomを取得
        $forward_to = $this->getOrCreateInquiryRoom($forward_from->inquiry_brand_id, $forward_from->inquiry_id, InquiryRoom::getOppositeType($forward_from->operator_type), array(
            'forwarded_flg' => 1
        ));

        // 転送先の情報を更新
        $this->updateInquiryRoom($forward_to->id, array(
            'status' => InquiryRoom::STATUS_OPEN,
            'remarks' => ">>> 転送メッセージ ". date('Y/m/d H:i:s') . PHP_EOL . $forward_memo .  PHP_EOL . PHP_EOL . $forward_to->remarks
        ));
        $this->updateInquiry($forward_to->inquiry_id);

        // 転送するInquiryMessageのInquiryMessageRelationを取得
        $filter = array('inquiry_room_id' => $forward_from->id);
        if ($forwarded_rooms_messages_relation_id) {
            $filter['id'] = $forwarded_rooms_messages_relation_id;
        }
        $inquiry_rooms_messages_relations = $this->getRecords(InquiryService::MODEL_TYPE_INQUIRY_ROOMS_MESSAGES_RELATIONS, $filter);

        foreach ($inquiry_rooms_messages_relations as $index => $inquiry_rooms_messages_relation) {
            // 転送されるInquiryMessageのInquiryRoomsMessagesRelationsにforwarded_flgを立てる
            $this->updateInquiryRoomsMessagesRelation($inquiry_rooms_messages_relation->id, array(
                'forwarded_flg' => 1
            ));

            $this->getOrCreateInquiryRoomsMessagesRelation($forward_to->id, $inquiry_rooms_messages_relation->inquiry_message_id, array('forward_flg' => 1));
        }
    }

    /***********************************************************
     * getSenderList
     ***********************************************************/
    /**
     * @param $brand
     * @param $inquiry_room_id
     * @return array
     */
    public function getSenderList($brand, $inquiry_room_id) {
        $inquiry_user_detail = $this->getInquiryUserDetail($inquiry_room_id);

        $sender_list = array();
        $sender_list[InquiryMessage::USER] = array(
            'name' => $inquiry_user_detail['user_name'],
            'image' => $inquiry_user_detail['profile_image_url']
        );
        $sender_list[InquiryMessage::ADMIN] = array(
            'name' => $brand->name,
            'image' => $brand->getProfileImage()
        );
        $sender_list[InquiryMessage::MANAGER] = array(
            'name' => InquiryBrand::MANAGER_BRAND_NAME,
            'image' => InquiryBrand::MANAGER_BRAND_IMAGE,
        );

        return $sender_list;
    }

    /***********************************************************
     * getSQL
     ***********************************************************/
    /**
     * @param $operator_type
     * @param $n_start
     * @param array $params
     * @return array
     */
    public function getInquiryList($operator_type, $n_start, $params = array()) {
        $where = '';
        $clauses = $this->createClauses($params, InquiryRoom::isManager($operator_type));
        foreach ($clauses as $clause) {
            $where .= ' AND ' . $clause;
        }

        $role = InquiryMessage::USER;
        $limit = ($n_start === -1) ? '' : 'LIMIT ' . $n_start . ', ' . self::N_INQUIRIES_PER_PAGE;

        $db = aafwDataBuilder::newBuilder();
        $sql = <<<EOS
        SELECT * FROM
        (
            SELECT * FROM
            (
                SELECT
                    inquiry_rooms.id					        id,
                    inquiries.user_name                         user_name,
                    inquiries.category                          category,
                    inquiry_messages.content                    content,
                    inquiry_rooms_messages_relations.created_at created_at,
                    inquiry_rooms.operator_name                 operator_name,
                    inquiry_rooms.status                        status,
                    inquiry_rooms.remarks                       remarks,
                    brands.name                                 brand_name
                FROM inquiry_rooms
                INNER JOIN
                	inquiry_rooms_messages_relations ON(
                		inquiry_rooms_messages_relations.inquiry_room_id = inquiry_rooms.id
                		AND inquiry_rooms_messages_relations.del_flg = 0
                	)
                INNER JOIN
                    inquiry_messages ON(
                        inquiry_messages.id = inquiry_rooms_messages_relations.inquiry_message_id
                        AND inquiry_messages.sender = {$role}
                        AND inquiry_messages.draft_flg = 0
                        AND inquiry_messages.del_flg = 0
                    )
                INNER JOIN
                    inquiries ON(
                        inquiries.id = inquiry_messages.inquiry_id
                        AND inquiries.del_flg = 0
                    )
                INNER JOIN
                    inquiry_users ON(
                        inquiry_users.id = inquiries.inquiry_user_id
                        AND inquiry_users.del_flg = 0
                    )
                INNER JOIN
                    inquiry_brands ON(
                        inquiry_brands.id = inquiry_rooms.inquiry_brand_id
                        AND inquiry_brands.del_flg = 0
                    )
                INNER JOIN
                	brands ON(
                		brands.id = inquiry_brands.brand_id
                		AND brands.del_flg = 0
                	)
                WHERE
                    inquiry_rooms.del_flg = 0
                    AND inquiry_rooms.operator_type = {$this->escape($operator_type)}
                    {$where}
                ORDER BY inquiry_rooms_messages_relations.created_at DESC
            ) D
            GROUP BY D.id
        ) E
        ORDER BY E.created_at DESC
        {$limit}
EOS;

        return $db->getBySQL($sql, array());
    }

    /**
     * @param $operator_type
     * @param array $params
     * @return mixed
     */
    public function countInquiryList($operator_type, $params = array()) {
        $where = '';
        $clauses = $this->createClauses($params, InquiryRoom::isManager($operator_type));
        foreach ($clauses as $clause) {
            $where .= ' AND ' . $clause;
        }

        $role = InquiryMessage::USER;
        $db = aafwDataBuilder::newBuilder();
        $sql = <<<EOS
        SELECT
            count(E.id) count
        FROM
        (
            SELECT
                *
            FROM
            (
                SELECT
                    inquiry_rooms.id					        id,
                    inquiries.user_name                         user_name,
                    inquiries.category                          category,
                    inquiry_messages.content                    content,
                    inquiry_rooms_messages_relations.created_at created_at,
                    inquiry_rooms.operator_name                 operator_name,
                    inquiry_rooms.status                        status,
                    inquiry_rooms.remarks                       remarks,
                    brands.name                                 brand_name
                FROM inquiry_rooms
                INNER JOIN
                    inquiry_rooms_messages_relations ON(
                        inquiry_rooms_messages_relations.inquiry_room_id = inquiry_rooms.id
                        AND inquiry_rooms_messages_relations.del_flg = 0
                    )
                INNER JOIN
                    inquiry_messages ON(
                        inquiry_messages.id = inquiry_rooms_messages_relations.inquiry_message_id
                        AND inquiry_messages.sender = {$role}
                        AND inquiry_messages.draft_flg = 0
                        AND inquiry_messages.del_flg = 0
                    )
                INNER JOIN
                    inquiries ON(
                        inquiries.id = inquiry_messages.inquiry_id
                        AND inquiries.del_flg = 0
                    )
                INNER JOIN
                    inquiry_users ON(
                        inquiry_users.id = inquiries.inquiry_user_id
                        AND inquiry_users.del_flg = 0
                    )
                INNER JOIN
                    inquiry_brands ON(
                        inquiry_brands.id = inquiry_rooms.inquiry_brand_id
                        AND inquiry_brands.del_flg = 0
                    )
                INNER JOIN
                    brands ON(
                        brands.id = inquiry_brands.brand_id
                        AND brands.del_flg = 0
                    )
                WHERE
                    inquiry_rooms.del_flg = 0
                    AND inquiry_rooms.operator_type = {$this->escape($operator_type)}
                    {$where}
                ORDER BY inquiry_rooms_messages_relations.created_at DESC
            ) D
            GROUP BY D.id
        ) E
EOS;

        list($result) = $db->getBySQL($sql, array());

        return $result['count'];
    }

    /**
     * @param array $params
     * @return array
     */
    public function getInquiryListForCSV($params = array()) {
        $where = '';
        $operator_type = InquiryRoom::TYPE_MANAGER;
        $clauses = $this->createClauses($params, true);
        foreach ($clauses as $clause) {
            $where .= ' AND ' . $clause;
        }

        $db = aafwDataBuilder::newBuilder();
        $sql = <<<EOS
        SELECT
            inquiry_rooms.id				            id,
            inquiry_rooms_messages_relations.created_at created_at,
            inquiry_rooms.id                            url,
            inquiries.category                          category,
            inquiries.user_name                         name,
            brands.name                                 brand_name,
            brands_users_relations.no                   no,
            users.monipla_user_id                       monipla_user_id,
            inquiry_users.mail_address                  mail_address,
            cp_entry_actions.title				        cp_title,
            inquiry_sections_1.name                     inquiry_section_id_1,
            inquiry_sections_2.name                     inquiry_section_id_2,
            inquiry_sections_3.name                     inquiry_section_id_3,
            inquiry_messages.sender						sender,
            inquiry_messages.content                    content,
            inquiries.user_agent						user_agent,
            inquiries.referer							referer,
            inquiry_rooms.status                        status,
            inquiry_rooms.remarks                       remarks
        FROM inquiry_rooms
        INNER JOIN
            inquiry_rooms_messages_relations ON(
                inquiry_rooms_messages_relations.inquiry_room_id = inquiry_rooms.id
                AND inquiry_rooms_messages_relations.del_flg = 0
            )
        INNER JOIN
            inquiry_messages ON(
                inquiry_messages.id = inquiry_rooms_messages_relations.inquiry_message_id
                AND inquiry_messages.draft_flg = 0
                AND inquiry_messages.del_flg = 0
            )
        INNER JOIN
            inquiries ON(
                inquiries.id = inquiry_messages.inquiry_id
                AND inquiries.del_flg = 0
            )
        INNER JOIN
            inquiry_users ON(
                inquiry_users.id = inquiries.inquiry_user_id
                AND inquiry_users.del_flg = 0
            )
        INNER JOIN
            inquiry_brands ON(
                inquiry_brands.id = inquiry_rooms.inquiry_brand_id
                AND inquiry_brands.del_flg = 0
            )
        INNER JOIN
            brands ON(
                brands.id = inquiry_brands.brand_id
                AND brands.del_flg = 0
            )
        LEFT OUTER JOIN
            inquiry_sections inquiry_sections_1 ON(
                inquiry_sections_1.id = inquiry_rooms.inquiry_section_id_1
                AND inquiry_sections_1.del_flg = 0
            )
        LEFT OUTER JOIN
            inquiry_sections inquiry_sections_2 ON(
                inquiry_sections_2.id = inquiry_rooms.inquiry_section_id_2
                AND inquiry_sections_2.del_flg = 0
            )
        LEFT OUTER JOIN
            inquiry_sections inquiry_sections_3 ON(
                inquiry_sections_3.id = inquiry_rooms.inquiry_section_id_3
                AND inquiry_sections_3.del_flg = 0
            )
        LEFT OUTER JOIN
            brands_users_relations ON(
                brands_users_relations.user_id = inquiry_users.user_id
                AND brands_users_relations.brand_id = inquiry_brands.brand_id
                AND brands_users_relations.del_flg = 0
            )
        LEFT OUTER JOIN
            users ON(
                users.id = inquiry_users.user_id
                AND users.del_flg = 0
            )
        LEFT OUTER JOIN
            cp_action_groups ON(
                cp_action_groups.cp_id = inquiries.cp_id
                AND cp_action_groups.order_no = 1
                AND cp_action_groups.del_flg = 0
            )
        LEFT OUTER JOIN
            cp_actions ON(
                cp_actions.cp_action_group_id = cp_action_groups.id
                AND cp_actions.type = 0
                AND cp_actions.del_flg = 0
            )
        LEFT OUTER JOIN
            cp_entry_actions ON(
                cp_entry_actions.cp_action_id = cp_actions.id
                AND cp_entry_actions.del_flg = 0
            )
        WHERE
            inquiry_rooms.del_flg = 0
            AND inquiry_rooms.operator_type = {$operator_type}
            {$where}
        ORDER BY inquiry_rooms.id DESC, inquiry_rooms_messages_relations.created_at DESC
EOS;

        return $db->getBySQL($sql, array());
    }

    /**
     * @param $operator_type
     * @param $current_inquiry_room_id
     * @param array $params
     * @param int $inquiry_brand_id
     * @return array
     */
    public function getInquiryHistory($operator_type, $current_inquiry_room_id, $params = array(), $inquiry_brand_id = 0) {
        $clauses = $this->createClauses($params, true);
        $where = ' AND (' . implode(' OR ', $clauses) . ')';
        $where_admin = $inquiry_brand_id ? ' AND inquiry_rooms.inquiry_brand_id = ' . $inquiry_brand_id : '';

        $role = InquiryMessage::USER;
        $db = aafwDataBuilder::newBuilder();
        $sql = <<<EOS
         SELECT * FROM
        (
            SELECT * FROM
            (
                SELECT
                	inquiry_rooms.id					id,
                    inquiry_messages.content            content,
                    inquiry_messages.created_at         created_at
                FROM inquiry_rooms
                INNER JOIN
                	inquiry_rooms_messages_relations ON(
                		inquiry_rooms_messages_relations.inquiry_room_id = inquiry_rooms.id
                		AND inquiry_rooms_messages_relations.del_flg = 0
                	)
                INNER JOIN
                    inquiry_messages ON(
                        inquiry_messages.id = inquiry_rooms_messages_relations.inquiry_message_id
                        AND inquiry_messages.sender = {$role}
                        AND inquiry_messages.draft_flg = 0
                        AND inquiry_messages.del_flg = 0
                    )
                INNER JOIN
                    inquiries ON(
                        inquiries.id = inquiry_rooms.inquiry_id
                        AND inquiries.del_flg = 0
                    )
                INNER JOIN
                    inquiry_users ON(
                        inquiry_users.id = inquiries.inquiry_user_id
                        AND inquiry_users.del_flg = 0
                    )
                WHERE
                    inquiry_rooms.del_flg = 0
                    AND inquiry_rooms.id != {$current_inquiry_room_id}
                    AND inquiry_rooms.operator_type = {$this->escape($operator_type)}
                    {$where_admin}
                    {$where}
                ORDER BY inquiry_messages.created_at DESC
            ) D
            GROUP BY D.id
        ) E
        ORDER BY E.created_at DESC
EOS;

        return $db->getBySQL($sql, array());
    }

    /**
     * @param $inquiry_room_id
     * @return array
     */
    public function getInquiryUserDetail($inquiry_room_id) {
        $db = aafwDataBuilder::newBuilder();
        $sql = <<<EOS
        SELECT * FROM
        (
            SELECT
                inquiries.id                        id,
                inquiries.user_name                 user_name,
                inquiries.user_agent                user_agent,
                cp_entry_actions.title				cp_title,
                inquiry_users.user_id               user_id,
                brands_users_relations.no           no,
                users.monipla_user_id               monipla_user_id,
                users.profile_image_url             profile_image_url,
                inquiry_users.mail_address          mail_address
            FROM inquiry_rooms
            INNER JOIN
                inquiry_rooms_messages_relations ON(
                inquiry_rooms_messages_relations.inquiry_room_id = inquiry_rooms.id
                AND inquiry_rooms_messages_relations.del_flg = 0
            )
            INNER JOIN
                inquiry_messages ON(
                    inquiry_messages.id = inquiry_rooms_messages_relations.inquiry_message_id
                    AND inquiry_messages.del_flg = 0
                )
            INNER JOIN
                inquiries ON(
                    inquiries.id = inquiry_messages.inquiry_id
                    AND inquiries.del_flg = 0
                )
            INNER JOIN
                inquiry_users ON(
                    inquiry_users.id = inquiries.inquiry_user_id
                    AND inquiry_users.del_flg = 0
                )
            INNER JOIN
                inquiry_brands ON(
                    inquiry_brands.id = inquiry_rooms.inquiry_brand_id
                    AND inquiry_brands.del_flg = 0
                )
            LEFT OUTER JOIN
                users ON(
                    users.id = inquiry_users.user_id
                    AND users.del_flg = 0
                )
            LEFT OUTER JOIN
                brands_users_relations ON(
                    brands_users_relations.user_id = inquiry_users.user_id
                    AND brands_users_relations.brand_id = inquiry_brands.brand_id
                    AND brands_users_relations.del_flg = 0
                )
            LEFT OUTER JOIN
                cp_action_groups ON(
                    cp_action_groups.cp_id = inquiries.cp_id
                    AND cp_action_groups.del_flg = 0
                )
            LEFT OUTER JOIN
                cp_actions ON(
                    cp_actions.cp_action_group_id = cp_action_groups.id
                    AND cp_actions.del_flg = 0
                )
            LEFT OUTER JOIN
                cp_entry_actions ON(
                    cp_entry_actions.cp_action_id = cp_actions.id
                    AND cp_entry_actions.del_flg = 0
                )
            WHERE
                inquiry_rooms.del_flg = 0
                AND inquiry_rooms.id = {$this->escape($inquiry_room_id)}
        ) D
        GROUP BY D.id
EOS;

        $result = $db->getBySQL($sql, array());

        return (is_array($result)) ? $result[0] : $result;
    }

    /**
     * @param $inquiry_room_id
     * @param $sender
     * @return array
     */
    public function getInquiryMessageDraft($inquiry_room_id, $sender) {
        $db = aafwDataBuilder::newBuilder();
        $sql = <<<EOS
        SELECT
            inquiry_messages.*
        FROM
            inquiry_rooms_messages_relations
        INNER JOIN
            inquiry_messages ON(
                inquiry_messages.id = inquiry_rooms_messages_relations.inquiry_message_id
                AND inquiry_messages.draft_flg = 1
                AND inquiry_messages.sender = {$this->escape($sender)}
                AND inquiry_messages.del_flg = 0
            )
        WHERE
            inquiry_rooms_messages_relations.inquiry_room_id = {$this->escape($inquiry_room_id)}
EOS;

        return $db->getBySQL($sql, array());
    }

    /**
     * @param $inquiry_room_id
     * @param bool|false $user_flg
     * @return array
     */
    public function getInquiryMessages($inquiry_room_id, $user_flg = false) {
        $db = aafwDataBuilder::newBuilder();

        $sql = <<<EOS
        SELECT
            inquiry_messages.id                             id,
            inquiry_messages.inquiry_id                     inquiry_id,
            inquiry_messages.sender                         sender,
            inquiry_messages.content                        content,
            inquiry_messages.draft_flg                      draft_flg,
            inquiry_messages.del_flg                        del_flg,
            inquiry_messages.created_at                     created_at,
            inquiry_messages.updated_at                     updated_at,
            inquiry_rooms_messages_relations.id             inquiry_rooms_messages_relation_id,
            inquiry_rooms_messages_relations.forwarded_flg  forwarded_flg
        FROM
            inquiry_rooms_messages_relations
        INNER JOIN
            inquiry_messages ON(
                inquiry_messages.id = inquiry_rooms_messages_relations.inquiry_message_id
                AND inquiry_messages.draft_flg = 0
                AND inquiry_messages.del_flg = 0
            )
        WHERE
            inquiry_rooms_messages_relations.inquiry_room_id = {$this->escape($inquiry_room_id)}
        ORDER BY inquiry_rooms_messages_relations.id ASC
EOS;

        return $db->getBySQL($sql, array());
    }

    /**
     * @param $str
     * @return エスケープ後文字列
     */
    public function escape($str) {
        return $this->models[self::MODEL_TYPE_INQUIRIES]->escapeForSQL($str);
    }

    /**
     * @param $str
     * @param string $char
     * @return null|string
     */
    public function enclose($str, $char = '') {
        if ($str && strlen($str) > 0) {
            return $char . $this->escape($str) . strrev($char);
        }

        return null;
    }

    /**
     * @param $str
     * @param string $char
     * @param string $time
     * @return null|string
     */
    public function encloseDateTime($str, $char = '', $time = '00:00:00') {
        if ($str && strlen($str) > 0) {
            return $char . $this->escape($str) . ' ' . $time . strrev($char);
        }

        return null;
    }

    /**
     * @param $array
     * @param string $char
     * @return null|string
     */
    public function encloseArray($array, $char = '') {
        if (is_array($array)) {
            foreach ($array as $key => $val) {
                $array[$key] = $this->enclose($val, $char);
            }

            return '(' . implode(',', $array) . ')';
        } else if ($array) {
            $this->encloseArray(array($array), $char);
        }

        return null;
    }

    /**
     * @param $table_name
     * @param $column_name
     * @param $operator
     * @param $val
     * @return null|string
     */
    public function createClause($table_name, $column_name, $operator, $val) {
        if ($val && strlen($val) > 0) {
            return $table_name . '.' . $column_name . ' ' . $operator . ' ' . $val;
        }

        return null;
    }

    /**
     * @param $params
     * @return array
     */
    public function createClauses($params, $extend_flg = false) {
        $clauses = array();
        $clause_definitions = array(
            'inquiry_brands' => array(
                array('id', '=', $this->enclose($params['inquiry_brand_id'])),
            ),
            'inquiries' => array(
                array('category', '=', $this->enclose($params['category'])),
            ),
            'inquiry_rooms' => array(
                array('operator_name', '=', $this->enclose($params['operator_name'], '"')),
                array('status', 'IN', $this->encloseArray($params['status'])),
            ),
            'inquiry_messages' => array(
                array('created_at', '>=', $this->encloseDateTime($params['date_begin'], '"')),
                array('created_at', '<=', $this->encloseDateTime(($params['period_flg']) ? $params['date_end'] : $params['date_begin'], '"', '23:59:59')),
            ),
        );

        if ($extend_flg) {
            $inquiry_users = array(
                array('user_id', '=', $this->enclose($params['user_id'], ''))
            );
            if ($params['user_id'] == 0) {
                $inquiry_users[] = array('mail_address', '=', $this->enclose($params['mail_address'], '"'));
            }

            $clause_definitions = array_merge($clause_definitions, array(
                'inquiry_users' => $inquiry_users
            ));
        }

        if (is_array($params)) {
            foreach ($clause_definitions as $table_name => $clause_elements) {
                foreach ($clause_elements as $clause_element) {
                    if ($clause = $this->createClause($table_name, $clause_element[0], $clause_element[1], $clause_element[2])) {
                        $clauses[] = $clause;
                    }
                }
            }

            $clause_definitions = array(
                'brands' => array('name'),
                'inquiries' => array('user_name'),
                'inquiry_rooms' => array('remarks', 'operator_name'),
                'inquiry_messages' => array('content'),
            );

            if ($extend_flg) {
                $clause_definitions = array_merge($clause_definitions, array(
                    'inquiry_users' => array('mail_address'),
                ));
            }

            $keyword_clauses = $this->createKeywordClauses($params['keywords'], $clause_definitions);
            if ($keyword_clauses) {
                $clauses[] = '(' . implode(' OR ', $keyword_clauses) . ')';
            }
        }

        return $clauses;
    }

    /**
     * @param $keywords
     * @param $definitions
     * @return array
     */
    public function createKeywordClauses($keywords, $definitions) {
        $keyword_clauses = array();
        if ($keywords && strlen($keywords) > 0) {
            $keywords = preg_split('/[\s]+/', mb_convert_kana($keywords, 's', 'UTF-8'), -1, PREG_SPLIT_NO_EMPTY);
            foreach ($keywords as $keyword) {
                foreach ($definitions as $table_name => $column_names) {
                    foreach ($column_names as $column_name) {
                        if ($clause = $this->createClause($table_name, $column_name, 'LIKE', $this->enclose($keyword, '"%'))) {
                            $keyword_clauses[] = $clause;
                        }
                    }
                }
            }
        }

        return $keyword_clauses;
    }
}
