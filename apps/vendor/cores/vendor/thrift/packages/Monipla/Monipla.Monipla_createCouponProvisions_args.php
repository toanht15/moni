<?php
/**
 *  @generated
 */
class Monipla_createCouponProvisions_args {
  static $_TSPEC;

  public $parameter = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'parameter',
          'type' => TType::STRUCT,
          'class' => 'Thrift_CouponProvisionParameter',
          ),
        );
    }
    if (is_array($vals)) {
      if (isset($vals['parameter'])) {
        $this->parameter = $vals['parameter'];
      }
    }
  }

  public function getName() {
    return 'Monipla_createCouponProvisions_args';
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
            $this->parameter = new Thrift_CouponProvisionParameter();
            $xfer += $this->parameter->read($input);
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
    $xfer += $output->writeStructBegin('Monipla_createCouponProvisions_args');
    if ($this->parameter !== null) {
      if (!is_object($this->parameter)) {
        throw new TProtocolException('Bad type in structure.', TProtocolException::INVALID_DATA);
      }
      $xfer += $output->writeFieldBegin('parameter', TType::STRUCT, 1);
      $xfer += $this->parameter->write($output);
      $xfer += $output->writeFieldEnd();
    }
    $xfer += $output->writeFieldStop();
    $xfer += $output->writeStructEnd();
    return $xfer;
  }

}


?>
