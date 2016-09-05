<?php


AAFW::import('jp.aainc.lib.base.aafwObject');
AAFW::import('jp.aainc.classes.brandco.cp.interface.CpCreator');
AAFW::import('jp.aainc.classes.brandco.cp.trait.CpTrait');
AAFW::import('jp.aainc.classes.brandco.cp.trait.CpActionGroupTrait');
AAFW::import('jp.aainc.classes.brandco.cp.trait.CpNextActionTrait');
AAFW::import('jp.aainc.classes.brandco.cp.interface.CpActionManager');
AAFW::import('jp.aainc.classes.brandco.cp.CpEntryActionManager');
AAFW::import('jp.aainc.classes.brandco.cp.CpJoinFinishActionManager');


/**
 * Class PresentCpCreator
 * このクラスはConst以外使われていません
 * TODO リファクタリング
 *
 */
class CpPresentCreator extends aafwObject implements CpCreator {

    use CpTrait;
    use CpActionGroupTrait;
    use CpNextActionTrait;

    public static $shipping_address_type = array(
        self::SHIPPING_ADDRESS_ALL => '全員',
        self::SHIPPING_ADDRESS_ELECTED => '当選者のみ',
        self::SHIPPING_ADDRESS_NONE=>'なし'
    );

    private $logger;
    private $cp;

    public function __construct() {
        $this->cps = $this->getModel("Cps");
        $this->cp_action_groups = $this->getModel("CpActionGroups");
        $this->cp_next_actions = $this->getModel("CpNextActions");
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
    }

    /**
     * @param $brand_id
     * @param null $options
     * @return mixed
     */
    public function create($brand_id, $options = null) {

        try {

            $this->cps->begin();

            // create cp
            $this->cp = $this->createCp($brand_id, $options['announce']);

            // create first_action_group
            $first_group = $this->createCpActionGroup($this->cp->id, 1);

            $actions = array();
            $no = 1;
            $cp_action_manager = new CpEntryActionManager();
            list($actions[], ) = $cp_action_manager->createCpActions($first_group->id, CpAction::TYPE_ENTRY, CpAction::STATUS_DRAFT, $no++);

            if($options['shipping_address_present'] == self::SHIPPING_ADDRESS_ALL) {
                $cp_action_manager = new CpShippingAddressActionManager();
                list($actions[], ) = $cp_action_manager->createCpActions($first_group->id, CpAction::TYPE_SHIPPING_ADDRESS, CpAction::STATUS_DRAFT, $no++);
            }

            $cp_action_manager = new CpJoinFinishActionManager();
            list($actions[], ) = $cp_action_manager->createCpActions($first_group->id, CpAction::TYPE_JOIN_FINISH, CpAction::STATUS_DRAFT, $no++);

            $this->createCpNextActionByActions($actions);

            // create second_action_group
            $second_group = $this->createCpActionGroup($this->cp->id, 2);

            $actions = array();
            $no = 1;
            $cp_action_manager = new CpAnnounceActionManager();
            list($actions[], ) = $cp_action_manager->createCpActions($second_group->id, CpAction::TYPE_ANNOUNCE, CpAction::STATUS_DRAFT, $no++);

            if($options['shipping_address_present'] == self::SHIPPING_ADDRESS_ELECTED) {
                $cp_action_manager = new CpShippingAddressActionManager();
                list($actions[], ) = $cp_action_manager->createCpActions($second_group->id, CpAction::TYPE_SHIPPING_ADDRESS, CpAction::STATUS_DRAFT, $no++);
            }

            $this->createCpNextActionByActions($actions);

            $this->cps->commit();

        } catch (Exception $e) {
            $this->logger->error("PresentCpCreator#create error" . $e);
            $this->cps->rollback();

        } finally {
            $this->logger->debug("PresentCpCreator#create success");
        }

        return $this->cp;
    }

    /**
     * @return null
     */
    public function getCp() {
        return $this->cp;
    }
}