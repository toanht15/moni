<?php
/**
 *  @generated
 */
class Thrift_Optin {
  static $_TSPEC;

  public $userId = null;
  public $optin = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'userId',
          'type' => TType::I64,
          ),
        2 => array(
          'var' => 'optin',
          'type' => TType::I16,
          ),
        );
    }
    if (is_array($vals)) {
      if (isset($vals['userId'])) {
        $this->userId = $vals['userId'];
      }
      if (isset($vals['optin'])) {
        $this->optin = $vals['optin'];
      }
    }
  }

  public function getName() {
    return 'Thrift_Optin';
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
          if ($ftype == TType::I64) {
            $xfer += $input->readI64($this->userId);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 2:
          if ($ftype == TType::I16) {
            $xfer += $input->readI16($this->optin);
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
    $xfer += $output->writeStructBegin('Thrift_Optin');
    if ($this->userId !== null) {
      $xfer += $output->writeFieldBegin('userId', TType::I64, 1);
      $xfer += $output->writeI64($this->userId);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->optin !== null) {
      $xfer += $output->writeFieldBegin('optin', TType::I16, 2);
      $xfer += $output->writeI16($this->optin);
      $xfer += $output->writeFieldEnd();
    }
    $xfer += $output->writeFieldStop();
    $xfer += $output->writeStructEnd();
    return $xfer;
  }

}


?>
