<?php
/**
 *  @generated
 */
class Thrift_AddCouponCode {
  static $_TSPEC;

  public $executionType = null;
  public $code = null;
  public $point = null;
  public $limitDateTime = null;
  public $assigned = 0;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'executionType',
          'type' => TType::I16,
          ),
        2 => array(
          'var' => 'code',
          'type' => TType::STRING,
          ),
        3 => array(
          'var' => 'point',
          'type' => TType::I32,
          ),
        4 => array(
          'var' => 'limitDateTime',
          'type' => TType::STRING,
          ),
        5 => array(
          'var' => 'assigned',
          'type' => TType::I16,
          ),
        );
    }
    if (is_array($vals)) {
      if (isset($vals['executionType'])) {
        $this->executionType = $vals['executionType'];
      }
      if (isset($vals['code'])) {
        $this->code = $vals['code'];
      }
      if (isset($vals['point'])) {
        $this->point = $vals['point'];
      }
      if (isset($vals['limitDateTime'])) {
        $this->limitDateTime = $vals['limitDateTime'];
      }
      if (isset($vals['assigned'])) {
        $this->assigned = $vals['assigned'];
      }
    }
  }

  public function getName() {
    return 'Thrift_AddCouponCode';
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
          if ($ftype == TType::I16) {
            $xfer += $input->readI16($this->executionType);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 2:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->code);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 3:
          if ($ftype == TType::I32) {
            $xfer += $input->readI32($this->point);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 4:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->limitDateTime);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 5:
          if ($ftype == TType::I16) {
            $xfer += $input->readI16($this->assigned);
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
    $xfer += $output->writeStructBegin('Thrift_AddCouponCode');
    if ($this->executionType !== null) {
      $xfer += $output->writeFieldBegin('executionType', TType::I16, 1);
      $xfer += $output->writeI16($this->executionType);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->code !== null) {
      $xfer += $output->writeFieldBegin('code', TType::STRING, 2);
      $xfer += $output->writeString($this->code);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->point !== null) {
      $xfer += $output->writeFieldBegin('point', TType::I32, 3);
      $xfer += $output->writeI32($this->point);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->limitDateTime !== null) {
      $xfer += $output->writeFieldBegin('limitDateTime', TType::STRING, 4);
      $xfer += $output->writeString($this->limitDateTime);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->assigned !== null) {
      $xfer += $output->writeFieldBegin('assigned', TType::I16, 5);
      $xfer += $output->writeI16($this->assigned);
      $xfer += $output->writeFieldEnd();
    }
    $xfer += $output->writeFieldStop();
    $xfer += $output->writeStructEnd();
    return $xfer;
  }

}


?>
