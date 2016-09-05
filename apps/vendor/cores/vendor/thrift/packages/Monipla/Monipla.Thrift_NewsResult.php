<?php
/**
 *  @generated
 */
class Thrift_NewsResult {
  static $_TSPEC;

  public $result = null;
  public $newsList = null;
  public $pages = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'result',
          'type' => TType::STRUCT,
          'class' => 'Thrift_APIResult',
          ),
        2 => array(
          'var' => 'newsList',
          'type' => TType::LST,
          'etype' => TType::STRUCT,
          'elem' => array(
            'type' => TType::STRUCT,
            'class' => 'Thrift_News',
            ),
          ),
        3 => array(
          'var' => 'pages',
          'type' => TType::STRUCT,
          'class' => 'Thrift_Pages',
          ),
        );
    }
    if (is_array($vals)) {
      if (isset($vals['result'])) {
        $this->result = $vals['result'];
      }
      if (isset($vals['newsList'])) {
        $this->newsList = $vals['newsList'];
      }
      if (isset($vals['pages'])) {
        $this->pages = $vals['pages'];
      }
    }
  }

  public function getName() {
    return 'Thrift_NewsResult';
  }

  public function read($input)
  {
    $xfer = 0;
    $fname = null;
    $ftype = 0;
    $fid = 0;
    $xfer += $input->readStructBegin($fname);
    while (true)
    {
      $xfer += $input->readFieldBegin($fname, $ftype, $fid);
      if ($ftype == TType::STOP) {
        break;
      }
      switch ($fid)
      {
        case 1:
          if ($ftype == TType::STRUCT) {
            $this->result = new Thrift_APIResult();
            $xfer += $this->result->read($input);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 2:
          if ($ftype == TType::LST) {
            $this->newsList = array();
            $_size160 = 0;
            $_etype163 = 0;
            $xfer += $input->readListBegin($_etype163, $_size160);
            for ($_i164 = 0; $_i164 < $_size160; ++$_i164)
            {
              $elem165 = null;
              $elem165 = new Thrift_News();
              $xfer += $elem165->read($input);
              $this->newsList []= $elem165;
            }
            $xfer += $input->readListEnd();
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 3:
          if ($ftype == TType::STRUCT) {
            $this->pages = new Thrift_Pages();
            $xfer += $this->pages->read($input);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        default:
          $xfer += $input->skip($ftype);
          break;
      }
      $xfer += $input->readFieldEnd();
    }
    $xfer += $input->readStructEnd();
    return $xfer;
  }

  public function write($output) {
    $xfer = 0;
    $xfer += $output->writeStructBegin('Thrift_NewsResult');
    if ($this->result !== null) {
      if (!is_object($this->result)) {
        throw new TProtocolException('Bad type in structure.', TProtocolException::INVALID_DATA);
      }
      $xfer += $output->writeFieldBegin('result', TType::STRUCT, 1);
      $xfer += $this->result->write($output);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->newsList !== null) {
      if (!is_array($this->newsList)) {
        throw new TProtocolException('Bad type in structure.', TProtocolException::INVALID_DATA);
      }
      $xfer += $output->writeFieldBegin('newsList', TType::LST, 2);
      {
        $output->writeListBegin(TType::STRUCT, count($this->newsList));
        {
          foreach ($this->newsList as $iter166)
          {
            $xfer += $iter166->write($output);
          }
        }
        $output->writeListEnd();
      }
      $xfer += $output->writeFieldEnd();
    }
    if ($this->pages !== null) {
      if (!is_object($this->pages)) {
        throw new TProtocolException('Bad type in structure.', TProtocolException::INVALID_DATA);
      }
      $xfer += $output->writeFieldBegin('pages', TType::STRUCT, 3);
      $xfer += $this->pages->write($output);
      $xfer += $output->writeFieldEnd();
    }
    $xfer += $output->writeFieldStop();
    $xfer += $output->writeStructEnd();
    return $xfer;
  }

}


?>
