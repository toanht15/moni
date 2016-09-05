<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');
AAFW::import('jp.aainc.classes.entities.CpMessageDeliveryReservation');

class save_message_option extends BrandcoPOSTActionBase {


    public $NeedOption = array();
    public $NeedAdminLogin = true;
    public $CsrfProtect = true;

    protected $ContainerName = 'setting_message_option';
    protected $Form = array(
        'package' => 'admin-cp',
        'action' => 'setting_message_option/{cp_action_id}',
    );

    protected $ValidatorDefinition = array(

        'delivery_date' => array(
            'type' => 'str',
            'length' => 10,
        ),
        'delivery_time_hh' => array(
            'type' => 'num',
            'range' => array(
                '<' => 24,
                '>=' => 0,
            )
        ),
        'delivery_time_mm' => array(
            'type' => 'num',
            'range' => array(
                '<' => 60,
                '>=' => 0,
            )
        ),
        'reservation_id' => array(),
        'cp_action_id' => array(),
    );

    /** @var CPMessageReservationMailOptionValidator $validator */
    private $reservation_validator;

    public function doThisFirst() {

        if ($this->POST['status'] == CpMessageDeliveryReservation::STATUS_FIX) {

            $this->ValidatorDefinition['reservation_id']['required'] = true;
            $this->ValidatorDefinition['send_mail_flg']['required'] = true;
            $this->ValidatorDefinition['delivery_type']['required'] = true;

            if ($this->POST['delivery_type'] == CpMessageDeliveryReservation::DELIVERY_TYPE_RESERVATION) {

                $this->ValidatorDefinition['delivery_date']['required'] = true;
                $this->ValidatorDefinition['delivery_time_hh']['required'] = true;
                $this->ValidatorDefinition['delivery_time_mm']['required'] = true;
            }
        }
    }

    public function validate() {

        $this->Data['brand'] = $this->getBrand();

        $this->Data['delivery_time'] = $this->POST['delivery_date'] . ' ' . $this->POST['delivery_time_hh'] . ':' . $this->POST['delivery_time_mm'] . ':00';

        $this->reservation_validator = new CPMessageReservationMailOptionValidator(
            $this->Data['brand']->id,
            $this->POST['reservation_id'],
            $this->POST['delivery_type'],
            $this->Data['delivery_time'],
            $this->POST['send_mail_flg'],
            $this->POST['status']
        );

        $this->reservation_validator->validate();
        if (!$this->reservation_validator->isValid()) {

            // アクションのValidatorに詰め替え
            $errors = $this->reservation_validator->getErrors();

            $keys = ["cp_id", "reservation_id"];
            foreach ($keys as $key) {
                if (array_key_exists($key, $errors)) {
                    return 404;
                }
            }

            foreach ($errors as $key => $value) {
                $this->Validator->setError($key, $value);
            }
            return false;
        }
        return true;
    }

    function doAction() {

        /** @var CpMessageDeliveryService $delivery_service */
        $delivery_service = $this->createService('CpMessageDeliveryService');

        $cleaned_data = $this->reservation_validator->getCleanedData();

        $reservation = $cleaned_data["reservation"];
        $cp_action = $cleaned_data["cp_action"];

        $reservation->delivery_type = $cleaned_data["delivery_type"];

        if ($cleaned_data["delivery_type"] == CpMessageDeliveryReservation::DELIVERY_TYPE_IMMEDIATELY) {
            $reservation->delivery_date = "0000-00-00 00:00:00";
        } else {
            $reservation->delivery_date = $cleaned_data["delivery_time"];
        }

        $reservation->send_mail_flg = $cleaned_data["send_mail_flg"];

        if ($cleaned_data["status"] == CpMessageDeliveryReservation::STATUS_FIX) {
            $reservation->status = CpMessageDeliveryReservation::STATUS_FIX;

        } else {
            $reservation->status = CpMessageDeliveryReservation::STATUS_DRAFT;
        }

        $delivery_service->updateCpMessageDeliveryReservation($reservation);

        $this->Data['saved'] = 1;

        if ($cleaned_data["status"] == CpMessageDeliveryReservation::STATUS_FIX) {
            $query['mid'] = 'reservation-fix';
        } else {
            $query['mid'] = 'reservation-draft';
        }

        $return = 'redirect: ' . Util::rewriteUrl('admin-cp', 'setting_message_option', array($cp_action->id), $query);

        return $return;
    }
}
