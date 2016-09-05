<?php
/**
 *  @generated
 */
class Thrift_ExchangeAmazonResult {
  static $_TSPEC;

  public $result = null;
  public $exchangeAmazons = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'result',
          'type' => TType::STRUCT,
          'class' => 'Thrift_APIResult',
          ),
        2 => array(
          'var' => 'exchangeAmazons',
          'type' => TType::LST,
          'etype' => TType::STRUCT,
          'elem' => array(
            'type' => TType::STRUCT,
            'class' => 'Thrift_ExchangeAmazon',
            ),
          ),
        );
    }
    if (is_array($vals)) {
      if (isset($vals['result'])) {
        $this->result = $vals['result'];
      }
      if (isset($vals['exchangeAmazons'])) {
        $this->exchangeAmazons = $vals['exchangeAmazons'];
      }
    }
  }

  public function getName() {
    return 'Thrift_ExchangeAmazonResult';
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
            $this->exchangeAmazons = array();
            $_size111 = 0;
            $_etype114 = 0;
            $xfer += $input->readListBegin($_etype114, $_size111);
            for ($_i115 = 0; $_i115 < $_size111; ++$_i115)
            {
              $elem116 = null;
              $elem116 = new Thrift_ExchangeAmazon();
              $xfer += $elem116->read($input);
              $this->exchangeAmazons []= $elem116;
            }
            $xfer += $input->readListEnd();
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
    $xfer += $output->writeStructBegin('Thrift_ExchangeAmazonResult');
    if ($this->result !== null) {
      if (!is_object($this->result)) {
        throw new TProtocolException('Bad type in structure.', TProtocolException::INVALID_DATA);
      }
      $xfer += $output->writeFieldBegin('result', TType::STRUCT, 1);
      $xfer += $this->result->write($output);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->exchangeAmazons !== null) {
      if (!is_array($this->exchangeAmazons)) {
        throw new TProtocolException('Bad type in structure.', TProtocolException::INVALID_DATA);
      }
      $xfer += $output->writeFieldBegin('exchangeAmazons', TType::LST, 2);
      {
        $output->writeListBegin(TType::STRUCT, count($this->exchangeAmazons));
        {
          foreach ($this->exchangeAmazons as $iter117)
          {
            $xfer += $iter117->write($output);
          }
        }
        $output->writeListEnd();
      }
      $xfer += $output->writeFieldEnd();
    }
    $xfer += $output->writeFieldStop();
    $xfer += $output->writeStructEnd();
    return $xfer;
  }

}


?>
