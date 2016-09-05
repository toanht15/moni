<?php
/**
 *  @generated
 */
class Thrift_AddPoint {
  static $_TSPEC;

  public $socialAccount = null;
  public $userId = null;
  public $point = null;
  public $description = null;
  public $limitDateTime = null;
  public $pointDateTime = null;
  public $clientId = null;
  public $enterpriseId = -1;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'socialAccount',
          'type' => TType::STRUCT,
          'class' => 'Thrift_SocialAccount',
          ),
        2 => array(
          'var' => 'userId',
          'type' => TType::I64,
          ),
        3 => array(
          'var' => 'point',
          'type' => TType::I32,
          ),
        4 => array(
          'var' => 'description',
          'type' => TType::STRING,
          ),
        5 => array(
          'var' => 'limitDateTime',
          'type' => TType::STRING,
          ),
        6 => array(
          'var' => 'pointDateTime',
          'type' => TType::STRING,
          ),
        7 => array(
          'var' => 'clientId',
          'type' => TType::STRING,
          ),
        8 => array(
          'var' => 'enterpriseId',
          'type' => TType::I64,
          ),
        );
    }
    if (is_array($vals)) {
      if (isset($vals['socialAccount'])) {
        $this->socialAccount = $vals['socialAccount'];
      }
      if (isset($vals['userId'])) {
        $this->userId = $vals['userId'];
      }
      if (isset($vals['point'])) {
        $this->point = $vals['point'];
      }
      if (isset($vals['description'])) {
        $this->description = $vals['description'];
      }
      if (isset($vals['limitDateTime'])) {
        $this->limitDateTime = $vals['limitDateTime'];
      }
      if (isset($vals['pointDateTime'])) {
        $this->pointDateTime = $vals['pointDateTime'];
      }
      if (isset($vals['clientId'])) {
        $this->clientId = $vals['clientId'];
      }
      if (isset($vals['enterpriseId'])) {
        $this->enterpriseId = $vals['enterpriseId'];
      }
    }
  }

  public function getName() {
    return 'Thrift_AddPoint';
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
            $this->socialAccount = new Thrift_SocialAccount();
            $xfer += $this->socialAccount->read($input);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 2:
          if ($ftype == TType::I64) {
            $xfer += $input->readI64($this->userId);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 3:
          if ($ftype == TType::I32) {
            $xfer += $input->readI32($this->point);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 4:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->description);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 5:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->limitDateTime);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 6:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->pointDateTime);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 7:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->clientId);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 8:
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
    $xfer += $output->writeStructBegin('Thrift_AddPoint');
    if ($this->socialAccount !== null) {
      if (!is_object($this->socialAccount)) {
        throw new TProtocolException('Bad type in structure.', TProtocolException::INVALID_DATA);
      }
      $xfer += $output->writeFieldBegin('socialAccount', TType::STRUCT, 1);
      $xfer += $this->socialAccount->write($output);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->userId !== null) {
      $xfer += $output->writeFieldBegin('userId', TType::I64, 2);
      $xfer += $output->writeI64($this->userId);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->point !== null) {
      $xfer += $output->writeFieldBegin('point', TType::I32, 3);
      $xfer += $output->writeI32($this->point);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->description !== null) {
      $xfer += $output->writeFieldBegin('description', TType::STRING, 4);
      $xfer += $output->writeString($this->description);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->limitDateTime !== null) {
      $xfer += $output->writeFieldBegin('limitDateTime', TType::STRING, 5);
      $xfer += $output->writeString($this->limitDateTime);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->pointDateTime !== null) {
      $xfer += $output->writeFieldBegin('pointDateTime', TType::STRING, 6);
      $xfer += $output->writeString($this->pointDateTime);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->clientId !== null) {
      $xfer += $output->writeFieldBegin('clientId', TType::STRING, 7);
      $xfer += $output->writeString($this->clientId);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->enterpriseId !== null) {
      $xfer += $output->writeFieldBegin('enterpriseId', TType::I64, 8);
      $xfer += $output->writeI64($this->enterpriseId);
      $xfer += $output->writeFieldEnd();
    }
    $xfer += $output->writeFieldStop();
    $xfer += $output->writeStructEnd();
    return $xfer;
  }

}


?>
