<?php

AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoBatchBase');

class CheckSSLCertificateExpirationDate extends BrandcoBatchBase {

    const BC_DEV_MAIL = 'bc-dev@aainc.co.jp';
    const INFRA_MAIL = 'system-infra@aainc.co.jp';

    const DEFAULT_PORT = 443;

    const MAX_EXPIRATION_DAY = 60;

    private $domains;

    function __construct($argv = null) {
        parent::__construct($argv);
        $settings = aafwApplicationConfig::getInstance();
        $this->domains = $settings->query('DomainMapping');
    }

    function executeProcess() {
        $expiryDomains = array();
        foreach($this->domains as $domain) {
            $expiryDate = $this->getSSLCertificateExpirationDate($domain, self::DEFAULT_PORT);
            if($expiryDate && ($expiryDate - time()) <= (self::MAX_EXPIRATION_DAY * 24 * 60 *60)) {
                $expiryDomains[]=array(
                    'DOMAIN' => $domain,
                    'EXPIRE_DATE' => date('Y-m-d',$expiryDate),
                );
            }
        }
        $this->sendMail($expiryDomains);
    }

    private function getSSLCertificateExpirationDate($domain, $port) {
        $cmd = "echo | openssl s_client -connect ".$domain.":".$port." 2>/dev/null | openssl x509 -noout -dates";
        $output = shell_exec($cmd);
        if (preg_match("/notAfter=/", $output)) {
            return strtotime(explode('notAfter=',$output)[1]);
        }
        return NUll;
    }

    private function sendMail($expiryDomains) {
        if (!count($expiryDomains)) return;
        $mailParams = array(
            'DATA' => $expiryDomains,
        );
        $mail = new MailManager();
        $mail->loadMailContent('alert_domain_ssl');
        $mail->sendNow(self::BC_DEV_MAIL, $mailParams, self::INFRA_MAIL);
    }
}