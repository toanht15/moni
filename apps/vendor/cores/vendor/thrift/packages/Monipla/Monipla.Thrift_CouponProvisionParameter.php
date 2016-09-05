<?php
/**
 *  @generated
 */
class Thrift_CouponProvisionParameter {
  static $_TSPEC;

  public $executionType = null;
  public $code = null;
  public $account = null;
  public $distributionDate = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'executionType',
          'type' => TType::STRING,
          ),
        2 => array(
          'var' => 'code',
          'type' => TType::STRING,
          ),
        3 => array(
          'var' => 'account',
          'type' => TType::STRUCT,
          'class' => 'Thrift_SocialAccount',
          ),
        4 => array(
          'var' => 'distributionDate',
          'type' => TType::STRING,
          ),
        );
    }
    if (is_array($vals)) {
      if (isset($vals['executionType'])) {
        $this->executionType = $vals['executionType'];
      }
      if (isset($vals['code'])) {
        $this->code = $vals['code'];
      }
      if (isset($vals['account'])) {
        $this->account = $vals['account'];
      }
      if (isset($vals['distributionDate'])) {
        $this->distributionDate = $vals['distributionDate'];
      }
    }
  }

  public function getName() {
    return 'Thrift_CouponProvisionParameter';
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
            $xfer += $input->readString($this->executionType);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 2:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->code);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 3:
          if ($ftype == TType::STRUCT) {
            $this->account = new Thrift_SocialAccount();
            $xfer += $this->account->read($input);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 4:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->distributionDate);
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
    $xfer += $output->writeStructBegin('Thrift_CouponProvisionParameter');
    if ($this->executionType !== null) {
      $xfer += $output->writeFieldBegin('executionType', TType::STRING, 1);
      $xfer += $output->writeString($this->executionType);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->code !== null) {
      $xfer += $output->writeFieldBegin('code', TType::STRING, 2);
      $xfer += $output->writeString($this->code);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->account !== null) {
      if (!is_object($this->account)) {
        throw new TProtocolException('Bad type in structure.', TProtocolException::INVALID_DATA);
      }
      $xfer += $output->writeFieldBegin('account', TType::STRUCT, 3);
      $xfer += $this->account->write($output);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->distributionDate !== null) {
      $xfer += $output->writeFieldBegin('distributionDate', TType::STRING, 4);
      $xfer += $output->writeString($this->distributionDate);
      $xfer += $output->writeFieldEnd();
    }
    $xfer += $output->writeFieldStop();
    $xfer += $output->writeStructEnd();
    return $xfer;
  }

}


?>
