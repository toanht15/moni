<?php
/**
 *  @generated
 */
class Thrift_CreateCodeParameter {
  static $_TSPEC;

  public $userId = null;
  public $clientId = null;
  public $scopes = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'userId',
          'type' => TType::I64,
          ),
        2 => array(
          'var' => 'clientId',
          'type' => TType::STRING,
          ),
        3 => array(
          'var' => 'scopes',
          'type' => TType::STRING,
          ),
        );
    }
    if (is_array($vals)) {
      if (isset($vals['userId'])) {
        $this->userId = $vals['userId'];
      }
      if (isset($vals['clientId'])) {
        $this->clientId = $vals['clientId'];
      }
      if (isset($vals['scopes'])) {
        $this->scopes = $vals['scopes'];
      }
    }
  }

  public function getName() {
    return 'Thrift_CreateCodeParameter';
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
            $xfer += $input->readString($this->clientId);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 3:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->scopes);
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
    $xfer += $output->writeStructBegin('Thrift_CreateCodeParameter');
    if ($this->userId !== null) {
      $xfer += $output->writeFieldBegin('userId', TType::I64, 1);
      $xfer += $output->writeI64($this->userId);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->clientId !== null) {
      $xfer += $output->writeFieldBegin('clientId', TType::STRING, 2);
      $xfer += $output->writeString($this->clientId);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->scopes !== null) {
      $xfer += $output->writeFieldBegin('scopes', TType::STRING, 3);
      $xfer += $output->writeString($this->scopes);
      $xfer += $output->writeFieldEnd();
    }
    $xfer += $output->writeFieldStop();
    $xfer += $output->writeStructEnd();
    return $xfer;
  }

}


?>
