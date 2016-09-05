<?php
AAFW::import('jp.aainc.aafw.text.aafwTemplateTag');
AAFW::import('jp.aainc.aafw.parsers.PHPParser');
AAFW::import('jp.aainc.aafw.text.aafwTemplateTag');

class TextTemplate {

    private $language;

    public function __construct($language = null) {
        if ($language) $this->language = $language;
        if (!$this->language) $this->language = aafwApplicationConfig::getInstance()->M17N['DefaultLanguage'];
    }

    public function loadContent($template_id, $replaceParams = null, $replace = true) {
        $file = AAFW_DIR . "/text_templates/{$this->getLanguage()}/" . $template_id . '.txt';
        if (is_file($file)) {
            $content = file_get_contents($file);
            if ($replace) {
                $tmplTag = new aafwTemplateTag($content, $replaceParams);
                return $tmplTag->evalTag();
            } else {
                return $content;
            }
        }
    }

    public function convertContent($content, $replaceParams = null) {
        if ($replaceParams !== null) {
            $tmplTag = new aafwTemplateTag($content, $replaceParams);
            return $tmplTag->evalTag();
        } else {
            return $content;
        }
    }

    public function loadContentFromPHPFile($template_id, $params) {
        $parser = new PHPParser();
        return $parser->out(array(
            '__view__' => "text_templates/{$this->getLanguage()}/" . $template_id . '.php',
            '__REQ__' => $params
        ));
    }

    public function getLanguage() {
        return $this->language;
    }
}
