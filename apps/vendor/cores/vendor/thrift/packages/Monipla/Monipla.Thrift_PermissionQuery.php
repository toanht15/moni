<?php
/**
 *  @generated
 */
class Thrift_PermissionQuery {
  static $_TSPEC;

  public $accessTokenId = null;
  public $userAttributeMasterId = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'accessTokenId',
          'type' => TType::I64,
          ),
        2 => array(
          'var' => 'userAttributeMasterId',
          'type' => TType::I64,
          ),
        );
    }
    if (is_array($vals)) {
      if (isset($vals['accessTokenId'])) {
        $this->accessTokenId = $vals['accessTokenId'];
      }
      if (isset($vals['userAttributeMasterId'])) {
        $this->userAttributeMasterId = $vals['userAttributeMasterId'];
      }
    }
  }

  public function getName() {
    return 'Thrift_PermissionQuery';
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
            $xfer += $input->readI64($this->accessTokenId);
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
    $xfer += $output->writeStructBegin('Thrift_PermissionQuery');
    if ($this->accessTokenId !== null) {
      $xfer += $output->writeFieldBegin('accessTokenId', TType::I64, 1);
      $xfer += $output->writeI64($this->accessTokenId);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->userAttributeMasterId !== null) {
      $xfer += $output->writeFieldBegin('userAttributeMasterId', TType::I64, 2);
      $xfer += $output->writeI64($this->userAttributeMasterId);
      $xfer += $output->writeFieldEnd();
    }
    $xfer += $output->writeFieldStop();
    $xfer += $output->writeStructEnd();
    return $xfer;
  }

}


?>
