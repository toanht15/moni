<?php

trait SegmentProvisionsUsersRelationTrait {

    public function updateSegmentProvisionsUsersRelations($segmenting_users, $segment_provision_id, $created_date) {
        $index = 0;
        $insert_query = "/* SegmentProvisionsUsersRelationTrait updateSegmentProvisionsUsersRelations */
                    INSERT INTO segment_provisions_users_relations (segment_provision_id, brands_users_relation_id, created_date, created_at) VALUES ";

        foreach ($segmenting_users as $segmenting_user) {
            if (++$index === 1) {
                $query = $insert_query;
            }

            $query .= "(" . $this->escapeForSQL($segment_provision_id)
                    . ", " . $this->escapeForSQL($segmenting_user['brands_users_relations_id'])
                    . ", " . $this->escapeForSQL($created_date)
                    . ", NOW()),";

            if ($index >= self::SQL_EXECUTE_LIMIT) {
                $index = 0;
                $query = trim($query, ",");
                $result = $this->data_builder->executeUpdate($query);

                if (!$result) {
                    throw new aafwException('SegmentingUserData createTmpSegmentingUsers Failed: ' . $result);
                }
            }
        }

        if ($index !== 0) {
            $query = trim($query, ",");
            $result = $this->data_builder->executeUpdate($query);

            if (!$result) {
                throw new aafwException('SegmentingUserData createTmpSegmentingUsers Failed: ' . $result);
            }
        }
    }

    public function calculateSegmentProvisionUserCount($provision_ids, $created_dates = array()) {

        if($provision_ids) {

            $condition = array(
                'provision_ids' => $provision_ids
            );

            if(count($created_dates) > 0) {
                $condition['SEARCH_BY_CREATED_DATE'] = "__ON__";
                $condition['created_dates'] = $created_dates;
            }

            $result =  $this->data_builder->getSegmentProvisonUserCount($condition);

            return $result[0]['cnt'];
        }

        return 0;
    }
}