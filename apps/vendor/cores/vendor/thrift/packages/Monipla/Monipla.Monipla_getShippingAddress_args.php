<?php
/**
 *  @generated
 */
class Monipla_getShippingAddress_args {
  static $_TSPEC;

  public $address = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'address',
          'type' => TType::STRUCT,
          'class' => 'Thrift_Address',
          ),
        );
    }
    if (is_array($vals)) {
      if (isset($vals['address'])) {
        $this->address = $vals['address'];
      }
    }
  }

  public function getName() {
    return 'Monipla_getShippingAddress_args';
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
            $this->address = new Thrift_Address();
            $xfer += $this->address->read($input);
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
    $xfer += $output->writeStructBegin('Monipla_getShippingAddress_args');
    if ($this->address !== null) {
      if (!is_object($this->address)) {
        throw new TProtocolException('Bad type in structure.', TProtocolException::INVALID_DATA);
      }
      $xfer += $output->writeFieldBegin('address', TType::STRUCT, 1);
      $xfer += $this->address->write($output);
      $xfer += $output->writeFieldEnd();
    }
    $xfer += $output->writeFieldStop();
    $xfer += $output->writeStructEnd();
    return $xfer;
  }

}


?>
