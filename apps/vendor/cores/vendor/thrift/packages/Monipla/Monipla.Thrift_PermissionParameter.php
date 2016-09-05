<?php
/**
 *  @generated
 */
class Thrift_PermissionParameter {
  static $_TSPEC;

  public $accessToken = null;
  public $userAttributeMasterId = null;
  public $permissionType = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'accessToken',
          'type' => TType::STRING,
          ),
        2 => array(
          'var' => 'userAttributeMasterId',
          'type' => TType::I64,
          ),
        3 => array(
          'var' => 'permissionType',
          'type' => TType::I32,
          ),
        );
    }
    if (is_array($vals)) {
      if (isset($vals['accessToken'])) {
        $this->accessToken = $vals['accessToken'];
      }
      if (isset($vals['userAttributeMasterId'])) {
        $this->userAttributeMasterId = $vals['userAttributeMasterId'];
      }
      if (isset($vals['permissionType'])) {
        $this->permissionType = $vals['permissionType'];
      }
    }
  }

  public function getName() {
    return 'Thrift_PermissionParameter';
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
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->accessToken);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 2:
          if ($ftype == TType::I64) {
            $xfer += $input->readI64($this->userAttributeMasterId);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 3:
          if ($ftype == TType::I32) {
            $xfer += $input->readI32($this->permissionType);
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
    $xfer += $output->writeStructBegin('Thrift_PermissionParameter');
    if ($this->accessToken !== null) {
      $xfer += $output->writeFieldBegin('accessToken', TType::STRING, 1);
      $xfer += $output->writeString($this->accessToken);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->userAttributeMasterId !== null) {
      $xfer += $output->writeFieldBegin('userAttributeMasterId', TType::I64, 2);
      $xfer += $output->writeI64($this->userAttributeMasterId);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->permissionType !== null) {
      $xfer += $output->writeFieldBegin('permissionType', TType::I32, 3);
      $xfer += $output->writeI32($this->permissionType);
      $xfer += $output->writeFieldEnd();
    }
    $xfer += $output->writeFieldStop();
    $xfer += $output->writeStructEnd();
    return $xfer;
  }

}


?>
