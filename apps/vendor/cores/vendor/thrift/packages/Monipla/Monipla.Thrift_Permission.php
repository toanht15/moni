<?php
/**
 *  @generated
 */
class Thrift_Permission {
  static $_TSPEC;

  public $permissionType = null;
  public $userAttributeMaster = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'permissionType',
          'type' => TType::I64,
          ),
        2 => array(
          'var' => 'userAttributeMaster',
          'type' => TType::STRUCT,
          'class' => 'Thrift_UserAttributeMaster',
          ),
        );
    }
    if (is_array($vals)) {
      if (isset($vals['permissionType'])) {
        $this->permissionType = $vals['permissionType'];
      }
      if (isset($vals['userAttributeMaster'])) {
        $this->userAttributeMaster = $vals['userAttributeMaster'];
      }
    }
  }

  public function getName() {
    return 'Thrift_Permission';
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
            $xfer += $input->readI64($this->permissionType);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 2:
          if ($ftype == TType::STRUCT) {
            $this->userAttributeMaster = new Thrift_UserAttributeMaster();
            $xfer += $this->userAttributeMaster->read($input);
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
    $xfer += $output->writeStructBegin('Thrift_Permission');
    if ($this->permissionType !== null) {
      $xfer += $output->writeFieldBegin('permissionType', TType::I64, 1);
      $xfer += $output->writeI64($this->permissionType);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->userAttributeMaster !== null) {
      if (!is_object($this->userAttributeMaster)) {
        throw new TProtocolException('Bad type in structure.', TProtocolException::INVALID_DATA);
      }
      $xfer += $output->writeFieldBegin('userAttributeMaster', TType::STRUCT, 2);
      $xfer += $this->userAttributeMaster->write($output);
      $xfer += $output->writeFieldEnd();
    }
    $xfer += $output->writeFieldStop();
    $xfer += $output->writeStructEnd();
    return $xfer;
  }

}


?>
