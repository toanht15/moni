<?php
AAFW::import ( 'jp.aainc.aafw.base.aafwObject' );
abstract class aafwParserBase extends aafwObject {
  abstract function getContentType();
  abstract function in($data);
  abstract function out($data);
}
