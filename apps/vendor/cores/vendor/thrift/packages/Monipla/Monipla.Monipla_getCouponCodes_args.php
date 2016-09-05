<?php
/**
 *  @generated
 */
class Monipla_getCouponCodes_args {
  static $_TSPEC;

  public $couponCodeQuery = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'couponCodeQuery',
          'type' => TType::STRUCT,
          'class' => 'Thrift_CouponCodeQuery',
          ),
        );
    }
    if (is_array($vals)) {
      if (isset($vals['couponCodeQuery'])) {
        $this->couponCodeQuery = $vals['couponCodeQuery'];
      }
    }
  }

  public function getName() {
    return 'Monipla_getCouponCodes_args';
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
            $this->couponCodeQuery = new Thrift_CouponCodeQuery();
            $xfer += $this->couponCodeQuery->read($input);
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
    $xfer += $output->writeStructBegin('Monipla_getCouponCodes_args');
    if ($this->couponCodeQuery !== null) {
      if (!is_object($this->couponCodeQuery)) {
        throw new TProtocolException('Bad type in structure.', TProtocolException::INVALID_DATA);
      }
      $xfer += $output->writeFieldBegin('couponCodeQuery', TType::STRUCT, 1);
      $xfer += $this->couponCodeQuery->write($output);
      $xfer += $output->writeFieldEnd();
    }
    $xfer += $output->writeFieldStop();
    $xfer += $output->writeStructEnd();
    return $xfer;
  }

}


?>
