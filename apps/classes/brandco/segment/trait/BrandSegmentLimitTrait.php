<?php
trait BrandSegmentLimitTrait {

    /**
     * @param $brand_id
     * @param $segment_type
     * @param $limit
     */
    public function updateBrandSegmentLimit($brand_id, $segment_type, $limit) {
        $brand_segment_limit = $this->getBrandSegmentLimit($segment_type, $brand_id);

        if (!$brand_segment_limit) {
            $brand_segment_limit = $this->brand_segment_limits->createEmptyObject();
            $brand_segment_limit->brand_id = $brand_id;
            $brand_segment_limit->segment_type = $segment_type;
        }

        $brand_segment_limit->segment_limit = $limit;

        $this->brand_segment_limits->save($brand_segment_limit);
    }

    /**
     * @param $segment_type
     * @param $brand_id
     * @return mixed
     */
    public function getBrandSegmentLimit($segment_type, $brand_id) {
        $filter = array(
            'brand_id' => $brand_id,
            'segment_type' => $segment_type
        );

        return $this->brand_segment_limits->findOne($filter);
    }

    /**
     * @param $segment_type
     * @param $brand_id
     * @return int
     */
    public function getSegmentLimit($segment_type, $brand_id) {
        $brand_segment_limit = $this->getBrandSegmentLimit($segment_type, $brand_id);

        if ($brand_segment_limit) {
            return $brand_segment_limit->segment_limit;
        }
        return BrandSegmentLimit::$segment_default_limit[$segment_type];
    }
}