<?php

trait SegmentProvisionTrait {

    /**
     * @return mixed
     */
    public function createEmptyObject() {
        return $this->segment_provisions->createEmptyObject();
    }

    /**
     * @param $segment_provision
     * @return mixed
     */
    public function saveSegmentProvision($segment_provision) {
        return $this->segment_provisions->save($segment_provision);
    }

    /**
     * @param $segment_provision
     * @return mixed
     */
    public function createTmpSegmentProvision($segment_provision) {
        $tmp_segment_provision = $this->segment_provisions->createEmptyObject();
        $tmp_segment_provision->type = $segment_provision['type'] ?: SegmentProvision::DEFAULT_SEGMENT_PROVISION;

        return $tmp_segment_provision;
    }

    /**
     * @param $segment_id
     * @return mixed
     */
    public function getRawSegmentProvisionsBySegmentId($segment_id) {
        $query = "SELECT * FROM segment_provisions WHERE segment_id = " . $this->escapeForSQL($segment_id) . " AND del_flg = 0 ORDER BY order_no";

        return $this->data_builder->getBySQL($query, array(array('__NOFETCH__')));
    }

    /**
     * @param $segment_id
     * @return mixed
     */
    public function getSegmentProvisionsBySegmentId($segment_id) {
        $filter = array(
            'conditions' => array(
                'segment_id' => $segment_id
            ),
            'order' => array(
                'name' => 'id'
            )
        );

        return $this->segment_provisions->find($filter);
    }

    /**
     * @param $segment_id
     * @param $type
     * @return mixed
     */
    public function getSegmentProvisionsBySegmentIdAndType($segment_id, $type) {
        $filter = array(
            'conditions' => array(
                'segment_id' => $segment_id,
                'type' => $type
            ),
            'order' => array(
                'name' => 'order_no'
            )
        );

        return $this->segment_provisions->find($filter);
    }

    /**
     * @param $segment_provision_id
     * @return mixed
     */
    public function getSegmentProvisionById($segment_provision_id) {
        return $this->segment_provisions->findOne($segment_provision_id);
    }

    /**
     * @param $segment_id
     * @param $target_sp
     */
    public function copySegmentProvision($segment_id, $target_sp) {
        $new_sp = $this->segment_provisions->createEmptyObject();

        $new_sp->segment_id = $segment_id;
        $new_sp->name = $target_sp->name;
        $new_sp->order_no = $target_sp->order_no;
        $new_sp->provision = $target_sp->provision;
        $new_sp->type = $target_sp->type;

        $this->segment_provisions->save($new_sp);
    }

    /**
     * @param $segment_id
     */
    public function deleteSegmentProvisionsBySegmentId($segment_id) {
        $segment_provisions = $this->getSegmentProvisionsBySegmentId($segment_id);

        foreach ($segment_provisions as $segment_provision) {
            $this->segment_provisions->delete($segment_provision);
        }
    }

    /**
     * テープルsegment_action_logsの$segment_provison_idsから、provisionを抽出
     * @param $segment_provison_ids
     *
     */
    public function findSegmentProvisonsByIds($segment_provison_ids) {

        $provisions = array();

        $segment_provison_id_array = json_decode($segment_provison_ids, true);

        foreach($segment_provison_id_array as $provision_id_array) {

            foreach($provision_id_array as $provision_id) {

                $provision = $this->getSegmentProvisionById($provision_id);

                $provisions[] = $provision;
            }
        }

        return $provisions;
    }
}
