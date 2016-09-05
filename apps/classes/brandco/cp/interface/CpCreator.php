<?php

interface CpCreator {

    const SHIPPING_ADDRESS_ALL = 'all';
    const SHIPPING_ADDRESS_ELECTED = 'elected';
    const SHIPPING_ADDRESS_NONE = 'none';

    const ANNOUNCE_SELECTION = '0';
    const ANNOUNCE_FIRST = '1';
    const ANNOUNCE_LOTTERY = '2';
    const ANNOUNCE_DELIVERY = '3';
    const ANNOUNCE_NON_INCENTIVE = '4';

    /**
     * @param $brand_id
     * @param null $options
     * @return mixed
     */
    public function create($brand_id, $options = null);
}