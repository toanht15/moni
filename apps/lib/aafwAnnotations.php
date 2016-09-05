<?php

/**
 * Class Annotations
 * @package curely\core
 */
class aafwAnnotations
{
    private $document = null;
    private $annotations = null;
    private $description = null;

    /**
     * パラメータ
     * @param $doc
     * @throws \Exception
     */
    public function __construct($doc)
    {
        if (!trim($doc)) throw new \Exception('ドキュメントが指定されていません');
        $this->document = $doc;
        $this->annotations = array();
        $this->analyze();
    }

    /**
     * アノテーションを取得する
     * @param アノテーションのキー
     * @return array|null アノテーション
     */
    public function getAnnotations($key = null)
    {

        if ($key == null) return $this->annotations;
        elseif (isset($this->annotations[$key])) return $this->annotations[$key];
        return null;
    }

    /**
     * 概要を取得する
     * @return 概要
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * ドキュメントを取得する
     * @return ドキュメント
     */
    public function getDocument()
    {
        return $this->document;
    }

    /**
     * 指定されたDocument文字列を解析する
     */
    public function analyze()
    {
        $buf = '';
        $lines = explode("\n", $this->document);
        for ($i = 0; $i < count($lines); $i++) {
            $line = preg_replace('#^\s*/?\*+\s*#', '', $lines[$i]);
            if (!trim($line)) continue;
            if (preg_match('#^@(\S+)\s+(.+)#', $line, $matches)) {
                if (!$this->annotations) {
                    $this->description = $buf;
                    $this->annotations = array();
                }
                list($all, $label, $value) = $matches;
                $label = strtolower($label);
                if (!isset($this->annotations[$label])) $this->annotations[$label] = array();
                if ($label == 'inject') {
                    $value = array_map(function ($val) {
                        return trim($val);
                    }, explode(',', $value));
                    $value = array_filter($value, function ($val) {
                        return trim($val) != '';
                    });
                } elseif ($label == 'param') {
                    $elements = preg_split('# +#', $value);
                    list($class, $default, $name) = null;
                    if (count($elements) == 2) {
                        list($class, $name) = $elements;
                    } elseif (count($elements) == 3) {
                        list($class, $default, $name) = $elements;
                    } else {
                        list($name) = $elements;
                    }
                    $value = array('class' => $class, 'default' => $default, 'name' => $name);
                }
                $this->annotations[$label][] = $value;
            } else {
                $buf .= $line;
            }
        }
    }
}