<?php
require_once 'text/aafwTemplateTag.php';
require_once 'parsers/PHPParser.php';
require_once 'base/aafwException.php';
require_once 'mail/aafwMail.php';
require_once 'mail/aafwSMTP.php';

class MailManager {

    private static $properties = array('Subject', 'FromAddress', 'BodyPlain', 'BodyHTML', 'ToAddress', 'CcAddress', 'BccAddress', 'BccSend', 'Envelope', 'Charset', 'RealCharset', 'ReplaceParams', 'Language');

    public $FromAddress = '';
    public $Subject = '';
    public $BodyPlain = '';
    public $BodyHTML = '';
    public $ToAddress = '';
    public $CcAddress = '';
    public $BccAddress = '';
    public $BccSend = true;
    public $Envelope = '';
    public $Charset = '';
    public $RealCharset = '';
    public $ReplaceParams = '';
    public $Language = '';

    /** @var aafwServiceFactory $service_factory */
    protected $service_factory;
    /** @var MailQueueService $mail_queue_service */
    protected $mail_queue_service;

    public function __construct($mailParams = array(), $defaultSet = true) {
        $this->service_factory = new aafwServiceFactory();
        $this->mail_queue_service = $this->service_factory->create('MailQueueService');

        $this->init($mailParams);

        if ($defaultSet) {
            //デフォルトセット
            $settings = aafwApplicationConfig::getInstance();
            if (!$this->FromAddress && $settings->Mail['Default']['FromAddress']) $this->FromAddress = $settings->Mail['Default']['FromAddress'];
            if (!$this->BccAddress && $settings->Mail['Default']['BccAddress'] && $this->BccSend) $this->BccAddress = $settings->Mail['Default']['BccAddress'];
            if (!$this->Envelope && $settings->Mail['Default']['Envelope']) $this->Envelope = $settings->Mail['Default']['Envelope'];
            if (!$this->Language && $settings->M17N['DefaultLanguage']) $this->Language = $settings->M17N['DefaultLanguage'];
            $this->loadCharSet($settings);
        }
    }

    private function init($mailParams) {
        if($mailParams['FromAddress'] && Util::isInvalidBrandName($mailParams['FromAddress'])){
            $mailParams['FromAddress'] = preg_replace('/\s+/','　',$mailParams['FromAddress']);
        }

        $properties = self::$properties;
        foreach ($properties as $property) {
            if (array_key_exists($property, $mailParams)) {
                $this->$property = $mailParams[$property];
            }
        }
    }

    public function restoreFromQueue($mailQueue) {
        $this->FromAddress = '';
        $this->Subject = '';
        $this->BodyPlain = '';
        $this->BodyHTML = '';
        $this->ToAddress = '';
        $this->CcAddress = '';
        $this->BccAddress = '';
        $this->Envelope = '';
        $this->init($mailQueue);
    }

    public function sendNow($ToAddress = null, $replaceParams = null, $CcAddress = null, $BccAddress = null) {
        if ($ToAddress) $this->ToAddress = $ToAddress;
        if ($CcAddress) $this->CcAddress = $CcAddress;
        if ($BccAddress) $this->BccAddress = $BccAddress;
        
        if ($this->validate()) $this->send($replaceParams);
        else                    throw new aafwException ("can't send mail");
    }

    public function getReplaceTemplate($params) {
        $content = trim($this->BodyHTML ? $this->BodyHTML : $this->BodyPlain);
        $subject = trim($this->Subject);
        if (!$content) throw new Exception("can't send mail with no body");
        if ($params) {
            $tmpl = new aafwTemplateTag ($content, $params);
            $content = $tmpl->evalTag();
            $tmpl = new aafwTemplateTag ($subject, $params);
            $subject = $tmpl->evalTag();
        }
        $content = str_replace(array("\r\n", "\r", "\n"), "\n", $content);
        return array('subject' => $subject, 'content' => $content);
    }

    private function validate() {
        if (!$this->Charset)  return false;
        if (!$this->ToAddress) return false;
        if (!$this->FromAddress) return false;
        if (!$this->BodyPlain && !$this->BodyHTML) return false;
        return true;
    }

    private function send($replaceParams = null) {
        if (!$this->BodyPlain) throw new Exception('not set BodyPlain');

        $mail = new aafwMail($this->Subject,
            ($this->BodyHTML ? $this->BodyHTML : $this->BodyPlain),
            ($this->BodyHTML ? true : false),
            $this->Charset,
            $this->RealCharset);
        $mail->setFrom($this->FromAddress);
        if ($this->Envelope) $mail->setEnvelope($this->Envelope);
        if ($this->BodyHTML) $mail->setAltText($this->BodyPlain);

        $mail->send($this->ToAddress, $replaceParams, $this->CcAddress, $this->BccAddress);
    }

    public function sendLater($ToAddress = null, $replaceParams = null, $CcAddress = null, $BccAddress = null, $sendSchedule = null) {
        if ($ToAddress) $this->ToAddress = $ToAddress;
        if ($CcAddress) $this->CcAddress = $CcAddress;
        if ($BccAddress) $this->BccAddress = $BccAddress;

        if (is_array($replaceParams)) {
            $tmpl = new aafwTemplateTag($this->BodyPlain, $replaceParams);
            $this->BodyPlain = $tmpl->evalTag();
            $tmpl = new aafwTemplateTag($this->BodyHTML, $replaceParams);
            $this->BodyHTML = $tmpl->evalTag();
            $tmpl = new aafwTemplateTag($this->Subject, $replaceParams);
            $this->Subject = $tmpl->evalTag();
        }

        if (!$this->validate()) throw new aafwException ("can't send mail");

        $mail_queue_store = $this->mail_queue_service->getMailQueueStore();
        $mail_queue = $mail_queue_store->createEmptyObject();
        $mail_queue->send_schedule = $sendSchedule ? $sendSchedule : '1970-01-01 00:00:00';
        $mail_queue->charset = $this->Charset;
        $mail_queue->real_charset = $this->RealCharset;
        $mail_queue->to_address = $this->ToAddress;
        $mail_queue->cc_address = $this->CcAddress;
        $mail_queue->bcc_address = $this->BccAddress;
        $mail_queue->subject = $this->Subject;
        $mail_queue->body_plain = $this->BodyPlain;
        $mail_queue->body_html = $this->BodyHTML;
        $mail_queue->from_address = $this->FromAddress;
        $mail_queue->envelope = $this->Envelope;
        $mail_queue_store->save($mail_queue);
    }

    public function loadSubject($template_id) {
        $file = AAFW_DIR . "/mail_templates/{$this->Language}/" . $template_id . '_subject.txt';
        if (is_file($file)) {
            $this->Subject = file_get_contents($file);
        }
    }

    public function loadBodyPlain($template_id) {
        $file = AAFW_DIR . "/mail_templates/{$this->Language}/" . $template_id . '_body_plain.txt';
        if (is_file($file)) {
            $this->BodyPlain = file_get_contents($file);
        }
    }

    public function loadBodyHTML($template_id) {
        $file = AAFW_DIR . "/mail_templates/{$this->Language}/" . $template_id . '_body_html.txt';
        if (is_file($file)) {
            $this->BodyHTML = file_get_contents($file);
        }
    }

    public function loadMailContent($template_id) {
        $this->loadSubject($template_id);
        $this->loadBodyPlain($template_id);
        $this->loadBodyHTML($template_id);
    }

    public function loadBodyPlainFromPHPFile($template_id, $params) {
        $parser = new PHPParser();
        $this->BodyPlain = $parser->out(array(
            '__view__' => "mail_templates/{$this->Language}/" . $template_id . '.php',
            '__REQ__' => $params
        ));
    }

    public function loadBodyHTMLFromPHPFile($template_id, $params) {
        $parser = new PHPParser();
        $this->BodyHTML = $parser->out(array(
            '__view__' => "mail_templates/{$this->Language}/" . $template_id . '.php',
            '__REQ__' => $params
        ));
    }

    public function loadCharset($settings) {
        if (!$this->Charset) $this->Charset = $settings->M17N['Mail'][$this->Language]['Charset'];
        if (!$this->RealCharset) $this->RealCharset = $settings->M17N['Mail'][$this->Language]['RealCharset'];
    }
}
