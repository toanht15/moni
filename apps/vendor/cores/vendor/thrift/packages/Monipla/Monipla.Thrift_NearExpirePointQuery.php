<?php
/**
 *  @generated
 */
class Thrift_NearExpirePointQuery {
  static $_TSPEC;

  public $userId = null;
  public $limitDateTime = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'userId',
          'type' => TType::I64,
          ),
        2 => array(
          'var' => 'limitDateTime',
          'type' => TType::STRING,
          ),
        );
    }
    if (is_array($vals)) {
      if (isset($vals['userId'])) {
        $this->userId = $vals['userId'];
      }
      if (isset($vals['limitDateTime'])) {
        $this->limitDateTime = $vals['limitDateTime'];
      }
    }
  }

  public function getName() {
    return 'Thrift_NearExpirePointQuery';
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
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->limitDateTime);
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
    $xfer += $output->writeStructBegin('Thrift_NearExpirePointQuery');
    if ($this->userId !== null) {
      $xfer += $output->writeFieldBegin('userId', TType::I64, 1);
      $xfer += $output->writeI64($this->userId);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->limitDateTime !== null) {
      $xfer += $output->writeFieldBegin('limitDateTime', TType::STRING, 2);
      $xfer += $output->writeString($this->limitDateTime);
      $xfer += $output->writeFieldEnd();
    }
    $xfer += $output->writeFieldStop();
    $xfer += $output->writeStructEnd();
    return $xfer;
  }

}


?>
