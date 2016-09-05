<?php
/**
 *  @generated
 */
class Thrift_ApplicationRemover {
  static $_TSPEC;

  public $userId = null;
  public $applicationId = null;
  public $enterpriseId = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'userId',
          'type' => TType::I64,
          ),
        2 => array(
          'var' => 'applicationId',
          'type' => TType::I64,
          ),
        3 => array(
          'var' => 'enterpriseId',
          'type' => TType::I64,
          ),
        );
    }
    if (is_array($vals)) {
      if (isset($vals['userId'])) {
        $this->userId = $vals['userId'];
      }
      if (isset($vals['applicationId'])) {
        $this->applicationId = $vals['applicationId'];
      }
      if (isset($vals['enterpriseId'])) {
        $this->enterpriseId = $vals['enterpriseId'];
      }
    }
  }

  public function getName() {
    return 'Thrift_ApplicationRemover';
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
          if ($ftype == TType::I64) {
            $xfer += $input->readI64($this->applicationId);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 3:
          if ($ftype == TType::I64) {
            $xfer += $input->readI64($this->enterpriseId);
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
    $xfer += $output->writeStructBegin('Thrift_ApplicationRemover');
    if ($this->userId !== null) {
      $xfer += $output->writeFieldBegin('userId', TType::I64, 1);
      $xfer += $output->writeI64($this->userId);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->applicationId !== null) {
      $xfer += $output->writeFieldBegin('applicationId', TType::I64, 2);
      $xfer += $output->writeI64($this->applicationId);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->enterpriseId !== null) {
      $xfer += $output->writeFieldBegin('enterpriseId', TType::I64, 3);
      $xfer += $output->writeI64($this->enterpriseId);
      $xfer += $output->writeFieldEnd();
    }
    $xfer += $output->writeFieldStop();
    $xfer += $output->writeStructEnd();
    return $xfer;
  }

}


?>
