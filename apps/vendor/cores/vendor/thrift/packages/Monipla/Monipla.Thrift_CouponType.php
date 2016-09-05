<?php
/**
 *  @generated
 */
class Thrift_CouponType {
  static $_TSPEC;

  public $id = null;
  public $name = null;
  public $value = null;
  public $rate = null;
  public $noLimit = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'id',
          'type' => TType::I16,
          ),
        2 => array(
          'var' => 'name',
          'type' => TType::STRING,
          ),
        3 => array(
          'var' => 'value',
          'type' => TType::I32,
          ),
        4 => array(
          'var' => 'rate',
          'type' => TType::DOUBLE,
          ),
        5 => array(
          'var' => 'noLimit',
          'type' => TType::BOOL,
          ),
        );
    }
    if (is_array($vals)) {
      if (isset($vals['id'])) {
        $this->id = $vals['id'];
      }
      if (isset($vals['name'])) {
        $this->name = $vals['name'];
      }
      if (isset($vals['value'])) {
        $this->value = $vals['value'];
      }
      if (isset($vals['rate'])) {
        $this->rate = $vals['rate'];
      }
      if (isset($vals['noLimit'])) {
        $this->noLimit = $vals['noLimit'];
      }
    }
  }

  public function getName() {
    return 'Thrift_CouponType';
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
            $xfer += $input->readI16($this->id);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 2:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->name);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 3:
          if ($ftype == TType::I32) {
            $xfer += $input->readI32($this->value);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 4:
          if ($ftype == TType::DOUBLE) {
            $xfer += $input->readDouble($this->rate);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 5:
          if ($ftype == TType::BOOL) {
            $xfer += $input->readBool($this->noLimit);
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
    $xfer += $output->writeStructBegin('Thrift_CouponType');
    if ($this->id !== null) {
      $xfer += $output->writeFieldBegin('id', TType::I16, 1);
      $xfer += $output->writeI16($this->id);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->name !== null) {
      $xfer += $output->writeFieldBegin('name', TType::STRING, 2);
      $xfer += $output->writeString($this->name);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->value !== null) {
      $xfer += $output->writeFieldBegin('value', TType::I32, 3);
      $xfer += $output->writeI32($this->value);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->rate !== null) {
      $xfer += $output->writeFieldBegin('rate', TType::DOUBLE, 4);
      $xfer += $output->writeDouble($this->rate);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->noLimit !== null) {
      $xfer += $output->writeFieldBegin('noLimit', TType::BOOL, 5);
      $xfer += $output->writeBool($this->noLimit);
      $xfer += $output->writeFieldEnd();
    }
    $xfer += $output->writeFieldStop();
    $xfer += $output->writeStructEnd();
    return $xfer;
  }

}


?>
