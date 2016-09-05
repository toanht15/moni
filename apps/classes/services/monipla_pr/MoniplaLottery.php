<?php

AAFW::import('jp.aainc.classes.services.monipla_pr.IMoniplaPR');

class MoniplaLottery implements IMoniplaPR {

    public function isMine(Cp $cp, CpUser $cp_user) {
        return config('MoniplaLottery.isBeingHeld');
    }

    public function parseTemplate(Cp $cp, CpUser $cp_user) {

        $cp_flow_service = new CpFlowService();

        $parser = new PHPParser();
        return $parser->parseTemplate(
            'UserMessageThreadMoniplaLottery.php',
            array(
                'cp' => $cp,
                'cp_user' => $cp_user,
                'cp_title' => $cp_flow_service->getCpTitleByCpId($cp->id),
            )
        );
    }
}