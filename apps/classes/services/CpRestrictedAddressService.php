<?php
AAFW::import('jp.aainc.lib.base.aafwServiceBase');

class CpRestrictedAddressService extends aafwServiceBase {

    private $cp_restricted_addresses;

    public function __construct() {
        $this->cp_restricted_addresses = $this->getModel('CpRestrictedAddresses');
    }

    public function getCpRestrictedAddressesByCpId($cp_id) {
        $filter = array(
            'cp_id' => $cp_id
        );

        return $this->cp_restricted_addresses->find($filter);
    }

    public function getCpRestrictedAddressIds($cp_id) {
        $restricted_address_ids = array();
        $restricted_addresses = $this->getCpRestrictedAddressesByCpId($cp_id);

        foreach ($restricted_addresses as $restricted_address) {
            $restricted_address_ids[] = $restricted_address->pref_id;
        }

        return $restricted_address_ids;
    }

    public function getCpRestrictedAddressesString($cp_id) {
        $addresses = array();
        $restricted_addresses = $this->getCpRestrictedAddressesByCpId($cp_id);

        foreach ($restricted_addresses as $restricted_address) {
            $cur_pref = $restricted_address->getPrefecture();
            $addresses[] = $cur_pref->name;
        }

        return implode('、', $addresses);
    }
}