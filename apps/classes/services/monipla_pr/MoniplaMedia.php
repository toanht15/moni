<?php

AAFW::import('jp.aainc.classes.services.monipla_pr.IMoniplaPR');
AAFW::import('jp.aainc.classes.services.monipla.OldMoniplaUserOptinService');

class MoniplaMedia implements IMoniplaPR {

    public function isMine(Cp $cp, CpUser $cp_user) {
        return true;
    }

    public function parseTemplate(Cp $cp, CpUser $cp_user) {
        $old_monipla_user_optin_service = new OldMoniplaUserOptinService();

        $parser = new PHPParser();
        return $parser->parseTemplate(
            'UserMessageThreadMoniplaMedia.php',
            array(
                'cp' => $cp,
                'cp_user' => $cp_user,
                'user_media_optin' => $old_monipla_user_optin_service->get_or_create($cp_user->user_id, 1),
            )
        );
    }
}